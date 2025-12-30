-- Create users table for gofund2 database
-- Run this SQL in phpMyAdmin or MySQL

USE gofund2;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `address` json DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `kc` tinyint(1) DEFAULT 0,
  `ec` tinyint(1) DEFAULT 0,
  `sc` tinyint(1) DEFAULT 0,
  `ts` tinyint(1) DEFAULT 0,
  `tc` tinyint(1) DEFAULT 0,
  `ref_by` bigint(20) UNSIGNED DEFAULT 0,
  `ver_code` varchar(255) DEFAULT NULL,
  `ver_code_send_at` timestamp NULL DEFAULT NULL,
  `balance` decimal(18,8) DEFAULT 0.00000000,
  `kyc_data` json DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

