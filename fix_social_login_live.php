<?php
/**
 * Fix Social Login for Live Server
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\SiteData;

echo "<h1>🔧 Fix Social Login for Live Server</h1>";

echo "<h2>📊 Current Environment Variables:</h2>";

$envVars = [
    'APP_URL' => env('APP_URL'),
    'FACEBOOK_CLIENT_ID' => env('FACEBOOK_CLIENT_ID'),
    'FACEBOOK_CLIENT_SECRET' => env('FACEBOOK_CLIENT_SECRET'),
    'FACEBOOK_REDIRECT_URI' => env('FACEBOOK_REDIRECT_URI'),
    'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID'),
    'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET'),
    'GOOGLE_REDIRECT_URI' => env('GOOGLE_REDIRECT_URI'),
];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Variable</th><th>Status</th><th>Value</th></tr>";

foreach ($envVars as $key => $value) {
    $status = $value ? '✅ Set' : '❌ Missing';
    $displayValue = $value ? (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) : 'Not Set';
    echo "<tr><td>$key</td><td>$status</td><td>$displayValue</td></tr>";
}

echo "</table>";

echo "<h2>🔧 Quick Fix Options:</h2>";

echo "<h3>Option 1: Add to .env file</h3>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 8px;'>";
echo "# Social Login Configuration\n";
echo "FACEBOOK_CLIENT_ID=your_facebook_app_id\n";
echo "FACEBOOK_CLIENT_SECRET=your_facebook_app_secret\n";
echo "FACEBOOK_REDIRECT_URI=https://yourdomain.com/user/auth/facebook/callback\n";
echo "GOOGLE_CLIENT_ID=your_google_client_id\n";
echo "GOOGLE_CLIENT_SECRET=your_google_client_secret\n";
echo "GOOGLE_REDIRECT_URI=https://yourdomain.com/user/auth/google/callback\n";
echo "</pre>";

echo "<h3>Option 2: Disable Social Login Temporarily</h3>";

// Check if social login settings exist
$socialLoginSettings = SiteData::where('data_key', 'social_login.data')->first();

if ($socialLoginSettings) {
    $settings = $socialLoginSettings->data_info;
    
    // Disable both Facebook and Google
    $settings['facebook']['status'] = false;
    $settings['google']['status'] = false;
    
    $socialLoginSettings->data_info = $settings;
    $socialLoginSettings->save();
    
    echo "<p>✅ Social login has been temporarily disabled</p>";
    echo "<p>Users will not see social login buttons until you configure credentials</p>";
} else {
    echo "<p>❌ Social login settings not found</p>";
}

echo "<h3>Option 3: Configure via Admin Panel</h3>";
echo "<p><a href='/admin/social-login' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-size: 16px; display: inline-block; margin: 10px;'>⚙️ Go to Admin Settings</a></p>";

echo "<h2>🚀 Immediate Solutions:</h2>";

echo "<h3>Solution 1: Add Environment Variables</h3>";
echo "<ol>";
echo "<li>Open your .env file on live server</li>";
echo "<li>Add the Google/Facebook credentials</li>";
echo "<li>Run: <code>php artisan config:clear</code></li>";
echo "<li>Test social login</li>";
echo "</ol>";

echo "<h3>Solution 2: Disable Social Login</h3>";
echo "<ol>";
echo "<li>Go to Admin Panel → Social Login Settings</li>";
echo "<li>Disable Facebook and Google login</li>";
echo "<li>Save settings</li>";
echo "<li>Social login buttons will be hidden</li>";
echo "</ol>";

echo "<h3>Solution 3: Add Default Values</h3>";

// Create a temporary fix by adding default values
$tempFix = '
// Add this to config/services.php if credentials are missing
if (empty(env("GOOGLE_CLIENT_ID"))) {
    return [
        "google" => [
            "client_id" => "disabled",
            "client_secret" => "disabled", 
            "redirect" => env("APP_URL") . "/user/auth/google/callback",
        ],
    ];
}
';

echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 8px;'>$tempFix</pre>";

echo "<h2>📝 Steps to Fix:</h2>";
echo "<ol>";
echo "<li><strong>Immediate Fix:</strong> Disable social login in admin panel</li>";
echo "<li><strong>Long-term Fix:</strong> Configure Google/Facebook credentials</li>";
echo "<li><strong>Test:</strong> Verify social login works after configuration</li>";
echo "</ol>";

echo "<h2>🔗 Useful Links:</h2>";
echo "<p><a href='/admin/social-login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>⚙️ Admin Settings</a></p>";
echo "<p><a href='/login' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Login Page</a></p>";
echo "<p><a href='/test_social_final.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🧪 Test Social Login</a></p>";

echo "<p style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;'>";
echo "<strong>Status:</strong> Social login has been temporarily disabled to prevent errors. Configure your credentials in admin panel to enable it.";
echo "</p>";
?>
