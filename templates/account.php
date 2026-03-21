<?php

/**
 * Account page template.
 *
 * Expected variables:
 *   $i18n     I18n
 *   $user     array<string, mixed>   — current user row
 *   $success  string|null            — 'password' | 'profile' | 'lang'
 *   $error    string|null            — 'current_pw' | 'mismatch' | 'too_short'
 *   $section  string|null            — which form was submitted: 'password' | 'profile' | 'lang'
 */

declare(strict_types=1);

use PluginInsight\Csrf;
use PluginInsight\I18n;

$successMsg = match ($success ?? null) {
    'password' => $i18n->t('auth.account_ok_password'),
    'profile'  => $i18n->t('auth.account_ok_profile'),
    'lang'     => $i18n->t('auth.account_ok_lang'),
    default    => null,
};

$errorMsg = match ($error ?? null) {
    'current_pw' => $i18n->t('auth.account_err_current_pw'),
    'mismatch'   => $i18n->t('auth.account_err_mismatch'),
    'too_short'  => $i18n->t('auth.account_err_too_short'),
    default      => null,
};

$languages = [
    'be'  => 'be — Беларуская',
    'bg'  => 'bg — Български',
    'ca'  => 'ca — Català',
    'cs'  => 'cs — Čeština',
    'da'  => 'da — Dansk',
    'de'  => 'de — Deutsch',
    'el'  => 'el — Ελληνικά',
    'en'  => 'en — English',
    'es'  => 'es — Español',
    'et'  => 'et — Eesti',
    'eu'  => 'eu — Euskara',
    'fi'  => 'fi — Suomi',
    'fr'  => 'fr — Français',
    'ga'  => 'ga — Gaeilge',
    'gl'  => 'gl — Galego',
    'hr'  => 'hr — Hrvatski',
    'hu'  => 'hu — Magyar',
    'it'  => 'it — Italiano',
    'lb'  => 'lb — Lëtzebuergesch',
    'lld' => 'lld — Ladin',
    'lt'  => 'lt — Lietuvių',
    'lv'  => 'lv — Latviešu',
    'mt'  => 'mt — Malti',
    'nl'  => 'nl — Nederlands',
    'pl'  => 'pl — Polski',
    'pt'  => 'pt — Português',
    'ro'  => 'ro — Română',
    'ru'  => 'ru — Русский',
    'se'  => 'se — Davvisámegiella',
    'sk'  => 'sk — Slovenčina',
    'sl'  => 'sl — Slovenščina',
    'sr'  => 'sr — Српски',
    'sv'  => 'sv — Svenska',
    'tr'  => 'tr — Türkçe',
    'uk'  => 'uk — Українська',
];

$currentLang = (string) ($user['preferred_lang'] ?? '');
?>

<main class="container py-5" style="max-width:640px">
    <h1 class="fw-bold mb-1"><?= htmlspecialchars($i18n->t('auth.account_heading'), ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="text-body-secondary mb-4">
        <?= htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
    </p>

    <?php if ($successMsg !== null) : ?>
    <div class="alert alert-success" role="status" aria-live="polite">
        <?= htmlspecialchars($successMsg, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- ── Change password ──────────────────────────────────── -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">
            <?= htmlspecialchars($i18n->t('auth.account_pw_section'), ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="card-body">
            <?php if (($error ?? null) !== null && ($section ?? '') === 'password') : ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars((string) $errorMsg, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <form method="post" action="/account/" novalidate>
                <?= Csrf::field() ?>
                <input type="hidden" name="action" value="password">

                <div class="mb-3">
                    <label for="acc-pw-current" class="form-label">
                        <?= htmlspecialchars($i18n->t('auth.account_pw_current'), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input type="password"
                           id="acc-pw-current"
                           name="current_password"
                           class="form-control"
                           autocomplete="current-password"
                           required>
                </div>

                <div class="mb-3">
                    <label for="acc-pw-new" class="form-label">
                        <?= htmlspecialchars($i18n->t('auth.account_pw_new'), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input type="password"
                           id="acc-pw-new"
                           name="new_password"
                           class="form-control"
                           autocomplete="new-password"
                           required
                           minlength="12">
                </div>

                <div class="mb-4">
                    <label for="acc-pw-confirm" class="form-label">
                        <?= htmlspecialchars($i18n->t('auth.account_pw_confirm'), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input type="password"
                           id="acc-pw-confirm"
                           name="confirm_password"
                           class="form-control"
                           autocomplete="new-password"
                           required
                           minlength="12">
                </div>

                <button type="submit" class="btn btn-primary">
                    <?= htmlspecialchars($i18n->t('auth.account_pw_btn'), ENT_QUOTES, 'UTF-8') ?>
                </button>
            </form>
        </div>
    </div>

    <!-- ── Profile ──────────────────────────────────────────── -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">
            <?= htmlspecialchars($i18n->t('auth.account_profile_section'), ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="card-body">
            <form method="post" action="/account/" novalidate>
                <?= Csrf::field() ?>
                <input type="hidden" name="action" value="profile">

                <div class="mb-4">
                    <label for="acc-name" class="form-label">
                        <?= htmlspecialchars($i18n->t('auth.account_name'), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <input type="text"
                           id="acc-name"
                           name="display_name"
                           class="form-control"
                           autocomplete="name"
                           maxlength="100"
                           value="<?= htmlspecialchars((string) ($user['display_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                           aria-describedby="acc-name-hint">
                    <div id="acc-name-hint" class="form-text">
                        <?= htmlspecialchars($i18n->t('auth.account_name_hint'), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?= htmlspecialchars($i18n->t('auth.account_profile_btn'), ENT_QUOTES, 'UTF-8') ?>
                </button>
            </form>
        </div>
    </div>

    <!-- ── Language preference ──────────────────────────────── -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">
            <?= htmlspecialchars($i18n->t('auth.account_lang_section'), ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="card-body">
            <form method="post" action="/account/" novalidate>
                <?= Csrf::field() ?>
                <input type="hidden" name="action" value="lang">

                <div class="mb-4">
                    <label for="acc-lang" class="form-label">
                        <?= htmlspecialchars($i18n->t('auth.account_lang'), ENT_QUOTES, 'UTF-8') ?>
                    </label>
                    <select id="acc-lang"
                            name="lang"
                            class="form-select">
                        <option value=""<?= $currentLang === '' ? ' selected' : '' ?>>
                            <?= htmlspecialchars($i18n->t('auth.account_lang_auto'), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php foreach ($languages as $code => $label) : ?>
                        <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $currentLang === $code ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?= htmlspecialchars($i18n->t('auth.account_lang_btn'), ENT_QUOTES, 'UTF-8') ?>
                </button>
            </form>
        </div>
    </div>
</main>
