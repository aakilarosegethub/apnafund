<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\NotificationTemplate;
use App\Constants\ManageStatus;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Payment Success Email Templates...\n\n";

// Check if templates exist
$adminTemplate = NotificationTemplate::where('act', 'ADMIN_PAYMENT_SUCCESS')->first();
$userTemplate = NotificationTemplate::where('act', 'USER_PAYMENT_SUCCESS')->first();

if ($adminTemplate) {
    echo "✅ Admin Payment Success Template Found\n";
    echo "   Subject: " . $adminTemplate->subj . "\n";
    echo "   Status: " . ($adminTemplate->email_status ? 'Active' : 'Inactive') . "\n\n";
} else {
    echo "❌ Admin Payment Success Template Not Found\n\n";
}

if ($userTemplate) {
    echo "✅ User Payment Success Template Found\n";
    echo "   Subject: " . $userTemplate->subj . "\n";
    echo "   Status: " . ($userTemplate->email_status ? 'Active' : 'Inactive') . "\n\n";
} else {
    echo "❌ User Payment Success Template Not Found\n\n";
}

// Test template shortcodes
if ($adminTemplate && $userTemplate) {
    echo "Testing template shortcodes...\n";
    
    $testShortcodes = [
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'campaign_name' => 'Test Campaign',
        'amount' => '100.00',
        'method_name' => 'Credit Card',
        'trx' => 'TXN123456789',
        'date' => 'Dec 19, 2024 10:30 AM',
        'admin_url' => 'https://admin.apnacrowdfunding.com/donations',
        'campaign_url' => 'https://apnacrowdfunding.com/campaign/test',
        'currency_symbol' => '$'
    ];
    
    // Test admin template
    $adminBody = $adminTemplate->email_body;
    foreach ($testShortcodes as $code => $value) {
        $adminBody = str_replace('{{' . $code . '}}', $value, $adminBody);
    }
    
    // Test user template
    $userBody = $userTemplate->email_body;
    foreach ($testShortcodes as $code => $value) {
        $userBody = str_replace('{{' . $code . '}}', $value, $userBody);
    }
    
    echo "✅ Template shortcodes processed successfully\n";
    echo "   Admin template length: " . strlen($adminBody) . " characters\n";
    echo "   User template length: " . strlen($userBody) . " characters\n\n";
}

echo "Email template test completed!\n";
