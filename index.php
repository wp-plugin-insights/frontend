<?php

declare(strict_types=1);

require_once __DIR__ . '/src/I18n.php';
require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/src/PluginRepository.php';

use PluginInsight\I18n;
use PluginInsight\Router;
use PluginInsight\PluginRepository;

// ── Locale detection ──────────────────────────────────────────────────────────

/**
 * Detects the preferred locale from (in order):
 *   1. ?lang= query parameter  (also sets a cookie for subsequent requests)
 *   2. pi_lang cookie
 *   3. Accept-Language HTTP header
 *   4. Default: 'en'
 */
function detectLocale(): string
{
    $supported = I18n::SUPPORTED;

    // 1. Explicit query parameter
    if (
        isset($_GET['lang'])
        && is_string($_GET['lang'])
        && in_array($_GET['lang'], $supported, true)
    ) {
        setcookie('pi_lang', $_GET['lang'], [
            'expires'  => time() + 365 * 24 * 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        return $_GET['lang'];
    }

    // 2. Cookie
    if (
        isset($_COOKIE['pi_lang'])
        && is_string($_COOKIE['pi_lang'])
        && in_array($_COOKIE['pi_lang'], $supported, true)
    ) {
        return $_COOKIE['pi_lang'];
    }

    // 3. Accept-Language header (match first two characters of each tag)
    $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    foreach (explode(',', $header) as $part) {
        $lang = strtolower(substr(trim(strtok($part, ';')), 0, 2));
        if (in_array($lang, $supported, true)) {
            return $lang;
        }
    }

    return 'en';
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────

$locale = detectLocale();
$i18n   = new I18n($locale);
$router = new Router();

$uri   = $_SERVER['REQUEST_URI'] ?? '/';
$route = $router->resolve($uri);

$page       = $route['page'];
$params     = $route['params'];
$activePage = $page;

// ── Database connection ───────────────────────────────────────────────────────

/** @var PluginRepository|null $repo */
$repo = null;

try {
    require_once __DIR__ . '/../dbcon.php';
    /** @var \mysqli $db */
    $repo = new PluginRepository($db);
} catch (\RuntimeException $e) {
    // Non-fatal: pages that don't need DB will still render.
    // Pages that require DB will show an empty state.
    error_log('[plugininsight] DB connection failed: ' . $e->getMessage());
}

// ── Route dispatch ────────────────────────────────────────────────────────────

$pageTitle   = 'WP Plugin Insights';
$pageMetaDesc = '';
$pageContent = '';

ob_start();

switch ($page) {
    case 'home':
        $pageTitle    = $i18n->t('home.page_title');
        $pageMetaDesc = $i18n->t('home.meta_desc');
        $plugins      = $repo !== null ? $repo->getRecent(12) : [];
        $total        = $repo !== null ? $repo->getTotalCount() : 0;
        require __DIR__ . '/templates/home.php';
        break;

    case 'plugin':
        $slug = $params['slug'];

        if ($repo !== null) {
            $plugin = $repo->findBySlug($slug);
        } else {
            $plugin = null;
        }

        if ($plugin === null) {
            http_response_code(404);
            $pageTitle = $i18n->t('plugin.not_found_title');

            // Inline not-found view (avoids a separate template for a one-liner case)
            echo '<main class="container py-5 text-center" style="max-width:560px">';
            echo '<h1 class="display-6 fw-bold mb-3">' . $i18n->t('plugin.not_found_heading') . '</h1>';
            echo '<p class="text-body-secondary mb-4">'
                . $i18n->t('plugin.not_found_desc', ['slug' => htmlspecialchars($slug, ENT_QUOTES, 'UTF-8')])
                . '</p>';
            echo '<a href="/" class="btn btn-primary">'
                . '<i class="bi bi-house me-1"></i>'
                . htmlspecialchars($i18n->t('plugin.not_found_back'), ENT_QUOTES, 'UTF-8')
                . '</a>';
            echo '</main>';
            break;
        }

        $pageTitle    = $i18n->t('plugin.page_title', ['name' => $plugin['plugin_name'] ?? $slug]);
        $pageMetaDesc = $i18n->t('plugin.meta_desc', ['name' => $plugin['plugin_name'] ?? $slug]);
        require __DIR__ . '/templates/plugin.php';
        break;

    case 'about':
        $pageTitle    = $i18n->t('about.page_title');
        $activePage   = 'about';
        require __DIR__ . '/templates/about.php';
        break;

    default:
        http_response_code(404);
        $pageTitle = $i18n->t('404.page_title');
        require __DIR__ . '/templates/404.php';
        break;
}

$pageContent = ob_get_clean();

// ── Render layout ─────────────────────────────────────────────────────────────

require __DIR__ . '/templates/layout.php';
