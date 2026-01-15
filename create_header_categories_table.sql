-- Create header_categories table
CREATE TABLE IF NOT EXISTS `header_categories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `header_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default header categories
INSERT INTO `header_categories` (`label`, `slug`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
('Art/Crafts', 'art-crafts', 1, 'active', NOW(), NOW()),
('Games/Comics', 'games-comics', 2, 'active', NOW(), NOW()),
('Film/Theatre', 'film-theatre', 3, 'active', NOW(), NOW()),
('Dance/Music', 'dance-music', 4, 'active', NOW(), NOW()),
('Fashion/Design', 'fashion-design', 5, 'active', NOW(), NOW()),
('Education/Journalism', 'education-journalism', 6, 'active', NOW(), NOW()),
('Photography/Publishing', 'photography-publishing', 7, 'active', NOW(), NOW()),
('Software/Technology', 'software-technology', 8, 'active', NOW(), NOW());
