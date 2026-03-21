<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * CSRF token helper.
 *
 * One token per session, embedded as a hidden field in every state-changing
 * form and validated with hash_equals() on POST to prevent timing attacks.
 */
class Csrf
{
    private const TOKEN_KEY = 'pi_csrf';

    /**
     * Returns the current session CSRF token, generating it on first call.
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Returns an HTML hidden input containing the CSRF token.
     * Safe to echo directly in a template.
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');

        return '<input type="hidden" name="_csrf" value="' . $token . '">';
    }

    /**
     * Validates a submitted token against the session token.
     * Uses hash_equals() to prevent timing-based attacks.
     */
    public static function validate(string $submitted): bool
    {
        $stored = $_SESSION[self::TOKEN_KEY] ?? '';

        if ($stored === '' || $submitted === '') {
            return false;
        }

        return hash_equals($stored, $submitted);
    }
}
