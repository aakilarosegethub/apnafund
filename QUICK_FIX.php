<?php
/**
 * QUICK FIX SCRIPT - Fix all common 500 errors
 */

echo "<h1>🔧 Quick Fix Script</h1>";
echo "<pre>";

$basePath = __DIR__;
$phpPath = '/Applications/XAMPP/xamppfiles/bin/php';

// Step 1: Create .env file
echo "Step 1: Checking .env file...\n";
$envFile = $basePath . '/.env';
$envTemplate = $basePath . '/env_template.txt';

if (!file_exists($envFile)) {
    if (file_exists($envTemplate)) {
        copy($envTemplate, $envFile);
        echo "✅ Created .env from template\n";
    } else {
        // Create basic .env
        $basicEnv = "APP_NAME=ApnaCrowdfunding\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost/apnacrowdfunding\n\nDB_CONNECTION=sqlite\nDB_DATABASE={$basePath}/database/database.sqlite\n";
        file_put_contents($envFile, $basicEnv);
        echo "✅ Created basic .env file\n";
    }
} else {
    echo "✅ .env file exists\n";
}

// Step 2: Generate APP_KEY if missing
echo "\nStep 2: Checking APP_KEY...\n";
$envContent = file_get_contents($envFile);
if (preg_match('/APP_KEY=(.*)/', $envContent, $matches)) {
    $appKey = trim($matches[1]);
    if (empty($appKey)) {
        echo "⚠️  APP_KEY is empty. Generating...\n";
        exec("cd {$basePath} && {$phpPath} artisan key:generate 2>&1", $output, $return);
        if ($return === 0) {
            echo "✅ APP_KEY generated successfully\n";
        } else {
            echo "❌ Failed to generate APP_KEY\n";
            echo implode("\n", $output) . "\n";
        }
    } else {
        echo "✅ APP_KEY is set\n";
    }
}

// Step 3: Fix storage permissions
echo "\nStep 3: Fixing storage permissions...\n";
$storageDirs = [
    'storage',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "✅ Created: {$dir}\n";
    }
    chmod($fullPath, 0755);
}

// Step 4: Clear all caches
echo "\nStep 4: Clearing caches...\n";
$commands = [
    'config:clear',
    'cache:clear',
    'route:clear',
    'view:clear',
];

foreach ($commands as $cmd) {
    exec("cd {$basePath} && {$phpPath} artisan {$cmd} 2>&1", $output, $return);
    if ($return === 0) {
        echo "✅ Cleared: {$cmd}\n";
    } else {
        echo "⚠️  Warning: {$cmd} - " . implode("\n", $output) . "\n";
    }
}

// Step 5: Check database
echo "\nStep 5: Checking database...\n";
$dbFile = $basePath . '/database/database.sqlite';
if (!file_exists($dbFile)) {
    touch($dbFile);
    chmod($dbFile, 0644);
    echo "✅ Created database.sqlite\n";
} else {
    echo "✅ database.sqlite exists\n";
}

// Step 6: Test Laravel bootstrap
echo "\nStep 6: Testing Laravel bootstrap...\n";
try {
    require $basePath . '/vendor/autoload.php';
    echo "✅ Autoloader OK\n";
    
    $app = require_once $basePath . '/bootstrap/app.php';
    echo "✅ Bootstrap OK\n";
    
    echo "\n✅✅✅ All checks passed! ✅✅✅\n";
    echo "\nNow try accessing: http://localhost/apnacrowdfunding/\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";

