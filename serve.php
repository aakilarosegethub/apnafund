<?php
/**
 * ApnaFund Development Server Launcher
 * Quick serve script for Laravel project
 */

echo "🚀 Starting ApnaFund Development Server...\n";
echo "📁 Project Directory: " . __DIR__ . "\n";
echo "🌐 Server URL: http://0.0.0.0:9001\n";
echo "⏰ Started at: " . date('Y-m-d H:i:s') . "\n";
echo "=" . str_repeat("=", 50) . "\n";

// Change to project directory
chdir(__DIR__);

// Check if artisan exists
if (!file_exists('artisan')) {
    echo "❌ Error: artisan file not found. Make sure you're in the Laravel project directory.\n";
    exit(1);
}

// Define XAMPP PHP path
$phpPath = '/Applications/XAMPP/xamppfiles/bin/php';

// Start the Laravel development server
echo "🔄 Executing: {$phpPath} artisan serve --host=0.0.0.0 --port=8000\n";
echo "=" . str_repeat("=", 50) . "\n";

// Execute the artisan serve command with XAMPP PHP
passthru("{$phpPath} artisan serve --host=0.0.0.0 --port=9001");
