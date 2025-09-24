<?php
// Simple email test script
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use App\Models\User;
use App\Notifications\WelcomeNotification;

echo "<h2>ApnaFund Email Test</h2>";

// Get current settings
$setting = Setting::first();
echo "<h3>Email Configuration:</h3>";
echo "<p><strong>Email Enabled:</strong> " . ($setting->ea ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Email Method:</strong> " . ($setting->mail_config->name ?? 'Not set') . "</p>";
echo "<p><strong>Email From:</strong> " . ($setting->email_from ?? 'Not set') . "</p>";

if (isset($setting->mail_config->appkey)) {
    echo "<p><strong>SendGrid API Key:</strong> " . substr($setting->mail_config->appkey, 0, 10) . "...</p>";
} else {
    echo "<p><strong>SendGrid API Key:</strong> Not configured</p>";
}

// Find user with email diyandani22@gmail.com
$user = User::where('email', 'diyandani22@gmail.com')->first();

if ($user) {
    echo "<h3>User Found:</h3>";
    echo "<p><strong>ID:</strong> " . $user->id . "</p>";
    echo "<p><strong>Name:</strong> " . $user->firstname . " " . $user->lastname . "</p>";
    echo "<p><strong>Email:</strong> " . $user->email . "</p>";
    echo "<p><strong>Username:</strong> " . $user->username . "</p>";
    
    // Test sending welcome email
    echo "<h3>Testing Welcome Email:</h3>";
    try {
        $user->notify(new WelcomeNotification($user));
        echo "<p style='color: green;'><strong>✅ Welcome email sent successfully!</strong></p>";
        echo "<p>Check the email logs in storage/logs/laravel.log for details.</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>❌ Error sending email:</strong> " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h3>User Not Found:</h3>";
    echo "<p>No user found with email: diyandani22@gmail.com</p>";
    
    // Show all users
    $allUsers = User::latest()->take(5)->get();
    echo "<h4>Recent Users:</h4>";
    foreach ($allUsers as $u) {
        echo "<p>- " . $u->email . " (" . $u->firstname . " " . $u->lastname . ")</p>";
    }
}

echo "<hr>";
echo "<p><a href='admin'>Go to Admin Panel</a></p>";
?>
