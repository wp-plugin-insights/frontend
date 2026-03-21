<?php

/**
 * Login page template.
 *
 * Expected variables:
 *   $i18n    I18n
 *   $error   string|null   — 'invalid' | 'locked' | null
 *
 * GET params read directly:
 *   ?registered=1  — shows account-created confirmation
 */

declare(strict_types=1);

use PluginInsight\Csrf;

$errorMsg = match ($error ?? null) {
    'locked'  => $i18n->t('auth.login_locked'),
    'invalid' => $i18n->t('auth.login_error'),
    default   => null,
};
?>

<main class="container py-5" style="max-width:440px" id="main-content">
    <h1 class="fw-bold mb-4"><?= htmlspecialchars($i18n->t('auth.login_heading'), ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if (isset($_GET['registered'])) : ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($i18n->t('auth.register_success'), ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <?php if ($errorMsg !== null) : ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <form method="post" action="/login/" novalidate>
        <?= Csrf::field() ?>

        <div class="mb-3">
            <label for="login-email" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.login_email'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="email"
                   id="login-email"
                   name="email"
                   class="form-control"
                   autocomplete="email"
                   required
                   maxlength="254"
                   aria-describedby="login-email-hint">
        </div>

        <div class="mb-4">
            <label for="login-password" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.login_password'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="password"
                   id="login-password"
                   name="password"
                   class="form-control"
                   autocomplete="current-password"
                   required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <?= htmlspecialchars($i18n->t('auth.login_btn'), ENT_QUOTES, 'UTF-8') ?>
        </button>
    </form>

    <p class="text-center mt-3 small">
        <a href="/forgot-password/">
            <?= htmlspecialchars($i18n->t('auth.login_forgot'), ENT_QUOTES, 'UTF-8') ?>
        </a>
    </p>

    <hr class="my-4">

    <p class="text-center small text-body-secondary mb-1">
        <?= htmlspecialchars($i18n->t('auth.login_new_here'), ENT_QUOTES, 'UTF-8') ?>
    </p>
    <a href="/register/" class="btn btn-outline-secondary w-100">
        <?= htmlspecialchars($i18n->t('auth.login_register'), ENT_QUOTES, 'UTF-8') ?>
    </a>
</main>
