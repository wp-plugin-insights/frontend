<?php

/**
 * 404 Not Found template.
 *
 * Expected variables (set by index.php before including this file):
 *   $i18n I18n
 */

declare(strict_types=1);

?>

<main class="container py-5 text-center" style="max-width:560px">
    <h1 class="display-6 fw-bold mb-3"><?= htmlspecialchars($i18n->t('404.heading'), ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="text-body-secondary mb-4"><?= htmlspecialchars($i18n->t('404.desc'), ENT_QUOTES, 'UTF-8') ?></p>
    <a href="/" class="btn btn-primary">
        <i class="bi bi-house me-1" aria-hidden="true"></i><?= htmlspecialchars($i18n->t('404.back'), ENT_QUOTES, 'UTF-8') ?>
    </a>
</main>
