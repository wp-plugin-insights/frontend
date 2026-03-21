<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Minimal front-controller router.
 *
 * Resolves a URI path to a named page and an array of extracted parameters.
 * Query-string parameters are intentionally ignored here — callers read
 * $_GET directly.
 *
 * Supported routes:
 *   /                          → home
 *   /plugin/<slug>/            → plugin          (param: slug)
 *   /plugin/                   → search          (GET ?slug= redirects to /plugin/<slug>/)
 *   /about/                    → about
 *   /login/                    → login
 *   /logout/                   → logout          (POST only; GET → redirect /)
 *   /forgot-password/          → forgot-password
 *   /reset-password/           → reset-password
 *   /account/                  → account         (auth required)
 *   /register/                 → register
 *   /api/<uuid>/               → plugin-upload   (param: uuid)
 *   /admin/                    → admin           (admin auth required)
 *   <anything else>            → 404
 */
class Router
{
    /** UUID v4 pattern used in the /api/{uuid}/ route. */
    private const RE_UPLOAD_UUID =
        '#^/api/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$#i';

    /**
     * Resolves the given URI to a page name and parameter map.
     *
     * @return array{page: string, params: array<string, string>}
     */
    public function resolve(string $uri): array
    {
        // Strip query string and normalise trailing slash
        $path = strtok($uri, '?') ?: '/';
        $path = '/' . trim($path, '/');

        return match (true) {
            $path === '/'                  => ['page' => 'home',             'params' => []],
            $path === '/about'             => ['page' => 'about',            'params' => []],
            $path === '/login'             => ['page' => 'login',            'params' => []],
            $path === '/logout'            => ['page' => 'logout',           'params' => []],
            $path === '/forgot-password'   => ['page' => 'forgot-password',  'params' => []],
            $path === '/reset-password'    => ['page' => 'reset-password',   'params' => []],
            $path === '/account'           => ['page' => 'account',          'params' => []],
            $path === '/register'          => ['page' => 'register',         'params' => []],
            $path === '/admin'             => ['page' => 'admin',            'params' => []],
            $path === '/plugin'            => ['page' => 'search',           'params' => []],
            (bool) preg_match('#^/plugin/([a-z0-9][a-z0-9\-]{0,98})$#', $path, $m)
                                           => ['page' => 'plugin',           'params' => ['slug' => $m[1]]],
            (bool) preg_match(self::RE_UPLOAD_UUID, $path, $m)
                                           => ['page' => 'plugin-upload',    'params' => ['uuid' => $m[1]]],
            default                        => ['page' => '404',              'params' => []],
        };
    }
}
