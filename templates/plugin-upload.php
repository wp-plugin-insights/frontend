<?php

/**
 * Plugin upload result template.
 *
 * Renders the metadata and processing status for a single plugin uploaded
 * via the API. The data comes from the plugin_upload table.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n            I18n
 *   $upload          array<string, mixed>   — row from the plugin_upload table
 *   $uuid            string                 — validated UUID
 *   $analysisResults array<string, array<string, mixed>>  — runner_slug → decoded result JSON
 *   $wpVersions      list<array{version: string, php_min: string, mysql_min: string}>
 */

declare(strict_types=1);

$slug        = (string) ($upload['plugin_slug']        ?? '');
$name        = html_entity_decode((string) ($upload['plugin_name'] ?? $slug), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$version     = (string) ($upload['plugin_version']     ?? '');
$author      = html_entity_decode((string) ($upload['plugin_author'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$requires    = (string) ($upload['plugin_requires']    ?? '');
$tested      = (string) ($upload['plugin_tested']      ?? '');
$requiresPhp = (string) ($upload['plugin_requires_php'] ?? '');
$description = html_entity_decode((string) ($upload['plugin_description'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$status      = (string) ($upload['upload_status']      ?? 'pending');
$uploadedAt  = (string) ($upload['uploaded_at']        ?? '');
$uploadError = (string) ($upload['upload_error']       ?? '');

$displayName = $name !== '' ? $name : ($slug !== '' ? $slug : $uuid);

$apiUrl = 'https://api.plugininsight.com/' . htmlspecialchars($uuid, ENT_QUOTES, 'UTF-8') . '/';

// Still pending only when no result has arrived yet.
// Once at least one runner has produced a result, stop refreshing even if the
// upload_status has not been updated to 'done' yet.
$isPending = in_array($status, ['pending', 'queued'], true) && empty($analysisResults);

// "Tested up to" badge — compare against live WP version data (same logic as plugin.php).
$latestWpVersion = !empty($wpVersions) ? (string) $wpVersions[0]['version'] : '6.6';
$latestWpMinor   = implode('.', array_slice(explode('.', $latestWpVersion), 0, 2));
$testedBadge     = '';
if ($tested !== '') {
    $testedBadge = version_compare($tested, $latestWpMinor, '>=') ? 'Current' : 'Outdated';
}
?>

<!-- ── Breadcrumb ────────────────────────────────────────── -->
<div class="container mt-3">
    <nav aria-label="Breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="/"><?= htmlspecialchars($i18n->t('nav.home'), ENT_QUOTES, 'UTF-8') ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php if ($slug !== '') : ?>
                <code class="plugin-slug"><?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?></code>
                <?php else : ?>
                <code class="plugin-slug"><?= htmlspecialchars($uuid, ENT_QUOTES, 'UTF-8') ?></code>
                <?php endif; ?>
            </li>
        </ol>
    </nav>
</div>

<!-- ── Plugin header ─────────────────────────────────────── -->
<header class="container mt-3 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3 flex-wrap">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                        <h1 class="h3 fw-bold mb-0">
                            <?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?>
                        </h1>
                        <span class="badge text-bg-secondary">Uploaded via API</span>
                    </div>
                    <?php if ($slug !== '') : ?>
                    <div class="mb-1">
                        <code class="plugin-slug text-body-secondary">
                            <?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>
                        </code>
                    </div>
                    <?php endif; ?>
                    <?php if ($description !== '') : ?>
                    <p class="text-body-secondary small mb-2">
                        <?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php endif; ?>
                    <div class="d-flex flex-wrap gap-3 text-body-secondary small">
                        <?php if ($version !== '') : ?>
                        <span>
                            <i class="bi bi-tag me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($i18n->t('plugin.version'), ENT_QUOTES, 'UTF-8') ?>
                            <?= htmlspecialchars($version, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($author !== '') : ?>
                        <span>
                            <i class="bi bi-person me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($i18n->t('plugin.author'), ENT_QUOTES, 'UTF-8') ?>:
                            <?= htmlspecialchars($author, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($uploadedAt !== '') : ?>
                        <span>
                            <i class="bi bi-cloud-upload me-1" aria-hidden="true"></i>
                            Uploaded
                            <time datetime="<?= htmlspecialchars(substr($uploadedAt, 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($i18n->date($uploadedAt), ENT_QUOTES, 'UTF-8') ?>
                            </time>
                        </span>
                        <?php endif; ?>
                        <a href="<?= $apiUrl ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="text-decoration-none"
                           aria-label="JSON API (opens in new tab)">
                            <i class="bi bi-braces me-1" aria-hidden="true"></i>JSON API
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

        <!-- Status banner -->
        <?php if ($isPending) : ?>
        <div class="alert alert-info d-flex align-items-center gap-2 mb-0" role="status" aria-live="polite">
            <div class="spinner-border spinner-border-sm flex-shrink-0" aria-hidden="true"></div>
            <div>
                <strong>Analysis in progress.</strong>
                This page will refresh automatically every 10 seconds.
            </div>
        </div>
        <?php elseif ($status === 'error') : ?>
        <div class="alert alert-danger mb-0" role="alert">
            <strong>Analysis failed.</strong>
            <?php if ($uploadError !== '') : ?>
            <?= htmlspecialchars($uploadError, ENT_QUOTES, 'UTF-8') ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

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
                    <?php if ($requires === '' && $tested === '' && $requiresPhp === '') : ?>
                    <p class="text-body-secondary mb-0">No compatibility information found in the plugin.</p>
                    <?php else : ?>
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
                                    <span class="badge ms-1 <?= $testedBadge === 'Current' ? 'text-bg-success' : 'text-bg-warning' ?>">
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
                        </tbody>
                    </table>
                    <?php endif; ?>
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

<?php if ($isPending) : ?>
<script>
    setTimeout(function () { location.reload(); }, 10000);
</script>
<?php endif; ?>
