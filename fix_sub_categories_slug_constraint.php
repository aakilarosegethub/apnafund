<?php
/**
 * Fix sub_categories slug unique constraint
 * This script changes the slug unique constraint from global to per-category
 * Run this once: php fix_sub_categories_slug_constraint.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Starting to fix sub_categories slug constraint...\n\n";
    
    // Check if table exists
    if (!Schema::hasTable('sub_categories')) {
        echo "ERROR: sub_categories table does not exist!\n";
        exit(1);
    }
    
    // Check current constraints
    $constraints = DB::select("SHOW INDEX FROM `sub_categories` WHERE Key_name = 'sub_categories_slug_unique'");
    
    if (empty($constraints)) {
        echo "INFO: sub_categories_slug_unique constraint not found. It may have been already removed.\n";
    } else {
        echo "Found existing constraint: sub_categories_slug_unique\n";
        echo "Dropping old constraint...\n";
        
        // Drop the old unique constraint
        DB::statement('ALTER TABLE `sub_categories` DROP INDEX `sub_categories_slug_unique`');
        echo "✓ Old constraint dropped successfully\n\n";
    }
    
    // Check if new constraint already exists
    $newConstraints = DB::select("SHOW INDEX FROM `sub_categories` WHERE Key_name = 'sub_categories_category_slug_unique'");
    
    if (!empty($newConstraints)) {
        echo "INFO: sub_categories_category_slug_unique constraint already exists.\n";
        echo "No changes needed.\n";
    } else {
        echo "Adding new composite unique constraint (category_id, slug)...\n";
        
        // Add new composite unique constraint
        DB::statement('ALTER TABLE `sub_categories` ADD UNIQUE KEY `sub_categories_category_slug_unique` (`category_id`, `slug`)');
        echo "✓ New constraint added successfully\n\n";
    }
    
    echo "========================================\n";
    echo "SUCCESS: Constraint fixed successfully!\n";
    echo "========================================\n";
    echo "\nNow slugs can be same in different categories,\n";
    echo "but must be unique within the same category.\n";
    
} catch (\Exception $e) {
    echo "\n========================================\n";
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "========================================\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
