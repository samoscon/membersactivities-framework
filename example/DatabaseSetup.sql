-- ============================================================================
-- MembersActivities / Ticketing System
-- Reference Database Schema
-- ============================================================================
--
-- Purpose:
--   Reference schema for creating a NEW client application database.
--
-- Intended environment:
--   MariaDB 10.6+
--   PHP 8.3+
--   Controller Framework 1.0.31
--
-- Character set:
--   utf8mb4
--
-- Storage engine:
--   InnoDB
--
-- ============================================================================


SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

SET FOREIGN_KEY_CHECKS = 0;


-- ============================================================================
-- TABLE: activity
-- ============================================================================
--
-- Activities can form a hierarchy:
--
--   activity
--      |
--      +-- child activity
--      |
--      +-- child activity
--
-- costitems belonging to an activity are stored in costitem.
--
-- ============================================================================

CREATE TABLE `activity` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(100) DEFAULT 'description',
    `classification` VARCHAR(4) NOT NULL DEFAULT 'RGLR',
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `date` DATE NOT NULL,
    `duedate` DATE DEFAULT NULL,
    `longdescription` VARCHAR(4000) DEFAULT NULL,
    `start` TIME NOT NULL DEFAULT '19:00:00',
    `end` TIME NOT NULL DEFAULT '21:00:00',
    `location` VARCHAR(200) DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `idx_activity_parent_id` (`parent_id`),

    CONSTRAINT `fk_activity_parent`
        FOREIGN KEY (`parent_id`)
        REFERENCES `activity` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- TABLE: member
-- ============================================================================
--
-- Members can be grouped through parent_id.
--
-- Example:
--
--   Family / group
--       |
--       +-- Member A
--       +-- Member B
--       +-- Member C
--
-- Deleting the parent/group member does NOT delete the child members.
-- Their parent_id is automatically set to NULL.
--
-- A member that has subscriptions or payments cannot be deleted because
-- those relationships use ON DELETE RESTRICT.
--
-- ============================================================================

CREATE TABLE `member` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(190) DEFAULT 'description',
    `classification` VARCHAR(4) NOT NULL DEFAULT 'RGLR',
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(100) DEFAULT NULL,
    `lastname` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(254) DEFAULT NULL,
    `role` VARCHAR(5) NOT NULL DEFAULT 'U'
        COMMENT 'USER (U) or ADMIN (A)',
    `password` VARCHAR(256) DEFAULT NULL,
    `ownpwd` TINYINT(1) NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 0,
    `subscriptionuntil` DATE DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `idx_member_parent_id` (`parent_id`),
    KEY `idx_member_email` (`email`),

    CONSTRAINT `fk_member_parent`
        FOREIGN KEY (`parent_id`)
        REFERENCES `member` (`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- TABLE: costitem
-- ============================================================================
--
-- A cost item belongs to one activity.
--
-- One activity can have multiple cost items.
--
-- ============================================================================

CREATE TABLE `costitem` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(100) DEFAULT 'TBD',
    `classification` VARCHAR(4) NOT NULL DEFAULT 'RGLR',
    `price` DECIMAL(8,2) DEFAULT NULL,
    `type` VARCHAR(45) DEFAULT 'W',
    `activity_id` INT UNSIGNED DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `idx_costitem_activity_id` (`activity_id`),

    CONSTRAINT `fk_costitem_activity`
        FOREIGN KEY (`activity_id`)
        REFERENCES `activity` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- TABLE: payment
-- ============================================================================
--
-- A payment belongs to one member.
--
-- One payment may cover multiple subscriptions.
--
-- Example:
--
--   Payment #100
--       |
--       +-- Subscription #1
--       +-- Subscription #2
--       +-- Subscription #3
--
-- Payments are therefore NOT linked directly to a single subscription.
--
-- ============================================================================

CREATE TABLE `payment` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(100) DEFAULT 'TBD',
    `classification` VARCHAR(4) NOT NULL DEFAULT 'RGLR',
    `member_id` INT UNSIGNED DEFAULT NULL,
    `date` DATE DEFAULT (CURRENT_DATE),
    `amount` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `status` VARCHAR(12) DEFAULT NULL,
    `type` VARCHAR(12) DEFAULT 'prepaid',
    `source` VARCHAR(26) DEFAULT 'mollie',

    PRIMARY KEY (`id`),

    KEY `idx_payment_member_id` (`member_id`),

    CONSTRAINT `fk_payment_member`
        FOREIGN KEY (`member_id`)
        REFERENCES `member` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- TABLE: subscription
-- ============================================================================
--
-- A subscription belongs to:
--
--   - one member
--   - one cost item
--   - optionally one payment
--
-- Multiple subscriptions can refer to the same payment.
--
-- ============================================================================

CREATE TABLE `subscription` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(100) DEFAULT 'TBD',
    `classification` VARCHAR(4) NOT NULL DEFAULT 'RGLR',
    `member_id` INT UNSIGNED DEFAULT NULL,
    `costitem_id` INT UNSIGNED DEFAULT NULL,
    `payment_id` INT UNSIGNED DEFAULT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 0,
    `remark` MEDIUMTEXT DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `idx_subscription_member_id` (`member_id`),
    KEY `idx_subscription_costitem_id` (`costitem_id`),
    KEY `idx_subscription_payment_id` (`payment_id`),

    CONSTRAINT `fk_subscription_member`
        FOREIGN KEY (`member_id`)
        REFERENCES `member` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT `fk_subscription_costitem`
        FOREIGN KEY (`costitem_id`)
        REFERENCES `costitem` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT `fk_subscription_payment`
        FOREIGN KEY (`payment_id`)
        REFERENCES `payment` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- TABLE: remember_tokens
-- ============================================================================
--
-- Persistent login tokens belonging to members.
--
-- Tokens are authentication data and may safely be removed automatically
-- when their member is deleted.
--
-- ============================================================================

CREATE TABLE `remember_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id` INT UNSIGNED NOT NULL,

    `selector` CHAR(24)
        CHARACTER SET ascii
        COLLATE ascii_bin
        NOT NULL,

    `token_hash` CHAR(64)
        CHARACTER SET ascii
        COLLATE ascii_bin
        NOT NULL,

    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_used_at` DATETIME DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `uq_remember_selector` (`selector`),
    KEY `idx_remember_member` (`member_id`),
    KEY `idx_remember_expires` (`expires_at`),

    CONSTRAINT `fk_remember_member`
        FOREIGN KEY (`member_id`)
        REFERENCES `member` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_general_ci;


-- ============================================================================
-- TABLE: mail_queue
-- ============================================================================
--
-- mail_queue is deliberately NOT linked through foreign keys.
--
-- parent_id is an application-level field and is not a database relation
-- that should be enforced by a foreign key.
--
-- ============================================================================

CREATE TABLE `mail_queue` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(100) DEFAULT 'description',
    `classification` VARCHAR(4) NOT NULL DEFAULT 'RGLR',
    `parent_id` INT UNSIGNED DEFAULT NULL,

    `subject` VARCHAR(255) NOT NULL,
    `body` MEDIUMTEXT NOT NULL,
    `recipient` VARCHAR(254) NOT NULL,
    `bcc` VARCHAR(1000) DEFAULT NULL,

    `status` ENUM(
        'pending',
        'sending',
        'sent',
        'failed'
    ) NOT NULL DEFAULT 'pending',

    `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,

    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `started_at` DATETIME DEFAULT NULL,
    `sent_at` DATETIME DEFAULT NULL,

    `error` TEXT DEFAULT NULL,

    PRIMARY KEY (`id`),

    KEY `idx_mail_queue_status_created`
        (`status`, `created_at`)

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_general_ci;


-- ============================================================================
-- Restore foreign key checking
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================================
-- END OF REFERENCE SCHEMA
-- ============================================================================
