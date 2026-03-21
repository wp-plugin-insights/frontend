<?php

/**
 * Registration page template.
 *
 * Expected variables:
 *   $i18n    I18n
 *   $error   string|null   — 'invalid_email' | 'too_short' | 'mismatch' | 'email_taken' | null
 */

declare(strict_types=1);

use PluginInsight\Csrf;

$errorMsg = match ($error ?? null) {
    'invalid_email' => $i18n->t('auth.register_err_invalid_email'),
    'too_short'     => $i18n->t('auth.reset_too_short'),
    'mismatch'      => $i18n->t('auth.reset_mismatch'),
    'email_taken'   => $i18n->t('auth.register_err_email_taken'),
    default         => null,
};
?>

<main class="container py-5" style="max-width:440px" id="main-content">
    <h1 class="fw-bold mb-4"><?= htmlspecialchars($i18n->t('auth.register_heading'), ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($errorMsg !== null) : ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <form method="post" action="/register/" novalidate>
        <?= Csrf::field() ?>

        <div class="mb-3">
            <label for="register-email" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.login_email'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="email"
                   id="register-email"
                   name="email"
                   class="form-control"
                   autocomplete="email"
                   required
                   maxlength="254">
        </div>

        <div class="mb-3">
            <label for="register-password" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.login_password'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="password"
                   id="register-password"
                   name="password"
                   class="form-control"
                   autocomplete="new-password"
                   required
                   minlength="12">
        </div>

        <div class="mb-4">
            <label for="register-confirm" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.reset_confirm'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="password"
                   id="register-confirm"
                   name="confirm"
                   class="form-control"
                   autocomplete="new-password"
                   required
                   minlength="12">
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <?= htmlspecialchars($i18n->t('auth.register_btn'), ENT_QUOTES, 'UTF-8') ?>
        </button>
    </form>

    <hr class="my-4">

    <p class="text-center small text-body-secondary mb-1">
        <?= htmlspecialchars($i18n->t('auth.register_have_account'), ENT_QUOTES, 'UTF-8') ?>
    </p>
    <a href="/login/" class="btn btn-outline-secondary w-100">
        <?= htmlspecialchars($i18n->t('auth.register_login_link'), ENT_QUOTES, 'UTF-8') ?>
    </a>
</main>
