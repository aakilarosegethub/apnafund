<?php
/**
 * Debug Social Login - Detailed Error Checking
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>🔍 Social Login Debug Page</h1>";

echo "<h2>📊 Database Schema Check:</h2>";

// Check if social login columns exist
$columns = Schema::getColumnListing('users');
$requiredColumns = ['provider', 'provider_id', 'avatar'];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Column</th><th>Exists</th><th>Type</th></tr>";

foreach ($requiredColumns as $column) {
    $exists = in_array($column, $columns);
    $type = $exists ? '✅ Exists' : '❌ Missing';
    echo "<tr><td>$column</td><td>$type</td><td>" . ($exists ? 'String' : 'N/A') . "</td></tr>";
}

echo "</table>";

echo "<h2>🧪 Test User Creation:</h2>";

try {
    // Test creating a user with minimal data
    $testUser = User::create([
        'username' => 'test_social_' . time(),
        'email' => 'test_social_' . time() . '@example.com',
        'firstname' => 'Test',
        'lastname' => 'User',
        'name' => 'Test User',
        'password' => bcrypt('password'),
        'provider' => 'test',
        'provider_id' => 'test_' . time(),
        'avatar' => 'https://example.com/avatar.jpg',
        'email_verified_at' => now(),
        'status' => 1,
        'ev' => 1,
        'sv' => 1,
        'last_login_at' => now(),
        'tc' => 1,
    ]);
    
    echo "<p>✅ Test user created successfully!</p>";
    echo "<p>User ID: " . $testUser->id . "</p>";
    
    // Delete test user
    $testUser->delete();
    echo "<p>✅ Test user deleted successfully!</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error creating test user: " . $e->getMessage() . "</p>";
    echo "<p>Error details: " . $e->getTraceAsString() . "</p>";
}

echo "<h2>📋 User Table Structure:</h2>";

try {
    $columns = DB::select("DESCRIBE users");
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column->Field . "</td>";
        echo "<td>" . $column->Type . "</td>";
        echo "<td>" . $column->Null . "</td>";
        echo "<td>" . $column->Key . "</td>";
        echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
        echo "<td>" . $column->Extra . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Error getting table structure: " . $e->getMessage() . "</p>";
}

echo "<h2>🔧 Laravel Configuration:</h2>";

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";

$configs = [
    'APP_URL' => env('APP_URL'),
    'DB_CONNECTION' => env('DB_CONNECTION'),
    'DB_DATABASE' => env('DB_DATABASE'),
    'FACEBOOK_CLIENT_ID' => env('FACEBOOK_CLIENT_ID'),
    'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID'),
];

foreach ($configs as $key => $value) {
    echo "<tr><td>$key</td><td>" . ($value ? $value : '❌ Not Set') . "</td></tr>";
}

echo "</table>";

echo "<h2>📝 Recent Logs:</h2>";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = array_slice(explode("\n", $logs), -20);
    
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: scroll;'>";
    foreach ($recentLogs as $log) {
        if (strpos($log, 'social') !== false || strpos($log, 'Social') !== false) {
            echo htmlspecialchars($log) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>❌ Log file not found</p>";
}

echo "<h2>🚀 Next Steps:</h2>";
echo "<ol>";
echo "<li>Check if social login columns exist in users table</li>";
echo "<li>Run migration if columns are missing: <code>php artisan migrate</code></li>";
echo "<li>Check Laravel logs for detailed error messages</li>";
echo "<li>Verify environment variables are set correctly</li>";
echo "<li>Test social login again</li>";
echo "</ol>";

echo "<p><a href='/test_social_login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧪 Go to Social Login Test</a></p>";
?>
