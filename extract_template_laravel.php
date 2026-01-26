<?php
/**
 * SMS Template Extractor for Laravel
 * 
 * This script extracts SMS template settings using Laravel's database
 * Usage: Run from web browser or command line within Laravel app
 */

// Check if running in Laravel environment
if (!function_exists('env')) {
    echo "This script must be run within a Laravel environment.\n";
    echo "Please use the web interface at: http://192.168.1.34:8000/admin/notification/template/edit/28\n";
    echo "Or run: php artisan tinker\n";
    echo "Then execute: include 'extract_template_laravel.php';\n";
    exit;
}

use App\Models\NotificationTemplate;

function extractTemplate($id = 28) {
    try {
        $template = NotificationTemplate::findOrFail($id);
        $setting = \App\Models\BasicSetting::first();

        $output = [];
        $output[] = "===================================================";
        $output[] = "  SMS TEMPLATE EXTRACTOR (LARAVEL)";
        $output[] = "===================================================";
        $output[] = "";
        
        $output[] = "📋 TEMPLATE INFORMATION";
        $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
        $output[] = "ID:              {$template->id}";
        $output[] = "Name:            {$template->name}";
        $output[] = "Action:          {$template->act}";
        $output[] = "Created:         {$template->created_at}";
        $output[] = "Updated:         {$template->updated_at}";
        $output[] = "";
        
        $output[] = "📧 EMAIL SETTINGS";
        $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
        $output[] = "Status:          " . ($template->email_status ? '✅ Active' : '❌ Inactive');
        $output[] = "Subject:         {$template->subj}";
        $output[] = "Body Length:     " . strlen($template->email_body) . " characters";
        $output[] = "";
        
        $output[] = "📱 SMS SETTINGS";
        $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
        $output[] = "Status:          " . ($template->sms_status ? '✅ Active' : '❌ Inactive');
        $sms_length = strlen($template->sms_body);
        $output[] = "Body Length:     {$sms_length} characters";
        $output[] = "SMS Parts:       " . ceil($sms_length / 160) . " message(s)";
        $output[] = "Body:";
        $output[] = "┌" . str_repeat("─", 70) . "┐";
        $output[] = wordwrap($template->sms_body, 70, "\n");
        $output[] = "└" . str_repeat("─", 70) . "┘";
        $output[] = "";
        
        if ($template->shortcodes) {
            $output[] = "🏷️  AVAILABLE SHORTCODES";
            $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            foreach ($template->shortcodes as $code => $description) {
                $output[] = sprintf("%-25s %s", "{{{$code}}}", $description);
            }
            $output[] = "";
        }
        
        if ($setting) {
            $output[] = "⚙️  GLOBAL SMS CONFIGURATION";
            $output[] = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
            $output[] = "Site Name:       {$setting->site_name}";
            $output[] = "SMS From:        {$setting->sms_from}";
            $output[] = "SMS Enabled:     " . ($setting->sa ? '✅ Yes' : '❌ No');
            if ($setting->sms_config && isset($setting->sms_config->name)) {
                $output[] = "Gateway:         {$setting->sms_config->name}";
            }
            $output[] = "";
        }
        
        $output[] = "===================================================";
        $output[] = "  EXTRACTION COMPLETE ✅";
        $output[] = "===================================================";
        
        return implode("\n", $output);
        
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
}

// If running from command line
if (php_sapi_name() === 'cli') {
    $id = isset($argv[1]) ? intval($argv[1]) : 28;
    echo extractTemplate($id);
    echo "\n";
}
