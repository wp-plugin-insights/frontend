<?php

/**
 * PluginInsight — front controller.
 *
 * Entry point for all requests (Apache RewriteRule → index.php).
 * Handles session, routing, locale detection, DB, and template rendering.
 */

declare(strict_types=1);

// ── Bootstrap — session must start before any output ─────────────────────────

require_once __DIR__ . '/src/I18n.php';
require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/src/PluginRepository.php';
require_once __DIR__ . '/src/UserRepository.php';
require_once __DIR__ . '/src/Auth.php';
require_once __DIR__ . '/src/PasswordReset.php';
require_once __DIR__ . '/src/Csrf.php';
require_once __DIR__ . '/../secrets.php';
require_once __DIR__ . '/../crons/src/UploadRepository.php';
require_once __DIR__ . '/src/SiteSettingRepository.php';
require_once __DIR__ . '/src/RunnerRepository.php';
require_once __DIR__ . '/src/RabbitMqInfo.php';
require_once __DIR__ . '/src/PluginResultRepository.php';
require_once __DIR__ . '/src/CronRunRepository.php';
require_once __DIR__ . '/src/WpCompatRepository.php';
require_once __DIR__ . '/src/GradeCalculator.php';

use PluginInsight\Auth;
use PluginInsight\Csrf;
use PluginInsight\I18n;
use PluginInsight\PasswordReset;
use PluginInsight\PluginRepository;
use PluginInsight\PluginResultRepository;
use PluginInsight\RabbitMqInfo;
use PluginInsight\Router;
use PluginInsight\RunnerRepository;
use PluginInsight\SiteSettingRepository;
use PluginInsight\CronRunRepository;
use PluginInsight\UploadRepository;
use PluginInsight\UserRepository;
use PluginInsight\WpCompatRepository;

Auth::startSession();

// ── Database connection ───────────────────────────────────────────────────────

/** @var PluginRepository|null $repo */
$repo = null;
/** @var UserRepository|null $userRepo */
$userRepo = null;
/** @var Auth|null $auth */
$auth = null;
/** @var PasswordReset|null $passwordReset */
$passwordReset = null;
/** @var UploadRepository|null $uploadRepo */
$uploadRepo = null;
/** @var SiteSettingRepository|null $settingRepo */
$settingRepo = null;
/** @var RunnerRepository|null $runnerRepo */
$runnerRepo = null;
/** @var PluginResultRepository|null $resultRepo */
$resultRepo = null;
/** @var CronRunRepository|null $cronRunRepo */
$cronRunRepo = null;
/** @var WpCompatRepository|null $wpCompatRepo */
$wpCompatRepo = null;

try {
    require_once __DIR__ . '/../dbcon.php';
    $db            = db_connect();
    $userRepo      = new UserRepository($db);
    $repo          = new PluginRepository($db);
    $auth          = new Auth($userRepo);
    $passwordReset = new PasswordReset($userRepo);
    $uploadRepo    = new UploadRepository($db);
    $settingRepo   = new SiteSettingRepository($db);
    $runnerRepo    = new RunnerRepository($db);
    $resultRepo    = new PluginResultRepository($db);
    $cronRunRepo   = new CronRunRepository($db);
    $wpCompatRepo  = new WpCompatRepository($db);
} catch (\RuntimeException $e) {
    error_log('[plugininsight] DB connection failed: ' . $e->getMessage());
}

// ── Locale detection ──────────────────────────────────────────────────────────

/**
 * Priority:
 *   1. ?lang= query parameter  (explicit override; saves cookie)
 *   2. Logged-in user's preferred_lang  (from DB via session)
 *   3. pi_lang cookie
 *   4. Accept-Language header
 *   5. 'en' fallback
 *
 * @param array<string, mixed>|null $user
 */
function detectLocale(?array $user): string
{
    $supported = I18n::SUPPORTED;

    // 1. Explicit ?lang= — also persists to cookie
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

    // 2. Logged-in user preference
    if (
        $user !== null
        && isset($user['preferred_lang'])
        && is_string($user['preferred_lang'])
        && in_array($user['preferred_lang'], $supported, true)
    ) {
        return $user['preferred_lang'];
    }

    // 3. Cookie
    if (
        isset($_COOKIE['pi_lang'])
        && is_string($_COOKIE['pi_lang'])
        && in_array($_COOKIE['pi_lang'], $supported, true)
    ) {
        return $_COOKIE['pi_lang'];
    }

    // 4. Accept-Language header (match first tag segment)
    $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    foreach (explode(',', $header) as $part) {
        $lang = strtolower(substr(trim(strtok($part, ';')), 0, 2));
        if (in_array($lang, $supported, true)) {
            return $lang;
        }
    }

    return 'en';
}

$currentUser = $auth?->currentUser();
$locale      = detectLocale($currentUser);
$i18n        = new I18n($locale);

// ── Routing ───────────────────────────────────────────────────────────────────

$router = new Router();
$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$route  = $router->resolve($uri);
$page   = $route['page'];
$params = $route['params'];

$activePage   = $page;
$pageTitle    = 'WP Plugin Insights';
$pageMetaDesc = '';
$pageContent  = '';

// ── Helper: Post-Redirect-Get ─────────────────────────────────────────────────

/**
 * Redirects to $url and stops execution.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

// ── Helper: require authentication ───────────────────────────────────────────

/**
 * Redirects to /login/ if the user is not authenticated.
 */
function requireAuth(?Auth $auth): void
{
    if ($auth === null || !$auth->isLoggedIn()) {
        redirect('/login/');
    }
}

// ── Helper: HTML escaping ─────────────────────────────────────────────────────

/**
 * Shorthand for htmlspecialchars with safe defaults.
 */
function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// ── WordPress version data ────────────────────────────────────────────────────
// Loaded once and passed to any template that needs to assess plugin compatibility.
// Populated hourly by crons/fetch-wp-versions.php via site_setting 'wp_versions'.

/** @var list<array{version: string, php_min: string, mysql_min: string}> $wpVersions */
$wpVersions = [];
if ($settingRepo !== null) {
    $raw = $settingRepo->get('wp_versions');
    if ($raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $wpVersions = array_values($decoded);
        }
    }
}

// ── Dispatch ──────────────────────────────────────────────────────────────────

ob_start();

switch ($page) {
    // ── Public: home ─────────────────────────────────────────────────────────
    case 'home':
        $pageTitle    = $i18n->t('home.page_title');
        $pageMetaDesc = $i18n->t('home.meta_desc');
        $plugins      = $repo !== null ? $repo->getRecentAnalysed(12) : [];
        $total        = $repo !== null ? $repo->getTotalCount() : 0;

        // Compute the real overall grade for each home card using GradeCalculator
        // so it matches the grade shown on the plugin detail page.
        if (!empty($plugins) && $resultRepo !== null) {
            $homePluginIds  = array_map('intval', array_column($plugins, 'plugin_id'));
            $homeBatch      = $resultRepo->getRunnerResultsByPluginIds($homePluginIds);
            $homeCompatRows = $wpCompatRepo !== null ? $wpCompatRepo->getAll() : [];
            $homeLatestWpMinor = '';
            if (!empty($wpVersions)) {
                $homeLatestWpMinor = implode(
                    '.',
                    array_slice(explode('.', (string) $wpVersions[0]['version']), 0, 2)
                );
            }

            foreach ($plugins as &$_p) {
                $pId          = (int) ($_p['plugin_id'] ?? 0);
                $pCompatGrade = \PluginInsight\GradeCalculator::compatGrade(
                    (string) ($_p['plugin_requires']     ?? ''),
                    (string) ($_p['plugin_requires_php'] ?? ''),
                    (string) ($_p['plugin_tested']       ?? ''),
                    (string) ($_p['plugin_last_updated'] ?? ''),
                    $homeCompatRows,
                    $homeLatestWpMinor
                );
                $pGradeResult         = \PluginInsight\GradeCalculator::calculate(
                    $pCompatGrade,
                    $homeBatch[$pId] ?? []
                );
                $_p['_computed_grade'] = $pGradeResult['grade'];
            }
            unset($_p);
        }

        require __DIR__ . '/templates/home.php';
        break;

    // ── Public: plugin detail ────────────────────────────────────────────────
    case 'plugin':
        $slug   = $params['slug'];
        $plugin = $repo?->findBySlug($slug);

        if ($plugin === null) {
            http_response_code(404);
            $pageTitle = $i18n->t('plugin.not_found_title');
            echo '<main class="container py-5 text-center" style="max-width:560px">';
            echo '<h1 class="display-6 fw-bold mb-3">'
                . htmlspecialchars($i18n->t('plugin.not_found_heading'), ENT_QUOTES, 'UTF-8') . '</h1>';
            echo '<p class="text-body-secondary mb-4">'
                . $i18n->t('plugin.not_found_desc', ['slug' => htmlspecialchars($slug, ENT_QUOTES, 'UTF-8')])
                . '</p>';
            echo '<a href="/" class="btn btn-primary">'
                . '<i class="bi bi-house me-1" aria-hidden="true"></i>'
                . htmlspecialchars($i18n->t('plugin.not_found_back'), ENT_QUOTES, 'UTF-8')
                . '</a></main>';
            break;
        }

        $pageTitle    = $i18n->t('plugin.page_title', ['name' => $plugin['plugin_name'] ?? $slug]);
        $pageMetaDesc = $i18n->t('plugin.meta_desc', ['name' => $plugin['plugin_name'] ?? $slug]);

        $pluginId = isset($plugin['plugin_id']) ? (int) $plugin['plugin_id'] : 0;

        // Load all distinct versions that have analysis results
        $analysedVersions = [];
        if ($resultRepo !== null && $pluginId > 0) {
            $analysedVersions = $resultRepo->getAnalysedVersions($pluginId);
        }

        // Honour ?version= if that version has results; otherwise default to the newest analysed
        $requestedVersion = trim((string) ($_GET['version'] ?? ''));
        $selectedVersion  = in_array($requestedVersion, $analysedVersions, true)
            ? $requestedVersion
            : ($analysedVersions[0] ?? (string) ($plugin['plugin_version'] ?? ''));

        $analysisResults = [];
        if ($resultRepo !== null && $pluginId > 0 && $selectedVersion !== '') {
            $analysisResults = $resultRepo->getByPluginVersion($pluginId, $selectedVersion);
        }

        // WordPress–PHP compatibility check for the Compatibility & Requirements card.
        // $pluginRequiresWp and $pluginRequiresPhp mirror the plugin table fields so the
        // template can compute the check without re-reading the repo.
        $pluginRequiresWp  = (string) ($plugin['plugin_requires']     ?? '');
        $pluginRequiresPhp = (string) ($plugin['plugin_requires_php'] ?? '');
        $wpMinPhpRequired  = null;
        if ($wpCompatRepo !== null && $pluginRequiresWp !== '') {
            $wpMinPhpRequired = $wpCompatRepo->getPhpRequirementForWp($pluginRequiresWp);
        }

        require __DIR__ . '/templates/plugin.php';
        break;

    // ── Public: about ────────────────────────────────────────────────────────
    case 'about':
        $pageTitle  = $i18n->t('about.page_title');
        $activePage = 'about';
        require __DIR__ . '/templates/about.php';
        break;

    // ── Auth: login ──────────────────────────────────────────────────────────
    case 'login':
        // Already logged in → home
        if ($auth !== null && $auth->isLoggedIn()) {
            redirect('/');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate((string) ($_POST['_csrf'] ?? ''))) {
                redirect('/login/');
            }

            $email    = trim((string) ($_POST['email']    ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $clientIp = Auth::clientIp();

            if ($auth === null || $userRepo === null) {
                $error = 'invalid';
            } elseif (!$auth->login($email, $password, $clientIp)) {
                $error = $userRepo->countRecentAttemptsByIp($clientIp) >= 5 ? 'locked' : 'invalid';
            } else {
                redirect('/');
            }
        }

        $pageTitle = $i18n->t('auth.login_title');
        require __DIR__ . '/templates/login.php';
        break;

    // ── Auth: logout (POST only) ─────────────────────────────────────────────
    case 'logout':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/');
        }

        if (!Csrf::validate((string) ($_POST['_csrf'] ?? ''))) {
            redirect('/');
        }

        $auth?->logout();
        redirect('/');

    // ── Auth: forgot password ────────────────────────────────────────────────
    case 'forgot-password':
        $sent = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate((string) ($_POST['_csrf'] ?? ''))) {
                redirect('/forgot-password/');
            }

            // Rate-limit by IP (reuse login_attempt window)
            $clientIp = Auth::clientIp();
            if ($userRepo !== null && $userRepo->countRecentAttemptsByIp($clientIp) >= 10) {
                $sent = true; // Show same confirmation; don't reveal rate limit
            } else {
                $email = trim((string) ($_POST['email'] ?? ''));
                // Always show the same confirmation to prevent enumeration
                $passwordReset?->request($email);
                $sent = true;
            }
        }

        $pageTitle = $i18n->t('auth.forgot_title');
        require __DIR__ . '/templates/forgot-password.php';
        break;

    // ── Auth: reset password ─────────────────────────────────────────────────
    case 'reset-password':
        $token    = trim((string) ($_GET['token'] ?? ($_POST['token'] ?? '')));
        $resetRow = $passwordReset?->validate($token);
        $error    = null;
        $success  = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate((string) ($_POST['_csrf'] ?? ''))) {
                redirect('/reset-password/?token=' . rawurlencode($token));
            }

            $password = (string) ($_POST['password'] ?? '');
            $confirm  = (string) ($_POST['confirm']  ?? '');

            if ($resetRow === null) {
                $error = 'invalid';
            } elseif (strlen($password) < 12) {
                $error = 'too_short';
            } elseif ($password !== $confirm) {
                $error = 'mismatch';
            } elseif ($passwordReset !== null) {
                $passwordReset->complete(
                    (int) $resetRow['reset_id'],
                    (int) $resetRow['user_id'],
                    $password
                );
                $success = true;
            }
        }

        $pageTitle = $i18n->t('auth.reset_title');
        require __DIR__ . '/templates/reset-password.php';
        break;

    // ── Auth: account (requires login) ───────────────────────────────────────
    case 'account':
        if ($auth === null || !$auth->isLoggedIn()) {
            redirect('/login/');
        }

        $user    = $auth->currentUser() ?? [];
        $success = null;
        $error   = null;
        $section = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate((string) ($_POST['_csrf'] ?? ''))) {
                redirect('/account/');
            }

            $action  = (string) ($_POST['action'] ?? '');
            $section = $action;

            if ($action === 'password' && $userRepo !== null) {
                $currentPw  = (string) ($_POST['current_password']  ?? '');
                $newPw      = (string) ($_POST['new_password']      ?? '');
                $confirmPw  = (string) ($_POST['confirm_password']  ?? '');

                if (!password_verify($currentPw, (string) ($user['password_hash'] ?? ''))) {
                    $error = 'current_pw';
                } elseif (strlen($newPw) < 12) {
                    $error = 'too_short';
                } elseif ($newPw !== $confirmPw) {
                    $error = 'mismatch';
                } else {
                    $userRepo->updatePassword((int) $user['user_id'], password_hash($newPw, PASSWORD_BCRYPT));
                    redirect('/account/?success=password');
                }
            } elseif ($action === 'profile' && $userRepo !== null) {
                $name = trim((string) ($_POST['display_name'] ?? ''));
                // Enforce max length and strip control characters server-side
                $name = substr(preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? '', 0, 100);
                $userRepo->updateName((int) $user['user_id'], $name !== '' ? $name : null);
                redirect('/account/?success=profile');
            } elseif ($action === 'lang' && $userRepo !== null) {
                $lang = trim((string) ($_POST['lang'] ?? ''));
                $userRepo->updateLang((int) $user['user_id'], $lang !== '' ? $lang : null);
                // Also update cookie so the UI reflects the change immediately
                if ($lang !== '' && in_array($lang, I18n::SUPPORTED, true)) {
                    setcookie('pi_lang', $lang, [
                        'expires'  => time() + 365 * 24 * 3600,
                        'path'     => '/',
                        'httponly' => true,
                        'samesite' => 'Lax',
                    ]);
                }
                redirect('/account/?success=lang');
            }

            // Re-fetch user after any update
            $user = $auth->currentUser() ?? $user;
        }

        // Read ?success= from GET after redirect
        if (isset($_GET['success'])) {
            $success = match ($_GET['success']) {
                'password', 'profile', 'lang' => $_GET['success'],
                default                        => null,
            };
        }

        $pageTitle  = $i18n->t('auth.account_title');
        $activePage = 'account';
        require __DIR__ . '/templates/account.php';
        break;

    // ── Public: uploaded plugin result ──────────────────────────────────────
    case 'plugin-upload':
        $uuid   = $params['uuid'];
        $upload = $uploadRepo?->findByUuid($uuid);

        if ($upload === null) {
            http_response_code(404);
            $pageTitle = $i18n->t('404.page_title');
            require __DIR__ . '/templates/404.php';
            break;
        }

        $uploadName   = (string) ($upload['plugin_name'] ?? $upload['plugin_slug'] ?? $uuid);
        $pageTitle    = $uploadName . ' — WP Plugin Insights';
        $pageMetaDesc = '';

        // Load analysis results for this upload (available as soon as any runner finishes).
        $analysisResults = [];
        $uploadPluginId  = isset($upload['plugin_id']) ? (int) $upload['plugin_id'] : 0;
        $uploadVersion   = (string) ($upload['plugin_version'] ?? '');

        if ($uploadPluginId > 0 && $uploadVersion !== '' && $resultRepo !== null) {
            $analysisResults = $resultRepo->getByPluginVersion($uploadPluginId, $uploadVersion);
        }

        require __DIR__ . '/templates/plugin-upload.php';
        break;

    // ── Admin panel ─────────────────────────────────────────────────────────
    case 'admin':
        if ($auth === null || !$auth->isLoggedIn()) {
            redirect('/login/');
        }

        if (!$auth->isAdmin()) {
            http_response_code(403);
            $pageTitle  = 'Access Denied — WP Plugin Insights';
            $activePage = '';
            ?>
            <main class="container py-5 text-center" style="max-width:480px">
                <h1 class="h3 fw-bold mb-3">
                    <i class="bi bi-shield-x me-2" aria-hidden="true"></i>Access Denied
                </h1>
                <p class="text-body-secondary">You do not have permission to access this page.</p>
                <a href="/" class="btn btn-primary">Back to home</a>
            </main>
            <?php
            break;
        }

        $adminSuccess = isset($_GET['success']) ? (string) $_GET['success'] : null;
        $adminError   = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate((string) ($_POST['_csrf'] ?? ''))) {
                redirect('/admin/');
            }

            $action = (string) ($_POST['action'] ?? '');

            // ── API Settings ─────────────────────────────────────────────
            if ($action === 'api_settings' && $settingRepo !== null) {
                $apiActive   = isset($_POST['api_active']) ? '1' : '0';
                $apiHostname = trim((string) ($_POST['api_hostname'] ?? ''));
                $apiHostname = preg_replace('/[^a-zA-Z0-9._\-]/', '', $apiHostname) ?? '';
                if ($apiHostname !== '') {
                    $settingRepo->set('api_hostname', $apiHostname);
                }
                $settingRepo->set('api_active', $apiActive);
                redirect('/admin/?success=api_settings&tab=settings');
            }

            // ── Runner: reorder ──────────────────────────────────────────
            if ($action === 'runner_reorder' && $runnerRepo !== null) {
                $rawOrders = $_POST['runner_order'] ?? [];
                if (is_array($rawOrders)) {
                    $orders = [];
                    foreach ($rawOrders as $rid => $ord) {
                        $rid = (int) $rid;
                        $ord = max(0, min(999, (int) $ord));
                        if ($rid > 0) {
                            $orders[$rid] = $ord;
                        }
                    }
                    $runnerRepo->setOrders($orders);
                }
                redirect('/admin/?success=runner_reorder&tab=pipeline');
            }

            // ── Runner: toggle active ────────────────────────────────────
            if ($action === 'runner_toggle' && $runnerRepo !== null) {
                $runnerId = (int) ($_POST['runner_id'] ?? 0);
                $active   = (int) ($_POST['runner_is_active'] ?? 0) === 1;
                if ($runnerId > 0) {
                    $runnerRepo->setActive($runnerId, $active);
                }
                redirect('/admin/?success=runner_toggle&tab=pipeline');
            }

            // ── Runner: add ──────────────────────────────────────────────
            if ($action === 'runner_add' && $runnerRepo !== null) {
                $rName  = trim((string) ($_POST['runner_name']  ?? ''));
                $rSlug  = trim((string) ($_POST['runner_slug']  ?? ''));
                $rQueue = trim((string) ($_POST['runner_queue'] ?? ''));
                $rSlug  = preg_replace('/[^a-z0-9_\-]/', '', strtolower($rSlug)) ?? '';

                if ($rName !== '' && $rSlug !== '' && $rQueue !== '') {
                    try {
                        $runnerRepo->create($rName, $rSlug, $rQueue);
                        redirect('/admin/?success=runner_add&tab=pipeline');
                    } catch (\RuntimeException) {
                        $adminError = 'A runner with that slug already exists.';
                    }
                } else {
                    $adminError = 'Name, slug, and queue are all required.';
                }
            }

            // ── Runner: delete ───────────────────────────────────────────
            if ($action === 'runner_delete' && $runnerRepo !== null) {
                $runnerId = (int) ($_POST['runner_id'] ?? 0);
                if ($runnerId > 0) {
                    $runnerRepo->delete($runnerId);
                }
                redirect('/admin/?success=runner_delete&tab=pipeline');
            }

            // ── Runner: restart systemd service ──────────────────────────
            if ($action === 'runner_restart' && $runnerRepo !== null) {
                $runnerId = (int) ($_POST['runner_id'] ?? 0);
                if ($runnerId > 0) {
                    $runners_all = $runnerRepo->findAll();
                    $rSlug = '';
                    foreach ($runners_all as $r) {
                        if ((int) $r['runner_id'] === $runnerId) {
                            $rSlug = (string) $r['runner_slug'];
                            break;
                        }
                    }
                    // Validate slug: only lowercase letters, digits, hyphens
                    if ($rSlug !== '' && preg_match('/^[a-z0-9\-]+$/', $rSlug)) {
                        $service = 'plugin-insights@runner-' . $rSlug . '.service';
                        exec('sudo /usr/bin/systemctl restart ' . escapeshellarg($service) . ' 2>&1', $out, $exitCode);
                        if ($exitCode !== 0) {
                            $adminError = 'Restart failed: ' . implode(' ', $out);
                        } else {
                            redirect('/admin/?success=runner_restart&tab=pipeline');
                        }
                    }
                }
                if ($adminError === null) {
                    redirect('/admin/?success=runner_restart&tab=pipeline');
                }
            }

            // ── Upload: requeue ──────────────────────────────────────────
            if ($action === 'upload_requeue' && $uploadRepo !== null) {
                $upUuid = trim((string) ($_POST['upload_uuid'] ?? ''));
                if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $upUuid)) {
                    $uploadRepo->requeueByUuid($upUuid);
                }
                redirect('/admin/?success=upload_requeue&tab=plugins');
            }

            // ── Queue: purge ─────────────────────────────────────────────
            if ($action === 'queue_purge') {
                $queueName = trim((string) ($_POST['queue_name'] ?? ''));
                if ($queueName !== '' && preg_match('/^[a-zA-Z0-9._\-]+$/', $queueName)) {
                    $rabbitCfg = __DIR__ . '/../crons/rabbitmq.php';
                    if (file_exists($rabbitCfg)) {
                        require_once $rabbitCfg;
                        $rabbitPurger = new RabbitMqInfo(RABBITMQ_HOST, RABBITMQ_USER, RABBITMQ_PASS);
                        if (!$rabbitPurger->purgeQueue($queueName)) {
                            $adminError = 'Purge failed: could not reach RabbitMQ management API.';
                        }
                    } else {
                        $adminError = 'Purge failed: RabbitMQ configuration not found.';
                    }
                }
                if ($adminError === null) {
                    redirect('/admin/?success=queue_purge&tab=pipeline');
                }
            }

            // ── WP–PHP compat: upsert ───────────────────────────────────
            if ($action === 'wp_compat_upsert' && $wpCompatRepo !== null) {
                $wcWp  = trim((string) ($_POST['wp_version']      ?? ''));
                $wcPhp = trim((string) ($_POST['php_min_version'] ?? ''));
                if (!$wpCompatRepo->upsert($wcWp, $wcPhp)) {
                    $adminError = 'Invalid version format. Use digits and dots only (e.g. "6.6", "7.2.24").';
                } else {
                    redirect('/admin/?success=wp_compat_upsert&tab=settings');
                }
            }

            // ── WP–PHP compat: delete ────────────────────────────────────
            if ($action === 'wp_compat_delete' && $wpCompatRepo !== null) {
                $wcWp = trim((string) ($_POST['wp_version'] ?? ''));
                $wpCompatRepo->delete($wcWp);
                redirect('/admin/?success=wp_compat_delete&tab=settings');
            }

            // ── User admin: toggle ───────────────────────────────────────
            if ($action === 'user_admin' && $userRepo !== null) {
                $targetId    = (int) ($_POST['user_id']           ?? 0);
                $isAdmin     = (int) ($_POST['user_is_admin']     ?? 0) === 1;
                $searchReturn = trim((string) ($_POST['user_search_return'] ?? ''));
                // Prevent self-demotion
                if ($targetId > 0 && $targetId !== (int) ($auth->currentUser()['user_id'] ?? 0)) {
                    $userRepo->setAdmin($targetId, $isAdmin);
                }
                $returnUrl = '/admin/?success=user_admin&tab=settings';
                if ($searchReturn !== '') {
                    $returnUrl .= '&user_search=' . urlencode($searchReturn);
                }
                redirect($returnUrl);
            }
        }

        // ── Load data for rendering ──────────────────────────────────────
        $settings = $settingRepo?->getAll() ?? [];
        $runners  = $runnerRepo?->findAll()  ?? [];

        // RabbitMQ exchanges + queues (optional; requires management plugin + rabbitmq.php)
        $exchanges    = [];
        $queues       = [];
        $rabbitConfig = __DIR__ . '/../crons/rabbitmq.php';
        if (file_exists($rabbitConfig)) {
            require_once $rabbitConfig;
            $rabbitInfo = new RabbitMqInfo(RABBITMQ_HOST, RABBITMQ_USER, RABBITMQ_PASS);
            $exchanges  = $rabbitInfo->getExchanges();
            $queues     = $rabbitInfo->getQueues();
        }

        // Analysis pipeline stats
        $runnerStats   = $resultRepo?->getRunnerSummary() ?? [];
        $recentResults = $resultRepo?->getRecent(20)      ?? [];

        // Platform stats (plugin/version/analysis counts)
        $platformStats  = $repo?->getStats()                 ?? ['plugin_count' => 0, 'version_count' => 0];
        $analysisStats  = $resultRepo?->getAnalysisStats()   ?? ['analyzed_plugins' => 0, 'total_results' => 0];

        // Uploads stuck in 'queued' with no results
        $stuckQueued = $uploadRepo?->getStuckQueued(50) ?? [];

        // Recent API uploads + grade enrichment
        $recentUploads = $uploadRepo?->getRecentForAdmin(20) ?? [];

        // Build slug:version pairs for done uploads to fetch grades in one query
        $uploadGrades = [];
        if ($resultRepo !== null && !empty($recentUploads)) {
            $pairs = [];
            foreach ($recentUploads as $up) {
                if (
                    (string) ($up['upload_status'] ?? '') === 'done'
                    && (string) ($up['plugin_slug']    ?? '') !== ''
                    && (string) ($up['plugin_version'] ?? '') !== ''
                ) {
                    $pairs[] = [
                        'slug'    => (string) $up['plugin_slug'],
                        'version' => (string) $up['plugin_version'],
                    ];
                }
            }
            if (!empty($pairs)) {
                $uploadGrades = $resultRepo->getGradesBySlugVersions($pairs);
            }
        }

        // Plugin search
        $pluginSearchTerm    = trim((string) ($_GET['plugin_search'] ?? ''));
        $pluginSearchResults = [];
        if ($pluginSearchTerm !== '' && $repo !== null) {
            $pluginSearchResults = $repo->searchBySlug($pluginSearchTerm);
        }

        // User search
        $userSearchTerm    = trim((string) ($_GET['user_search'] ?? ''));
        $userSearchResults = [];
        if ($userSearchTerm !== '' && $userRepo !== null) {
            $userSearchResults = $userRepo->searchByEmail($userSearchTerm);
        }

        // Cron run history (crons tab)
        $cronRuns = $cronRunRepo?->getRecentByName(10) ?? [];

        // Paginated user list (settings tab)
        $userListPerPage = 25;
        $userListPage    = max(1, (int) ($_GET['user_page'] ?? 1));
        $userListTotal   = $userRepo?->getTotalCount() ?? 0;
        $userListPages   = $userListTotal > 0 ? (int) ceil($userListTotal / $userListPerPage) : 1;
        $userListPage    = min($userListPage, $userListPages);
        $userList        = $userRepo?->getPaginated($userListPage, $userListPerPage) ?? [];

        // WP–PHP compatibility table (settings tab)
        $wpCompatEntries = $wpCompatRepo?->getAll() ?? [];

        $pageTitle  = 'Admin — WP Plugin Insights';
        $activePage = 'admin';
        require __DIR__ . '/templates/admin.php';
        break;

    // ── Search redirect ──────────────────────────────────────────────────────
    case 'search':
        $slug = strtolower(trim((string) ($_GET['slug'] ?? '')));
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug) ?? '';
        if ($slug === '') {
            redirect('/');
        }
        redirect('/plugin/' . $slug . '/');

    // ── Auth: register ───────────────────────────────────────────────────────
    case 'register':
        // Already logged in → home
        if ($auth !== null && $auth->isLoggedIn()) {
            redirect('/');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Csrf::validate((string) ($_POST['_csrf'] ?? ''))) {
                redirect('/register/');
            }

            $email    = trim((string) ($_POST['email']    ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm  = (string) ($_POST['confirm']  ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'invalid_email';
            } elseif (strlen($password) < 12) {
                $error = 'too_short';
            } elseif ($password !== $confirm) {
                $error = 'mismatch';
            } elseif ($userRepo !== null) {
                try {
                    $userRepo->create($email, password_hash($password, PASSWORD_BCRYPT));
                    redirect('/login/?registered=1');
                } catch (\RuntimeException) {
                    $error = 'email_taken';
                }
            } else {
                $error = 'email_taken';
            }
        }

        $pageTitle = $i18n->t('auth.register_title');
        require __DIR__ . '/templates/register.php';
        break;

    // ── 404 ──────────────────────────────────────────────────────────────────
    default:
        http_response_code(404);
        $pageTitle = $i18n->t('404.page_title');
        require __DIR__ . '/templates/404.php';
        break;
}

$pageContent = ob_get_clean();

// ── Render layout ─────────────────────────────────────────────────────────────

require __DIR__ . '/templates/layout.php';
