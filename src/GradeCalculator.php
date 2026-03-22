<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Calculates the overall weighted grade for a plugin from its analysis results.
 *
 * All grade logic lives here so it can be maintained independently of the
 * rendering templates.
 *
 * Usage:
 *   $result = GradeCalculator::calculate($compatGrade, $analysisResults);
 *   $result['grade']     // 'A'–'F', or '' when no scoreable data
 *   $result['pct']       // int 0–100, or null
 *   $result['css_class'] // 'grade-a' … 'grade-f', or ''
 *   $result['breakdown'] // per-runner entries (see return type below)
 */
class GradeCalculator
{
    /**
     * Runner weights used in the weighted average.
     *
     * Weight 0.0 = runner appears in the breakdown strip for transparency
     * but does NOT contribute to the overall score.
     *
     * The 'compat' key is synthetic (Compatibility & Requirements card).
     *
     * @var array<string, float>
     */
    private const WEIGHTS = [
        'compat'            => 2.0,
        'security'          => 3.0,
        'php-compatibility' => 2.5,
        'coding-standards'  => 2.0,
        'wp-since'          => 1.5,
        'hooks'             => 1.0,
        'translate'         => 0.5,
        'translations'      => 0.5,
        'ai'                => 0.0,
        'basic'             => 0.0,
    ];

    /** Default weight for runners not listed above. */
    private const DEFAULT_WEIGHT = 1.0;

    /**
     * Canonical midpoint percentages used when a runner reports a letter grade
     * but no numeric percentage.
     *
     * @var array<string, int>
     */
    private const GRADE_PCT = ['A' => 95, 'B' => 80, 'C' => 65, 'D' => 50, 'F' => 20];

    /**
     * Computes the overall grade from the compatibility grade and runner results.
     *
     * @param string                              $compatGrade     'A'–'F'
     * @param array<string, array<string, mixed>> $analysisResults runner_slug → result row
     *
     * @return array{
     *   grade:     string,
     *   pct:       int|null,
     *   css_class: string,
     *   breakdown: list<array{
     *     slug: string, label: string, grade: string,
     *     pct: int, weight: float, scored: bool, css_class: string
     *   }>
     * }
     */
    public static function calculate(string $compatGrade, array $analysisResults): array
    {
        $breakdown = [];

        // Compatibility entry — synthetic, always present when a compat grade exists.
        $compatPct = self::gradeToPercentage($compatGrade);
        if ($compatPct !== null) {
            $breakdown[] = [
                'slug'      => 'compat',
                'label'     => 'Compatibility',
                'grade'     => $compatGrade,
                'pct'       => $compatPct,
                'weight'    => self::WEIGHTS['compat'],
                'scored'    => true,
                'css_class' => self::gradeClass($compatGrade),
            ];
        }

        // Runner entries.
        foreach ($analysisResults as $slug => $row) {
            $grade = (string) ($row['score']['grade'] ?? '');
            if ($grade === '' || !isset(self::GRADE_PCT[$grade])) {
                continue;
            }

            // Prefer the explicit percentage; fall back to the grade midpoint.
            $rawPct = $row['score']['percentage'] ?? null;
            $pct    = (is_int($rawPct) || is_float($rawPct))
                ? (int) $rawPct
                : self::gradeToPercentage($grade);

            if ($pct === null) {
                continue;
            }

            $weight      = (float) (self::WEIGHTS[$slug] ?? self::DEFAULT_WEIGHT);
            $breakdown[] = [
                'slug'      => $slug,
                'label'     => (string) ($row['runner'] ?? $slug),
                'grade'     => $grade,
                'pct'       => $pct,
                'weight'    => $weight,
                'scored'    => $weight > 0.0,
                'css_class' => self::gradeClass($grade),
            ];
        }

        // Weighted average across all scored entries.
        $weightedSum = 0.0;
        $weightTotal = 0.0;
        foreach ($breakdown as $entry) {
            if ($entry['scored']) {
                $weightedSum += $entry['pct'] * $entry['weight'];
                $weightTotal += $entry['weight'];
            }
        }

        if ($weightTotal <= 0.0 || empty($breakdown)) {
            return ['grade' => '', 'pct' => null, 'css_class' => '', 'breakdown' => $breakdown];
        }

        $overallPct   = (int) round($weightedSum / $weightTotal);
        $overallGrade = self::pctToGrade($overallPct);

        return [
            'grade'     => $overallGrade,
            'pct'       => $overallPct,
            'css_class' => self::gradeClass($overallGrade),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Computes the Compatibility & Requirements letter grade from raw plugin data.
     *
     * Replicates the penalty logic from the plugin detail template so the same
     * grade can be produced anywhere without template-level code.
     *
     * Penalties (cumulative, capped at F):
     *   No WP version declared              → −1 grade
     *   No PHP version declared             → −1 grade
     *   PHP below WP-required PHP           → −2 grades
     *   Last update > 1 year ago            → −1 grade
     *   Tested-up-to behind current WP      → −1 grade
     *
     * @param string                                                  $requires      plugin_requires (e.g. "6.0")
     * @param string                                                  $requiresPhp   plugin_requires_php (e.g. "8.0")
     * @param string                                                  $tested        plugin_tested (e.g. "6.7")
     * @param string                                                  $lastUpdated   plugin_last_updated datetime
     * @param list<array{wp_version: string, php_min_version: string}> $wpCompatRows from WpCompatRepository::getAll()
     * @param string                                                  $latestWpMinor e.g. "6.7"
     */
    public static function compatGrade(
        string $requires,
        string $requiresPhp,
        string $tested,
        string $lastUpdated,
        array $wpCompatRows,
        string $latestWpMinor
    ): string {
        $gl  = ['A', 'B', 'C', 'D', 'F'];
        $pos = 0;

        if ($requires === '') {
            $pos++;
        }

        if ($requiresPhp === '') {
            $pos++;
        }

        if ($requires !== '' && $requiresPhp !== '') {
            $wpMinPhp = self::resolveWpPhpMin($requires, $wpCompatRows);
            if ($wpMinPhp !== null && version_compare($requiresPhp, $wpMinPhp, '<')) {
                $pos += 2;
            }
        }

        if ($lastUpdated !== '') {
            $updatedYear = (int) substr($lastUpdated, 0, 4);
            if ($updatedYear < (int) date('Y') - 1) {
                $pos++;
            }
        }

        if ($tested !== '' && $latestWpMinor !== '' && version_compare($tested, $latestWpMinor, '<')) {
            $pos++;
        }

        $pos = max(0, min(count($gl) - 1, $pos));

        return $gl[$pos];
    }

    /**
     * Finds the minimum PHP version required for a given WordPress version
     * by walking the compat rows (sorted ascending) and returning the last
     * entry whose wp_version is ≤ $wpVersion.
     *
     * @param list<array{wp_version: string, php_min_version: string}> $wpCompatRows
     */
    private static function resolveWpPhpMin(string $wpVersion, array $wpCompatRows): ?string
    {
        $best = null;
        foreach ($wpCompatRows as $row) {
            if (version_compare($row['wp_version'], $wpVersion, '<=')) {
                $best = $row['php_min_version'];
            }
        }

        return $best;
    }

    /**
     * Converts a percentage (0–100) to a letter grade.
     */
    public static function pctToGrade(int $pct): string
    {
        return match (true) {
            $pct >= 90 => 'A',
            $pct >= 75 => 'B',
            $pct >= 60 => 'C',
            $pct >= 45 => 'D',
            default    => 'F',
        };
    }

    /**
     * Returns the canonical midpoint percentage for a letter grade,
     * or null if the grade letter is not recognised.
     */
    public static function gradeToPercentage(string $grade): ?int
    {
        return self::GRADE_PCT[$grade] ?? null;
    }

    /**
     * Maps a letter grade to its CSS helper class.
     */
    private static function gradeClass(string $grade): string
    {
        return match ($grade) {
            'A'     => 'grade-a',
            'B'     => 'grade-b',
            'C'     => 'grade-c',
            'D'     => 'grade-d',
            default => 'grade-f',
        };
    }
}
