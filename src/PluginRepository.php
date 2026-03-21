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
            'SELECT plugin_slug, plugin_name, plugin_version, plugin_installs,
                    plugin_zip, plugin_requires, plugin_tested, plugin_requires_php,
                    plugin_requires_plugins, plugin_rating, plugin_num_ratings,
                    plugin_support_threads, plugin_support_threads_resolved,
                    plugin_downloaded, plugin_last_updated, plugin_added, plugin_source
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
                    plugin_installs, plugin_downloaded, plugin_last_updated
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
}
