<?php

declare(strict_types=1);

namespace PluginInsight;

/**
 * Password-reset flow.
 *
 * Security properties:
 *   - Token is 32 random bytes encoded as 64-char hex (256 bits of entropy).
 *   - Only HMAC-SHA-256(token, APP_SECRET) is stored in the DB; the raw token
 *     is never persisted, so a DB leak does not expose usable tokens.
 *   - Tokens expire after 1 hour and are single-use (consumed on redemption).
 *   - The forgot-password response is identical whether the e-mail exists or
 *     not, preventing user enumeration.
 *   - Reset URL is HTTPS only (enforced via APP_URL constant).
 */
class PasswordReset
{
    private const TTL_SECONDS = 3600; // 1 hour

    public function __construct(private readonly UserRepository $users)
    {
    }

    /**
     * Initiates a password-reset request for the given e-mail.
     *
     * Returns true if the e-mail exists and a token was generated (so the
     * caller can send the e-mail). Returns false silently if the address is
     * not found — the caller should show the same success message either way.
     */
    public function request(string $email): bool
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            return false;
        }

        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = $this->hashToken($rawToken);
        $expiresAt = new \DateTimeImmutable(
            '+' . self::TTL_SECONDS . ' seconds',
            new \DateTimeZone('UTC')
        );

        $this->users->createResetToken((int) $user['user_id'], $tokenHash, $expiresAt);
        $this->sendEmail((string) $user['email'], $rawToken);

        return true;
    }

    /**
     * Validates a raw token from the reset URL.
     *
     * Returns the reset row (with user_id) on success, or null if the token
     * is invalid, expired, or already used.
     *
     * @return array<string, mixed>|null
     */
    public function validate(string $rawToken): ?array
    {
        if (!$this->isValidTokenFormat($rawToken)) {
            return null;
        }

        $tokenHash = $this->hashToken($rawToken);

        return $this->users->findValidResetToken($tokenHash);
    }

    /**
     * Completes the reset: updates the password and consumes the token.
     * The caller is responsible for validating the token first via validate().
     */
    public function complete(int $resetId, int $userId, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $this->users->updatePassword($userId, $hash);
        $this->users->consumeResetToken($resetId);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Computes HMAC-SHA-256 of the raw token using APP_SECRET.
     * Constant-time comparison is handled by hash_equals() at lookup time
     * (see UserRepository::findValidResetToken — DB lookup is by hash).
     */
    private function hashToken(string $rawToken): string
    {
        return hash_hmac('sha256', $rawToken, APP_SECRET);
    }

    /**
     * Sanity-checks that the token looks like 64 lowercase hex characters.
     * Rejects obviously malformed input before hitting the DB.
     */
    private function isValidTokenFormat(string $token): bool
    {
        return (bool) preg_match('/^[0-9a-f]{64}$/', $token);
    }

    /**
     * Sends the password-reset e-mail to the user.
     */
    private function sendEmail(string $to, string $rawToken): void
    {
        $url = APP_URL . '/reset-password/?token=' . rawurlencode($rawToken);

        $subject = 'Reset your PluginInsight password';

        $body  = "You requested a password reset for your PluginInsight account.\n\n";
        $body .= "Click the link below to set a new password (valid for 1 hour):\n\n";
        $body .= $url . "\n\n";
        $body .= "If you did not request this, you can safely ignore this e-mail.\n";

        $headers  = 'From: ' . MAIL_FROM . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "MIME-Version: 1.0\r\n";

        mail($to, $subject, $body, $headers);
    }
}
