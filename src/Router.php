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
 *   <anything else>            → 404
 */
class Router
{
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
            $path === '/plugin'            => ['page' => 'search',           'params' => []],
            (bool) preg_match('#^/plugin/([a-z0-9][a-z0-9\-]{0,98})$#', $path, $m)
                                           => ['page' => 'plugin',           'params' => ['slug' => $m[1]]],
            default                        => ['page' => '404',              'params' => []],
        };
    }
}
