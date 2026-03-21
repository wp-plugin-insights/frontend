<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read-only access to the `cron_run` table.
 *
 * Provides the admin panel with cron execution history, grouped by cron name,
 * to display health statistics for the last N runs of each script.
 *
 * All queries use prepared statements with bound parameters.
 */
class CronRunRepository
{
    /**
     * @param \mysqli $db Active database connection.
     */
    public function __construct(private readonly \mysqli $db)
    {
    }

    /**
     * Returns the last $limit runs for every cron name, grouped by name.
     *
     * Uses a window function (ROW_NUMBER OVER PARTITION BY cron_name) so that
     * exactly $limit rows per cron are returned in one query.
     *
     * Within each group rows are ordered newest-first. Groups themselves are
     * ordered alphabetically by cron_name.
     *
     * @param  int $limit Maximum runs to return per cron (default 10).
     *
     * @return array<string, list<array{
     *     cron_run_id: int,
     *     cron_name: string,
     *     started_at: string,
     *     finished_at: string|null,
     *     duration_ms: int|null,
     *     status: string,
     *     items_processed: int,
     *     error_message: string|null,
     * }>>  Associative array keyed by cron_name.
     */
    public function getRecentByName(int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT cron_run_id, cron_name, started_at, finished_at,
                    duration_ms, status, items_processed, error_message
             FROM (
                 SELECT cron_run_id, cron_name, started_at, finished_at,
                        duration_ms, status, items_processed, error_message,
                        ROW_NUMBER() OVER (
                            PARTITION BY cron_name
                            ORDER BY started_at DESC
                        ) AS rn
                 FROM `cron_run`
             ) sub
             WHERE sub.rn <= ?
             ORDER BY cron_name ASC, started_at DESC'
        );

        if ($stmt === false) {
            return [];
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();

        $result  = $stmt->get_result();
        $grouped = [];

        while ($row = $result->fetch_assoc()) {
            $name             = (string) $row['cron_name'];
            $grouped[$name][] = $row;
        }

        $stmt->close();

        return $grouped;
    }

    /**
     * Returns one summary row per cron name: the latest run's status and
     * timestamp, plus the total number of recorded runs.
     *
     * Used by the admin panel overview to show a quick health indicator
     * without loading the full run history.
     *
     * @return list<array{
     *     cron_name: string,
     *     last_started_at: string,
     *     last_status: string,
     *     last_duration_ms: int|null,
     *     total_runs: int,
     * }>
     */
    public function getSummary(): array
    {
        $result = $this->db->query(
            'SELECT cron_name,
                    MAX(started_at)  AS last_started_at,
                    SUBSTRING_INDEX(
                        GROUP_CONCAT(status ORDER BY started_at DESC),
                        \',\', 1
                    )                AS last_status,
                    SUBSTRING_INDEX(
                        GROUP_CONCAT(
                            IFNULL(duration_ms, \'\') ORDER BY started_at DESC
                        ),
                        \',\', 1
                    )                AS last_duration_ms,
                    COUNT(*)         AS total_runs
             FROM `cron_run`
             GROUP BY cron_name
             ORDER BY cron_name ASC'
        );

        if ($result === false) {
            return [];
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }
}
