<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read-only access to the `plugin` table.
 *
 * All public-facing queries exclude plugins with source='api' — those are
 * private uploads accessed only via their UUID at /api/{uuid}/.
 *
 * Only SELECT queries are issued here. All parameters are bound to prevent
 * SQL injection.
 */
class PluginRepository
{
    public function __construct(private readonly \mysqli $db)
    {
    }

    /**
     * Returns a single public plugin row by slug, or null if not found.
     *
     * Excludes API-sourced plugins (source='api').
     *
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT plugin_id, plugin_slug, plugin_name, plugin_version, plugin_installs,
                    plugin_zip, plugin_requires, plugin_tested, plugin_requires_php,
                    plugin_requires_plugins, plugin_rating, plugin_num_ratings,
                    plugin_support_threads, plugin_support_threads_resolved,
                    plugin_downloaded, plugin_last_updated, plugin_added, plugin_source,
                    plugin_author, plugin_author_profile,
                    plugin_short_description, plugin_icons
             FROM plugin
             WHERE plugin_slug = ?
               AND plugin_source != 'api'
             LIMIT 1"
        );

        $stmt->bind_param('s', $slug);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Returns a single plugin row by its numeric ID, regardless of source.
     *
     * Used internally to fetch plugin metadata for API upload result pages.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $pluginId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT plugin_id, plugin_slug, plugin_name, plugin_version,
                    plugin_requires, plugin_tested, plugin_requires_php,
                    plugin_author, plugin_short_description, plugin_source
             FROM plugin
             WHERE plugin_id = ?
             LIMIT 1'
        );

        $stmt->bind_param('i', $pluginId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Returns the $limit most recently *analysed* public plugins, one row per plugin
     * (no duplicates across versions or runners), ordered by the date of the
     * most recent analysis result.
     *
     * Excludes API-sourced plugins (source='api').
     *
     * Each row includes `latest_analysis` (datetime) and `latest_grade`
     * (single letter A–F, or null if the JSON has no grade field).
     *
     * @return list<array<string, mixed>>
     */
    public function getRecentAnalysed(int $limit = 12): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.plugin_slug,
                    p.plugin_name,
                    p.plugin_version,
                    p.plugin_installs,
                    p.plugin_downloaded,
                    p.plugin_last_updated,
                    p.plugin_icons,
                    p.plugin_short_description,
                    MAX(pr.pluginresult_date) AS latest_analysis,
                    (SELECT JSON_VALUE(pr2.pluginresult_result, '$.score.grade')
                     FROM pluginresult pr2
                     WHERE pr2.plugin_id = p.plugin_id
                     ORDER BY pr2.pluginresult_date DESC
                     LIMIT 1) AS latest_grade
             FROM plugin p
             JOIN pluginresult pr ON pr.plugin_id = p.plugin_id
             WHERE p.plugin_source != 'api'
             GROUP BY p.plugin_id
             ORDER BY latest_analysis DESC
             LIMIT ?"
        );

        $stmt->bind_param('i', $limit);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    /**
     * Returns the total number of public plugin records in the database.
     *
     * Excludes API-sourced plugins (source='api').
     */
    public function getTotalCount(): int
    {
        $result = $this->db->query(
            "SELECT COUNT(*) FROM plugin WHERE plugin_source != 'api'"
        );

        if (!$result instanceof \mysqli_result) {
            return 0;
        }

        return (int) ($result->fetch_row()[0] ?? 0);
    }

    /**
     * Returns high-level counts for the stats panel.
     *
     * Excludes API-sourced plugins (source='api').
     *
     * @return array{plugin_count: int, version_count: int}
     */
    public function getStats(): array
    {
        $result = $this->db->query(
            "SELECT
                 (SELECT COUNT(*)
                  FROM plugin
                  WHERE plugin_source != 'api')                     AS plugin_count,
                 (SELECT COUNT(*)
                  FROM plugin_version pv
                  JOIN plugin p ON p.plugin_id = pv.plugin_id
                  WHERE p.plugin_source != 'api')                   AS version_count"
        );

        if (!$result instanceof \mysqli_result) {
            return ['plugin_count' => 0, 'version_count' => 0];
        }

        $row = $result->fetch_assoc();

        return [
            'plugin_count'  => (int) ($row['plugin_count']  ?? 0),
            'version_count' => (int) ($row['version_count'] ?? 0),
        ];
    }

    /**
     * Returns public plugins whose slug contains $term (case-insensitive), ordered
     * by relevance (exact match first, then prefix, then anywhere), up to $limit.
     *
     * Excludes API-sourced plugins (source='api').
     *
     * Each row also includes a `result_count` column with the number of
     * analysis results stored for that plugin.
     *
     * @return list<array<string, mixed>>
     */
    public function searchBySlug(string $term, int $limit = 20): array
    {
        $like = '%' . $term . '%';

        $stmt = $this->db->prepare(
            "SELECT p.plugin_id,
                    p.plugin_slug,
                    p.plugin_name,
                    p.plugin_version,
                    p.plugin_last_updated,
                    COUNT(pr.plugin_id) AS result_count
             FROM plugin p
             LEFT JOIN pluginresult pr ON pr.plugin_id = p.plugin_id
             WHERE p.plugin_slug LIKE ?
               AND p.plugin_source != 'api'
             GROUP BY p.plugin_id
             ORDER BY
                 CASE WHEN p.plugin_slug = ? THEN 0
                      WHEN p.plugin_slug LIKE ? THEN 1
                      ELSE 2 END,
                 p.plugin_slug
             LIMIT ?"
        );

        $prefix = $term . '%';
        $stmt->bind_param('sssi', $like, $term, $prefix, $limit);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }
}
