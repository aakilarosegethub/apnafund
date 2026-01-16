<?php
/**
 * Fix Page SEO Data - Ensure all fields are properly saved
 * Run this file once to fix any existing page_seo entries
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SiteData;

echo "Checking Page SEO Data...\n\n";

// Get all page_seo elements
$pageSeoItems = SiteData::where('data_key', 'page_seo.element')->get();

if ($pageSeoItems->isEmpty()) {
    echo "No page_seo entries found.\n";
    exit;
}

foreach ($pageSeoItems as $item) {
    echo "Processing ID: {$item->id}\n";
    
    $dataInfo = is_array($item->data_info) ? $item->data_info : (array)$item->data_info;
    
    // Expected fields from site.json
    $expectedFields = [
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image_url',
        'twitter_title',
        'twitter_description',
        'twitter_image_url',
        'canonical_url',
        'meta_robots'
    ];
    
    $updated = false;
    foreach ($expectedFields as $field) {
        if (!isset($dataInfo[$field])) {
            $dataInfo[$field] = '';
            $updated = true;
            echo "  - Added missing field: {$field}\n";
        }
    }
    
    if ($updated) {
        $item->data_info = $dataInfo;
        $item->save();
        echo "  ✓ Updated ID: {$item->id}\n";
    } else {
        echo "  ✓ ID: {$item->id} is already complete\n";
    }
    
    // Display current data
    echo "  Current data:\n";
    foreach ($expectedFields as $field) {
        $value = $dataInfo[$field] ?? '';
        if (!empty($value)) {
            $displayValue = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
            echo "    - {$field}: {$displayValue}\n";
        }
    }
    echo "\n";
}

echo "Done!\n";
