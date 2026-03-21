<?php

/**
 * Homepage template.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n    I18n
 *   $plugins list<array<string, mixed>>  — recently analysed plugins (one row per plugin)
 *   $total   int                         — total plugins tracked in the database
 */

declare(strict_types=1);

?>

<!-- ── Hero ─────────────────────────────────────────────── -->
<section class="hero text-center" aria-label="<?php echo htmlspecialchars($i18n->t('home.headline'), ENT_QUOTES, 'UTF-8') ?>">
    <div class="container">
        <h1 class="fw-bold mb-2"><?php echo htmlspecialchars($i18n->t('home.headline'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="text-body-secondary mb-4"><?php echo htmlspecialchars($i18n->t('home.subheadline'), ENT_QUOTES, 'UTF-8') ?></p>
        <div class="search-wrap">
            <form id="plugin-search-form"
                  role="search"
                  action="/plugin/"
                  method="get"
                  aria-label="<?php echo htmlspecialchars($i18n->t('home.search_placeholder'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="input-group input-group-lg shadow-sm">
                    <label class="visually-hidden" for="plugin-search-input">
                        <?php echo htmlspecialchars($i18n->t('home.search_placeholder'), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input
                        id="plugin-search-input"
                        type="search"
                        name="slug"
                        class="form-control"
                        placeholder="<?php echo htmlspecialchars($i18n->t('home.search_placeholder'), ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="off"
                        spellcheck="false"
                        maxlength="100"
                        pattern="[a-z0-9\-]+"
                    >
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search me-1" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('home.search_btn'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </div>
            </form>
        </div>
        <?php if ($total > 0) : ?>
        <p class="text-body-secondary small mt-3" aria-live="polite">
            <?php echo htmlspecialchars($i18n->t('home.db_count', ['count' => $i18n->number($total)]), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <?php endif; ?>
    </div>
</section>

<!-- ── Recently reviewed ─────────────────────────────────── -->
<main class="container pb-5" aria-label="<?php echo htmlspecialchars($i18n->t('home.recently_reviewed'), ENT_QUOTES, 'UTF-8') ?>">
    <h2 class="h5 fw-semibold mb-3"><?php echo htmlspecialchars($i18n->t('home.recently_reviewed'), ENT_QUOTES, 'UTF-8') ?></h2>

    <?php if (empty($plugins)) : ?>
    <p class="text-body-secondary"><?php echo htmlspecialchars($i18n->t('home.no_plugins'), ENT_QUOTES, 'UTF-8') ?></p>
    <?php else : ?>
    <div class="row g-3">
        <?php foreach ($plugins as $p) : ?>
            <?php
            $pSlug     = (string) ($p['plugin_slug'] ?? '');
            $pName     = html_entity_decode((string) ($p['plugin_name'] ?? $p['plugin_slug'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $pDesc     = html_entity_decode((string) ($p['plugin_short_description'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $pIconsRaw = (string) ($p['plugin_icons'] ?? '');
            $pIcons    = $pIconsRaw !== '' ? json_decode($pIconsRaw, true) : null;
            $pIcons    = is_array($pIcons) ? $pIcons : [];
            $pIconUrl  = (string) ($pIcons['svg'] ?? $pIcons['1x'] ?? $pIcons['default'] ?? '');
            if ($pIconUrl !== '' && !str_starts_with($pIconUrl, 'https://')) {
                $pIconUrl = '';
            }
            $pGrade    = strtolower((string) ($p['latest_grade'] ?? ''));
            $pAnalysis = (string) ($p['latest_analysis'] ?? '');
            ?>
        <div class="col-12 col-sm-6 col-lg-4">
            <a href="/plugin/<?php echo htmlspecialchars($pSlug, ENT_QUOTES, 'UTF-8') ?>/"
               class="card plugin-card text-decoration-none h-100"
               aria-label="<?php echo htmlspecialchars($pName, ENT_QUOTES, 'UTF-8') ?> (<?php echo htmlspecialchars($pSlug, ENT_QUOTES, 'UTF-8') ?>)">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="overflow-hidden flex-grow-1 me-2">
                            <div class="fw-semibold text-truncate" aria-hidden="true">
                                <?php echo htmlspecialchars($pName, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <code class="plugin-slug text-body-secondary" aria-hidden="true">
                                <?php echo htmlspecialchars($pSlug, ENT_QUOTES, 'UTF-8') ?>
                            </code>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <?php if ($pGrade !== '' && in_array($pGrade, ['a','b','c','d','f'], true)) : ?>
                            <span class="grade grade-<?php echo htmlspecialchars($pGrade, ENT_QUOTES, 'UTF-8') ?>"
                                  aria-label="<?php echo htmlspecialchars($i18n->t('plugin.grade_label', ['grade' => strtoupper($pGrade)]), ENT_QUOTES, 'UTF-8') ?>">
                                <?php echo htmlspecialchars(strtoupper($pGrade), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php endif; ?>
                            <?php if ($pIconUrl !== '') : ?>
                            <img src="<?php echo htmlspecialchars($pIconUrl, ENT_QUOTES, 'UTF-8') ?>"
                                 alt=""
                                 width="40"
                                 height="40"
                                 class="rounded-1"
                                 loading="lazy"
                                 aria-hidden="true">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($pDesc !== '') : ?>
                    <p class="text-body-secondary small plugin-desc mb-2" aria-hidden="true">
                        <?php echo htmlspecialchars($pDesc, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php endif; ?>
                    <div class="text-body-secondary small mt-auto">
                        <?php if ($pAnalysis !== '') : ?>
                        <i class="bi bi-check2-circle me-1 text-success" aria-hidden="true"></i><?php echo htmlspecialchars($i18n->t('home.analysed_on'), ENT_QUOTES, 'UTF-8') ?>
                        <time datetime="<?php echo htmlspecialchars(substr($pAnalysis, 0, 10), ENT_QUOTES, 'UTF-8') ?>">
                            <?php echo htmlspecialchars($i18n->date($pAnalysis), ENT_QUOTES, 'UTF-8') ?>
                        </time>
                        <?php endif; ?>
                        <?php if (!empty($p['plugin_downloaded']) && (int) $p['plugin_downloaded'] > 0) : ?>
                        &nbsp;·&nbsp;<?php echo htmlspecialchars($i18n->number((int) $p['plugin_downloaded']), ENT_QUOTES, 'UTF-8') ?>
                            <?php echo htmlspecialchars($i18n->t('plugin.downloads'), ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>
