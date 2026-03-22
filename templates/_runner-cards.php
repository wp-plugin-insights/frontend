<?php

/**
 * Shared partial — runner analysis cards.
 *
 * Renders one collapsible card per runner in $analysisResults.
 * When the array is empty, shows a single "Analysis pending" card.
 *
 * Required variables (inherited from the including template):
 *   $i18n            I18n
 *   $analysisResults array<string, array<string, mixed>>  — runner_slug → decoded result JSON
 */

declare(strict_types=1);

// Map runner slugs to Bootstrap Icons class names
$_runnerIcons = [
    'security'          => 'bi-shield-check',
    'php-compatibility' => 'bi-filetype-php',
    'translate'         => 'bi-translate',
    'translations'      => 'bi-translate',
    'wp-since'          => 'bi-wordpress',
    'hooks'             => 'bi-puzzle',
    'code-quality'      => 'bi-code-slash',
    'performance'       => 'bi-speedometer2',
    'license'           => 'bi-file-earmark-text',
    'maintenance'       => 'bi-tools',
];

// Grade → CSS class (matches .grade-a … .grade-f in stylesheet)
$_gradeClass = [
    'A' => 'grade-a', 'B' => 'grade-b', 'C' => 'grade-c',
    'D' => 'grade-d', 'F' => 'grade-f',
];

// Severity → Bootstrap badge class
$_severityBadge = [
    'critical' => 'text-bg-danger',
    'high'     => 'text-bg-danger',
    'medium'   => 'text-bg-warning',
    'low'      => 'text-bg-secondary',
    'info'     => 'text-bg-info',
];

if (empty($analysisResults)) :
    ?>
<div class="card analysis-card">
    <div class="card-header p-0">
        <button class="card-header-btn"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#card-analysis-pending"
                aria-expanded="true"
                aria-controls="card-analysis-pending">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-hourglass-split fs-5 text-body-secondary" aria-hidden="true"></i>
                <span class="fw-semibold"><?= htmlspecialchars($i18n->t('plugin.analysis_title'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <i class="bi bi-chevron-down toggle-icon text-body-secondary" aria-hidden="true"></i>
        </button>
    </div>
    <div class="collapse show" id="card-analysis-pending">
        <div class="card-body border-top">
            <p class="text-body-secondary mb-0">
                <?= htmlspecialchars($i18n->t('plugin.analysis_pending'), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>
</div>
<?php else :
    $_cardIndex = 0;
    foreach ($analysisResults as $_runnerSlug => $_result) :
        $_cardIndex++;
        $_cardId      = 'card-runner-' . htmlspecialchars($_runnerSlug, ENT_QUOTES, 'UTF-8');
        $_runnerName  = (string) ($_result['_runner_name'] ?? ucwords(str_replace('-', ' ', $_runnerSlug)));
        $_resultDate  = (string) ($_result['_date'] ?? '');
        $_score       = is_array($_result['score'] ?? null) ? $_result['score'] : [];
        $_grade       = strtoupper((string) ($_score['grade']      ?? ''));
        $_percentage  = isset($_score['percentage']) ? (int) $_score['percentage'] : null;
        $_reasoning   = (string) ($_score['reasoning'] ?? '');
        $_metrics     = is_array($_result['metrics'] ?? null) ? $_result['metrics'] : [];
        $_issues      = is_array($_result['issues']  ?? null) ? $_result['issues']  : [];
        $_iconClass   = $_runnerIcons[$_runnerSlug] ?? 'bi-bar-chart';
        $_gradeCs     = $_gradeClass[$_grade]       ?? '';
        $_isPhpCompat        = ($_runnerSlug === 'php-compatibility');
        $_isTranslate        = ($_runnerSlug === 'translate' || $_runnerSlug === 'translations');
        $_isWpSince          = ($_runnerSlug === 'wp-since');
        $_isHooks            = ($_runnerSlug === 'hooks');
        $_isCodingStandards  = ($_runnerSlug === 'coding-standards');
        $_isSecurity         = ($_runnerSlug === 'security');

        // ── php-compatibility: grade adjustment ───────────────────────────
        // The runner computes its grade without knowing the WP-required PHP floor.
        // Combine declared_min_php, detected_min_php, and $wpMinPhpRequired to find
        // the effective minimum, then adjust the grade before the card header renders.
        //
        //  declared == effective           → reward (+1 step: e.g. C→B)
        //  declared > effective (too high) → penalise (−1 step same major, −2 cross-major)
        //  declared < effective (too low)  → severely penalise (−3 steps, min F)
        $_pcGradeNote = '';
        if ($_isPhpCompat) {
            $_earlyDetected  = isset($_metrics['detected_min_php']) ? (string) $_metrics['detected_min_php'] : null;
            $_earlyDeclared  = isset($_metrics['declared_min_php'])  ? (string) $_metrics['declared_min_php']  : null;

            // Apply plugin-header fallback for declared (same logic as the display block below)
            if (
                $_earlyDeclared === null
                && isset($requiresPhp) && is_string($requiresPhp) && $requiresPhp !== ''
            ) {
                $_earlyDeclared = $requiresPhp;
            }

            // Compute effective minimum = max(detected, wpMinPhpRequired).
            // Declared is intentionally excluded so we can compare it against the true floor.
            $_earlyEffective = $_earlyDetected;
            $_earlyWpReq     = isset($wpMinPhpRequired) && is_string($wpMinPhpRequired)
                ? $wpMinPhpRequired : null;
            if (
                $_earlyWpReq !== null && $_earlyWpReq !== ''
                && ($_earlyEffective === null || version_compare($_earlyWpReq, $_earlyEffective, '>'))
            ) {
                $_earlyEffective = $_earlyWpReq;
            }

            // Only adjust when we have both declared and effective
            if ($_earlyDeclared !== null && $_earlyDeclared !== '' && $_earlyEffective !== null) {
                $_gl    = ['A', 'B', 'C', 'D', 'F'];
                $_gPos  = (int) (array_search($_grade, $_gl, true) !== false
                    ? array_search($_grade, $_gl, true) : 2);
                $cmpDE  = version_compare($_earlyDeclared, $_earlyEffective);

                if ($cmpDE === 0) {
                    // Exact match: improve one step
                    $_gPos -= 1;
                    $_pcGradeNote = $i18n->t('runner.php_grade_note_match', [
                        'declared'  => $_earlyDeclared,
                        'effective' => $_earlyEffective,
                    ]);
                } elseif ($cmpDE > 0) {
                    // Over-declared: unnecessarily high
                    $_dMaj = (int) explode('.', $_earlyDeclared)[0];
                    $_eMaj = (int) explode('.', $_earlyEffective)[0];
                    $_drop = ($_dMaj !== $_eMaj) ? 2 : 1;
                    $_gPos += $_drop;
                    $_pcGradeNote = $i18n->t('runner.php_grade_note_over', [
                        'declared'  => $_earlyDeclared,
                        'effective' => $_earlyEffective,
                    ]);
                } else {
                    // Under-declared: wrong, below effective minimum
                    $_gPos += 3;
                    $_pcGradeNote = $i18n->t('runner.php_grade_note_under', [
                        'declared'  => $_earlyDeclared,
                        'effective' => $_earlyEffective,
                    ]);
                }

                $_gPos        = max(0, min(count($_gl) - 1, $_gPos));
                $_grade       = $_gl[$_gPos];
                $_gradeCs     = $_gradeClass[$_grade] ?? '';
                $_percentage  = match ($_grade) {
                    'A' => 95, 'B' => 80, 'C' => 65, 'D' => 50, default => 20,
                };
            }
        }

        // Translate, hooks, and security runners: issues is an associative object {high,medium,low,trivial,top:[...]}
        // Extract the top-issues list and suppress the generic issues loop.
        $_translateIssueCounts = [];
        if (($_isTranslate || $_isHooks || $_isSecurity) && isset($_issues['top'])) {
            $_translateIssueCounts = $_issues;
            $_issues               = [];
        }

        ?>
<div class="card analysis-card">
    <div class="card-header p-0">
        <button class="card-header-btn"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#<?= $_cardId ?>"
                aria-expanded="false"
                aria-controls="<?= $_cardId ?>">
            <div class="d-flex align-items-center gap-2">
                <i class="bi <?= htmlspecialchars($_iconClass, ENT_QUOTES, 'UTF-8') ?> fs-5 text-body-secondary" aria-hidden="true"></i>
                <span class="fw-semibold"><?= htmlspecialchars($_runnerName, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <?php if ($_grade !== '' && $_gradeCs !== '') : ?>
                <span class="grade <?= htmlspecialchars($_gradeCs, ENT_QUOTES, 'UTF-8') ?>"
                      aria-label="<?= htmlspecialchars($i18n->t('plugin.grade_label', ['grade' => $_grade]), ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($_grade, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
                <i class="bi bi-chevron-down toggle-icon text-body-secondary" aria-hidden="true"></i>
            </div>
        </button>
    </div>
    <div class="collapse" id="<?= $_cardId ?>">
        <div class="card-body border-top">

            <?php
            // Dedicated blocks below handle their own summaries; suppress generic reasoning.
            $_showReasoning = $_reasoning !== '' && !$_isPhpCompat && !$_isTranslate && !$_isWpSince && !$_isHooks && !$_isSecurity;
            ?>
            <?php if ($_percentage !== null || $_showReasoning) : ?>
            <div class="mb-3">
                <?php if ($_percentage !== null) : ?>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="progress flex-grow-1" style="height:8px"
                         role="progressbar"
                         aria-valuenow="<?= $_percentage ?>"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         aria-label="<?= htmlspecialchars($i18n->t('plugin.score_label'), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="progress-bar <?= $_gradeCs === 'grade-a' || $_gradeCs === 'grade-b' ? 'bg-success' : ($_gradeCs === 'grade-c' ? 'bg-warning' : 'bg-danger') ?>"
                             style="width:<?= $_percentage ?>%"></div>
                    </div>
                    <span class="small text-body-secondary text-nowrap"><?= $_percentage ?>%</span>
                </div>
                <?php endif; ?>
                <?php if ($_showReasoning) : ?>
                <p class="mb-0 small"><?= htmlspecialchars($_reasoning, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php
            // Keys handled by dedicated blocks; exclude from generic rendering.
            $_phpCompatKeys = [
                'detected_min_php', 'declared_min_php', 'declared_min_php_source',
                'tested_versions', 'based_on_version_scan', 'summary',
            ];
            $_translateKeys = [
                'has_translatable_strings',
                'translation_locales_detected', 'translation_locales_compliant',
                'translation_major_locale_coverage',
                'text_domain_consistency', 'text_domain_valid',
                'issues_high', 'issues_medium', 'issues_low', 'issues_trivial',
                'untranslated_strings',
                'js_has_translations', 'js_total_strings', 'js_translated',
                'load_hook_has_call', 'load_hook_issues', 'best_practices_issues',
            ];
            $_wpSinceKeys = ['declared_min_wp', 'suggested_min_wp'];
            $_hooksKeys   = [
                'total_actions_used', 'total_filters_used',
                'total_actions_provided', 'total_filters_provided',
                'total_hooks_used', 'total_hooks_provided',
                'unique_actions_used', 'unique_filters_used',
                'unique_actions_provided', 'unique_filters_provided',
                'documented_hooks_count', 'documented_hooks_percentage',
                'well_documented_hooks_count',
            ];
            $_csKeys = [
                'total_errors', 'total_warnings', 'total_fixable',
                'files_with_issues', 'scanned_files', 'php_files',
                'php_non_empty_lines', 'weighted_issue_points',
                'issue_density_per_100_lines', 'summary',
            ];
            $_secKeys = [
                'files_total', 'files_selected', 'files_analyzed', 'files_skipped',
                'batches_total', 'batches_failed', 'findings_total', 'findings_critical',
                'findings_error', 'findings_warning', 'findings_info',
            ];

            // Split metrics into scalar (display as stat boxes) and array (display as tag lists),
            // skipping keys rendered separately by dedicated blocks.
            $_metricsScalar = [];
            $_metricsArray  = [];
            foreach ($_metrics as $_mk => $_mv) {
                if ($_isPhpCompat && in_array($_mk, $_phpCompatKeys, true)) {
                    continue;
                }
                if ($_isTranslate && in_array($_mk, $_translateKeys, true)) {
                    continue;
                }
                if ($_isWpSince && in_array($_mk, $_wpSinceKeys, true)) {
                    continue;
                }
                if ($_isHooks && in_array($_mk, $_hooksKeys, true)) {
                    continue;
                }
                if ($_isCodingStandards && in_array($_mk, $_csKeys, true)) {
                    continue;
                }
                if ($_isSecurity && in_array($_mk, $_secKeys, true)) {
                    continue;
                }
                if (is_array($_mv)) {
                    $_metricsArray[$_mk] = $_mv;
                } elseif ($_mv !== null && $_mv !== '') {
                    $_metricsScalar[$_mk] = $_mv;
                }
            }
            ?>

            <?php if ($_isPhpCompat) :
                // ── php-compatibility dedicated block ──────────────────────
                $_pcStatus       = (string) ($_result['status'] ?? '');
                $_pcDetected     = isset($_metrics['detected_min_php'])
                    ? (string) $_metrics['detected_min_php'] : null;
                $_pcDeclared     = isset($_metrics['declared_min_php'])
                    ? (string) $_metrics['declared_min_php'] : null;
                $_pcDeclaredSrc  = (string) ($_metrics['declared_min_php_source'] ?? '');

                // Fallback: the runner searches readme.txt for "Requires PHP:", but many
                // plugins only declare it in the PHP file plugin header (which WP.org reads
                // and stores in plugin_requires_php). Use that value when the runner found
                // nothing, so the display is consistent with the compatibility card above.
                if (
                    $_pcDeclared === null
                    && isset($requiresPhp)
                    && is_string($requiresPhp)
                    && $requiresPhp !== ''
                ) {
                    $_pcDeclared    = $requiresPhp;
                    $_pcDeclaredSrc = 'plugin header';
                }
                $_pcVersions     = is_array($_metrics['tested_versions'] ?? null)
                    ? $_metrics['tested_versions'] : [];
                $_pcSummary      = (string) ($_metrics['summary'] ?? '');

                // ── Effective minimum PHP ──────────────────────────────────
                // The real floor is the maximum of:
                //   1. $_pcDetected        — lowest PHP the code actually requires
                //   2. $wpMinPhpRequired   — minimum PHP to run the target WP version
                // Declared is intentionally excluded from this calculation so it
                // can be compared against the effective floor for grade purposes.
                $_pcEffective = $_pcDetected;
                $_pcWpReq     = isset($wpMinPhpRequired) && is_string($wpMinPhpRequired)
                    ? $wpMinPhpRequired : null;
                if (
                    $_pcWpReq !== null
                    && $_pcWpReq !== ''
                    && (
                        $_pcEffective === null
                        || version_compare($_pcWpReq, $_pcEffective, '>')
                    )
                ) {
                    $_pcEffective = $_pcWpReq;
                }

                // Show a notice when the effective floor is higher than what
                // the runner detected — i.e. the runner's grade doesn't reflect reality.
                $_pcEffectiveMismatch = (
                    $_pcEffective !== null
                    && $_pcDetected !== null
                    && version_compare($_pcEffective, $_pcDetected, '>')
                );

                // Status badge colour map
                $_pcStatusBadge = match (true) {
                    str_starts_with($_pcStatus, 'version-detected') => 'text-bg-success',
                    $_pcStatus === ''                                => null,
                    default                                          => 'text-bg-warning',
                };
                // Human-readable status label
                $_pcStatusLabel = ucwords(str_replace(['-', '_'], ' ', $_pcStatus));
    ?>
            <div class="mb-3">
                <?php if ($_pcStatusBadge !== null && $_pcStatus !== '') : ?>
                <div class="mb-2">
                    <span class="badge <?= htmlspecialchars($_pcStatusBadge, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($_pcStatusLabel, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-3 mb-2">
                    <div>
                        <div class="small text-body-secondary mb-1"><?php echo htmlspecialchars($i18n->t('runner.php_declared'), ENT_QUOTES, 'UTF-8') ?></div>
                        <?php if ($_pcDeclared !== null && $_pcDeclared !== '') : ?>
                        <span class="fw-semibold">
                            <?= htmlspecialchars($_pcDeclared, ENT_QUOTES, 'UTF-8') ?>+
                        </span>
                            <?php if ($_pcDeclaredSrc !== '') : ?>
                        <span class="small text-body-secondary ms-1">
                            (<?= htmlspecialchars($_pcDeclaredSrc, ENT_QUOTES, 'UTF-8') ?>)
                        </span>
                            <?php endif; ?>
                        <?php else : ?>
                        <span class="text-body-secondary small">—</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="small text-body-secondary mb-1"><?php echo htmlspecialchars($i18n->t('runner.php_detected'), ENT_QUOTES, 'UTF-8') ?></div>
                        <?php if ($_pcDetected !== null && $_pcDetected !== '') : ?>
                        <span class="fw-semibold">
                            <?= htmlspecialchars($_pcDetected, ENT_QUOTES, 'UTF-8') ?>+
                        </span>
                        <?php else : ?>
                        <span class="text-body-secondary small">—</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($_pcGradeNote !== '') : ?>
                <p class="small text-body-secondary mb-2">
                    <i class="bi bi-info-circle me-1" aria-hidden="true"></i><?php echo htmlspecialchars($_pcGradeNote, ENT_QUOTES, 'UTF-8') ?>.
                    <?php if ($_pcEffective !== null) : ?>
                        <?php echo htmlspecialchars($i18n->t('runner.php_effective_min', ['version' => $_pcEffective]), ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </p>
                <?php endif; ?>

                <?php if (!empty($_pcVersions)) : ?>
                <div class="mb-2">
                    <div class="small text-body-secondary mb-1"><?php echo htmlspecialchars($i18n->t('runner.php_tested_versions'), ENT_QUOTES, 'UTF-8') ?></div>
                    <?php foreach ($_pcVersions as $_pcV) :
                        $_pcVStr = (string) $_pcV;
                        if ($_pcDetected !== null && $_pcDetected !== '') {
                            $_pcVBadge = version_compare($_pcVStr, $_pcDetected, '>=')
                                ? 'text-bg-success text-white'
                                : 'text-bg-danger text-white';
                        } else {
                            $_pcVBadge = 'text-bg-light border text-body-secondary';
                        }
                        ?>
                    <span class="badge <?php echo htmlspecialchars($_pcVBadge, ENT_QUOTES, 'UTF-8') ?> me-1 mb-1">
                        PHP <?php echo htmlspecialchars($_pcVStr, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($_pcSummary !== '') : ?>
                <p class="small text-body-secondary mb-0"><?= htmlspecialchars($_pcSummary, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($_isWpSince) :
                // ── wp-since dedicated block ───────────────────────────────
                $_wsStatus    = (string) ($_result['status']  ?? '');
                $_wsDeclared  = isset($_metrics['declared_min_wp']) && $_metrics['declared_min_wp'] !== null
                    ? (string) $_metrics['declared_min_wp'] : null;
                $_wsSuggested = isset($_metrics['suggested_min_wp']) && $_metrics['suggested_min_wp'] !== null
                    ? (string) $_metrics['suggested_min_wp'] : null;
                $_wsStdout    = (string) ($_result['stdout'] ?? '');
                // Sanitise stdout: strip ANSI escape codes and emoji-like bytes
                $_wsStdout    = (string) preg_replace('/\x1B\[[0-9;]*m/', '', $_wsStdout);
                $_wsStdout    = trim($_wsStdout);

                $_wsStatusBadge = match (true) {
                    $_wsStatus === 'ok'           => 'text-bg-success',
                    $_wsStatus === 'issues-found' => 'text-bg-warning',
                    default                       => 'text-bg-secondary',
                };
    ?>
            <!-- Reasoning -->
                <?php if ($_reasoning !== '') : ?>
            <p class="small mb-3"><?php echo htmlspecialchars($_reasoning, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

            <!-- Status + version columns -->
                <?php if ($_wsStatus !== '') : ?>
            <div class="mb-2">
                <span class="badge <?php echo htmlspecialchars($_wsStatusBadge, ENT_QUOTES, 'UTF-8') ?>">
                    <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $_wsStatus)), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
                <?php endif; ?>

            <div class="d-flex flex-wrap gap-4 mb-3">
                <div>
                    <div class="small text-body-secondary mb-1"><?php echo htmlspecialchars($i18n->t('runner.wp_declared_min'), ENT_QUOTES, 'UTF-8') ?></div>
                    <?php if ($_wsDeclared !== null && $_wsDeclared !== '') : ?>
                    <span class="fw-semibold"><?php echo htmlspecialchars($_wsDeclared, ENT_QUOTES, 'UTF-8') ?>+</span>
                    <?php else : ?>
                    <span class="text-body-secondary small">—</span>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="small text-body-secondary mb-1"><?php echo htmlspecialchars($i18n->t('runner.wp_suggested_min'), ENT_QUOTES, 'UTF-8') ?></div>
                    <?php if ($_wsSuggested !== null && $_wsSuggested !== '') : ?>
                    <span class="fw-semibold"><?php echo htmlspecialchars($_wsSuggested, ENT_QUOTES, 'UTF-8') ?>+</span>
                    <?php else : ?>
                    <span class="text-body-secondary small">—</span>
                    <?php endif; ?>
                </div>
            </div>

                <?php if ($_wsStdout !== '') : ?>
            <div class="mb-1">
                <div class="small text-body-secondary mb-1"><?php echo htmlspecialchars($i18n->t('runner.tool_output'), ENT_QUOTES, 'UTF-8') ?></div>
                <pre class="small bg-body-secondary p-2 rounded mb-0" style="white-space:pre-wrap;word-break:break-all"><?php echo htmlspecialchars($_wsStdout, ENT_QUOTES, 'UTF-8') ?></pre>
            </div>
                <?php endif; ?>
            <?php endif; // $_isWpSince ?>

            <?php if ($_isHooks) :
                // ── hooks dedicated block ──────────────────────────────────
                $_hkCapabs          = is_array($_result['capabilities'] ?? null) ? $_result['capabilities'] : [];
                $_hkProvidesHooks   = (bool) ($_hkCapabs['provides_hooks'] ?? false);
                $_hkExtensible      = (bool) ($_hkCapabs['extensible']     ?? false);
                $_hkActionsProvided = is_array($_hkCapabs['actions_provided'] ?? null)
                    ? $_hkCapabs['actions_provided'] : [];
                $_hkFiltersProvided = is_array($_hkCapabs['filters_provided'] ?? null)
                    ? $_hkCapabs['filters_provided'] : [];

                // Presentation tables (pre-aggregated top-10 per category)
                $_hkPresent         = is_array($_result['presentation'] ?? null)
                    ? $_result['presentation'] : [];
                $_hkWpActions       = is_array($_hkPresent['wordpress_actions_used']['rows'] ?? null)
                    ? $_hkPresent['wordpress_actions_used']['rows'] : [];
                $_hkPluginActions   = is_array($_hkPresent['plugin_actions_used']['rows'] ?? null)
                    ? $_hkPresent['plugin_actions_used']['rows'] : [];
                $_hkWpFilters       = is_array($_hkPresent['wordpress_filters_used']['rows'] ?? null)
                    ? $_hkPresent['wordpress_filters_used']['rows'] : [];
                $_hkPluginFilters   = is_array($_hkPresent['plugin_filters_used']['rows'] ?? null)
                    ? $_hkPresent['plugin_filters_used']['rows'] : [];
                $_hkActProvidedRows = is_array($_hkPresent['actions_provided']['rows'] ?? null)
                    ? $_hkPresent['actions_provided']['rows'] : [];
                $_hkFiltProvidedRows = is_array($_hkPresent['filters_provided']['rows'] ?? null)
                    ? $_hkPresent['filters_provided']['rows'] : [];

                // Numeric totals
                $_hkTotalUsed     = isset($_metrics['total_hooks_used'])     ? (int) $_metrics['total_hooks_used']     : 0;
                $_hkTotalProvided = isset($_metrics['total_hooks_provided'])  ? (int) $_metrics['total_hooks_provided']  : 0;
                $_hkUActUsed      = isset($_metrics['unique_actions_used'])   ? (int) $_metrics['unique_actions_used']   : 0;
                $_hkUFiltUsed     = isset($_metrics['unique_filters_used'])   ? (int) $_metrics['unique_filters_used']   : 0;
                $_hkUActProv      = isset($_metrics['unique_actions_provided']) ? (int) $_metrics['unique_actions_provided'] : 0;
                $_hkUFiltProv     = isset($_metrics['unique_filters_provided']) ? (int) $_metrics['unique_filters_provided'] : 0;
                $_hkTActUsed      = isset($_metrics['total_actions_used'])    ? (int) $_metrics['total_actions_used']    : 0;
                $_hkTFiltUsed     = isset($_metrics['total_filters_used'])    ? (int) $_metrics['total_filters_used']    : 0;
                $_hkTActProv        = isset($_metrics['total_actions_provided'])
                    ? (int) $_metrics['total_actions_provided'] : 0;
                $_hkTFiltProv       = isset($_metrics['total_filters_provided'])
                    ? (int) $_metrics['total_filters_provided'] : 0;

                // Documentation metrics (new)
                $_hkDocCount        = isset($_metrics['documented_hooks_count'])
                    ? (int) $_metrics['documented_hooks_count']      : 0;
                $_hkDocPct          = isset($_metrics['documented_hooks_percentage'])
                    ? (int) $_metrics['documented_hooks_percentage']  : 0;
                $_hkWellDocCount    = isset($_metrics['well_documented_hooks_count'])
                    ? (int) $_metrics['well_documented_hooks_count']  : 0;

                // Issues (e.g. hooks.no_documentation) stored in $_translateIssueCounts
                $_hkIssuesTop  = is_array($_translateIssueCounts['top'] ?? null)
                    ? $_translateIssueCounts['top'] : [];
                $_hkIssueHigh  = isset($_translateIssueCounts['high'])
                    ? (int) $_translateIssueCounts['high']   : 0;
                ?>

            <!-- Reasoning -->
                <?php if ($_reasoning !== '') : ?>
            <p class="small mb-3"><?php echo htmlspecialchars($_reasoning, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

            <!-- Quick stats -->
            <div class="d-flex flex-wrap gap-3 mb-3">
                <div class="text-center">
                    <div class="fw-bold fs-5"><?php echo htmlspecialchars((string) $_hkTotalUsed, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_total_used'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo $_hkTotalProvided > 0 ? 'text-success' : 'text-body-secondary' ?>">
                        <?php echo htmlspecialchars((string) $_hkTotalProvided, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_total_provided'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="text-center">
                    <div class="fw-bold fs-5">
                        <?php if ($_hkProvidesHooks) : ?>
                        <i class="bi bi-check-circle-fill text-success" aria-label="Yes"></i>
                        <?php else : ?>
                        <i class="bi bi-x-circle-fill text-danger" aria-label="No"></i>
                        <?php endif; ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_provides'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="text-center">
                    <div class="fw-bold fs-5">
                        <?php if ($_hkExtensible) : ?>
                        <i class="bi bi-check-circle-fill text-success" aria-label="Yes"></i>
                        <?php else : ?>
                        <i class="bi bi-x-circle-fill text-danger" aria-label="No"></i>
                        <?php endif; ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_extensible'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php if ($_hkTotalProvided > 0) : ?>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo $_hkDocPct >= 80 ? 'text-success' : ($_hkDocPct >= 40 ? 'text-warning' : 'text-danger') ?>">
                        <?php echo htmlspecialchars($_hkDocPct . '%', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_doc_pct'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Issues (e.g. no documentation warning) -->
                <?php if (!empty($_hkIssuesTop)) : ?>
            <div class="mb-3">
                <ul class="list-unstyled mb-0 d-flex flex-column gap-1">
                    <?php foreach ($_hkIssuesTop as $_hkIssue) :
                        if (!is_array($_hkIssue)) {
                            continue;
                        }
                        $_hkISev   = strtolower((string) ($_hkIssue['severity'] ?? 'info'));
                        $_hkIMsg   = (string) ($_hkIssue['message'] ?? '');
                        $_hkIBadge = match ($_hkISev) {
                            'critical', 'high' => 'text-bg-danger',
                            'medium'           => 'text-bg-warning',
                            'low'              => 'text-bg-secondary',
                            default            => 'text-bg-info',
                        };
                        ?>
                    <li class="d-flex align-items-start gap-2 small">
                        <span class="badge <?php echo htmlspecialchars($_hkIBadge, ENT_QUOTES, 'UTF-8') ?> mt-1 flex-shrink-0">
                            <?php echo htmlspecialchars($_hkISev, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span><?php echo htmlspecialchars($_hkIMsg, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
                <?php endif; ?>

            <!-- Breakdown table: unique + total per category -->
            <div class="mb-3">
                <table class="table table-sm table-borderless mb-0 small"
                       aria-label="<?php echo htmlspecialchars($i18n->t('runner.hooks_breakdown'), ENT_QUOTES, 'UTF-8') ?>">
                    <thead>
                        <tr>
                            <th class="text-body-secondary fw-normal" style="width:50%"></th>
                            <th class="text-body-secondary fw-normal text-end"><?php echo htmlspecialchars($i18n->t('runner.hooks_col_unique'), ENT_QUOTES, 'UTF-8') ?></th>
                            <th class="text-body-secondary fw-normal text-end"><?php echo htmlspecialchars($i18n->t('runner.hooks_col_total'), ENT_QUOTES, 'UTF-8') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_actions_used'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end fw-semibold"><?php echo htmlspecialchars((string) $_hkUActUsed, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end"><?php echo htmlspecialchars((string) $_hkTActUsed, ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <tr>
                            <td class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_filters_used'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end fw-semibold"><?php echo htmlspecialchars((string) $_hkUFiltUsed, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end"><?php echo htmlspecialchars((string) $_hkTFiltUsed, ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <tr>
                            <td class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_actions_provided'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end fw-semibold"><?php echo htmlspecialchars((string) $_hkUActProv, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end"><?php echo htmlspecialchars((string) $_hkTActProv, ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <tr>
                            <td class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.hooks_filters_provided'), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end fw-semibold"><?php echo htmlspecialchars((string) $_hkUFiltProv, ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end"><?php echo htmlspecialchars((string) $_hkTFiltProv, ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Top hooks used (from presentation) -->
                <?php
                $_hkUsedSections = [
                    'wp_actions'     => ['label' => $i18n->t('runner.hooks_wp_actions_used'),    'rows' => $_hkWpActions],
                    'plugin_actions' => ['label' => $i18n->t('runner.hooks_plugin_actions_used'), 'rows' => $_hkPluginActions],
                    'wp_filters'     => ['label' => $i18n->t('runner.hooks_wp_filters_used'),    'rows' => $_hkWpFilters],
                    'plugin_filters' => ['label' => $i18n->t('runner.hooks_plugin_filters_used'), 'rows' => $_hkPluginFilters],
                ];
                foreach ($_hkUsedSections as $_hkSecId => $_hkSec) :
                    if (empty($_hkSec['rows'])) {
                        continue;
                    }
                    $_hkCollapseId = htmlspecialchars($_cardId . '-' . $_hkSecId, ENT_QUOTES, 'UTF-8');
                    ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <?php echo htmlspecialchars($_hkSec['label'], ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-secondary ms-1"><?php echo count($_hkSec['rows']) ?></span>
                </h3>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless small mb-0">
                        <thead>
                            <tr>
                                <th class="text-body-secondary fw-normal"><?php echo htmlspecialchars($i18n->t('runner.hooks_col_hook'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th class="text-body-secondary fw-normal text-end" style="width:6rem"><?php echo htmlspecialchars($i18n->t('runner.hooks_col_count'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th class="text-body-secondary fw-normal" style="width:10rem"><?php echo htmlspecialchars($i18n->t('runner.hooks_col_locations'), ENT_QUOTES, 'UTF-8') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_hkSec['rows'] as $_hkRow) :
                                if (!is_array($_hkRow)) {
                                    continue;
                                }
                                ?>
                            <tr>
                                <td><code class="plugin-slug small"><?php echo htmlspecialchars((string) ($_hkRow['hook'] ?? ''), ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td class="text-end text-body-secondary"><?php echo htmlspecialchars((string) ($_hkRow['count'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-body-secondary small"><?php echo htmlspecialchars((string) ($_hkRow['locations'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
                <?php endforeach; ?>

            <!-- Hooks provided -->
                <?php
                $_hkProvidedSections = [];
                if (!empty($_hkActProvidedRows)) {
                    $_hkProvidedSections[] = [
                        'label' => $i18n->t('runner.hooks_actions_provided'),
                        'rows'  => $_hkActProvidedRows,
                    ];
                }
                if (!empty($_hkFiltProvidedRows)) {
                    $_hkProvidedSections[] = [
                        'label' => $i18n->t('runner.hooks_filters_provided'),
                        'rows'  => $_hkFiltProvidedRows,
                    ];
                }
                if (!empty($_hkProvidedSections)) :
                    $_hkProvidedId = htmlspecialchars($_cardId . '-provided', ENT_QUOTES, 'UTF-8');
                    ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-puzzle me-1" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('runner.hooks_provided_title'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-success ms-1"><?php echo htmlspecialchars((string) $_hkTotalProvided, ENT_QUOTES, 'UTF-8') ?></span>
                    <button class="btn btn-link btn-sm p-0 ms-2 text-body-secondary text-decoration-none small"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo $_hkProvidedId ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo $_hkProvidedId ?>">
                        <?php echo htmlspecialchars($i18n->t('runner.hooks_show_all'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </h3>
                <!-- Badge preview: first 10 provided hooks -->
                    <?php
                    $_hkAllProvidedNames = [];
                    foreach ($_hkProvidedSections as $_hkPS) {
                        foreach ($_hkPS['rows'] as $_hkPR) {
                            if (is_array($_hkPR) && isset($_hkPR['hook'])) {
                                $_hkAllProvidedNames[] = (string) $_hkPR['hook'];
                            }
                        }
                    }
                    foreach (array_slice($_hkAllProvidedNames, 0, 12) as $_hkPN) : ?>
                <span class="badge text-bg-light border text-body-secondary me-1 mb-1 font-monospace small">
                        <?php echo htmlspecialchars($_hkPN, ENT_QUOTES, 'UTF-8') ?>
                </span>
                    <?php endforeach; ?>
                    <?php if (count($_hkAllProvidedNames) > 12) : ?>
                <span class="text-body-secondary small">
                    +<?php echo count($_hkAllProvidedNames) - 12 ?> <?php echo htmlspecialchars($i18n->t('runner.hooks_more'), ENT_QUOTES, 'UTF-8') ?>
                </span>
                    <?php endif; ?>

                <!-- Full collapsible list per category -->
                <div class="collapse mt-2" id="<?php echo $_hkProvidedId ?>">
                    <?php foreach ($_hkProvidedSections as $_hkPS) : ?>
                    <h4 class="h6 text-body-secondary mt-2 mb-1"><?php echo htmlspecialchars($_hkPS['label'], ENT_QUOTES, 'UTF-8') ?></h4>
                        <?php foreach ($_hkPS['rows'] as $_hkPR) :
                            if (!is_array($_hkPR)) {
                                continue;
                            }
                            ?>
                    <span class="badge text-bg-light border text-body-secondary me-1 mb-1 font-monospace small">
                            <?php echo htmlspecialchars((string) ($_hkPR['hook'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
                <?php endif; // provided hooks ?>

            <?php endif; // $_isHooks ?>

            <?php if ($_isTranslate) :
                // ── translate dedicated block ──────────────────────────────
                $_trDetails    = is_array($_result['details']      ?? null) ? $_result['details']      : [];
                $_trLocales    = is_array($_trDetails['locales']   ?? null) ? $_trDetails['locales']   : [];
                $_trTextDomain = is_array($_trDetails['text_domain']   ?? null) ? $_trDetails['text_domain']   : [];
                $_trJsI18n     = is_array($_trDetails['javascript_i18n'] ?? null) ? $_trDetails['javascript_i18n'] : [];
                $_trLoadHook   = is_array($_trDetails['load_hook'] ?? null) ? $_trDetails['load_hook'] : [];
                $_trCapabs     = is_array($_result['capabilities'] ?? null) ? $_result['capabilities'] : [];

                // Supported locales (≥80% coverage)
                $_trSupportedLocales = is_array($_trCapabs['supported_locales'] ?? null)
                    ? $_trCapabs['supported_locales'] : [];

                // Metrics
                $_trDetected     = isset($_metrics['translation_locales_detected'])
                    ? (int) $_metrics['translation_locales_detected'] : 0;
                $_trCompliant    = isset($_metrics['translation_locales_compliant'])
                    ? (int) $_metrics['translation_locales_compliant'] : 0;
                $_trMajorCov     = isset($_metrics['translation_major_locale_coverage'])
                    ? (float) $_metrics['translation_major_locale_coverage'] : null;
                $_trUntranslated = isset($_metrics['untranslated_strings'])
                    ? (int) $_metrics['untranslated_strings'] : 0;
                $_trTdValid      = (bool) ($_metrics['text_domain_valid']   ?? false);
                $_trJsHasTr      = (bool) ($_metrics['js_has_translations'] ?? false);
                $_trJsTotal      = isset($_metrics['js_total_strings'])
                    ? (int) $_metrics['js_total_strings']  : 0;
                $_trJsTranslated = isset($_metrics['js_translated'])
                    ? (int) $_metrics['js_translated']     : 0;
                $_trLoadCall     = (bool) ($_metrics['load_hook_has_call']  ?? false);
                $_trLoadIssues   = isset($_metrics['load_hook_issues'])
                    ? (int) $_metrics['load_hook_issues']  : 0;

                // Text domain
                $_trTdDeclared  = (isset($_trTextDomain['declared']) && $_trTextDomain['declared'] !== null)
                    ? (string) $_trTextDomain['declared'] : null;
                $_trTdExpected  = (string) ($_trTextDomain['expected']    ?? '');
                $_trTdUsage     = is_array($_trTextDomain['usage']        ?? null) ? $_trTextDomain['usage'] : [];
                $_trTdIssues    = is_array($_trTextDomain['issues']       ?? null) ? $_trTextDomain['issues'] : [];

                // Top issues + counts
                $_trTopIssues   = is_array($_translateIssueCounts['top']  ?? null)
                    ? $_translateIssueCounts['top'] : [];
                $_trHighCount   = isset($_translateIssueCounts['high'])   ? (int) $_translateIssueCounts['high']   : 0;
                $_trMedCount    = isset($_translateIssueCounts['medium'])  ? (int) $_translateIssueCounts['medium']  : 0;
                $_trLowCount    = isset($_translateIssueCounts['low'])    ? (int) $_translateIssueCounts['low']    : 0;
                $_trTrivCount   = isset($_translateIssueCounts['trivial']) ? (int) $_translateIssueCounts['trivial'] : 0;

                // Untranslated strings preview (from presentation or details)
                $_trPresent     = is_array($_result['presentation'] ?? null) ? $_result['presentation'] : [];
                $_trUntrRows    = is_array($_trPresent['untranslated_strings']['rows'] ?? null)
                    ? $_trPresent['untranslated_strings']['rows'] : [];
                ?>
            <!-- Reasoning -->
                <?php if ($_reasoning !== '') : ?>
            <p class="small mb-3"><?php echo htmlspecialchars($_reasoning, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

            <!-- Quick stats row -->
            <div class="d-flex flex-wrap gap-3 mb-3">
                <div class="text-center">
                    <div class="fw-bold fs-5"><?php echo htmlspecialchars((string) $_trDetected, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.locales_detected'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo $_trCompliant > 0 ? 'text-success' : 'text-body-secondary' ?>">
                        <?php echo htmlspecialchars((string) $_trCompliant, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.locales_compliant'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php if ($_trMajorCov !== null) : ?>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo $_trMajorCov >= 80 ? 'text-success' : ($_trMajorCov >= 50 ? 'text-warning' : 'text-danger') ?>">
                        <?php echo htmlspecialchars(number_format($_trMajorCov, 1) . '%', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.tr_major_coverage'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endif; ?>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo $_trUntranslated > 0 ? 'text-warning' : 'text-success' ?>">
                        <?php echo htmlspecialchars((string) $_trUntranslated, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.untranslated_strings'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="text-center">
                    <div class="fw-bold fs-5">
                        <?php if ($_trTdValid) : ?>
                        <i class="bi bi-check-circle-fill text-success" aria-label="Valid"></i>
                        <?php else : ?>
                        <i class="bi bi-x-circle-fill text-danger" aria-label="Invalid"></i>
                        <?php endif; ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.text_domain'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php if ($_trJsTotal > 0) : ?>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo $_trJsHasTr ? 'text-success' : 'text-warning' ?>">
                        <?php echo htmlspecialchars($_trJsTranslated . '/' . $_trJsTotal, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.js_strings_translated'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Supported locales -->
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-globe me-1" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('runner.supported_locales'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-secondary ms-1"><?php echo count($_trSupportedLocales) ?></span>
                </h3>
                <?php if (empty($_trSupportedLocales)) : ?>
                <p class="small text-body-secondary mb-0"><em><?php echo htmlspecialchars($i18n->t('runner.no_locale_coverage'), ENT_QUOTES, 'UTF-8') ?></em></p>
                <?php else : ?>
                    <?php foreach ($_trSupportedLocales as $_trLoc) : ?>
                <span class="badge text-bg-success me-1 mb-1">
                        <?php echo htmlspecialchars((string) $_trLoc, ENT_QUOTES, 'UTF-8') ?>
                </span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Coverage by locale (collapsible) -->
                <?php if (!empty($_trLocales)) :
                    // Sort by coverage desc, then locale code asc on tie
                    uksort($_trLocales, static function (string $a, string $b) use ($_trLocales): int {
                        $_pctA = isset($_trLocales[$a]['percentage']) ? (int) $_trLocales[$a]['percentage'] : 0;
                        $_pctB = isset($_trLocales[$b]['percentage']) ? (int) $_trLocales[$b]['percentage'] : 0;
                        return $_pctB !== $_pctA ? $_pctB - $_pctA : strcmp($a, $b);
                    });
                    $_trLocColId = htmlspecialchars($_cardId . '-locales', ENT_QUOTES, 'UTF-8');
                    ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-bar-chart me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('runner.coverage_by_locale'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-secondary ms-1"><?php echo count($_trLocales) ?></span>
                    <button class="btn btn-link btn-sm p-0 ms-2 text-body-secondary text-decoration-none small"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo $_trLocColId ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo $_trLocColId ?>">
                        <?php echo htmlspecialchars($i18n->t('runner.hooks_show_all'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </h3>
                <div class="collapse" id="<?php echo $_trLocColId ?>">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0 small"
                               aria-label="<?php echo htmlspecialchars($i18n->t('runner.coverage_by_locale'), ENT_QUOTES, 'UTF-8') ?>">
                            <thead>
                                <tr>
                                    <th class="text-body-secondary fw-normal" style="width:20%"><?php echo htmlspecialchars($i18n->t('runner.locale_col'), ENT_QUOTES, 'UTF-8') ?></th>
                                    <th class="text-body-secondary fw-normal" style="width:25%"><?php echo htmlspecialchars($i18n->t('runner.language_col'), ENT_QUOTES, 'UTF-8') ?></th>
                                    <th class="text-body-secondary fw-normal"><?php echo htmlspecialchars($i18n->t('runner.coverage_col'), ENT_QUOTES, 'UTF-8') ?></th>
                                    <th class="text-body-secondary fw-normal text-end" style="width:10%">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_trLocales as $_trLocCode => $_trLocRow) :
                                    $_trPct = isset($_trLocRow['percentage']) ? (int) $_trLocRow['percentage'] : 0;
                                    $_trBar = $_trPct >= 80 ? 'bg-success' : ($_trPct >= 40 ? 'bg-warning' : 'bg-danger');
                                    ?>
                                <tr>
                                    <td><code class="plugin-slug"><?php echo htmlspecialchars((string) $_trLocCode, ENT_QUOTES, 'UTF-8') ?></code></td>
                                    <td><?php echo htmlspecialchars((string) ($_trLocRow['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <div class="progress" style="height:6px" role="progressbar"
                                             aria-valuenow="<?php echo $_trPct ?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar <?php echo htmlspecialchars($_trBar, ENT_QUOTES, 'UTF-8') ?>"
                                                 style="width:<?php echo $_trPct ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end"><?php echo htmlspecialchars((string) $_trPct, ENT_QUOTES, 'UTF-8') ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                <?php endif; ?>

            <!-- Text domain -->
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-code-square me-1" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('runner.text_domain'), ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($_trTdValid) : ?>
                    <span class="badge text-bg-success ms-1"><?php echo htmlspecialchars($i18n->t('runner.td_valid'), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php else : ?>
                    <span class="badge text-bg-danger ms-1"><?php echo htmlspecialchars($i18n->t('runner.td_invalid'), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </h3>
                <div class="d-flex flex-wrap gap-3 small mb-2">
                    <div>
                        <span class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.td_declared'), ENT_QUOTES, 'UTF-8') ?>: </span>
                        <?php if ($_trTdDeclared !== null && $_trTdDeclared !== '') : ?>
                        <code class="plugin-slug"><?php echo htmlspecialchars($_trTdDeclared, ENT_QUOTES, 'UTF-8') ?></code>
                        <?php elseif ($_trTdValid) : ?>
                        <em class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.td_auto'), ENT_QUOTES, 'UTF-8') ?></em>
                        <?php else : ?>
                        <em class="text-danger"><?php echo htmlspecialchars($i18n->t('runner.td_none'), ENT_QUOTES, 'UTF-8') ?></em>
                        <?php endif; ?>
                    </div>
                    <?php if ($_trTdExpected !== '') : ?>
                    <div>
                        <span class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.td_expected'), ENT_QUOTES, 'UTF-8') ?>: </span>
                        <code class="plugin-slug"><?php echo htmlspecialchars($_trTdExpected, ENT_QUOTES, 'UTF-8') ?></code>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($_trTdUsage)) : ?>
                <div class="small">
                    <span class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.td_usage'), ENT_QUOTES, 'UTF-8') ?>: </span>
                    <?php foreach ($_trTdUsage as $_tdDomain => $_tdCount) : ?>
                    <span class="badge text-bg-light border text-body-secondary me-1">
                        <code class="plugin-slug"><?php echo htmlspecialchars((string) $_tdDomain, ENT_QUOTES, 'UTF-8') ?></code>
                        ×<?php echo (int) $_tdCount ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($_trTdIssues)) : ?>
                <ul class="list-unstyled mb-0 mt-2 d-flex flex-column gap-1">
                    <?php foreach ($_trTdIssues as $_trTdIssue) : ?>
                    <li class="small text-danger">
                        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
                        <?php echo htmlspecialchars((string) $_trTdIssue, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>

            <!-- Top issues -->
                <?php if (!empty($_trTopIssues)) : ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <?php echo htmlspecialchars($i18n->t('plugin.issues_title'), ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($_trHighCount > 0) : ?>
                    <span class="badge text-bg-danger ms-1"><?php echo $_trHighCount ?> <?php echo htmlspecialchars($i18n->t('runner.severity_high'), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                    <?php if ($_trMedCount > 0) : ?>
                    <span class="badge text-bg-warning ms-1"><?php echo $_trMedCount ?> <?php echo htmlspecialchars($i18n->t('runner.severity_medium'), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                    <?php if ($_trLowCount > 0) : ?>
                    <span class="badge text-bg-secondary ms-1"><?php echo $_trLowCount ?> <?php echo htmlspecialchars($i18n->t('runner.severity_low'), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                    <?php if ($_trTrivCount > 0) : ?>
                    <span class="badge text-bg-light border text-body-secondary ms-1"><?php echo $_trTrivCount ?> <?php echo htmlspecialchars($i18n->t('runner.severity_trivial'), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </h3>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <?php foreach ($_trTopIssues as $_trIssue) :
                        if (!is_array($_trIssue)) {
                            continue;
                        }
                        $_trISev  = strtolower((string) ($_trIssue['severity'] ?? 'info'));
                        $_trIMsg  = (string) ($_trIssue['message'] ?? '');
                        $_trICode = (string) ($_trIssue['code']    ?? '');
                        $_trIExamples = is_array($_trIssue['examples'] ?? null) ? array_slice($_trIssue['examples'], 0, 3) : [];
                        $_trIBadge = $_severityBadge[$_trISev] ?? 'text-bg-secondary';
                        ?>
                    <li class="d-flex align-items-start gap-2">
                        <span class="badge <?php echo htmlspecialchars($_trIBadge, ENT_QUOTES, 'UTF-8') ?> mt-1 flex-shrink-0">
                            <?php echo htmlspecialchars($_trISev, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <div class="small">
                            <?php echo htmlspecialchars($_trIMsg, ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($_trICode !== '') : ?>
                            <span class="text-body-secondary ms-1 fst-italic small">
                                <?php echo htmlspecialchars($_trICode, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php endif; ?>
                            <?php foreach ($_trIExamples as $_trEx) :
                                if (!is_array($_trEx)) {
                                    continue;
                                }
                                $_trExStr  = (string) ($_trEx['string'] ?? '');
                                $_trExFile = (string) ($_trEx['file']   ?? '');
                                $_trExLine = isset($_trEx['line']) ? (int) $_trEx['line'] : null;
                                if ($_trExStr === '' && $_trExFile === '') {
                                    continue;
                                }
                                ?>
                            <div class="text-body-secondary mt-1 ms-2 border-start ps-2">
                                <?php if ($_trExStr !== '') : ?>
                                <div class="font-monospace small text-truncate" style="max-width:40rem">
                                    <?php echo htmlspecialchars($_trExStr, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($_trExFile !== '') : ?>
                                <div>
                                    <code class="plugin-slug">
                                        <?php echo htmlspecialchars(basename($_trExFile), ENT_QUOTES, 'UTF-8') ?>
                                        <?php if ($_trExLine !== null) : ?>
                                        :<?php echo $_trExLine ?>
                                        <?php endif; ?>
                                    </code>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
                <?php endif; ?>

            <!-- Untranslated strings preview -->
                <?php if (!empty($_trUntrRows)) : ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-file-text me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('runner.untranslated_preview', ['count' => count($_trUntrRows)]), ENT_QUOTES, 'UTF-8') ?>
                </h3>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless small mb-0"
                           aria-label="<?php echo htmlspecialchars($i18n->t('runner.untranslated_strings'), ENT_QUOTES, 'UTF-8') ?>">
                        <thead>
                            <tr>
                                <th class="text-body-secondary fw-normal" style="width:55%"><?php echo htmlspecialchars($i18n->t('runner.str_col'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th class="text-body-secondary fw-normal"><?php echo htmlspecialchars($i18n->t('runner.file_col'), ENT_QUOTES, 'UTF-8') ?></th>
                                <th class="text-body-secondary fw-normal text-end"><?php echo htmlspecialchars($i18n->t('runner.line_col'), ENT_QUOTES, 'UTF-8') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_trUntrRows as $_trUntrRow) :
                                if (!is_array($_trUntrRow)) {
                                    continue;
                                }
                                $_trUStr  = (string) ($_trUntrRow['string'] ?? '');
                                $_trUFile = (string) ($_trUntrRow['file']   ?? '');
                                $_trULine = isset($_trUntrRow['line']) ? (int) $_trUntrRow['line'] : null;
                                ?>
                            <tr>
                                <td class="text-truncate font-monospace" style="max-width:0">
                                    <span title="<?php echo htmlspecialchars($_trUStr, ENT_QUOTES, 'UTF-8') ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($_trUStr, 0, 80, '…'), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td>
                                    <code class="plugin-slug">
                                        <?php echo htmlspecialchars(basename($_trUFile), ENT_QUOTES, 'UTF-8') ?>
                                    </code>
                                </td>
                                <td class="text-end text-body-secondary">
                                    <?php echo $_trULine !== null ? htmlspecialchars((string) $_trULine, ENT_QUOTES, 'UTF-8') : '—' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
                <?php endif; ?>
            <?php endif; // $_isTranslate ?>

            <?php if ($_isCodingStandards) :
                // ── coding-standards dedicated block ──────────────────────
                $_csErrors    = isset($_metrics['total_errors'])    ? (int) $_metrics['total_errors']    : 0;
                $_csWarnings  = isset($_metrics['total_warnings'])  ? (int) $_metrics['total_warnings']  : 0;
                $_csFixable   = isset($_metrics['total_fixable'])   ? (int) $_metrics['total_fixable']   : 0;
                $_csFiles     = isset($_metrics['files_with_issues']) ? (int) $_metrics['files_with_issues'] : 0;
                $_csScanned   = isset($_metrics['scanned_files'])   ? (int) $_metrics['scanned_files']   : 0;
                $_csDensity   = isset($_metrics['issue_density_per_100_lines'])
                    ? number_format((float) $_metrics['issue_density_per_100_lines'], 2) : '0.00';
                $_csSummary   = (string) ($_metrics['summary'] ?? '');
                $_csDetails   = is_array($_result['details'] ?? null) ? $_result['details'] : [];
                $_csBySource  = is_array($_csDetails['issue_counts_by_source'] ?? null)
                    ? $_csDetails['issue_counts_by_source'] : [];
                // Sort by count descending, exclude internal sniff entries
                arsort($_csBySource);
                $_csBySourceFiltered = array_filter(
                    $_csBySource,
                    static fn ($k) => !str_starts_with((string) $k, 'Internal.'),
                    ARRAY_FILTER_USE_KEY
                );
            ?>
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-3 mb-2">
                    <div class="text-center">
                        <div class="fw-bold fs-5 <?= $_csErrors > 0 ? 'text-danger' : 'text-body-secondary' ?>">
                            <?= $_csErrors ?>
                        </div>
                        <div class="small text-body-secondary">errors</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5 <?= $_csWarnings > 0 ? 'text-warning' : 'text-body-secondary' ?>">
                            <?= $_csWarnings ?>
                        </div>
                        <div class="small text-body-secondary">warnings</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5"><?= $_csFiles ?></div>
                        <div class="small text-body-secondary">files with issues</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5"><?= $_csScanned ?></div>
                        <div class="small text-body-secondary">files scanned</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5"><?= htmlspecialchars($_csDensity, ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="small text-body-secondary">issues / 100 lines</div>
                    </div>
                    <?php if ($_csFixable > 0) : ?>
                    <div class="text-center">
                        <div class="fw-bold fs-5 text-success"><?= $_csFixable ?></div>
                        <div class="small text-body-secondary">auto-fixable</div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($_csSummary !== '') : ?>
                <p class="small text-body-secondary mb-0"><?= htmlspecialchars($_csSummary, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($_csBySourceFiltered)) : ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">Issues by rule</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <?php foreach ($_csBySourceFiltered as $_csRule => $_csCount) : ?>
                            <tr>
                                <td class="py-1 ps-0">
                                    <code class="small"><?= htmlspecialchars((string) $_csRule, ENT_QUOTES, 'UTF-8') ?></code>
                                </td>
                                <td class="py-1 pe-0 text-end" style="width:3rem">
                                    <span class="badge text-bg-secondary"><?= (int) $_csCount ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; // $_isCodingStandards ?>

            <?php if ($_isSecurity) :
                // ── security dedicated block ───────────────────────────────
                $_secDetails    = is_array($_result['details'] ?? null) ? $_result['details'] : [];
                $_secFindings   = is_array($_secDetails['findings'] ?? null) ? $_secDetails['findings'] : [];
                $_secPresent    = is_array($_result['presentation'] ?? null) ? $_result['presentation'] : [];
                $_secAnaldRows  = is_array($_secPresent['analyzed_files']['rows'] ?? null)
                    ? $_secPresent['analyzed_files']['rows'] : [];

                $_secFilesAnalyzed = isset($_metrics['files_analyzed'])  ? (int) $_metrics['files_analyzed']  : 0;
                $_secFilesTotal    = isset($_metrics['files_total'])      ? (int) $_metrics['files_total']     : 0;
                $_secFindingsCrit  = isset($_metrics['findings_critical']) ? (int) $_metrics['findings_critical'] : 0;
                $_secFindingsErr   = isset($_metrics['findings_error'])   ? (int) $_metrics['findings_error']  : 0;
                $_secFindingsWarn  = isset($_metrics['findings_warning']) ? (int) $_metrics['findings_warning'] : 0;
                $_secFindingsInfo  = isset($_metrics['findings_info'])    ? (int) $_metrics['findings_info']   : 0;

                // Split into warnings (problems) and positive (pass) findings.
                $_secWarnFindings = [];
                $_secPassFindings = [];
                foreach ($_secFindings as $_sfItem) {
                    if (!is_array($_sfItem)) {
                        continue;
                    }
                    if ((string) ($_sfItem['status'] ?? '') === 'pass') {
                        $_secPassFindings[] = $_sfItem;
                    } else {
                        $_secWarnFindings[] = $_sfItem;
                    }
                }

                $_secPassColId  = htmlspecialchars($_cardId . '-sec-pass', ENT_QUOTES, 'UTF-8');
                $_secFilesColId = htmlspecialchars($_cardId . '-sec-files', ENT_QUOTES, 'UTF-8');
                ?>

            <!-- Reasoning -->
                <?php if ($_reasoning !== '') : ?>
            <p class="small mb-3"><?php echo htmlspecialchars($_reasoning, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

            <!-- Quick stats -->
            <div class="d-flex flex-wrap gap-3 mb-3">
                <div class="text-center">
                    <div class="fw-bold fs-5">
                        <?php echo htmlspecialchars($_secFilesAnalyzed . '/' . $_secFilesTotal, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.sec_files_analyzed'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php if ($_secFindingsCrit > 0) : ?>
                <div class="text-center">
                    <div class="fw-bold fs-5 text-danger"><?php echo htmlspecialchars((string) $_secFindingsCrit, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.sec_critical'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endif; ?>
                <?php if ($_secFindingsErr > 0) : ?>
                <div class="text-center">
                    <div class="fw-bold fs-5 text-danger"><?php echo htmlspecialchars((string) $_secFindingsErr, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.sec_errors'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endif; ?>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo $_secFindingsWarn > 0 ? 'text-warning' : 'text-body-secondary' ?>">
                        <?php echo htmlspecialchars((string) $_secFindingsWarn, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.sec_warnings'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="text-center">
                    <div class="fw-bold fs-5 <?php echo count($_secPassFindings) > 0 ? 'text-success' : 'text-body-secondary' ?>">
                        <?php echo htmlspecialchars((string) count($_secPassFindings), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="small text-body-secondary"><?php echo htmlspecialchars($i18n->t('runner.sec_positive'), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>

            <!-- Warning findings -->
                <?php if (!empty($_secWarnFindings)) : ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('runner.sec_findings'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-warning ms-1"><?php echo count($_secWarnFindings) ?></span>
                </h3>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <?php foreach ($_secWarnFindings as $_sf) :
                        $_sfSev     = strtolower((string) ($_sf['severity'] ?? 'warning'));
                        $_sfName    = (string) ($_sf['name']    ?? '');
                        $_sfSummary = (string) ($_sf['summary'] ?? '');
                        $_sfDets    = is_array($_sf['details'] ?? null) ? $_sf['details'] : [];
                        $_sfFile    = (string) ($_sfDets['file']           ?? '');
                        $_sfLine    = isset($_sfDets['line']) && (int) $_sfDets['line'] > 0
                            ? (int) $_sfDets['line'] : null;
                        $_sfReco    = (string) ($_sfDets['recommendation'] ?? '');
                        $_sfBadge   = match ($_sfSev) {
                            'critical', 'error' => 'text-bg-danger',
                            'warning'           => 'text-bg-warning',
                            default             => 'text-bg-secondary',
                        };
                        ?>
                    <li class="border-bottom pb-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge <?php echo htmlspecialchars($_sfBadge, ENT_QUOTES, 'UTF-8') ?> mt-1 flex-shrink-0">
                                <?php echo htmlspecialchars($_sfSev, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <div class="small flex-grow-1 min-w-0">
                                <?php if ($_sfName !== '') : ?>
                                <code class="plugin-slug d-block mb-1">
                                    <?php echo htmlspecialchars($_sfName, ENT_QUOTES, 'UTF-8') ?>
                                </code>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($_sfSummary, ENT_QUOTES, 'UTF-8') ?>
                                <?php if ($_sfFile !== '' || $_sfLine !== null) : ?>
                                <div class="text-body-secondary mt-1">
                                    <code class="plugin-slug">
                                        <?php echo htmlspecialchars(
                                            $_sfFile . ($_sfLine !== null ? ':' . $_sfLine : ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </code>
                                </div>
                                <?php endif; ?>
                                <?php if ($_sfReco !== '') : ?>
                                <p class="text-body-secondary fst-italic mb-0 mt-1">
                                    <?php echo htmlspecialchars($_sfReco, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
                <?php endif; ?>

            <!-- Positive findings (collapsed) -->
                <?php if (!empty($_secPassFindings)) : ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-check-circle me-1 text-success" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('runner.sec_positive_findings'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-success ms-1"><?php echo count($_secPassFindings) ?></span>
                    <button class="btn btn-link btn-sm p-0 ms-2 text-body-secondary text-decoration-none small"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo $_secPassColId ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo $_secPassColId ?>">
                        <?php echo htmlspecialchars($i18n->t('runner.hooks_show_all'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </h3>
                <div class="collapse" id="<?php echo $_secPassColId ?>">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-1">
                        <?php foreach ($_secPassFindings as $_sfp) :
                            $_sfpName    = (string) ($_sfp['name']    ?? '');
                            $_sfpSummary = (string) ($_sfp['summary'] ?? '');
                            ?>
                        <li class="d-flex align-items-start gap-2 small">
                            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <div>
                                <?php if ($_sfpName !== '') : ?>
                                <code class="plugin-slug me-1">
                                    <?php echo htmlspecialchars($_sfpName, ENT_QUOTES, 'UTF-8') ?>
                                </code>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($_sfpSummary, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
                <?php endif; ?>

            <!-- Analyzed files (collapsed) -->
                <?php if (!empty($_secAnaldRows)) : ?>
            <div class="mb-1">
                <h3 class="h6 text-body-secondary mb-2">
                    <i class="bi bi-file-code me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('runner.sec_analyzed_files'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-secondary ms-1"><?php echo count($_secAnaldRows) ?></span>
                    <button class="btn btn-link btn-sm p-0 ms-2 text-body-secondary text-decoration-none small"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo $_secFilesColId ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo $_secFilesColId ?>">
                        <?php echo htmlspecialchars($i18n->t('runner.hooks_show_all'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </h3>
                <div class="collapse" id="<?php echo $_secFilesColId ?>">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0 small"
                               aria-label="<?php echo htmlspecialchars($i18n->t('runner.sec_analyzed_files'), ENT_QUOTES, 'UTF-8') ?>">
                            <thead>
                                <tr>
                                    <th class="text-body-secondary fw-normal"><?php echo htmlspecialchars($i18n->t('runner.file_col'), ENT_QUOTES, 'UTF-8') ?></th>
                                    <th class="text-body-secondary fw-normal text-end" style="width:6rem"><?php echo htmlspecialchars($i18n->t('runner.sec_size_col'), ENT_QUOTES, 'UTF-8') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_secAnaldRows as $_sfRow) :
                                    if (!is_array($_sfRow)) {
                                        continue;
                                    }
                                    ?>
                                <tr>
                                    <td>
                                        <code class="plugin-slug small">
                                            <?php echo htmlspecialchars((string) ($_sfRow['file'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                        </code>
                                    </td>
                                    <td class="text-end text-body-secondary">
                                        <?php echo htmlspecialchars((string) ($_sfRow['size'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
                <?php endif; ?>
            <?php endif; // $_isSecurity ?>

            <?php if (!empty($_metricsScalar) || !empty($_metricsArray)) : ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2"><?= htmlspecialchars($i18n->t('plugin.metrics_title'), ENT_QUOTES, 'UTF-8') ?></h3>
                <?php if (!empty($_metricsScalar)) : ?>
                <div class="d-flex flex-wrap gap-3 mb-2">
                    <?php foreach ($_metricsScalar as $_metricKey => $_metricVal) : ?>
                    <div class="text-center">
                        <div class="fw-bold fs-5"><?= htmlspecialchars((string) $_metricVal, ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="small text-body-secondary">
                            <?= htmlspecialchars(str_replace('_', ' ', (string) $_metricKey), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php foreach ($_metricsArray as $_metricKey => $_metricItems) : ?>
                    <?php if (!empty($_metricItems)) : ?>
                <div class="mb-1">
                    <span class="small text-body-secondary me-1">
                        <?= htmlspecialchars(str_replace('_', ' ', (string) $_metricKey), ENT_QUOTES, 'UTF-8') ?>:
                    </span>
                        <?php foreach ($_metricItems as $_item) : ?>
                    <span class="badge text-bg-light border text-body-secondary me-1">
                            <?= htmlspecialchars((string) $_item, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                        <?php endforeach; ?>
                </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($_issues)) : ?>
                <?php
            // Detect issue format: php-compatibility uses file/line/column/type/source
            // Standard runners use severity/message/location
                $_firstIssue   = is_array($_issues[0] ?? null) ? $_issues[0] : [];
                $_isPhpCompatFmt = isset($_firstIssue['file']) || isset($_firstIssue['type']);
            // Severity mapping for php-compatibility type field
                $_phpCompatSeverity = [
                'error'   => 'text-bg-danger',
                'warning' => 'text-bg-warning',
                'info'    => 'text-bg-info',
                ];
                ?>
            <div>
                <h3 class="h6 text-body-secondary mb-2">
                    <?= htmlspecialchars($i18n->t('plugin.issues_title'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-secondary ms-1"><?= count($_issues) ?></span>
                </h3>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <?php foreach ($_issues as $_issue) :
                        if (!is_array($_issue)) {
                            continue;
                        }
                        if ($_isPhpCompatFmt) {
                            // php-compatibility / coding-standards runner format
                            $_rawType  = strtolower((string) ($_issue['type'] ?? 'warning'));
                            $_sev      = $_rawType;
                            $_msg      = (string) ($_issue['message'] ?? '');
                            $_source   = (string) ($_issue['source']  ?? '');
                            $_file     = (string) ($_issue['file']    ?? '');
                            $_line     = isset($_issue['line'])   ? (int) $_issue['line']   : null;
                            $_col      = isset($_issue['column']) ? (int) $_issue['column'] : null;
                            // For coding-standards, strip the extraction prefix to show
                            // a relative path (e.g. "includes/class-foo.php") instead of basename.
                            if ($_isCodingStandards && str_contains($_file, '/extracted/')) {
                                $_fileParts = explode('/extracted/', $_file, 2);
                                if (count($_fileParts) === 2) {
                                    $_fileSegs = explode('/', $_fileParts[1], 3);
                                    if (count($_fileSegs) === 3) {
                                        $_file = $_fileSegs[2];
                                    }
                                }
                            }
                            $_locParts = [];
                            if ($_file !== '') {
                                $_locParts[] = $_isCodingStandards ? $_file : basename($_file);
                            }
                            if ($_line !== null) {
                                $_locParts[] = 'line ' . $_line . ($_col !== null ? ':' . $_col : '');
                            }
                            $_loc      = implode(' ', $_locParts);
                            $_badgeCls = $_phpCompatSeverity[$_rawType] ?? 'text-bg-secondary';
                        } else {
                            // Standard runner format
                            $_sev      = strtolower((string) ($_issue['severity'] ?? 'info'));
                            $_msg      = (string) ($_issue['message']  ?? '');
                            $_loc      = (string) ($_issue['location'] ?? '');
                            $_source   = '';
                            $_badgeCls = $_severityBadge[$_sev] ?? 'text-bg-secondary';
                        }
                        ?>
                    <li class="d-flex align-items-start gap-2">
                        <span class="badge <?= htmlspecialchars($_badgeCls, ENT_QUOTES, 'UTF-8') ?> mt-1 flex-shrink-0">
                            <?= htmlspecialchars($_sev, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <div class="small">
                            <?= htmlspecialchars($_msg, ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($_source !== '') : ?>
                            <div class="text-body-secondary fst-italic">
                                <?= htmlspecialchars($_source, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($_loc !== '') : ?>
                            <div class="text-body-secondary">
                                <code class="plugin-slug"><?= htmlspecialchars($_loc, ENT_QUOTES, 'UTF-8') ?></code>
                            </div>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if ($_resultDate !== '') : ?>
            <div class="mt-3 pt-3 border-top text-end">
                <small class="text-body-secondary">
                    <?= htmlspecialchars($i18n->t('plugin.analysed_on'), ENT_QUOTES, 'UTF-8') ?>
                    <time datetime="<?= htmlspecialchars(substr($_resultDate, 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($i18n->date($_resultDate), ENT_QUOTES, 'UTF-8') ?>
                    </time>
                </small>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
    <?php endforeach; ?>
<?php endif; ?>
