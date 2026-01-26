<?php
/**
 * SMS Template Extractor Utility
 * 
 * This script extracts SMS template settings from the database
 * Usage: php extract_template_settings.php [template_id]
 * Example: php extract_template_settings.php 28
 */

// Configuration
$db_host = 'localhost';
$db_name = 'apnafund';  // Change to your database name
$db_user = 'root';       // Change to your username
$db_pass = '';           // Change to your password

// Get template ID from command line or default to 28
$template_id = isset($argv[1]) ? intval($argv[1]) : 28;

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "===================================================\n";
    echo "  SMS TEMPLATE EXTRACTOR\n";
    echo "===================================================\n\n";

    // Fetch template data
    $stmt = $pdo->prepare("
        SELECT 
            id,
            act,
            name,
            subj as email_subject,
            email_body,
            email_status,
            sms_body,
            sms_status,
            shortcodes,
            created_at,
            updated_at
        FROM notification_templates 
        WHERE id = ?
    ");
    $stmt->execute([$template_id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$template) {
        echo "❌ Template ID {$template_id} not found!\n";
        exit(1);
    }

    // Display template information
    echo "📋 TEMPLATE INFORMATION\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID:              {$template['id']}\n";
    echo "Name:            {$template['name']}\n";
    echo "Action:          {$template['act']}\n";
    echo "Created:         {$template['created_at']}\n";
    echo "Updated:         {$template['updated_at']}\n\n";

    // Email settings
    echo "📧 EMAIL SETTINGS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Status:          " . ($template['email_status'] ? '✅ Active' : '❌ Inactive') . "\n";
    echo "Subject:         {$template['email_subject']}\n";
    echo "Body Length:     " . strlen($template['email_body']) . " characters\n\n";

    // SMS settings
    echo "📱 SMS SETTINGS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Status:          " . ($template['sms_status'] ? '✅ Active' : '❌ Inactive') . "\n";
    echo "Body Length:     " . strlen($template['sms_body']) . " characters\n";
    echo "SMS Parts:       " . ceil(strlen($template['sms_body']) / 160) . " message(s)\n";
    echo "Body:\n";
    echo "┌" . str_repeat("─", 70) . "┐\n";
    echo wordwrap($template['sms_body'], 70, "\n");
    echo "\n└" . str_repeat("─", 70) . "┘\n\n";

    // Shortcodes
    if ($template['shortcodes']) {
        $shortcodes = json_decode($template['shortcodes'], true);
        if ($shortcodes) {
            echo "🏷️  AVAILABLE SHORTCODES\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            foreach ($shortcodes as $code => $description) {
                echo sprintf("%-25s %s\n", "{{{$code}}}", $description);
            }
            echo "\n";
        }
    }

    // Get SMS configuration
    $stmt = $pdo->query("
        SELECT 
            site_name,
            sms_from,
            sms_body as universal_sms_body,
            sa as sms_enabled,
            sms_config
        FROM basic_settings 
        LIMIT 1
    ");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($settings) {
        echo "⚙️  GLOBAL SMS CONFIGURATION\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Site Name:       {$settings['site_name']}\n";
        echo "SMS From:        {$settings['sms_from']}\n";
        echo "SMS Enabled:     " . ($settings['sms_enabled'] ? '✅ Yes' : '❌ No') . "\n";
        
        if ($settings['sms_config']) {
            $sms_config = json_decode($settings['sms_config'], true);
            if ($sms_config && isset($sms_config['name'])) {
                echo "Gateway:         {$sms_config['name']}\n";
            }
        }
        echo "\n";
    }

    // Export options
    echo "💾 EXPORT OPTIONS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    // Export as JSON
    $export_data = [
        'template' => $template,
        'settings' => $settings,
        'exported_at' => date('Y-m-d H:i:s'),
        'exported_by' => 'CLI Extractor'
    ];
    
    $json_filename = "template_{$template_id}_export_" . date('Ymd_His') . ".json";
    file_put_contents($json_filename, json_encode($export_data, JSON_PRETTY_PRINT));
    echo "✅ JSON Export:   {$json_filename}\n";

    // Export as text
    $txt_filename = "template_{$template_id}_export_" . date('Ymd_His') . ".txt";
    ob_start();
    echo "TEMPLATE EXPORT - {$template['name']}\n";
    echo str_repeat("=", 70) . "\n\n";
    echo "ID: {$template['id']}\n";
    echo "Action: {$template['act']}\n";
    echo "Email Status: " . ($template['email_status'] ? 'Active' : 'Inactive') . "\n";
    echo "SMS Status: " . ($template['sms_status'] ? 'Active' : 'Inactive') . "\n\n";
    echo "EMAIL SUBJECT:\n{$template['email_subject']}\n\n";
    $sms_length = strlen($template['sms_body']);
    $sms_parts = ceil($sms_length / 160);
    echo "SMS BODY ({$sms_length} chars, {$sms_parts} SMS):\n";
    echo str_repeat("-", 70) . "\n";
    echo $template['sms_body'];
    echo "\n" . str_repeat("-", 70) . "\n\n";
    echo "Exported: " . date('Y-m-d H:i:s') . "\n";
    $txt_content = ob_get_clean();
    file_put_contents($txt_filename, $txt_content);
    echo "✅ Text Export:   {$txt_filename}\n";

    echo "\n";
    echo "===================================================\n";
    echo "  EXTRACTION COMPLETE ✅\n";
    echo "===================================================\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
