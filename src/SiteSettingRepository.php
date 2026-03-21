<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read/write access to the `site_setting` key-value table.
 *
 * Settings are stored as plain text strings. Callers are responsible for
 * interpreting values (e.g. '1'/'0' for boolean flags).
 */
class SiteSettingRepository
{
    public function __construct(private readonly \mysqli $db)
    {
    }

    /**
     * Returns the value for $key, or $default if the key does not exist.
     */
    public function get(string $key, string $default = ''): string
    {
        $stmt = $this->db->prepare(
            'SELECT setting_value FROM `site_setting` WHERE setting_key = ? LIMIT 1'
        );
        $stmt->bind_param('s', $key);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_row();
        $stmt->close();

        return $row !== null ? (string) ($row[0] ?? $default) : $default;
    }

    /**
     * Returns all settings as an associative array (key => value).
     *
     * @return array<string, string>
     */
    public function getAll(): array
    {
        $result   = $this->db->query(
            'SELECT setting_key, setting_value FROM `site_setting` ORDER BY setting_key'
        );
        $settings = [];

        if ($result instanceof \mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $settings[(string) $row['setting_key']] = (string) ($row['setting_value'] ?? '');
            }
        }

        return $settings;
    }

    /**
     * Inserts or updates a setting value.
     */
    public function set(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO `site_setting` (setting_key, setting_value)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = ?'
        );
        $stmt->bind_param('sss', $key, $value, $value);
        $stmt->execute();
        $stmt->close();
    }
}
