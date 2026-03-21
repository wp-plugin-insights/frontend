<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read-only access to the `plugin` table.
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
     * Returns a single plugin row by slug, or null if not found.
     *
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT plugin_id, plugin_slug, plugin_name, plugin_version, plugin_installs,
                    plugin_zip, plugin_requires, plugin_tested, plugin_requires_php,
                    plugin_requires_plugins, plugin_rating, plugin_num_ratings,
                    plugin_support_threads, plugin_support_threads_resolved,
                    plugin_downloaded, plugin_last_updated, plugin_added, plugin_source,
                    plugin_author, plugin_author_profile,
                    plugin_short_description, plugin_icons
             FROM plugin
             WHERE plugin_slug = ?
             LIMIT 1'
        );

        $stmt->bind_param('s', $slug);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Returns the most recently updated plugins that have a name populated.
     *
     * @return list<array<string, mixed>>
     */
    public function getRecent(int $limit = 12): array
    {
        $stmt = $this->db->prepare(
            'SELECT plugin_slug, plugin_name, plugin_version,
                    plugin_installs, plugin_downloaded, plugin_last_updated,
                    plugin_icons, plugin_short_description
             FROM plugin
             WHERE plugin_name IS NOT NULL AND plugin_name != \'\'
             ORDER BY plugin_last_updated DESC
             LIMIT ?'
        );

        $stmt->bind_param('i', $limit);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    /**
     * Returns the total number of plugin records in the database.
     */
    public function getTotalCount(): int
    {
        $result = $this->db->query('SELECT COUNT(*) FROM plugin');

        if (!$result instanceof \mysqli_result) {
            return 0;
        }

        return (int) ($result->fetch_row()[0] ?? 0);
    }

    /**
     * Returns high-level counts for the stats panel.
     *
     * @return array{plugin_count: int, version_count: int}
     */
    public function getStats(): array
    {
        $result = $this->db->query(
            'SELECT
                 (SELECT COUNT(*) FROM plugin)          AS plugin_count,
                 (SELECT COUNT(*) FROM plugin_version)  AS version_count'
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
     * Returns plugins whose slug contains $term (case-insensitive), ordered by
     * relevance (exact match first, then prefix, then anywhere), up to $limit.
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
            'SELECT p.plugin_id,
                    p.plugin_slug,
                    p.plugin_name,
                    p.plugin_version,
                    p.plugin_last_updated,
                    COUNT(pr.plugin_id) AS result_count
             FROM plugin p
             LEFT JOIN pluginresult pr ON pr.plugin_id = p.plugin_id
             WHERE p.plugin_slug LIKE ?
             GROUP BY p.plugin_id
             ORDER BY
                 CASE WHEN p.plugin_slug = ? THEN 0
                      WHEN p.plugin_slug LIKE ? THEN 1
                      ELSE 2 END,
                 p.plugin_slug
             LIMIT ?'
        );

        $prefix = $term . '%';
        $stmt->bind_param('sssi', $like, $term, $prefix, $limit);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }
}
