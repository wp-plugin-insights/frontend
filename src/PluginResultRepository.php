<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read-only access to the pluginresult table.
 *
 * Each row stores one runner's full JSON analysis output for a specific
 * plugin version. This repository is used by the admin panel to display
 * pipeline activity and by the plugin detail page to render analysis cards.
 *
 * Result JSON structure (produced by runners):
 *   score.grade        — letter grade A–F
 *   score.percentage   — 0–100
 *   score.reasoning    — human-readable explanation
 *   metrics            — runner-specific numeric counters
 *   issues             — array of issue objects (severity, message, ...)
 *   presentation       — rendering hints for the frontend (tables, lists, ...)
 */
class PluginResultRepository
{
    public function __construct(private readonly \mysqli $db)
    {
    }

    /**
     * Returns a summary row for every runner that has produced at least one
     * result: result count and the date of the most recent result.
     *
     * Runners with zero results are excluded (use RunnerRepository for the
     * full runner list).
     *
     * @return list<array{
     *     runner_id: int,
     *     runner_name: string,
     *     runner_slug: string,
     *     result_count: int,
     *     latest_date: string
     * }>
     */
    public function getRunnerSummary(): array
    {
        $result = $this->db->query(
            'SELECT r.runner_id,
                    r.runner_name,
                    r.runner_slug,
                    COUNT(*) AS result_count,
                    MAX(pr.pluginresult_date) AS latest_date
             FROM pluginresult pr
             JOIN runner r ON r.runner_id = pr.runner_id
             GROUP BY r.runner_id, r.runner_name, r.runner_slug
             ORDER BY r.runner_name'
        );

        if (!$result instanceof \mysqli_result) {
            return [];
        }

        /** @var list<array{runner_id: int, runner_name: string, runner_slug: string, result_count: int, latest_date: string}> */
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Returns the $limit most recent analysis results across all runners,
     * with plugin slug, version, runner identity, grade, and date.
     *
     * Grade is extracted from the JSON using MariaDB's JSON_VALUE function.
     * Rows where the grade cannot be extracted return null for that field.
     *
     * @return list<array<string, mixed>>
     */
    public function getRecent(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT p.plugin_slug,
                    pr.plugin_version,
                    r.runner_name,
                    r.runner_slug,
                    JSON_VALUE(pr.pluginresult_result, '$.score.grade') AS grade,
                    pr.pluginresult_date
             FROM pluginresult pr
             JOIN plugin p ON p.plugin_id = pr.plugin_id
             JOIN runner r ON r.runner_id = pr.runner_id
             ORDER BY pr.pluginresult_date DESC
             LIMIT ?"
        );
        $stmt->bind_param('i', $limit);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    /**
     * Returns all results for a specific plugin + version, keyed by runner slug.
     *
     * Used by the plugin detail page to render per-runner analysis cards.
     *
     * @return array<string, array<string, mixed>>  runner_slug → decoded result
     */
    public function getByPluginVersion(int $pluginId, string $version): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.runner_slug, pr.pluginresult_result, pr.pluginresult_date
             FROM pluginresult pr
             JOIN runner r ON r.runner_id = pr.runner_id
             WHERE pr.plugin_id = ?
               AND pr.plugin_version = ?
             ORDER BY pr.pluginresult_date DESC'
        );
        $stmt->bind_param('is', $pluginId, $version);
        $stmt->execute();

        $rows   = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // One result per runner slug (most recent wins, ORDER BY DESC + keyed overwrite)
        $bySlug = [];
        foreach (array_reverse($rows) as $row) {
            $decoded = json_decode((string) $row['pluginresult_result'], true);
            if (is_array($decoded)) {
                $decoded['_date'] = $row['pluginresult_date'];
                $bySlug[(string) $row['runner_slug']] = $decoded;
            }
        }

        return $bySlug;
    }
}
