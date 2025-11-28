<?php
// Test script for SendGrid sender configuration
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use App\Models\User;
use App\Notify\Email;

echo "<h2>SendGrid Sender Configuration Test</h2>";

// Get current settings
$setting = Setting::first();
echo "<h3>Current Email Configuration:</h3>";
echo "<p><strong>Email Method:</strong> " . ($setting->mail_config->name ?? 'Not set') . "</p>";
echo "<p><strong>Global Email From:</strong> " . ($setting->email_from ?? 'Not set') . "</p>";

if (isset($setting->mail_config->sender_email)) {
    echo "<p><strong>SendGrid Sender Email:</strong> " . $setting->mail_config->sender_email . "</p>";
} else {
    echo "<p><strong>SendGrid Sender Email:</strong> Not configured (will use global email_from)</p>";
}

if (isset($setting->mail_config->appkey)) {
    echo "<p><strong>SendGrid API Key:</strong> " . substr($setting->mail_config->appkey, 0, 10) . "...</p>";
} else {
    echo "<p><strong>SendGrid API Key:</strong> Not configured</p>";
}

// Test email sending with SendGrid
if ($setting->mail_config->name === 'sendgrid' && isset($setting->mail_config->appkey)) {
    echo "<h3>Testing SendGrid Email:</h3>";
    
    try {
        $email = new Email();
        $email->email = 'test@example.com'; // Replace with actual test email
        $email->receiverName = 'Test User';
        $email->subject = 'SendGrid Sender Test';
        $email->finalMessage = '<h1>Test Email</h1><p>This is a test email to verify SendGrid sender configuration.</p>';
        $email->setting = $setting;
        
        // This will test the sendSendGridMail method
        $email->sendSendGridMail();
        
        echo "<p style='color: green;'><strong>✅ SendGrid email configuration test passed!</strong></p>";
        echo "<p><strong>Sender Email Used:</strong> " . ($setting->mail_config->sender_email ?? $setting->email_from) . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>❌ Error testing SendGrid:</strong> " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h3>SendGrid Configuration:</h3>";
    echo "<p style='color: orange;'><strong>⚠️ SendGrid is not configured or not selected as email method.</strong></p>";
    echo "<p>To test SendGrid sender configuration:</p>";
    echo "<ul>";
    echo "<li>Go to Admin Panel → Notification Settings → Email</li>";
    echo "<li>Select 'SendGrid API' as email method</li>";
    echo "<li>Enter your SendGrid API key</li>";
    echo "<li>Enter your desired sender email address</li>";
    echo "<li>Save the configuration</li>";
    echo "</ul>";
}

echo "<h3>Configuration Summary:</h3>";
echo "<p>The SendGrid sender configuration has been updated to:</p>";
echo "<ul>";
echo "<li>✅ Added sender email field to SendGrid configuration form</li>";
echo "<li>✅ Updated validation to require sender email for SendGrid</li>";
echo "<li>✅ Modified Email.php to use SendGrid-specific sender email</li>";
echo "<li>✅ Fallback to global email_from if SendGrid sender email not set</li>";
echo "</ul>";
?>
