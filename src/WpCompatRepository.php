<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read-only access to the `wp_php_compat` table.
 *
 * Stores the minimum PHP version required by each major WordPress release.
 * Used by the frontend to validate that a plugin's declared PHP requirement
 * is not lower than what its declared minimum WordPress version needs.
 *
 * The table is seeded and maintained by the migration system (migration 2.1.0).
 */
class WpCompatRepository
{
    /**
     * @param \mysqli $db Active database connection.
     */
    public function __construct(private readonly \mysqli $db)
    {
    }

    /**
     * Returns all WordPress → PHP minimum version mappings, ordered by
     * WordPress version ascending.
     *
     * @return list<array{wp_version: string, php_min_version: string}>
     */
    public function getAll(): array
    {
        $result = $this->db->query(
            'SELECT wp_version, php_min_version
             FROM `wp_php_compat`
             ORDER BY wp_version ASC'
        );

        if ($result === false) {
            return [];
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                'wp_version'      => (string) $row['wp_version'],
                'php_min_version' => (string) $row['php_min_version'],
            ];
        }

        return $rows;
    }

    /**
     * Inserts or updates a WordPress → PHP minimum version mapping.
     *
     * Version strings are trimmed and validated to contain only digits and dots.
     * Returns false when either string is empty or contains invalid characters.
     *
     * @param  string $wpVersion      WordPress version string, e.g. "6.6".
     * @param  string $phpMinVersion  PHP minimum version string, e.g. "7.2.24".
     * @return bool   True on success, false on invalid input or query failure.
     */
    public function upsert(string $wpVersion, string $phpMinVersion): bool
    {
        $wpVersion     = trim($wpVersion);
        $phpMinVersion = trim($phpMinVersion);

        if (
            $wpVersion === ''
            || $phpMinVersion === ''
            || !preg_match('/^\d+(\.\d+)*$/', $wpVersion)
            || !preg_match('/^\d+(\.\d+)*$/', $phpMinVersion)
        ) {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO `wp_php_compat` (wp_version, php_min_version) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE php_min_version = VALUES(php_min_version)'
        );

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param('ss', $wpVersion, $phpMinVersion);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Deletes the mapping for the given WordPress version.
     *
     * @param  string $wpVersion WordPress version string, e.g. "6.6".
     * @return bool   True on success (including when the row did not exist).
     */
    public function delete(string $wpVersion): bool
    {
        $wpVersion = trim($wpVersion);
        if ($wpVersion === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            'DELETE FROM `wp_php_compat` WHERE wp_version = ?'
        );

        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param('s', $wpVersion);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    /**
     * Returns the minimum PHP version required to run the given WordPress version.
     *
     * Finds the highest `wp_version` in the table that is less than or equal to
     * `$wpVersion` (using version_compare), then returns its `php_min_version`.
     *
     * Returns null when the table is empty, the table does not exist, or no
     * entry is found that is <= the requested version.
     *
     * @param  string $wpVersion WordPress version string, e.g. "6.6" or "5.8.3".
     * @return string|null       PHP version string, e.g. "7.2.24", or null.
     */
    public function getPhpRequirementForWp(string $wpVersion): ?string
    {
        $rows = $this->getAll();

        if ($rows === []) {
            return null;
        }

        // Walk from highest WP version downward; return first whose wp_version ≤ $wpVersion.
        $reversed = array_reverse($rows);
        foreach ($reversed as $row) {
            if (version_compare($row['wp_version'], $wpVersion, '<=')) {
                return $row['php_min_version'];
            }
        }

        return null;
    }
}
