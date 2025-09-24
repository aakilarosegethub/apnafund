<?php
/**
 * Social Login Test Page
 * This page helps test social login functionality
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Models\SiteData;

echo "<h1>🔐 Social Login Test Page</h1>";

// Check if social login is configured
$socialLoginSettings = SiteData::where('data_key', 'social_login.data')->first();

if (!$socialLoginSettings) {
    echo "<p>❌ Social login settings not found. Please configure in admin panel first.</p>";
    exit;
}

$settings = $socialLoginSettings->data_info;

echo "<h2>📊 Current Configuration:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Provider</th><th>Status</th><th>Client ID</th><th>Redirect URI</th></tr>";

// Facebook
echo "<tr>";
echo "<td>Facebook</td>";
echo "<td>" . ($settings['facebook']['status'] ? '✅ Enabled' : '❌ Disabled') . "</td>";
echo "<td>" . (empty($settings['facebook']['client_id']) ? '❌ Not Set' : '✅ Set') . "</td>";
echo "<td>" . $settings['facebook']['redirect_uri'] . "</td>";
echo "</tr>";

// Google
echo "<tr>";
echo "<td>Google</td>";
echo "<td>" . ($settings['google']['status'] ? '✅ Enabled' : '❌ Disabled') . "</td>";
echo "<td>" . (empty($settings['google']['client_id']) ? '❌ Not Set' : '✅ Set') . "</td>";
echo "<td>" . $settings['google']['redirect_uri'] . "</td>";
echo "</tr>";

echo "</table>";

echo "<h2>🔗 Test Links:</h2>";

if ($settings['facebook']['status'] && !empty($settings['facebook']['client_id'])) {
    echo "<p><a href='/user/auth/facebook' style='background: #1877f2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔵 Test Facebook Login</a></p>";
} else {
    echo "<p>❌ Facebook login not configured</p>";
}

if ($settings['google']['status'] && !empty($settings['google']['client_id'])) {
    echo "<p><a href='/user/auth/google' style='background: #db4437; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔴 Test Google Login</a></p>";
} else {
    echo "<p>❌ Google login not configured</p>";
}

echo "<h2>👥 Recent Social Login Users:</h2>";

$socialUsers = User::whereNotNull('provider')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($socialUsers->count() > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Name</th><th>Email</th><th>Provider</th><th>Created At</th><th>Last Login</th></tr>";
    
    foreach ($socialUsers as $user) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user->name ?? $user->firstname . ' ' . $user->lastname) . "</td>";
        echo "<td>" . htmlspecialchars($user->email) . "</td>";
        echo "<td>" . ucfirst($user->provider) . "</td>";
        echo "<td>" . $user->created_at->format('Y-m-d H:i:s') . "</td>";
        echo "<td>" . ($user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No social login users found yet.</p>";
}

echo "<h2>⚙️ Environment Variables:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

$envVars = [
    'FACEBOOK_CLIENT_ID',
    'FACEBOOK_CLIENT_SECRET',
    'FACEBOOK_REDIRECT_URI',
    'GOOGLE_CLIENT_ID',
    'GOOGLE_CLIENT_SECRET',
    'GOOGLE_REDIRECT_URI',
    'APP_URL'
];

foreach ($envVars as $var) {
    $value = env($var);
    echo "<tr>";
    echo "<td>" . $var . "</td>";
    echo "<td>" . (empty($value) ? '❌ Not Set' : '✅ ' . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value)) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>📝 Instructions:</h2>";
echo "<ol>";
echo "<li>Make sure all environment variables are set correctly</li>";
echo "<li>Configure Facebook and Google apps with correct redirect URIs</li>";
echo "<li>Enable social login in admin panel</li>";
echo "<li>Test the login links above</li>";
echo "</ol>";

echo "<p><a href='/admin/social-login' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>⚙️ Go to Admin Settings</a></p>";
echo "<p><a href='/login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Go to Login Page</a></p>";
?>
