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
    'security'     => 'bi-shield-check',
    'code-quality' => 'bi-code-slash',
    'performance'  => 'bi-speedometer2',
    'translations' => 'bi-translate',
    'license'      => 'bi-file-earmark-text',
    'maintenance'  => 'bi-tools',
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
?>
<div class="card analysis-card">
    <div class="card-header p-0">
        <button class="card-header-btn"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#<?= $_cardId ?>"
                aria-expanded="<?= $_cardIndex === 1 ? 'true' : 'false' ?>"
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
    <div class="collapse <?= $_cardIndex === 1 ? 'show' : '' ?>" id="<?= $_cardId ?>">
        <div class="card-body border-top">

            <?php if ($_percentage !== null || $_reasoning !== '') : ?>
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
                <?php if ($_reasoning !== '') : ?>
                <p class="mb-0 small"><?= htmlspecialchars($_reasoning, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($_metrics)) : ?>
            <div class="mb-3">
                <h3 class="h6 text-body-secondary mb-2"><?= htmlspecialchars($i18n->t('plugin.metrics_title'), ENT_QUOTES, 'UTF-8') ?></h3>
                <div class="d-flex flex-wrap gap-3">
                    <?php foreach ($_metrics as $_metricKey => $_metricVal) : ?>
                    <div class="text-center">
                        <div class="fw-bold fs-5"><?= htmlspecialchars((string) $_metricVal, ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="small text-body-secondary"><?= htmlspecialchars(str_replace('_', ' ', (string) $_metricKey), ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($_issues)) : ?>
            <div>
                <h3 class="h6 text-body-secondary mb-2">
                    <?= htmlspecialchars($i18n->t('plugin.issues_title'), ENT_QUOTES, 'UTF-8') ?>
                    <span class="badge text-bg-secondary ms-1"><?= count($_issues) ?></span>
                </h3>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <?php foreach ($_issues as $_issue) :
                        if (!is_array($_issue)) { continue; }
                        $_sev      = strtolower((string) ($_issue['severity'] ?? 'info'));
                        $_msg      = (string) ($_issue['message']  ?? '');
                        $_loc      = (string) ($_issue['location'] ?? '');
                        $_badgeCls = $_severityBadge[$_sev] ?? 'text-bg-secondary';
                    ?>
                    <li class="d-flex align-items-start gap-2">
                        <span class="badge <?= htmlspecialchars($_badgeCls, ENT_QUOTES, 'UTF-8') ?> mt-1 flex-shrink-0">
                            <?= htmlspecialchars($_sev, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <div class="small">
                            <?= htmlspecialchars($_msg, ENT_QUOTES, 'UTF-8') ?>
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
