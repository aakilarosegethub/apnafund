<?php
// SMTP Email Test Script
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<!DOCTYPE html>
<html>
<head>
    <title>SMTP Email Test - ApnaFund</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        h3 { color: #666; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; border-radius: 4px; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0; border-radius: 4px; color: #721c24; }
        .warning { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 15px 0; border-radius: 4px; color: #856404; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .label { font-weight: bold; color: #555; }
        .value { color: #333; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
        hr { border: none; border-top: 2px solid #eee; margin: 30px 0; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>📧 SMTP Email Test - ApnaFund</h1>";

// Get current settings
$setting = Setting::first();

if (!$setting) {
    echo "<div class='error'><strong>❌ Error:</strong> No settings found in database!</div>";
    echo "</div></body></html>";
    exit;
}

echo "<h2>📋 Current Email Configuration</h2>";

// Check if email is enabled
$emailEnabled = $setting->ea ?? false;
echo "<div class='" . ($emailEnabled ? 'success' : 'warning') . "'>";
echo "<strong>" . ($emailEnabled ? '✅' : '⚠️') . " Email Status:</strong> " . ($emailEnabled ? 'ENABLED' : 'DISABLED');
echo "</div>";

// Check email method
$mailConfig = $setting->mail_config ?? null;
$emailMethod = $mailConfig->name ?? 'Not Set';

echo "<table>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td class='label'>Email Method</td><td class='value'><code>" . htmlspecialchars($emailMethod) . "</code></td></tr>";
echo "<tr><td class='label'>Email From</td><td class='value'>" . htmlspecialchars($setting->email_from ?? 'Not Set') . "</td></tr>";
echo "<tr><td class='label'>Site Name</td><td class='value'>" . htmlspecialchars($setting->site_name ?? 'Not Set') . "</td></tr>";

if ($emailMethod === 'smtp' && $mailConfig) {
    echo "<tr><td class='label'>SMTP Host</td><td class='value'><code>" . htmlspecialchars($mailConfig->host ?? 'Not Set') . "</code></td></tr>";
    echo "<tr><td class='label'>SMTP Port</td><td class='value'><code>" . htmlspecialchars($mailConfig->port ?? 'Not Set') . "</code></td></tr>";
    echo "<tr><td class='label'>Encryption</td><td class='value'><code>" . strtoupper(htmlspecialchars($mailConfig->enc ?? 'Not Set')) . "</code></td></tr>";
    echo "<tr><td class='label'>SMTP Username</td><td class='value'>" . htmlspecialchars($mailConfig->username ?? 'Not Set') . "</td></tr>";
    echo "<tr><td class='label'>SMTP Password</td><td class='value'>" . (isset($mailConfig->password) && $mailConfig->password ? str_repeat('*', min(strlen($mailConfig->password), 10)) : 'Not Set') . "</td></tr>";
} else {
    echo "<tr><td colspan='2' class='warning'><strong>⚠️ Warning:</strong> Email method is not set to SMTP. Current method: <code>" . htmlspecialchars($emailMethod) . "</code></td></tr>";
}

echo "</table>";

// Validation checks
$errors = [];
$warnings = [];

if (!$emailEnabled) {
    $errors[] = "Email is disabled in settings. Please enable it in Admin Panel → Notification → Email";
}

if ($emailMethod !== 'smtp') {
    $errors[] = "Email method is not set to SMTP. Current method: " . $emailMethod;
}

if ($emailMethod === 'smtp' && $mailConfig) {
    if (empty($mailConfig->host)) {
        $errors[] = "SMTP Host is not configured";
    }
    if (empty($mailConfig->port)) {
        $errors[] = "SMTP Port is not configured";
    }
    if (empty($mailConfig->username)) {
        $errors[] = "SMTP Username is not configured";
    }
    if (empty($mailConfig->password)) {
        $errors[] = "SMTP Password is not configured";
    }
    if (empty($mailConfig->enc)) {
        $warnings[] = "SMTP Encryption is not set (should be 'ssl' or 'tls')";
    }
    
    // Check if From email matches SMTP username (required by most SMTP servers)
    if (!empty($mailConfig->username) && !empty($setting->email_from)) {
        $smtpEmail = strtolower(trim($mailConfig->username));
        $fromEmail = strtolower(trim($setting->email_from));
        if ($smtpEmail !== $fromEmail) {
            $errors[] = "⚠️ IMPORTANT: 'From Email' must match 'SMTP Username' for Hostinger SMTP. Current: From='{$setting->email_from}' but SMTP Username='{$mailConfig->username}'. Please update 'Email From' to '{$mailConfig->username}' in Admin Panel → Notification → Email";
        }
    }
}

if (empty($setting->email_from)) {
    $errors[] = "Email From address is not configured";
}

// Display errors and warnings
if (!empty($errors)) {
    echo "<h2>❌ Configuration Errors</h2>";
    foreach ($errors as $error) {
        echo "<div class='error'><strong>Error:</strong> " . htmlspecialchars($error) . "</div>";
    }
}

if (!empty($warnings)) {
    echo "<h2>⚠️ Configuration Warnings</h2>";
    foreach ($warnings as $warning) {
        echo "<div class='warning'><strong>Warning:</strong> " . htmlspecialchars($warning) . "</div>";
    }
}

// Test email sending
if (empty($errors) && $emailMethod === 'smtp') {
    echo "<hr>";
    echo "<h2>🧪 Testing SMTP Email</h2>";
    
    // Get test email from query parameter or use default
    $testEmail = $_GET['email'] ?? 'diyandani22@gmail.com';
    
    echo "<div class='info'>";
    echo "<strong>ℹ️ Test Email Address:</strong> " . htmlspecialchars($testEmail);
    echo "<br><small>You can change this by adding <code>?email=your@email.com</code> to the URL</small>";
    echo "</div>";
    
    try {
        // Use PHPMailer directly for testing
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $mailConfig->host;
        $mail->SMTPAuth = true;
        $mail->Username = $mailConfig->username;
        $mail->Password = $mailConfig->password;
        
        if ($mailConfig->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        
        $mail->Port = $mailConfig->port;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0; // Set to 2 for verbose debug output
        
        // Recipients
        // Use SMTP username as From address (required by Hostinger and most SMTP servers)
        $fromEmail = $mailConfig->username ?? $setting->email_from;
        $mail->setFrom($fromEmail, $setting->site_name);
        $mail->addAddress($testEmail, 'Test User');
        $mail->addReplyTo($fromEmail, $setting->site_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'SMTP Test Email - ApnaFund';
        $mail->Body = '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
                <h2 style="color: #007bff;">✅ SMTP Email Test Successful!</h2>
                <p>This is a test email sent from <strong>ApnaFund</strong> using SMTP configuration.</p>
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                <h3>Email Configuration Details:</h3>
                <ul>
                    <li><strong>Method:</strong> SMTP</li>
                    <li><strong>Host:</strong> ' . htmlspecialchars($mailConfig->host ?? 'N/A') . '</li>
                    <li><strong>Port:</strong> ' . htmlspecialchars($mailConfig->port ?? 'N/A') . '</li>
                    <li><strong>Encryption:</strong> ' . strtoupper(htmlspecialchars($mailConfig->enc ?? 'N/A')) . '</li>
                    <li><strong>From:</strong> ' . htmlspecialchars($setting->email_from ?? 'N/A') . '</li>
                    <li><strong>Sent At:</strong> ' . date('Y-m-d H:i:s') . '</li>
                </ul>
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="color: #666; font-size: 12px;">If you received this email, your SMTP configuration is working correctly! 🎉</p>
            </div>
        </body>
        </html>';
        
        echo "<div class='info'><strong>🔄 Attempting to send email via SMTP...</strong></div>";
        echo "<div class='info'>Connecting to: <code>" . htmlspecialchars($mailConfig->host) . ":" . htmlspecialchars($mailConfig->port) . "</code></div>";
        
        // Send email
        $mail->send();
        
        echo "<div class='success'>";
        echo "<strong>✅ SUCCESS!</strong> Email sent successfully!<br>";
        echo "Check your inbox at: <strong>" . htmlspecialchars($testEmail) . "</strong><br>";
        echo "If you don't see it, check your spam/junk folder.";
        echo "</div>";
        
        // Check email logs
        echo "<h3>📝 Email Logs</h3>";
        echo "<div class='info'>Check <code>storage/logs/laravel.log</code> for detailed email sending logs.</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<strong>❌ ERROR:</strong> Failed to send email<br>";
        echo "<strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>";
        
        // Common SMTP errors and solutions
        echo "<strong>💡 Common SMTP Issues & Solutions:</strong><br>";
        echo "<ul>";
        
        $errorMsg = strtolower($e->getMessage());
        
        if (strpos($errorMsg, 'connection') !== false || strpos($errorMsg, 'timeout') !== false) {
            echo "<li><strong>Connection Error:</strong> Check if SMTP host and port are correct</li>";
            echo "<li>Verify your firewall is not blocking the connection</li>";
            echo "<li>Try using different port (587 for TLS, 465 for SSL)</li>";
        }
        
        if (strpos($errorMsg, 'authentication') !== false || strpos($errorMsg, 'login') !== false) {
            echo "<li><strong>Authentication Error:</strong> Check SMTP username and password</li>";
            echo "<li>Some email providers require 'App Password' instead of regular password</li>";
            echo "<li>For Gmail: Enable 2FA and use App Password</li>";
        }
        
        if (strpos($errorMsg, 'ssl') !== false || strpos($errorMsg, 'tls') !== false) {
            echo "<li><strong>Encryption Error:</strong> Check encryption setting (SSL/TLS)</li>";
            echo "<li>Port 465 usually requires SSL</li>";
            echo "<li>Port 587 usually requires TLS</li>";
        }
        
        if (strpos($errorMsg, 'from') !== false) {
            echo "<li><strong>From Address Error:</strong> Verify 'Email From' address is valid</li>";
            echo "<li>Some SMTP servers require 'From' to match SMTP username</li>";
        }
        
        echo "<li>Check email logs: <code>storage/logs/laravel.log</code></li>";
        echo "</ul>";
        echo "</div>";
        
        // Show full error details
        echo "<details style='margin-top: 15px;'>";
        echo "<summary style='cursor: pointer; color: #007bff;'><strong>Show Full Error Details</strong></summary>";
        echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
        echo htmlspecialchars($e->getTraceAsString());
        echo "</pre>";
        echo "</details>";
    }
} else {
    echo "<hr>";
    echo "<h2>⚠️ Cannot Test Email</h2>";
    echo "<div class='warning'>";
    echo "Please fix the configuration errors above before testing email.";
    echo "</div>";
}

echo "<hr>";
echo "<h2>📚 Quick Links</h2>";
echo "<a href='admin' class='btn'>Go to Admin Panel</a>";
echo "<a href='test_smtp_email.php' class='btn'>Refresh Test</a>";
echo "<a href='test_smtp_email.php?email=" . urlencode($testEmail ?? 'diyandani22@gmail.com') . "' class='btn'>Test Again</a>";

echo "</div></body></html>";
?>

