<!DOCTYPE html>
<html lang="<?= htmlspecialchars($i18n->locale(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <?php if (!empty($pageMetaDesc)) : ?>
    <meta name="description" content="<?= htmlspecialchars($pageMetaDesc, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>

<!-- ── Skip navigation ──────────────────────────────────── -->
<a class="skip-link visually-hidden-focusable" href="#main-content">
    Skip to main content
</a>

<!-- ── Navbar ───────────────────────────────────────────── -->
<nav class="navbar navbar-expand-md border-bottom" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/" aria-label="PluginInsight — home">
            <i class="bi bi-plugin me-1" aria-hidden="true"></i>PluginInsight
        </a>
        <div class="d-flex align-items-center gap-2 ms-auto">
            <a href="/about/"
               class="nav-link px-2<?= $activePage === 'about' ? ' active" aria-current="page' : '' ?>"
            ><?= htmlspecialchars($i18n->t('nav.about'), ENT_QUOTES, 'UTF-8') ?></a>

            <?php if (!empty($currentUser)) : ?>
            <a href="/account/"
               class="nav-link px-2<?= $activePage === 'account' ? ' active" aria-current="page' : '' ?>">
                <?= htmlspecialchars($i18n->t('nav.account'), ENT_QUOTES, 'UTF-8') ?>
            </a>
            <form method="post" action="/logout/" class="d-inline m-0">
                <?= \PluginInsight\Csrf::field() ?>
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <?= htmlspecialchars($i18n->t('nav.logout'), ENT_QUOTES, 'UTF-8') ?>
                </button>
            </form>
            <?php else : ?>
            <a href="/login/"
               class="nav-link px-2<?= $activePage === 'login' ? ' active" aria-current="page' : '' ?>">
                <?= htmlspecialchars($i18n->t('auth.login_heading'), ENT_QUOTES, 'UTF-8') ?>
            </a>
            <?php endif; ?>

            <!-- Language switcher -->
            <?php
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
            $currentLang = htmlspecialchars($i18n->locale(), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                        type="button"
                        id="lang-switcher-btn"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-haspopup="true"
                        aria-label="Language: <?= $currentLang ?>">
                    <?= $currentLang ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-0"
                    role="menu"
                    aria-labelledby="lang-switcher-btn">
                    <li role="none">
                        <div class="lang-scroll">
                            <?php foreach ($languages as $code => $label) : ?>
                            <a class="dropdown-item<?= $i18n->locale() === $code ? ' active' : '' ?>"
                               role="menuitem"
                               href="?lang=<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                               <?= $i18n->locale() === $code ? 'aria-current="true"' : '' ?>>
                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Theme toggle -->
            <button id="theme-toggle"
                    class="btn btn-sm btn-outline-secondary"
                    type="button"
                    data-label-light="<?= htmlspecialchars($i18n->t('theme.to_light'), ENT_QUOTES, 'UTF-8') ?>"
                    data-label-dark="<?= htmlspecialchars($i18n->t('theme.to_dark'), ENT_QUOTES, 'UTF-8') ?>"
                    aria-label="<?= htmlspecialchars($i18n->t('theme.to_dark'), ENT_QUOTES, 'UTF-8') ?>">
                <i class="bi bi-sun-fill theme-icon-light" aria-hidden="true"></i>
                <i class="bi bi-moon-fill theme-icon-dark d-none" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</nav>

<!-- ── Page content ─────────────────────────────────────── -->
<div id="main-content" tabindex="-1">
    <?= $pageContent ?>
</div>

<!-- ── Footer ────────────────────────────────────────────── -->
<footer class="border-top py-4 text-center text-body-secondary small">
    <div class="container">
        <?= htmlspecialchars($i18n->t('footer.tagline'), ENT_QUOTES, 'UTF-8') ?><br>
        <?= htmlspecialchars($i18n->t('footer.disclaimer'), ENT_QUOTES, 'UTF-8') ?>
    </div>
</footer>

<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
