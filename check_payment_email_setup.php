<?php

/**
 * Payment Email Setup Checker
 * This script checks if payment email templates exist and are properly configured
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Constants\ManageStatus;

echo "========================================\n";
echo "Payment Email Setup Checker\n";
echo "========================================\n\n";

// Check Email Templates
echo "1. Checking Email Templates...\n";
echo "----------------------------------------\n";

$adminTemplate = NotificationTemplate::where('act', 'ADMIN_PAYMENT_SUCCESS')->first();
$userTemplate = NotificationTemplate::where('act', 'USER_PAYMENT_SUCCESS')->first();

if ($adminTemplate) {
    echo "✅ Admin Payment Success Template: FOUND\n";
    echo "   - Subject: " . $adminTemplate->subj . "\n";
    echo "   - Status: " . ($adminTemplate->email_status == ManageStatus::ACTIVE ? 'ACTIVE' : 'INACTIVE') . "\n";
    if ($adminTemplate->email_status != ManageStatus::ACTIVE) {
        echo "   ⚠️  WARNING: Template is INACTIVE! Emails won't be sent.\n";
    }
} else {
    echo "❌ Admin Payment Success Template: NOT FOUND\n";
    echo "   Run: php artisan db:seed --class=PaymentSuccessEmailTemplateSeeder\n";
}

echo "\n";

if ($userTemplate) {
    echo "✅ User Payment Success Template: FOUND\n";
    echo "   - Subject: " . $userTemplate->subj . "\n";
    echo "   - Status: " . ($userTemplate->email_status == ManageStatus::ACTIVE ? 'ACTIVE' : 'INACTIVE') . "\n";
    if ($userTemplate->email_status != ManageStatus::ACTIVE) {
        echo "   ⚠️  WARNING: Template is INACTIVE! Emails won't be sent.\n";
    }
} else {
    echo "❌ User Payment Success Template: NOT FOUND\n";
    echo "   Run: php artisan db:seed --class=PaymentSuccessEmailTemplateSeeder\n";
}

echo "\n";

// Check Email Settings
echo "2. Checking Email Settings...\n";
echo "----------------------------------------\n";

$setting = Setting::first();

if ($setting) {
    echo "Email Enabled: " . ($setting->ea ? 'YES ✅' : 'NO ❌') . "\n";
    if (!$setting->ea) {
        echo "   ⚠️  WARNING: Email is disabled in settings! Enable it in Admin Panel.\n";
    }
    
    echo "Email Provider: " . ($setting->mail_config->name ?? 'Not Set') . "\n";
    echo "From Email: " . ($setting->email_from ?? 'Not Set') . "\n";
    echo "Site Email (Admin): " . ($setting->site_email ?? 'Not Set') . "\n";
    
    if (empty($setting->site_email)) {
        echo "   ⚠️  WARNING: Admin email (site_email) is not set! Admin emails won't be sent.\n";
    }
} else {
    echo "❌ Settings not found!\n";
}

echo "\n";

// Check Email Logs Table
echo "3. Checking Email Logs Table...\n";
echo "----------------------------------------\n";

try {
    $emailLogsCount = \App\Models\EmailLog::count();
    echo "✅ Email Logs Table: EXISTS\n";
    echo "   - Total Logs: " . $emailLogsCount . "\n";
    
    $recentLogs = \App\Models\EmailLog::where('created_at', '>=', now()->subDays(7))->count();
    echo "   - Logs in last 7 days: " . $recentLogs . "\n";
    
    $paymentLogs = \App\Models\EmailLog::where('email_type', 'payment_success')->count();
    echo "   - Payment Success Logs: " . $paymentLogs . "\n";
} catch (\Exception $e) {
    echo "❌ Email Logs Table: ERROR - " . $e->getMessage() . "\n";
    echo "   Run migration: php artisan migrate\n";
}

echo "\n";

// Summary
echo "========================================\n";
echo "Summary\n";
echo "========================================\n";

$allGood = true;

if (!$adminTemplate || !$userTemplate) {
    echo "❌ Email templates are missing. Run seeder to create them.\n";
    $allGood = false;
}

if ($adminTemplate && $adminTemplate->email_status != ManageStatus::ACTIVE) {
    echo "⚠️  Admin template is inactive. Activate it in Admin Panel.\n";
    $allGood = false;
}

if ($userTemplate && $userTemplate->email_status != ManageStatus::ACTIVE) {
    echo "⚠️  User template is inactive. Activate it in Admin Panel.\n";
    $allGood = false;
}

if (!$setting || !$setting->ea) {
    echo "❌ Email is disabled. Enable it in Admin Panel > Notification > Email.\n";
    $allGood = false;
}

if (!$setting || empty($setting->site_email)) {
    echo "⚠️  Admin email (site_email) is not set. Set it in Admin Panel > Settings.\n";
    $allGood = false;
}

if ($allGood) {
    echo "✅ All checks passed! Email system is properly configured.\n";
} else {
    echo "\nTo fix issues:\n";
    echo "1. Run seeder: php artisan db:seed --class=PaymentSuccessEmailTemplateSeeder\n";
    echo "2. Check Admin Panel > Notification > Templates\n";
    echo "3. Check Admin Panel > Notification > Email Settings\n";
    echo "4. Check Admin Panel > Settings > Site Email\n";
}

echo "\n";

