<?php
/**
 * Error Test Script - Check what's causing 500 error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Error Test Script</h1>";
echo "<pre>";

// 1. Check PHP version
echo "1. PHP Version: " . phpversion() . "\n";

// 2. Check if .env exists
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "2. ✅ .env file exists\n";
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'APP_KEY=') !== false) {
        preg_match('/APP_KEY=(.*)/', $envContent, $matches);
        if (!empty(trim($matches[1] ?? ''))) {
            echo "3. ✅ APP_KEY is set\n";
        } else {
            echo "3. ❌ APP_KEY is EMPTY - This is likely the issue!\n";
        }
    } else {
        echo "3. ❌ APP_KEY not found in .env\n";
    }
} else {
    echo "2. ❌ .env file NOT FOUND - This is the issue!\n";
}

// 3. Check vendor directory
if (is_dir(__DIR__ . '/vendor')) {
    echo "4. ✅ vendor directory exists\n";
} else {
    echo "4. ❌ vendor directory NOT FOUND - Run: composer install\n";
}

// 4. Check storage permissions
$storagePath = __DIR__ . '/storage';
if (is_writable($storagePath)) {
    echo "5. ✅ storage is writable\n";
} else {
    echo "5. ❌ storage is NOT writable\n";
}

// 5. Try to load Laravel
echo "\n6. Trying to load Laravel...\n";
try {
    require __DIR__ . '/vendor/autoload.php';
    echo "   ✅ Autoloader loaded\n";
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "   ✅ Bootstrap loaded\n";
    
    echo "\n✅ Laravel can be loaded!\n";
} catch (Exception $e) {
    echo "   ❌ Error loading Laravel: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "</pre>";

