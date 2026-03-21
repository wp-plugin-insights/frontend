-- PluginInsight — Add is_admin flag to user table
-- Migration: 003_user_is_admin
-- Requires: 002 (auth tables)

SET NAMES utf8mb4;

ALTER TABLE `user`
    ADD COLUMN `is_admin` TINYINT(1) NOT NULL DEFAULT 0
        COMMENT '1 = administrator, 0 = regular user'
        AFTER `preferred_lang`;
