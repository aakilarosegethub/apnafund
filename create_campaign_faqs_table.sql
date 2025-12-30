-- Create campaign_faqs table for storing campaign FAQs
-- Run this SQL in your database

CREATE TABLE IF NOT EXISTS `campaign_faqs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_faqs_campaign_id_foreign` (`campaign_id`),
  CONSTRAINT `campaign_faqs_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

