<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Authentication service.
 *
 * Handles login, logout, session management and password rehashing.
 * Session ini options must be configured before the first call to
 * Auth::startSession() — typically at the top of index.php.
 *
 * Security properties:
 *   - Session ID regenerated on login (prevents fixation).
 *   - Session destroyed completely on logout (cookie + server data).
 *   - Only user_id is stored in the session; full row fetched on demand.
 *   - Passwords verified with password_verify(); rehashed transparently
 *     when PASSWORD_BCRYPT cost changes.
 *   - Generic error message returned for wrong e-mail AND wrong password
 *     (prevents user enumeration).
 *   - Failed attempts logged for rate limiting by IP.
 */
class Auth
{
    private const SESSION_USER_KEY = 'pi_user_id';

    /** Cached user row for the current request. */
    private ?array $cachedUser = null; // @phpstan-ignore-line

    public function __construct(private readonly UserRepository $users)
    {
    }

    // ── Session bootstrap ─────────────────────────────────────────────────────

    /**
     * Configures session ini options and calls session_start().
     * Must be called once, before any output, early in index.php.
     */
    public static function startSession(): void
    {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.cookie_secure', self::isHttps() ? '1' : '0');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Attempts to log in with the given credentials.
     *
     * Returns true on success; false on failure (wrong credentials, rate
     * limited, or account not found). Always takes the same code path for
     * wrong e-mail vs wrong password to prevent user enumeration.
     */
    public function login(string $email, string $password, string $clientIp): bool
    {
        // Rate limit: max 5 attempts per IP per 15 minutes
        if ($this->users->countRecentAttemptsByIp($clientIp) >= 5) {
            return false;
        }

        $user = $this->users->findByEmail($email);

        // Always run password_verify to keep timing consistent
        $dummy = '$2y$12$invalid.hash.placeholder.00000000000000000000000';
        $hash  = $user !== null ? (string) $user['password_hash'] : $dummy;
        $valid = password_verify($password, $hash);

        if ($user === null || !$valid) {
            $this->users->recordLoginAttempt($clientIp, $email);
            return false;
        }

        // Rehash if the bcrypt cost has changed
        if (password_needs_rehash($hash, PASSWORD_BCRYPT)) {
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            $this->users->updatePassword((int) $user['user_id'], $newHash);
        }

        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION[self::SESSION_USER_KEY] = (int) $user['user_id'];
        $this->cachedUser = $user;

        return true;
    }

    /**
     * Logs out the current user: destroys the session and clears the cookie.
     */
    public function logout(): void
    {
        $this->cachedUser = null;

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires'  => time() - 42000,
                    'path'     => $params['path'],
                    'domain'   => $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => 'Strict',
                ]
            );
        }

        session_destroy();
    }

    /**
     * Returns the currently logged-in user row, or null if not authenticated.
     * The row is fetched from the DB once per request and cached.
     *
     * @return array<string, mixed>|null
     */
    public function currentUser(): ?array
    {
        if ($this->cachedUser !== null) {
            return $this->cachedUser;
        }

        $userId = isset($_SESSION[self::SESSION_USER_KEY])
            ? (int) $_SESSION[self::SESSION_USER_KEY]
            : null;

        if ($userId === null || $userId <= 0) {
            return null;
        }

        $this->cachedUser = $this->users->findById($userId);

        return $this->cachedUser;
    }

    /**
     * Returns true if a user is currently authenticated.
     */
    public function isLoggedIn(): bool
    {
        return $this->currentUser() !== null;
    }

    /**
     * Returns the best client IP address for rate limiting.
     *
     * Note: X-Forwarded-For is used only if the request comes from a trusted
     * reverse proxy (127.0.0.1). Do not trust it blindly on public servers.
     */
    public static function clientIp(): string
    {
        $remoteAddr = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

        if ($remoteAddr === '127.0.0.1' || $remoteAddr === '::1') {
            $forwarded = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
            if ($forwarded !== '') {
                // Take only the first (leftmost) IP; strip whitespace
                $first = explode(',', $forwarded)[0];
                $ip    = trim($first);
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }

        return $remoteAddr;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
            return true;
        }

        return false;
    }
}
