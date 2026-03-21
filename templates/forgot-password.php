<?php

/**
 * Forgot-password page template.
 *
 * Expected variables:
 *   $i18n  I18n
 *   $sent  bool   — true once the form has been submitted (show confirmation)
 */

declare(strict_types=1);

use PluginInsight\Csrf;

?>

<main class="container py-5" style="max-width:440px">
    <h1 class="fw-bold mb-2"><?= htmlspecialchars($i18n->t('auth.forgot_heading'), ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($sent ?? false) : ?>
    <div class="alert alert-success" role="status">
        <?= htmlspecialchars($i18n->t('auth.forgot_sent'), ENT_QUOTES, 'UTF-8') ?>
    </div>

    <?php else : ?>
    <p class="text-body-secondary mb-4">
        <?= htmlspecialchars($i18n->t('auth.forgot_desc'), ENT_QUOTES, 'UTF-8') ?>
    </p>

    <form method="post" action="/forgot-password/" novalidate>
        <?= Csrf::field() ?>

        <div class="mb-4">
            <label for="forgot-email" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.forgot_email'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="email"
                   id="forgot-email"
                   name="email"
                   class="form-control"
                   autocomplete="email"
                   required
                   maxlength="254">
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <?= htmlspecialchars($i18n->t('auth.forgot_btn'), ENT_QUOTES, 'UTF-8') ?>
        </button>
    </form>

    <?php endif; ?>

    <p class="text-center mt-3 small">
        <a href="/login/"><?= htmlspecialchars($i18n->t('auth.login_heading'), ENT_QUOTES, 'UTF-8') ?></a>
    </p>
</main>
