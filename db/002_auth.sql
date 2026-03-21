-- PluginInsight — Auth schema migration
-- Migration: 002_auth
-- Requires: 001 (plugin table, already applied)

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ── Users ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `user` (
    `user_id`        BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `email`          VARCHAR(254)     NOT NULL,
    `password_hash`  VARCHAR(255)     NOT NULL  COMMENT 'bcrypt via password_hash()',
    `display_name`   VARCHAR(100)     NULL      DEFAULT NULL,
    `preferred_lang` VARCHAR(5)       NULL      DEFAULT NULL COMMENT 'ISO locale code, e.g. en, es, lld; NULL = auto-detect',
    `created_at`     DATETIME         NOT NULL  DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME         NOT NULL  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `uq_user_email` (`email`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ── Password reset tokens ────────────────────────────────────────────────────
-- Only the HMAC-SHA-256 hash of the token is stored; the raw token is sent by
-- e-mail and never persisted.
CREATE TABLE IF NOT EXISTS `password_reset` (
    `reset_id`    BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED  NOT NULL,
    `token_hash`  VARCHAR(64)      NOT NULL COMMENT 'hex(hash_hmac(sha256, token, secret))',
    `expires_at`  DATETIME         NOT NULL,
    `used_at`     DATETIME         NULL     DEFAULT NULL,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`reset_id`),
    UNIQUE KEY `uq_reset_token_hash` (`token_hash`),
    KEY `idx_reset_user` (`user_id`),
    KEY `idx_reset_expires` (`expires_at`),
    CONSTRAINT `fk_reset_user`
        FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ── Login attempt log (rate limiting) ────────────────────────────────────────
-- Rows older than 1 hour can be pruned; the application checks only recent rows.
CREATE TABLE IF NOT EXISTS `login_attempt` (
    `attempt_id`   BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `ip_address`   VARCHAR(45)      NOT NULL COMMENT 'IPv4 or IPv6',
    `email_hash`   VARCHAR(64)      NULL     DEFAULT NULL COMMENT 'hex(sha256(lower(email))); no plaintext stored',
    `attempted_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`attempt_id`),
    KEY `idx_attempt_ip_time`    (`ip_address`, `attempted_at`),
    KEY `idx_attempt_email_time` (`email_hash`, `attempted_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
