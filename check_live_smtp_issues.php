<?php
// Live SMTP Issues Checker
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Setting;
use PHPMailer\PHPMailer\PHPMailer;

echo "=== LIVE SMTP ISSUES CHECKER ===\n\n";

$setting = Setting::first();
$config = $setting->mail_config ?? null;

if (!$config || $config->name !== 'smtp') {
    echo "❌ SMTP is not configured\n";
    exit;
}

echo "📋 Current Configuration:\n";
echo "Host: " . ($config->host ?? 'NOT SET') . "\n";
echo "Port: " . ($config->port ?? 'NOT SET') . "\n";
echo "Encryption: " . strtoupper($config->enc ?? 'NOT SET') . "\n";
echo "Username: " . ($config->username ?? 'NOT SET') . "\n";
echo "Email From: " . ($setting->email_from ?? 'NOT SET') . "\n\n";

$issues = [];

// Issue 1: Port and Encryption Mismatch
if (($config->port ?? 0) == 587 && ($config->enc ?? '') == 'ssl') {
    $issues[] = "❌ CRITICAL: Port 587 requires TLS, not SSL! This will cause connection failures.";
}

if (($config->port ?? 0) == 465 && ($config->enc ?? '') == 'tls') {
    $issues[] = "❌ CRITICAL: Port 465 requires SSL, not TLS! This will cause connection failures.";
}

// Issue 2: From email mismatch
if (!empty($config->username) && !empty($setting->email_from)) {
    if (strtolower(trim($config->username)) !== strtolower(trim($setting->email_from))) {
        $issues[] = "⚠️ WARNING: From email doesn't match SMTP username. Hostinger requires them to match.";
    }
}

// Issue 3: Missing configuration
if (empty($config->host)) $issues[] = "❌ SMTP Host is missing";
if (empty($config->port)) $issues[] = "❌ SMTP Port is missing";
if (empty($config->username)) $issues[] = "❌ SMTP Username is missing";
if (empty($config->password)) $issues[] = "❌ SMTP Password is missing";
if (empty($config->enc)) $issues[] = "❌ Encryption type is missing";

// Issue 4: Test connection
echo "🔍 Testing SMTP Connection...\n";
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $config->host;
    $mail->SMTPAuth = true;
    $mail->Username = $config->username;
    $mail->Password = $config->password;
    $mail->SMTPDebug = 0; // Set to 2 for verbose output
    $mail->Timeout = 10;
    
    if ($config->enc == 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }
    
    $mail->Port = $config->port;
    
    // Just connect, don't send
    $mail->smtpConnect();
    $mail->smtpClose();
    
    echo "✅ SMTP Connection: SUCCESS\n\n";
    
} catch (\Exception $e) {
    $issues[] = "❌ SMTP Connection Failed: " . $e->getMessage();
    echo "❌ SMTP Connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Display all issues
if (!empty($issues)) {
    echo "🚨 ISSUES FOUND:\n";
    echo str_repeat("=", 50) . "\n";
    foreach ($issues as $issue) {
        echo $issue . "\n";
    }
    echo str_repeat("=", 50) . "\n\n";
    
    echo "💡 SOLUTIONS:\n";
    echo "1. Port 587 = Use TLS (not SSL)\n";
    echo "2. Port 465 = Use SSL (not TLS)\n";
    echo "3. From Email must match SMTP Username for Hostinger\n";
    echo "4. Check firewall allows outbound connections on port 587/465\n";
    echo "5. Verify SMTP credentials are correct\n";
    echo "6. Check if email service is enabled (ea = 1)\n\n";
} else {
    echo "✅ No issues found! Configuration looks good.\n\n";
}

echo "📝 To fix issues:\n";
echo "1. Go to Admin Panel → Notification → Email\n";
echo "2. Update SMTP settings\n";
echo "3. Make sure Port 587 uses TLS (not SSL)\n";
echo "4. Make sure From Email matches SMTP Username\n";
echo "5. Save and test again\n";

?>


