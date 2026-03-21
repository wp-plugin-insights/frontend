<?php

/**
 * Reset-password page template.
 *
 * Expected variables:
 *   $i18n      I18n
 *   $token     string                  — raw token from the URL (not yet validated)
 *   $resetRow  array<string,mixed>|null — valid reset row, or null if invalid/expired
 *   $error     string|null             — 'mismatch' | 'too_short' | null
 *   $success   bool
 */

declare(strict_types=1);

use PluginInsight\Csrf;

?>

<main class="container py-5" style="max-width:440px">
    <h1 class="fw-bold mb-4"><?= htmlspecialchars($i18n->t('auth.reset_heading'), ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($success ?? false) : ?>

    <div class="alert alert-success" role="status">
        <?= htmlspecialchars($i18n->t('auth.reset_success'), ENT_QUOTES, 'UTF-8') ?>
    </div>
    <p class="text-center mt-3 small">
        <a href="/login/"><?= htmlspecialchars($i18n->t('auth.login_heading'), ENT_QUOTES, 'UTF-8') ?></a>
    </p>

    <?php elseif (($resetRow ?? null) === null) : ?>

    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($i18n->t('auth.reset_invalid'), ENT_QUOTES, 'UTF-8') ?>
    </div>
    <p class="text-center mt-3 small">
        <a href="/forgot-password/">
            <?= htmlspecialchars($i18n->t('auth.forgot_heading'), ENT_QUOTES, 'UTF-8') ?>
        </a>
    </p>

    <?php else : ?>

    <?php
    $errorMsg = match ($error ?? null) {
        'mismatch'  => $i18n->t('auth.reset_mismatch'),
        'too_short' => $i18n->t('auth.reset_too_short'),
        default     => null,
    };
    ?>

    <?php if ($errorMsg !== null) : ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <form method="post" action="/reset-password/" novalidate>
        <?= Csrf::field() ?>
        <input type="hidden" name="token"
               value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="mb-3">
            <label for="reset-password" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.reset_password'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="password"
                   id="reset-password"
                   name="password"
                   class="form-control"
                   autocomplete="new-password"
                   required
                   minlength="12"
                   aria-describedby="reset-pw-hint">
            <div id="reset-pw-hint" class="form-text">
                <?= htmlspecialchars($i18n->t('auth.reset_too_short'), ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <div class="mb-4">
            <label for="reset-confirm" class="form-label">
                <?= htmlspecialchars($i18n->t('auth.reset_confirm'), ENT_QUOTES, 'UTF-8') ?>
            </label>
            <input type="password"
                   id="reset-confirm"
                   name="confirm"
                   class="form-control"
                   autocomplete="new-password"
                   required
                   minlength="12">
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <?= htmlspecialchars($i18n->t('auth.reset_btn'), ENT_QUOTES, 'UTF-8') ?>
        </button>
    </form>

    <?php endif; ?>
</main>
