<?php

/**
 * Plugin detail template.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n            I18n
 *   $plugin          array<string, mixed>   — row from the plugin table
 *   $analysisResults array<string, array<string, mixed>>  — runner_slug → decoded result JSON
 */

declare(strict_types=1);

$slug        = (string) ($plugin['plugin_slug']         ?? '');
$name        = (string) ($plugin['plugin_name']         ?? $slug);
$version     = (string) ($plugin['plugin_version']      ?? '');
$installs    = (int)    ($plugin['plugin_installs']     ?? 0);
$downloaded  = (int)    ($plugin['plugin_downloaded']   ?? 0);
$requires    = (string) ($plugin['plugin_requires']     ?? '');
$tested      = (string) ($plugin['plugin_tested']       ?? '');
$requiresPhp = (string) ($plugin['plugin_requires_php'] ?? '');
$lastUpdated = (string) ($plugin['plugin_last_updated'] ?? '');
$added       = (string) ($plugin['plugin_added']        ?? '');
$source      = (string) ($plugin['plugin_source']       ?? '');
$shortDesc   = (string) ($plugin['plugin_short_description'] ?? '');

// Author: stored as HTML (<a href="...">Name</a>); extract plain name + use profile URL
$authorHtml    = (string) ($plugin['plugin_author']         ?? '');
$authorName    = strip_tags($authorHtml);
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

// Simple freshness indicators (WP 6.6+ = "current"; updated within 365 days = "recent")
$testedBadge  = '';
if ($tested !== '') {
    $testedBadge = version_compare($tested, '6.6', '>=')
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
?>

<!-- ── Breadcrumb ────────────────────────────────────────── -->
<div class="container mt-3">
    <nav aria-label="<?= htmlspecialchars($i18n->t('nav.home'), ENT_QUOTES, 'UTF-8') ?>">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="/"><?= htmlspecialchars($i18n->t('nav.home'), ENT_QUOTES, 'UTF-8') ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <code class="plugin-slug"><?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?></code>
            </li>
        </ol>
    </nav>
</div>

<!-- ── Plugin header ─────────────────────────────────────── -->
<header class="container mt-3 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3 flex-wrap">
                <?php if ($iconUrl !== '') : ?>
                <img src="<?= htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8') ?>"
                     alt=""
                     width="80"
                     height="80"
                     class="rounded-2 flex-shrink-0"
                     loading="lazy"
                     aria-hidden="true">
                <?php endif; ?>
                <div class="flex-grow-1">
                    <h1 class="h3 fw-bold mb-1">
                        <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
                    </h1>
                    <div class="mb-1">
                        <code class="plugin-slug text-body-secondary">
                            <?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>
                        </code>
                    </div>
                    <?php if ($shortDesc !== '') : ?>
                    <p class="text-body-secondary small mb-2">
                        <?= htmlspecialchars($shortDesc, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-3 text-body-secondary small">
                        <?php if ($version !== '') : ?>
                        <span>
                            <i class="bi bi-tag me-1" aria-hidden="true"></i><?= htmlspecialchars($i18n->t('plugin.version'), ENT_QUOTES, 'UTF-8') ?>
                            <?= htmlspecialchars($version, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($lastUpdated !== '') : ?>
                        <span>
                            <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($i18n->t('plugin.updated'), ENT_QUOTES, 'UTF-8') ?>
                            <time datetime="<?= htmlspecialchars(substr($lastUpdated, 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($i18n->date($lastUpdated), ENT_QUOTES, 'UTF-8') ?>
                            </time>
                        </span>
                        <?php endif; ?>
                        <?php if ($downloaded > 0) : ?>
                        <span>
                            <i class="bi bi-download me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($i18n->number($downloaded), ENT_QUOTES, 'UTF-8') ?>+
                            <?= htmlspecialchars($i18n->t('plugin.downloads'), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($installs > 0) : ?>
                        <span>
                            <i class="bi bi-people me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($i18n->number($installs), ENT_QUOTES, 'UTF-8') ?>+
                            <?= htmlspecialchars($i18n->t('plugin.active_installs'), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($authorName !== '') : ?>
                        <span>
                            <i class="bi bi-person me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($i18n->t('plugin.author'), ENT_QUOTES, 'UTF-8') ?>:
                            <?php if ($authorProfile !== '') : ?>
                            <a href="<?= htmlspecialchars($authorProfile, ENT_QUOTES, 'UTF-8') ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="text-decoration-none"
                               aria-label="<?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?> (opens in new tab)">
                                <?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <?php else : ?>
                                <?= htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($source !== '') : ?>
                        <span>
                            <i class="bi bi-database me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($i18n->t('plugin.source'), ENT_QUOTES, 'UTF-8') ?>:
                            <?= htmlspecialchars($source, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                        <a href="https://wordpress.org/plugins/<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>/"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="text-decoration-none"
                           aria-label="<?= htmlspecialchars($i18n->t('plugin.wp_org_link'), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?> (opens in new tab)">
                            <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i><?= htmlspecialchars($i18n->t('plugin.wp_org_link'), ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
                        <span class="fw-semibold"><?= htmlspecialchars($i18n->t('plugin.compat_title'), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <i class="bi bi-chevron-down toggle-icon text-body-secondary" aria-hidden="true"></i>
                </button>
            </div>
            <div class="collapse show" id="card-compat">
                <div class="card-body border-top">
                    <table class="table table-sm table-borderless mb-0"
                           aria-label="<?= htmlspecialchars($i18n->t('plugin.compat_title'), ENT_QUOTES, 'UTF-8') ?>">
                        <tbody>
                            <?php if ($requires !== '') : ?>
                            <tr>
                                <td class="text-body-secondary" style="width:55%">
                                    <?= htmlspecialchars($i18n->t('plugin.compat_requires_wp'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($requires, ENT_QUOTES, 'UTF-8') ?>
                                    <?= htmlspecialchars($i18n->t('plugin.compat_or_higher'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($tested !== '') : ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?= htmlspecialchars($i18n->t('plugin.compat_tested'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($tested, ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($testedBadge !== '') : ?>
                                    <span class="badge ms-1 <?= $testedBadge === $i18n->t('plugin.compat_badge_current') ? 'text-bg-success' : 'text-bg-warning' ?>">
                                        <?= htmlspecialchars($testedBadge, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($requiresPhp !== '') : ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?= htmlspecialchars($i18n->t('plugin.compat_requires_php'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($requiresPhp, ENT_QUOTES, 'UTF-8') ?>
                                    <?= htmlspecialchars($i18n->t('plugin.compat_or_higher'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($lastUpdated !== '') : ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?= htmlspecialchars($i18n->t('plugin.compat_last_updated'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <time datetime="<?= htmlspecialchars(substr($lastUpdated, 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($i18n->date($lastUpdated), ENT_QUOTES, 'UTF-8') ?>
                                    </time>
                                    <?php if ($updatedBadge !== '') : ?>
                                    <span class="badge ms-1 <?= $updatedBadge === $i18n->t('plugin.compat_badge_recent') ? 'text-bg-success' : 'text-bg-warning' ?>">
                                        <?= htmlspecialchars($updatedBadge, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-body-secondary">
                                    <?= htmlspecialchars($i18n->t('plugin.compat_dependencies'), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php if (empty($deps)) : ?>
                                    <em class="text-body-secondary"><?= htmlspecialchars($i18n->t('plugin.compat_none'), ENT_QUOTES, 'UTF-8') ?></em>
                                    <?php else : ?>
                                        <?php foreach ($deps as $dep) : ?>
                                    <code class="plugin-slug me-1"><?= htmlspecialchars((string) $dep, ENT_QUOTES, 'UTF-8') ?></code>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php require __DIR__ . '/_runner-cards.php'; ?>

    </div><!-- /.d-flex -->

    <!-- Grade legend -->
    <div class="mt-4" role="region" aria-label="<?= htmlspecialchars($i18n->t('plugin.grade_scale'), ENT_QUOTES, 'UTF-8') ?>">
        <h2 class="h6 text-body-secondary mb-2"><?= htmlspecialchars($i18n->t('plugin.grade_scale'), ENT_QUOTES, 'UTF-8') ?></h2>
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
                <span class="grade grade-<?= htmlspecialchars($letter, ENT_QUOTES, 'UTF-8') ?>"
                      style="width:1.4rem;height:1.4rem;font-size:.75rem"
                      aria-label="<?= htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars(strtoupper($letter), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</main>
