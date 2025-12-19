<?php
/**
 * Quick Webhook Test - Direct Database Check
 * Ye script webhook logs ko database mein check karta hai
 */

// Database connection (adjust these settings as per your config)
$host = 'localhost';
$dbname = 'apnacrowdfunding'; // Change this to your database name
$username = 'root'; // Change this to your database username
$password = ''; // Change this to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Webhook Logs Database Check ===\n\n";
    
    // Check data_logs table
    echo "1. Recent Data Logs (last 10):\n";
    echo "----------------------------------------\n";
    $stmt = $pdo->query("SELECT id, endpoint, method, status, transaction_id, created_at FROM data_logs ORDER BY created_at DESC LIMIT 10");
    $dataLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($dataLogs)) {
        echo "No data logs found.\n";
    } else {
        foreach ($dataLogs as $log) {
            echo "ID: {$log['id']} | Endpoint: {$log['endpoint']} | Method: {$log['method']} | Status: {$log['status']} | TxnID: {$log['transaction_id']} | Time: {$log['created_at']}\n";
        }
    }
    
    echo "\n2. Recent Webhook Logs (last 10):\n";
    echo "----------------------------------------\n";
    $stmt = $pdo->query("SELECT id, webhook_type, method, status, execution_time, created_at FROM webhook_logs ORDER BY created_at DESC LIMIT 10");
    $webhookLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($webhookLogs)) {
        echo "No webhook logs found.\n";
    } else {
        foreach ($webhookLogs as $log) {
            echo "ID: {$log['id']} | Type: {$log['webhook_type']} | Method: {$log['method']} | Status: {$log['status']} | Time: {$log['execution_time']}s | Created: {$log['created_at']}\n";
        }
    }
    
    echo "\n3. Statistics:\n";
    echo "----------------------------------------\n";
    
    // Data logs statistics
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM data_logs GROUP BY status");
    $dataStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Data Logs by Status:\n";
    foreach ($dataStats as $stat) {
        echo "  {$stat['status']}: {$stat['count']}\n";
    }
    
    // Webhook logs statistics
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM webhook_logs GROUP BY status");
    $webhookStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nWebhook Logs by Status:\n";
    foreach ($webhookStats as $stat) {
        echo "  {$stat['status']}: {$stat['count']}\n";
    }
    
    // Recent activity
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM data_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $recentDataLogs = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM webhook_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $recentWebhookLogs = $stmt->fetchColumn();
    
    echo "\nRecent Activity (last 1 hour):\n";
    echo "  Data Logs: $recentDataLogs\n";
    echo "  Webhook Logs: $recentWebhookLogs\n";
    
    echo "\n=== Test Complete ===\n";
    echo "To test webhook logging, run:\n";
    echo "curl -X POST 'http://localhost/apnacrowdfunding/jazzcash/ipn' -d 'TransactionID=TEST_123&Amount=100&Currency=PKR&Status=Success&Hash=test'\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database configuration.\n";
}
?>
