-- SQL Updates for Footer Menu Sorting and Filtering Feature
-- Date: 2025-01-XX
-- Description: This file contains SQL queries to update existing footer_menu items with sort_order field

-- Note: The sort_order is stored in the JSON data_info column, not as a separate column
-- This is because the site_data table uses JSON to store flexible data structures

-- 1. Update existing footer_menu items to add sort_order if it doesn't exist
-- This sets sort_order to 0 for items that don't have it yet
UPDATE site_data 
SET data_info = JSON_SET(
    COALESCE(data_info, '{}'),
    '$.sort_order',
    COALESCE(JSON_EXTRACT(data_info, '$.sort_order'), 0)
)
WHERE data_key = 'footer_menu.element'
AND (JSON_EXTRACT(data_info, '$.sort_order') IS NULL OR JSON_EXTRACT(data_info, '$.sort_order') = '');

-- 2. View all footer_menu items with their sort_order values
SELECT 
    id,
    data_key,
    JSON_EXTRACT(data_info, '$.menu_label') as menu_label,
    JSON_EXTRACT(data_info, '$.slug') as slug,
    JSON_EXTRACT(data_info, '$.section_type') as section_type,
    JSON_EXTRACT(data_info, '$.status') as status,
    JSON_EXTRACT(data_info, '$.sort_order') as sort_order,
    created_at,
    updated_at
FROM site_data
WHERE data_key = 'footer_menu.element'
ORDER BY CAST(JSON_EXTRACT(data_info, '$.sort_order') AS UNSIGNED) ASC, id ASC;

-- 3. Update sort_order for specific items (example)
-- Update item with id = 1 to have sort_order = 10
-- UPDATE site_data 
-- SET data_info = JSON_SET(data_info, '$.sort_order', 10)
-- WHERE id = 1 AND data_key = 'footer_menu.element';

-- 4. Filter footer_menu items by section_type (example query)
-- SELECT 
--     id,
--     JSON_EXTRACT(data_info, '$.menu_label') as menu_label,
--     JSON_EXTRACT(data_info, '$.section_type') as section_type,
--     JSON_EXTRACT(data_info, '$.sort_order') as sort_order
-- FROM site_data
-- WHERE data_key = 'footer_menu.element'
-- AND JSON_EXTRACT(data_info, '$.section_type') = 'about'
-- ORDER BY CAST(JSON_EXTRACT(data_info, '$.sort_order') AS UNSIGNED) ASC;

-- 5. Get all unique section_type values from footer_menu items
SELECT DISTINCT
    JSON_EXTRACT(data_info, '$.section_type') as section_type,
    COUNT(*) as count
FROM site_data
WHERE data_key = 'footer_menu.element'
GROUP BY JSON_EXTRACT(data_info, '$.section_type');

-- 6. Reset all sort_order values to 0 (if needed)
-- UPDATE site_data 
-- SET data_info = JSON_SET(data_info, '$.sort_order', 0)
-- WHERE data_key = 'footer_menu.element';

-- Notes:
-- - The sort_order field is stored in the JSON data_info column
-- - Lower sort_order values appear first in the list
-- - Default sort_order is 0 if not specified
-- - The application code handles sorting in PHP using Laravel's collection sortBy method
-- - Filtering by section_type is handled in the SiteController using whereJsonContains
