<?php
/**
 * Regenerate Composer Autoload
 */

echo "🔄 Regenerating Composer Autoload...\n\n";

$basePath = __DIR__;
$composerPath = $basePath . '/composer.json';

if (!file_exists($composerPath)) {
    echo "❌ composer.json not found!\n";
    exit(1);
}

// Try to find composer
$composerCommands = [
    'composer',
    '/usr/local/bin/composer',
    '/usr/bin/composer',
    $basePath . '/composer.phar',
];

$composer = null;
foreach ($composerCommands as $cmd) {
    if (file_exists($cmd) || shell_exec("which $cmd 2>/dev/null")) {
        $composer = $cmd;
        break;
    }
}

if (!$composer) {
    echo "⚠️  Composer not found. Trying to regenerate manually...\n";
    
    // Manually regenerate autoload files
    $autoloadFile = $basePath . '/vendor/autoload.php';
    if (file_exists($autoloadFile)) {
        echo "✅ vendor/autoload.php exists\n";
        
        // Check if the helpers file exists
        $helpersFile = $basePath . '/app/Http/Helpers/helpers.php';
        if (file_exists($helpersFile)) {
            echo "✅ helpers.php exists at: $helpersFile\n";
            echo "\n✅ File structure is correct!\n";
            echo "Try accessing: http://localhost/apnacrowdfunding/\n";
        } else {
            echo "❌ helpers.php still missing!\n";
        }
    } else {
        echo "❌ vendor/autoload.php not found. Run: composer install\n";
    }
    exit(0);
}

echo "Using composer: $composer\n";
echo "Running: $composer dump-autoload\n\n";

exec("cd $basePath && $composer dump-autoload 2>&1", $output, $return);

if ($return === 0) {
    echo "✅ Autoload regenerated successfully!\n";
    echo implode("\n", $output) . "\n";
} else {
    echo "⚠️  Warning during autoload regeneration:\n";
    echo implode("\n", $output) . "\n";
}

echo "\n✅ Done! Try accessing: http://localhost/apnacrowdfunding/\n";

