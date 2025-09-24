-- Create data_logs table
CREATE TABLE IF NOT EXISTS `data_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_data` json DEFAULT NULL,
  `headers` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `raw_input` longtext COLLATE utf8mb4_unicode_ci,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'received',
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `data_logs_endpoint_created_at_index` (`endpoint`,`created_at`),
  KEY `data_logs_transaction_id_index` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create webhook_logs table
CREATE TABLE IF NOT EXISTS `webhook_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `webhook_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'POST',
  `headers` json DEFAULT NULL,
  `payload` json DEFAULT NULL,
  `response_status` int(11) DEFAULT NULL,
  `response_body` longtext COLLATE utf8mb4_unicode_ci,
  `response_headers` json DEFAULT NULL,
  `execution_time` decimal(8,3) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `campaign_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhook_logs_user_id_foreign` (`user_id`),
  KEY `webhook_logs_campaign_id_foreign` (`campaign_id`),
  KEY `webhook_logs_webhook_type_index` (`webhook_type`),
  KEY `webhook_logs_status_index` (`status`),
  KEY `webhook_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
