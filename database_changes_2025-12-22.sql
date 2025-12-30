-- =====================================================
-- Database Changes Summary - December 22, 2025
-- =====================================================
-- 
-- This file contains all database-related changes made today
-- Note: Most changes were code-level (routes, views, controllers)
-- No new tables were created as dynamic_pages uses existing site_data table
--
-- =====================================================

-- =====================================================
-- 1. VERIFY site_data TABLE EXISTS
-- =====================================================
-- The site_data table is used for:
-- - Dynamic Pages (data_key: 'dynamic_pages.element')
-- - Policy Pages (data_key: 'policy_pages.element')
-- - About Section (data_key: 'about.content')
-- - All other site sections

-- Check if site_data table exists (should already exist)
-- If not exists, run the migration:
-- php artisan migrate

-- =====================================================
-- 2. DYNAMIC PAGES DATA STRUCTURE
-- =====================================================
-- Dynamic pages are stored in site_data table with:
-- data_key: 'dynamic_pages.element'
-- data_info (JSON): {
--   "title": "Page Title",
--   "slug": "page-slug",
--   "details": "HTML content",
--   "meta_title": "SEO Meta Title",
--   "meta_description": "SEO Meta Description",
--   "meta_keywords": "keyword1, keyword2",
--   "images": {
--     "image": "image-filename.jpg"
--   }
-- }

-- =====================================================
-- 3. SAMPLE DYNAMIC PAGE INSERT (Optional)
-- =====================================================
-- Uncomment below to create a sample dynamic page:

/*
INSERT INTO `site_data` (`data_key`, `data_info`, `created_at`, `updated_at`) 
VALUES (
    'dynamic_pages.element',
    JSON_OBJECT(
        'title', 'Sample Page',
        'slug', 'sample-page',
        'details', '<p>This is a sample dynamic page content.</p>',
        'meta_title', 'Sample Page - SEO Title',
        'meta_description', 'This is a sample page description for SEO',
        'meta_keywords', 'sample, page, example',
        'images', JSON_OBJECT('image', '')
    ),
    NOW(),
    NOW()
);
*/

-- =====================================================
-- 4. VERIFY EXISTING DATA STRUCTURE
-- =====================================================
-- Check existing site_data entries:
-- SELECT id, data_key, data_info, created_at, updated_at 
-- FROM site_data 
-- WHERE data_key LIKE 'dynamic_pages%' 
--    OR data_key LIKE 'policy_pages%'
--    OR data_key LIKE 'about%';

-- =====================================================
-- 5. UPDATE EXISTING ABOUT SECTION (If needed)
-- =====================================================
-- About section should have data_key: 'about.content'
-- If missing, create it:

/*
INSERT INTO `site_data` (`data_key`, `data_info`, `created_at`, `updated_at`) 
VALUES (
    'about.content',
    JSON_OBJECT(
        'heading', 'About Us',
        'description', 'Your about us description here',
        'button_text', 'Learn More',
        'button_url', '#',
        'images', JSON_OBJECT('image', '')
    ),
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE 
    data_info = VALUES(data_info),
    updated_at = NOW();
*/

-- =====================================================
-- 6. CLEANUP: Remove Success Stories Data (Optional)
-- =====================================================
-- Since success stories routes were removed, you may want to:
-- Note: Only uncomment if you want to delete success stories data

/*
-- Delete success story elements
DELETE FROM site_data WHERE data_key = 'success_story.element';

-- Delete success story content
DELETE FROM site_data WHERE data_key = 'success_story.content';

-- Delete success story SEO
DELETE FROM site_data WHERE data_key = 'success_story.seo';
*/

-- =====================================================
-- 7. VERIFY TABLE STRUCTURE
-- =====================================================
-- Ensure site_data table has correct structure:
-- 
-- CREATE TABLE IF NOT EXISTS `site_data` (
--   `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
--   `data_key` varchar(255) NOT NULL UNIQUE,
--   `data_info` json DEFAULT NULL,
--   `created_at` timestamp NULL DEFAULT NULL,
--   `updated_at` timestamp NULL DEFAULT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `site_data_data_key_unique` (`data_key`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. INDEXES (Already exists in migration)
-- =====================================================
-- The site_data table already has:
-- - PRIMARY KEY on `id`
-- - UNIQUE KEY on `data_key`
-- 
-- No additional indexes needed for current usage

-- =====================================================
-- 9. NOTES
-- =====================================================
-- Today's changes summary:
-- 
-- 1. Added Dynamic Pages functionality
--    - Uses existing site_data table
--    - Route: /{slug} (e.g., /my-page-slug)
--    - Admin: /admin/site/sections/dynamic_pages
--
-- 2. Removed Success Stories routes
--    - Routes removed from web.php
--    - Removed from sitemap
--    - Data still exists in database (optional cleanup above)
--
-- 3. Updated About Section
--    - Added URL support for images
--    - Fixed data access (array notation)
--
-- 4. Updated Contact Page
--    - Fixed data_info array access
--
-- 5. Updated Creator Profile
--    - Changed campaign display to use campaign-item partial
--
-- =====================================================
-- END OF SQL FILE
-- =====================================================

