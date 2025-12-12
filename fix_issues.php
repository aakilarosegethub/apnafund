<?php
/**
 * Fix Common Issues Script
 */

echo "🔧 Fixing Common Issues...\n\n";

$basePath = __DIR__;

// 1. Create .env file if it doesn't exist
$envFile = $basePath . '/.env';
if (!file_exists($envFile)) {
    echo "📝 Creating .env file...\n";
    $envTemplate = $basePath . '/env_template.txt';
    if (file_exists($envTemplate)) {
        copy($envTemplate, $envFile);
        echo "✅ .env file created from template\n";
    } else {
        echo "⚠️  env_template.txt not found. Please create .env manually.\n";
    }
} else {
    echo "✅ .env file exists\n";
}

// 2. Check APP_KEY
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'APP_KEY=') !== false && preg_match('/APP_KEY=(.*)/', $envContent, $matches)) {
        if (empty(trim($matches[1]))) {
            echo "⚠️  APP_KEY is empty. Run: php artisan key:generate\n";
        } else {
            echo "✅ APP_KEY is set\n";
        }
    }
}

// 3. Clear caches
echo "\n🧹 Clearing caches...\n";
$commands = [
    'cache:clear',
    'config:clear',
    'route:clear',
    'view:clear',
];

$phpPath = '/Applications/XAMPP/xamppfiles/bin/php';
foreach ($commands as $cmd) {
    $output = [];
    $returnVar = 0;
    exec("$phpPath artisan $cmd 2>&1", $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ Cleared: $cmd\n";
    } else {
        echo "⚠️  Failed: $cmd\n";
    }
}

// 4. Check storage permissions
echo "\n📁 Checking storage permissions...\n";
$storageDirs = [
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
];

foreach ($storageDirs as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "✅ Created: $dir\n";
    } else {
        chmod($fullPath, 0755);
        echo "✅ Permissions OK: $dir\n";
    }
}

// 5. Check database.sqlite
echo "\n💾 Checking database...\n";
$dbFile = $basePath . '/database/database.sqlite';
if (!file_exists($dbFile)) {
    touch($dbFile);
    chmod($dbFile, 0644);
    echo "✅ Created database.sqlite\n";
} else {
    echo "✅ database.sqlite exists\n";
}

echo "\n✨ Done! Try accessing http://localhost/apnacrowdfunding/\n";
echo "If still having issues, check:\n";
echo "1. XAMPP Apache is running\n";
echo "2. .env file has APP_KEY (run: php artisan key:generate)\n";
echo "3. Run migrations: php artisan migrate\n";

