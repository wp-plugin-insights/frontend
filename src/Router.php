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
 *   /plugin/<slug>/            → plugin  (param: slug)
 *   /about/                    → about
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

        if ($path === '/') {
            return ['page' => 'home', 'params' => []];
        }

        if ($path === '/about') {
            return ['page' => 'about', 'params' => []];
        }

        if (preg_match('#^/plugin/([a-z0-9][a-z0-9\-]{0,98})$#', $path, $m)) {
            return ['page' => 'plugin', 'params' => ['slug' => $m[1]]];
        }

        return ['page' => '404', 'params' => []];
    }
}
