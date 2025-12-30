-- =====================================================
-- Live Server Database Changes - December 22, 2025
-- =====================================================
-- 
-- This SQL file contains all database changes for today
-- Run this on your live server to apply changes
--
-- IMPORTANT: Backup your database before running this!
--
-- =====================================================

-- =====================================================
-- 1. VERIFY site_data TABLE EXISTS
-- =====================================================
-- Ensure site_data table exists (should already exist)
-- If table doesn't exist, run migrations first:
-- php artisan migrate

-- Check table structure
SHOW CREATE TABLE `site_data`;

-- =====================================================
-- 2. VERIFY EXISTING DATA
-- =====================================================
-- Check existing dynamic_pages entries
SELECT id, data_key, JSON_EXTRACT(data_info, '$.title') as title, 
       JSON_EXTRACT(data_info, '$.slug') as slug, created_at, updated_at 
FROM site_data 
WHERE data_key = 'dynamic_pages.element';

-- Check about section data
SELECT id, data_key, JSON_EXTRACT(data_info, '$.heading') as heading, updated_at 
FROM site_data 
WHERE data_key = 'about.content';

-- =====================================================
-- 3. CLEANUP: Remove Image References from Dynamic Pages
-- =====================================================
-- Update existing dynamic_pages entries to remove image field from data_info
-- This is safe to run multiple times

UPDATE `site_data` 
SET `data_info` = JSON_REMOVE(`data_info`, '$.image', '$.images')
WHERE `data_key` = 'dynamic_pages.element'
  AND (JSON_EXTRACT(`data_info`, '$.image') IS NOT NULL 
       OR JSON_EXTRACT(`data_info`, '$.images') IS NOT NULL);

-- =====================================================
-- 4. ENSURE REQUIRED FIELDS EXIST
-- =====================================================
-- Update any dynamic_pages entries that might be missing title or slug
-- This will add empty strings if fields don't exist

UPDATE `site_data` 
SET `data_info` = JSON_SET(
    COALESCE(`data_info`, JSON_OBJECT()),
    '$.title', COALESCE(JSON_EXTRACT(`data_info`, '$.title'), ''),
    '$.slug', COALESCE(JSON_EXTRACT(`data_info`, '$.slug'), '')
)
WHERE `data_key` = 'dynamic_pages.element'
  AND (
    JSON_EXTRACT(`data_info`, '$.title') IS NULL 
    OR JSON_EXTRACT(`data_info`, '$.slug') IS NULL
  );

-- =====================================================
-- 5. SAMPLE DYNAMIC PAGE (Optional - Comment out if not needed)
-- =====================================================
-- Uncomment below to create a sample dynamic page:

/*
INSERT INTO `site_data` (`data_key`, `data_info`, `created_at`, `updated_at`) 
VALUES (
    'dynamic_pages.element',
    JSON_OBJECT(
        'title', 'Welcome Page',
        'slug', 'welcome',
        'details', '<h1>Welcome to Our Platform</h1><p>This is a sample dynamic page.</p>',
        'meta_title', 'Welcome - SEO Title',
        'meta_description', 'Welcome page description for SEO',
        'meta_keywords', 'welcome, page, example'
    ),
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE 
    updated_at = NOW();
*/

-- =====================================================
-- 6. VERIFY DATA INTEGRITY
-- =====================================================
-- Check all dynamic_pages have title and slug
SELECT 
    id,
    data_key,
    JSON_EXTRACT(data_info, '$.title') as title,
    JSON_EXTRACT(data_info, '$.slug') as slug,
    CASE 
        WHEN JSON_EXTRACT(data_info, '$.title') IS NULL OR JSON_EXTRACT(data_info, '$.title') = '' THEN 'MISSING TITLE'
        WHEN JSON_EXTRACT(data_info, '$.slug') IS NULL OR JSON_EXTRACT(data_info, '$.slug') = '' THEN 'MISSING SLUG'
        ELSE 'OK'
    END as status
FROM site_data 
WHERE data_key = 'dynamic_pages.element';

-- =====================================================
-- 7. UPDATE ABOUT SECTION (If needed)
-- =====================================================
-- Ensure about.content exists with proper structure
-- This will create if not exists, or update if exists

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
    updated_at = NOW();

-- =====================================================
-- 8. CLEANUP: Success Stories Data (Optional)
-- =====================================================
-- Since success stories routes were removed, you may want to clean up data
-- WARNING: This will delete success stories data permanently!
-- Only uncomment if you want to remove success stories completely

/*
-- Delete success story elements
DELETE FROM site_data WHERE data_key = 'success_story.element';

-- Delete success story content  
DELETE FROM site_data WHERE data_key = 'success_story.content';

-- Delete success story SEO
DELETE FROM site_data WHERE data_key = 'success_story.seo';
*/

-- =====================================================
-- 9. VERIFY ALL CHANGES
-- =====================================================
-- Final verification query
SELECT 
    'Dynamic Pages' as section,
    COUNT(*) as total_count,
    SUM(CASE WHEN JSON_EXTRACT(data_info, '$.title') IS NOT NULL AND JSON_EXTRACT(data_info, '$.title') != '' THEN 1 ELSE 0 END) as with_title,
    SUM(CASE WHEN JSON_EXTRACT(data_info, '$.slug') IS NOT NULL AND JSON_EXTRACT(data_info, '$.slug') != '' THEN 1 ELSE 0 END) as with_slug
FROM site_data 
WHERE data_key = 'dynamic_pages.element'

UNION ALL

SELECT 
    'About Content' as section,
    COUNT(*) as total_count,
    SUM(CASE WHEN JSON_EXTRACT(data_info, '$.heading') IS NOT NULL THEN 1 ELSE 0 END) as with_title,
    SUM(CASE WHEN JSON_EXTRACT(data_info, '$.description') IS NOT NULL THEN 1 ELSE 0 END) as with_slug
FROM site_data 
WHERE data_key = 'about.content';

-- =====================================================
-- 10. INDEX VERIFICATION
-- =====================================================
-- Verify indexes exist (should already exist)
SHOW INDEXES FROM `site_data`;

-- Expected indexes:
-- - PRIMARY on `id`
-- - UNIQUE on `data_key`

-- =====================================================
-- NOTES FOR LIVE SERVER DEPLOYMENT
-- =====================================================
-- 
-- Code Changes Made Today:
-- 1. Added Dynamic Pages functionality
--    - Route: /{slug} (e.g., /my-page-slug)
--    - Admin: /admin/site/sections/dynamic_pages
--    - Uses site_data table with data_key: 'dynamic_pages.element'
--
-- 2. Removed Success Stories routes
--    - Routes removed from web.php
--    - Removed from sitemap
--    - Data cleanup optional (see section 8)
--
-- 3. Updated About Section
--    - Added URL support for images
--    - Fixed data access (array notation)
--
-- 4. Updated Contact Page
--    - Fixed data_info array access
--
-- 5. Updated Creator Profile
--    - Changed campaign display layout
--
-- Files Changed (for reference):
-- - routes/web.php
-- - app/Http/Controllers/WebsiteController.php
-- - app/Http/Controllers/Admin/SiteController.php
-- - resources/views/themes/apnafund/site.json
-- - resources/views/themes/primary/site.json
-- - resources/views/admin/site/element.blade.php
-- - resources/views/admin/site/index.blade.php
-- - resources/views/themes/apnafund/page/contact.blade.php
-- - resources/views/themes/apnafund/page/creatorProfile.blade.php
-- - resources/views/themes/apnafund/page/businessResources.blade.php
-- - resources/views/themes/apnafund/partials/blog-part.blade.php
-- - resources/views/partials/seo.blade.php
--
-- =====================================================
-- END OF SQL FILE
-- =====================================================
-- 
-- After running this SQL:
-- 1. Clear cache: php artisan cache:clear
-- 2. Clear route cache: php artisan route:clear
-- 3. Clear view cache: php artisan view:clear
-- 4. Clear config cache: php artisan config:clear
--
-- =====================================================

