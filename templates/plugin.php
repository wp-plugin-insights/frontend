<?php

/**
 * Plugin detail template.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n             I18n
 *   $plugin           array<string, mixed>   — row from the plugin table
 *   $analysisResults  array<string, array<string, mixed>>  — runner_slug → decoded result JSON
 *   $wpVersions       list<array{version: string, php_min: string, mysql_min: string}>
 *   $analysedVersions list<string>           — versions with at least one analysis result
 *   $selectedVersion  string                 — version whose analysis is currently displayed
 */

declare(strict_types=1);

$slug        = (string) ($plugin['plugin_slug']         ?? '');
$name        = html_entity_decode((string) ($plugin['plugin_name'] ?? $slug), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$version     = (string) ($plugin['plugin_version']      ?? '');
$installs    = (int)    ($plugin['plugin_installs']     ?? 0);
$downloaded  = (int)    ($plugin['plugin_downloaded']   ?? 0);
$requires    = (string) ($plugin['plugin_requires']     ?? '');
$tested      = (string) ($plugin['plugin_tested']       ?? '');
$requiresPhp = (string) ($plugin['plugin_requires_php'] ?? '');
$lastUpdated = (string) ($plugin['plugin_last_updated'] ?? '');
$added       = (string) ($plugin['plugin_added']        ?? '');
$source      = (string) ($plugin['plugin_source']       ?? '');
$shortDesc   = html_entity_decode((string) ($plugin['plugin_short_description'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Author: stored as HTML (<a href="...">Name</a>); extract plain name + use profile URL
$authorHtml    = (string) ($plugin['plugin_author']         ?? '');
$authorName    = html_entity_decode(strip_tags($authorHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$authorProfile = (string) ($plugin['plugin_author_profile'] ?? '');
// Only use the profile URL if it's a safe HTTPS link
if ($authorProfile !== '' && !str_starts_with($authorProfile, 'https://')) {
    $authorProfile = '';
}

// Icons: JSON object with keys like 'svg', '1x', '2x', 'default'
$iconsRaw = (string) ($plugin['plugin_icons'] ?? '');
$icons    = $iconsRaw !== '' ? json_decode($iconsRaw, true) : null;
$icons    = is_array($icons) ? $icons : [];
$iconUrl  = (string) ($icons['svg'] ?? $icons['1x'] ?? $icons['2x'] ?? $icons['default'] ?? '');
if ($iconUrl !== '' && !str_starts_with($iconUrl, 'https://')) {
    $iconUrl = '';
}

// Decode plugin dependencies (stored as JSON array)
$depsRaw = (string) ($plugin['plugin_requires_plugins'] ?? '[]');
$deps    = json_decode($depsRaw, true);
$deps    = is_array($deps) ? $deps : [];

// Determine current WP major.minor from live version data (or fall back to hardcoded).
// $wpVersions[0] is the latest release, e.g. "6.9.4" → major.minor = "6.9".
$latestWpVersion = !empty($wpVersions) ? (string) $wpVersions[0]['version'] : '6.6';
$latestWpMinor   = implode('.', array_slice(explode('.', $latestWpVersion), 0, 2));

// "Tested up to" badge: current if the plugin covers the latest major.minor branch.
$testedBadge  = '';
if ($tested !== '') {
    $testedBadge = version_compare($tested, $latestWpMinor, '>=')
        ? $i18n->t('plugin.compat_badge_current')
        : $i18n->t('plugin.compat_badge_outdated');
}

$updatedBadge = '';
if ($lastUpdated !== '') {
    $ts = strtotime($lastUpdated);
    if ($ts !== false) {
        $updatedBadge = (time() - $ts) < 365 * 24 * 3600
            ? $i18n->t('plugin.compat_badge_recent')
            : $i18n->t('plugin.compat_badge_outdated');
    }
}

// Version picker: $analysedVersions + $selectedVersion are set by index.php
/** @var list<string> $analysedVersions */
$analysedVersions = $analysedVersions ?? [];
/** @var string $selectedVersion */
$selectedVersion  = $selectedVersion ?? $version;
$hasMultipleVersions = count($analysedVersions) > 1;

// ── Compatibility & Requirements grade ───────────────────────────────────────
// Graded independently of runner results so it can appear in the card header
// and feed into the overall weighted average.
//
// Penalties (cumulative, capped at F):
//   No WP version declared                        → −1 grade
//   No PHP version declared                       → −1 grade
//   Declared PHP below WP-required PHP            → −2 grades
//   Last update > 1 year ago                      → −1 grade
//   Tested-up-to is behind current WP branch      → −1 grade
/** @var string|null $wpMinPhpRequired */
$wpMinPhpRequired = $wpMinPhpRequired ?? null;

$_compatGl    = ['A', 'B', 'C', 'D', 'F'];
$_compatPos   = 0;
$_compatNotes = [];

if ($requires === '') {
    $_compatPos++;
    $_compatNotes[] = $i18n->t('plugin.compat_note_no_wp');
}
if ($requiresPhp === '') {
    $_compatPos++;
    $_compatNotes[] = $i18n->t('plugin.compat_note_no_php');
}
if (
    $requires !== ''
    && $wpMinPhpRequired !== null
    && $requiresPhp !== ''
    && version_compare($requiresPhp, $wpMinPhpRequired, '<')
) {
    $_compatPos += 2;
    $_compatNotes[] = $i18n->t('plugin.compat_note_php_wp_mismatch');
}
if ($updatedBadge === $i18n->t('plugin.compat_badge_outdated')) {
    $_compatPos++;
    $_compatNotes[] = $i18n->t('plugin.compat_note_outdated');
}
if ($testedBadge === $i18n->t('plugin.compat_badge_outdated')) {
    $_compatPos++;
    $_compatNotes[] = $i18n->t('plugin.compat_note_tested_outdated');
}

$_compatPos        = max(0, min(count($_compatGl) - 1, $_compatPos));
$_compatGrade      = $_compatGl[$_compatPos];
$_compatGradeClass = [
    'A' => 'grade-a', 'B' => 'grade-b', 'C' => 'grade-c',
    'D' => 'grade-d', 'F' => 'grade-f',
][$_compatGrade];
$_compatPct        = match ($_compatGrade) {
    'A' => 95, 'B' => 80, 'C' => 65, 'D' => 50, default => 20,
};

// ── Overall grade (weighted) ─────────────────────────────────────────────────
// Delegated to GradeCalculator so the weighting logic lives in one place.
$_gradeResult       = \PluginInsight\GradeCalculator::calculate($_compatGrade, $analysisResults);
$_overallGrade      = $_gradeResult['grade'];
$_overallPct        = $_gradeResult['pct'];
$_overallGradeClass = $_gradeResult['css_class'];
$_gradeBreakdown    = $_gradeResult['breakdown'];
?>

<!-- ── Breadcrumb ────────────────────────────────────────── -->
<div class="container mt-3">
    <nav aria-label="<?php echo htmlspecialchars($i18n->t('nav.home'), ENT_QUOTES, 'UTF-8') ?>">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="/"><?php echo htmlspecialchars($i18n->t('nav.home'), ENT_QUOTES, 'UTF-8') ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <code class="plugin-slug"><?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?></code>
            </li>
        </ol>
    </nav>
</div>

<!-- ── Plugin header ─────────────────────────────────────── -->
<header class="container mt-3 mb-4">
    <div class="card">
        <div class="card-body">

            <!-- Row 1: icon · identity block · grade ─────────────────── -->
            <div class="d-flex align-items-start gap-3">
                <?php if ($iconUrl !== '') : ?>
                <img src="<?php echo htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8') ?>"
                     alt=""
                     width="80"
                     height="80"
                     class="rounded-2 flex-shrink-0"
                     loading="lazy"
                     aria-hidden="true">
                <?php endif; ?>

                <!-- Identity: name / slug / description -->
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div class="min-w-0">
                            <h1 class="h3 fw-bold mb-1">
                                <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
                            </h1>
                            <div class="mb-1">
                                <code class="plugin-slug text-body-secondary">
                                    <?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>
                                </code>
                            </div>
                            <?php if ($shortDesc !== '') : ?>
                            <p class="text-body-secondary small mb-0">
                                <?php echo htmlspecialchars($shortDesc, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <?php endif; ?>
                        </div>

                        <!-- Grade badge — always top-right, never wraps with meta -->
                        <?php if ($_overallGrade !== '') : ?>
                        <div class="flex-shrink-0 text-center">
                            <div class="grade <?php echo htmlspecialchars($_overallGradeClass, ENT_QUOTES, 'UTF-8') ?>"
                                 style="width:3.5rem;height:3.5rem;font-size:1.6rem;line-height:3.5rem"
                                 aria-label="<?php echo htmlspecialchars($i18n->t('plugin.overall_grade') . ' ' . $_overallGrade, ENT_QUOTES, 'UTF-8') ?>"
                                 title="<?php echo htmlspecialchars($i18n->t('plugin.overall_grade') . ': ' . $_overallGrade . ' (' . $_overallPct . '%)', ENT_QUOTES, 'UTF-8') ?>">
                                <?php echo htmlspecialchars($_overallGrade, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="text-body-secondary small mt-1">
                                <?php echo htmlspecialchars($_overallPct . '%', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Row 2: meta-data — full card width, always below icon/title/grade -->
            <div class="d-flex flex-wrap gap-3 text-body-secondary small align-items-center mt-3 pt-3 border-top">
                <?php if ($hasMultipleVersions) : ?>
                <span class="d-flex align-items-center gap-1">
                    <i class="bi bi-tag me-1" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('plugin.version'), ENT_QUOTES, 'UTF-8') ?>
                    <select id="version-picker"
                            class="form-select form-select-sm d-inline-block w-auto"
                            aria-label="<?php echo htmlspecialchars($i18n->t('plugin.version_picker_label'), ENT_QUOTES, 'UTF-8') ?>">
                        <?php foreach ($analysedVersions as $_pv) : ?>
                        <option value="<?php echo htmlspecialchars($_pv, ENT_QUOTES, 'UTF-8') ?>"
                                <?php echo $_pv === $selectedVersion ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($_pv, ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($_pv === $version) : ?>
                            (<?php echo htmlspecialchars($i18n->t('plugin.version_current'), ENT_QUOTES, 'UTF-8') ?>)
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </span>
                <?php elseif ($version !== '') : ?>
                <span>
                    <i class="bi bi-tag me-1" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('plugin.version'), ENT_QUOTES, 'UTF-8') ?>
                    <?php echo htmlspecialchars($version, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
                <?php if ($lastUpdated !== '') : ?>
                <span>
                    <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('plugin.updated'), ENT_QUOTES, 'UTF-8') ?>
                    <time datetime="<?php echo htmlspecialchars(substr($lastUpdated, 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                        <?php echo htmlspecialchars($i18n->date($lastUpdated), ENT_QUOTES, 'UTF-8') ?>
                    </time>
                </span>
                <?php endif; ?>
                <?php if ($downloaded > 0) : ?>
                <span>
                    <i class="bi bi-download me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->number($downloaded), ENT_QUOTES, 'UTF-8') ?>+
                    <?php echo htmlspecialchars($i18n->t('plugin.downloads'), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
                <?php if ($installs > 0) : ?>
                <span>
                    <i class="bi bi-people me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->number($installs), ENT_QUOTES, 'UTF-8') ?>+
                    <?php echo htmlspecialchars($i18n->t('plugin.active_installs'), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
                <?php if ($authorName !== '') : ?>
                <span>
                    <i class="bi bi-person me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('plugin.author'), ENT_QUOTES, 'UTF-8') ?>:
                    <?php if ($authorProfile !== '') : ?>
                    <a href="<?php echo htmlspecialchars($authorProfile, ENT_QUOTES, 'UTF-8') ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="text-decoration-none"
                       aria-label="<?php echo htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?> (opens in new tab)">
                        <?php echo htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?php else : ?>
                        <?php echo htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </span>
                <?php endif; ?>
                <?php if ($source !== '') : ?>
                <span>
                    <i class="bi bi-database me-1" aria-hidden="true"></i>
                    <?php echo htmlspecialchars($i18n->t('plugin.source'), ENT_QUOTES, 'UTF-8') ?>:
                    <?php echo htmlspecialchars($source, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
                <a href="https://wordpress.org/plugins/<?php echo htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="text-decoration-none"
                   aria-label="<?php echo htmlspecialchars($i18n->t('plugin.wp_org_link'), ENT_QUOTES, 'UTF-8') ?> <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?> (opens in new tab)">
                    <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('plugin.wp_org_link'), ENT_QUOTES, 'UTF-8') ?>
                </a>
            </div>
        </div>
        <?php if (!empty($_gradeBreakdown)) : ?>
        <div class="border-top pt-3 mt-3 px-3 pb-1">
            <p class="text-body-secondary small mb-2 fw-semibold">
                <?php echo htmlspecialchars($i18n->t('plugin.grade_breakdown'), ENT_QUOTES, 'UTF-8') ?>
            </p>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($_gradeBreakdown as $_be) : ?>
                <div class="d-flex align-items-center gap-1 small <?php echo !$_be['scored'] ? 'opacity-50' : '' ?>">
                    <span class="grade <?php echo htmlspecialchars($_be['css_class'], ENT_QUOTES, 'UTF-8') ?>"
                          style="width:1.6rem;height:1.6rem;font-size:.75rem;line-height:1.6rem"
                          title="<?php echo htmlspecialchars($_be['label'] . ': ' . $_be['grade'] . ' (' . $_be['pct'] . '%)', ENT_QUOTES, 'UTF-8') ?>"
                          aria-label="<?php echo htmlspecialchars($_be['label'] . ' ' . $_be['grade'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php echo htmlspecialchars($_be['grade'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="text-body-secondary">
                        <?php echo htmlspecialchars($_be['label'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php if (!$_be['scored']) : ?>
                    <span class="badge text-bg-secondary" style="font-size:.6rem">info</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</header>

<!-- ── Analysis cards ────────────────────────────────────── -->
<main class="container pb-5" aria-label="Plugin analysis">
    <div class="d-flex flex-column gap-3">

        <!-- Card: Compatibility & Requirements -->
        <div class="card analysis-card">
            <div class="card-header p-0">
                <button class="card-header-btn"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#card-compat"
                        aria-expanded="true"
                        aria-controls="card-compat">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle fs-5 text-success" aria-hidden="true"></i>
                        <span class="fw-semibold"><?php echo htmlspecialchars($i18n->t('plugin.compat_title'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="grade <?php echo htmlspecialchars($_compatGradeClass, ENT_QUOTES, 'UTF-8') ?>"
                              aria-label="<?php echo htmlspecialchars($i18n->t('plugin.grade_label', ['grade' => $_compatGrade]), ENT_QUOTES, 'UTF-8') ?>">
                            <?php echo htmlspecialchars($_compatGrade, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <i class="bi bi-chevron-down toggle-icon text-body-secondary" aria-hidden="true"></i>
                    </div>
                </button>
            </div>
            <div class="collapse show" id="card-compat">
                <div class="card-body border-top">
                    <?php if (!empty($_compatNotes)) : ?>
                    <p class="small text-body-secondary mb-2">
                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i><?php echo htmlspecialchars(implode('; ', $_compatNotes), ENT_QUOTES, 'UTF-8') ?>.
                    </p>
                    <?php endif; ?>
                    <table class="table table-sm table-borderless mb-0"
                           aria-label="<?php echo htmlspecialchars($i18n->t('plugin.compat_title'), ENT_QUOTES, 'UTF-8') ?>">
                        <tbody>
                            <?php if ($requires !== '') : ?>
                            <tr>
                                <td class="text-body-secondary" style="width:55%">
                                    <?php echo htmlspecialchars($i18n->t('plugin.compat_requires_wp'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($requires, ENT_QUOTES, 'UTF-8') ?>
                                    <?php echo htmlspecialchars($i18n->t('plugin.compat_or_higher'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($tested !== '') : ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?php echo htmlspecialchars($i18n->t('plugin.compat_tested'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($tested, ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($testedBadge !== '') : ?>
                                    <span class="badge ms-1 <?php echo $testedBadge === $i18n->t('plugin.compat_badge_current') ? 'text-bg-success' : 'text-bg-warning' ?>">
                                        <?php echo htmlspecialchars($testedBadge, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($requiresPhp !== '') : ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?php echo htmlspecialchars($i18n->t('plugin.compat_requires_php'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($requiresPhp, ENT_QUOTES, 'UTF-8') ?>
                                    <?php echo htmlspecialchars($i18n->t('plugin.compat_or_higher'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($lastUpdated !== '') : ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?php echo htmlspecialchars($i18n->t('plugin.compat_last_updated'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <time datetime="<?php echo htmlspecialchars(substr($lastUpdated, 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                                        <?php echo htmlspecialchars($i18n->date($lastUpdated), ENT_QUOTES, 'UTF-8') ?>
                                    </time>
                                    <?php if ($updatedBadge !== '') : ?>
                                    <span class="badge ms-1 <?php echo $updatedBadge === $i18n->t('plugin.compat_badge_recent') ? 'text-bg-success' : 'text-bg-warning' ?>">
                                        <?php echo htmlspecialchars($updatedBadge, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?php echo htmlspecialchars($i18n->t('plugin.compat_dependencies'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php if (empty($deps)) : ?>
                                    <em class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('plugin.compat_none'), ENT_QUOTES, 'UTF-8') ?></em>
                                    <?php else : ?>
                                        <?php foreach ($deps as $dep) : ?>
                                    <code class="plugin-slug me-1"><?php echo htmlspecialchars((string) $dep, ENT_QUOTES, 'UTF-8') ?></code>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                            // ── WordPress–PHP compatibility check ──────────────────────────
                            // $wpMinPhpRequired: minimum PHP needed to run $requires WP (from wp_php_compat table).
                            // Only shown when we have both a WP requirement and a table entry.
                            /** @var string|null $wpMinPhpRequired */
                            $wpMinPhpRequired = $wpMinPhpRequired ?? null;

                            if ($requires !== '' && $wpMinPhpRequired !== null) :
                                // Determine compatibility status
                                if ($requiresPhp === '') {
                                    // Plugin doesn't declare PHP at all — just inform
                                    $wpCompatStatus  = 'info';
                                    $wpCompatBadge   = 'text-bg-info';
                                    $wpCompatIcon    = 'bi-info-circle';
                                    $wpCompatMessage = $i18n->t('plugin.wp_php_compat_info', [
                                        'wp'  => $requires,
                                        'php' => $wpMinPhpRequired,
                                    ]);
                                } elseif (version_compare($requiresPhp, $wpMinPhpRequired, '<')) {
                                    // Plugin PHP requirement is lower than what its WP version needs — mismatch
                                    $wpCompatStatus  = 'warning';
                                    $wpCompatBadge   = 'text-bg-danger';
                                    $wpCompatIcon    = 'bi-exclamation-triangle-fill';
                                    $wpCompatMessage = $i18n->t('plugin.wp_php_compat_warn', [
                                        'wp'       => $requires,
                                        'php'      => $wpMinPhpRequired,
                                        'declared' => $requiresPhp,
                                    ]);
                                } else {
                                    // Plugin PHP requirement is at or above what its WP version needs — OK
                                    $wpCompatStatus  = 'ok';
                                    $wpCompatBadge   = 'text-bg-success';
                                    $wpCompatIcon    = 'bi-check-circle-fill';
                                    $wpCompatMessage = $i18n->t('plugin.wp_php_compat_ok', [
                                        'declared' => $requiresPhp,
                                        'wp'       => $requires,
                                        'php'      => $wpMinPhpRequired,
                                    ]);
                                }
                                ?>
                            <tr>
                                <td class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('plugin.wp_php_compat'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge <?php echo htmlspecialchars($wpCompatBadge, ENT_QUOTES, 'UTF-8') ?> me-1">
                                        <i class="bi <?php echo htmlspecialchars($wpCompatIcon, ENT_QUOTES, 'UTF-8') ?> me-1" aria-hidden="true"></i><?php echo htmlspecialchars($wpCompatStatus, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="small text-body-secondary">
                                        <?php echo htmlspecialchars($wpCompatMessage, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php require __DIR__ . '/_runner-cards.php'; ?>

    </div><!-- /.d-flex -->

    <!-- Grade legend -->
    <div class="mt-4" role="region" aria-label="<?php echo htmlspecialchars($i18n->t('plugin.grade_scale'), ENT_QUOTES, 'UTF-8') ?>">
        <h2 class="h6 text-body-secondary mb-2"><?php echo htmlspecialchars($i18n->t('plugin.grade_scale'), ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="d-flex flex-wrap gap-2 small">
            <?php
            $grades = [
                'a' => $i18n->t('plugin.grade_a'),
                'b' => $i18n->t('plugin.grade_b'),
                'c' => $i18n->t('plugin.grade_c'),
                'd' => $i18n->t('plugin.grade_d'),
                'f' => $i18n->t('plugin.grade_f'),
            ];
            foreach ($grades as $letter => $label) :
                ?>
            <span class="d-flex align-items-center gap-1">
                <span class="grade grade-<?php echo htmlspecialchars($letter, ENT_QUOTES, 'UTF-8') ?>"
                      style="width:1.4rem;height:1.4rem;font-size:.75rem"
                      aria-label="<?php echo htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8') ?>">
                    <?php echo htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php if ($hasMultipleVersions) : ?>
<script>
(function () {
    var picker = document.getElementById('version-picker');
    if (!picker) { return; }
    picker.addEventListener('change', function () {
        var url = new URL(window.location.href);
        url.searchParams.set('version', this.value);
        window.location.href = url.toString();
    });
}());
</script>
<?php endif; ?>
