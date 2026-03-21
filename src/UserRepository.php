<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Read/write access to the `user` table and supporting auth tables.
 *
 * All queries use prepared statements with bound parameters.
 * No raw user input is ever interpolated into SQL.
 */
class UserRepository
{
    public function __construct(private readonly \mysqli $db)
    {
    }

    /**
     * Returns a user row by e-mail address, or null if not found.
     * The lookup is case-insensitive (LOWER() on both sides).
     *
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT user_id, email, password_hash, display_name, preferred_lang,
                    user_is_admin, created_at, updated_at
             FROM `user`
             WHERE LOWER(email) = LOWER(?)
             LIMIT 1'
        );

        $stmt->bind_param('s', $email);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Returns a user row by primary key, or null if not found.
     *
     * @return array<string, mixed>|null
     */
    public function findById(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT user_id, email, password_hash, display_name, preferred_lang,
                    user_is_admin, created_at, updated_at
             FROM `user`
             WHERE user_id = ?
             LIMIT 1'
        );

        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Inserts a new user and returns the new user_id.
     *
     * @throws \RuntimeException if the INSERT fails (e.g. duplicate e-mail).
     */
    public function create(string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO `user` (email, password_hash)
             VALUES (?, ?)'
        );

        $stmt->bind_param('ss', $email, $passwordHash);

        if (!$stmt->execute()) {
            $detail = $stmt->error;
            $stmt->close();
            error_log('[plugininsight] UserRepository::create failed: ' . $detail);
            throw new \RuntimeException('Failed to create user account. Please try again.');
        }

        $newId = (int) $this->db->insert_id;
        $stmt->close();

        return $newId;
    }

    /**
     * Updates the bcrypt hash for a user.
     */
    public function updatePassword(int $userId, string $passwordHash): void
    {
        $stmt = $this->db->prepare(
            'UPDATE `user`
             SET password_hash = ?
             WHERE user_id = ?'
        );

        $stmt->bind_param('si', $passwordHash, $userId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Updates the display name (null clears it).
     */
    public function updateName(int $userId, ?string $displayName): void
    {
        $stmt = $this->db->prepare(
            'UPDATE `user`
             SET display_name = ?
             WHERE user_id = ?'
        );

        $stmt->bind_param('si', $displayName, $userId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Updates the preferred UI language (null = auto-detect).
     * Only accepts locale codes present in I18n::SUPPORTED.
     */
    public function updateLang(int $userId, ?string $lang): void
    {
        $validated = ($lang !== null && in_array($lang, I18n::SUPPORTED, true))
            ? $lang
            : null;

        $stmt = $this->db->prepare(
            'UPDATE `user`
             SET preferred_lang = ?
             WHERE user_id = ?'
        );

        $stmt->bind_param('si', $validated, $userId);
        $stmt->execute();
        $stmt->close();
    }

    // ── Admin management ─────────────────────────────────────────────────────

    /**
     * Returns up to 20 users whose e-mail address contains $term.
     * Results are ordered by e-mail.
     *
     * @return list<array<string, mixed>>
     */
    public function searchByEmail(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            'SELECT user_id, email, display_name, user_is_admin, created_at
             FROM `user`
             WHERE email LIKE ?
             ORDER BY email
             LIMIT 20'
        );
        $stmt->bind_param('s', $like);
        $stmt->execute();

        $result = $stmt->get_result();
        $rows   = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Returns the total number of registered users.
     */
    public function getTotalCount(): int
    {
        $result = $this->db->query('SELECT COUNT(*) FROM `user`');
        return (int) (($result !== false ? $result->fetch_row() : null)[0] ?? 0);
    }

    /**
     * Returns one page of users ordered by registration date (newest first).
     *
     * @param  int $page    1-based page number.
     * @param  int $perPage Number of rows per page.
     *
     * @return list<array<string, mixed>>
     */
    public function getPaginated(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt   = $this->db->prepare(
            'SELECT user_id, email, user_is_admin, created_at
             FROM `user`
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $rows   = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        return $rows;
    }

    /**
     * Grants or revokes the admin flag for a user.
     */
    public function setAdmin(int $userId, bool $isAdmin): void
    {
        $val  = $isAdmin ? 1 : 0;
        $stmt = $this->db->prepare(
            'UPDATE `user` SET user_is_admin = ? WHERE user_id = ?'
        );
        $stmt->bind_param('ii', $val, $userId);
        $stmt->execute();
        $stmt->close();
    }

    // ── Password reset ────────────────────────────────────────────────────────

    /**
     * Stores a password-reset token hash for a user.
     * Any existing unused tokens for the same user are invalidated first.
     *
     * @param string $tokenHash hex(hash_hmac('sha256', $rawToken, $secret))
     */
    public function createResetToken(int $userId, string $tokenHash, \DateTimeImmutable $expiresAt): void
    {
        // Invalidate existing pending tokens for this user
        $del = $this->db->prepare(
            'DELETE FROM `password_reset`
             WHERE user_id = ? AND used_at IS NULL'
        );
        $del->bind_param('i', $userId);
        $del->execute();
        $del->close();

        $expires = $expiresAt->format('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            'INSERT INTO `password_reset` (user_id, token_hash, expires_at)
             VALUES (?, ?, ?)'
        );

        $stmt->bind_param('iss', $userId, $tokenHash, $expires);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Looks up a valid (unused, not expired) reset token row by its hash.
     *
     * @return array<string, mixed>|null
     */
    public function findValidResetToken(string $tokenHash): ?array
    {
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            'SELECT reset_id, user_id, expires_at
             FROM `password_reset`
             WHERE token_hash = ?
               AND used_at IS NULL
               AND expires_at > ?
             LIMIT 1'
        );

        $stmt->bind_param('ss', $tokenHash, $now);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Marks a reset token as used (consumes it).
     */
    public function consumeResetToken(int $resetId): void
    {
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            'UPDATE `password_reset`
             SET used_at = ?
             WHERE reset_id = ?'
        );

        $stmt->bind_param('si', $now, $resetId);
        $stmt->execute();
        $stmt->close();
    }

    // ── Rate limiting ─────────────────────────────────────────────────────────

    /**
     * Records a failed login attempt. Stores the IP and a SHA-256 hash of the
     * lowercased e-mail — never plaintext.
     */
    public function recordLoginAttempt(string $ip, string $email): void
    {
        $emailHash = hash('sha256', strtolower($email));

        $stmt = $this->db->prepare(
            'INSERT INTO `login_attempt` (ip_address, email_hash)
             VALUES (?, ?)'
        );

        $stmt->bind_param('ss', $ip, $emailHash);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Returns the number of failed attempts for an IP in the last $windowSeconds.
     */
    public function countRecentAttemptsByIp(string $ip, int $windowSeconds = 900): int
    {
        $ago   = "-{$windowSeconds} seconds";
        $since = (new \DateTimeImmutable($ago, new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM `login_attempt`
             WHERE ip_address = ?
               AND attempted_at > ?'
        );

        $stmt->bind_param('ss', $ip, $since);
        $stmt->execute();

        $count = (int) ($stmt->get_result()->fetch_row()[0] ?? 0);
        $stmt->close();

        return $count;
    }

    /**
     * Removes attempt records older than $windowSeconds to keep the table small.
     */
    public function pruneOldAttempts(int $windowSeconds = 900): void
    {
        $ago    = "-{$windowSeconds} seconds";
        $cutoff = (new \DateTimeImmutable($ago, new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            'DELETE FROM `login_attempt`
             WHERE attempted_at < ?'
        );

        $stmt->bind_param('s', $cutoff);
        $stmt->execute();
        $stmt->close();
    }
}
