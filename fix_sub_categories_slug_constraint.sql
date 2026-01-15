-- ============================================================
-- Fix sub_categories slug unique constraint
-- ============================================================
-- This SQL changes the slug unique constraint from global to per-category
-- Now same slug can exist in different categories
-- But slug must be unique within the same category
-- ============================================================

-- Step 1: Drop the existing global unique constraint on slug
ALTER TABLE `sub_categories` DROP INDEX `sub_categories_slug_unique`;

-- Step 2: Add composite unique constraint on (category_id, slug)
-- This allows same slug in different categories but unique within same category
ALTER TABLE `sub_categories` ADD UNIQUE KEY `sub_categories_category_slug_unique` (`category_id`, `slug`);

-- ============================================================
-- Verification Query (Optional - run to check)
-- ============================================================
-- SHOW INDEX FROM `sub_categories` WHERE Key_name LIKE '%slug%';
-- ============================================================
