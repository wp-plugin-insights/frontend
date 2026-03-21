<?php

/**
 * Homepage template.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n    I18n
 *   $plugins list<array<string, mixed>>
 *   $total   int
 */

declare(strict_types=1);

?>

<!-- ── Hero ─────────────────────────────────────────────── -->
<section class="hero text-center" aria-label="<?= htmlspecialchars($i18n->t('home.headline'), ENT_QUOTES, 'UTF-8') ?>">
    <div class="container">
        <h1 class="fw-bold mb-2"><?= htmlspecialchars($i18n->t('home.headline'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="text-body-secondary mb-4"><?= htmlspecialchars($i18n->t('home.subheadline'), ENT_QUOTES, 'UTF-8') ?></p>
        <div class="search-wrap">
            <form id="plugin-search-form"
                  role="search"
                  action="/plugin/"
                  method="get"
                  aria-label="<?= htmlspecialchars($i18n->t('home.search_placeholder'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="input-group input-group-lg shadow-sm">
                    <label class="visually-hidden" for="plugin-search-input">
                        <?= htmlspecialchars($i18n->t('home.search_placeholder'), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input
                        id="plugin-search-input"
                        type="search"
                        name="slug"
                        class="form-control"
                        placeholder="<?= htmlspecialchars($i18n->t('home.search_placeholder'), ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="off"
                        spellcheck="false"
                        maxlength="100"
                        pattern="[a-z0-9\-]+"
                    >
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search me-1" aria-hidden="true"></i><?= htmlspecialchars($i18n->t('home.search_btn'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </div>
            </form>
        </div>
        <?php if ($total > 0) : ?>
        <p class="text-body-secondary small mt-3" aria-live="polite">
            <?= htmlspecialchars($i18n->t('home.db_count', ['count' => $i18n->number($total)]), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php endif; ?>
    </div>
</section>

<!-- ── Recently reviewed ─────────────────────────────────── -->
<main class="container pb-5" aria-label="<?= htmlspecialchars($i18n->t('home.recently_reviewed'), ENT_QUOTES, 'UTF-8') ?>">
    <h2 class="h5 fw-semibold mb-3"><?= htmlspecialchars($i18n->t('home.recently_reviewed'), ENT_QUOTES, 'UTF-8') ?></h2>

    <?php if (empty($plugins)) : ?>
    <p class="text-body-secondary"><?= htmlspecialchars($i18n->t('home.no_plugins'), ENT_QUOTES, 'UTF-8') ?></p>
    <?php else : ?>
    <div class="row g-3">
        <?php foreach ($plugins as $p) : ?>
        <?php
        $pSlug = htmlspecialchars($p['plugin_slug'], ENT_QUOTES, 'UTF-8');
        $pName = htmlspecialchars($p['plugin_name'] ?? $p['plugin_slug'], ENT_QUOTES, 'UTF-8');
        ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="/plugin/<?= $pSlug ?>/"
               class="card plugin-card text-decoration-none h-100"
               aria-label="<?= $pName ?> (<?= $pSlug ?>)">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="overflow-hidden">
                            <div class="fw-semibold text-truncate" aria-hidden="true">
                                <?= $pName ?>
                            </div>
                            <code class="plugin-slug text-body-secondary" aria-hidden="true">
                                <?= $pSlug ?>
                            </code>
                        </div>
                    </div>
                    <div class="text-body-secondary small mt-auto">
                        <?php if (!empty($p['plugin_last_updated'])) : ?>
                            <?= htmlspecialchars($i18n->t('home.updated_on'), ENT_QUOTES, 'UTF-8') ?>
                        <time datetime="<?= htmlspecialchars(substr($p['plugin_last_updated'], 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($i18n->date($p['plugin_last_updated']), ENT_QUOTES, 'UTF-8') ?>
                        </time>
                        <?php endif; ?>
                        <?php if (!empty($p['plugin_downloaded']) && (int) $p['plugin_downloaded'] > 0) : ?>
                        &nbsp;·&nbsp;<?= htmlspecialchars($i18n->number((int) $p['plugin_downloaded']), ENT_QUOTES, 'UTF-8') ?>
                            <?= htmlspecialchars($i18n->t('plugin.downloads'), ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>
