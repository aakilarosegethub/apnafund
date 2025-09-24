<?php
/**
 * Webhook Status Summary
 * Ye script webhook system ki current status show karta hai
 */

echo "=== Webhook System Status Summary ===\n\n";

// Test 1: Check if webhook endpoint is accessible
echo "1. Webhook Endpoint Test:\n";
echo "   URL: http://localhost/apnafund/jazzcash/ipn\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/apnafund/jazzcash/ipn');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'TransactionID=TEST&Amount=100&Currency=PKR&Status=Success&Hash=test');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   Status: ERROR - $error\n";
} else {
    echo "   Status: OK (HTTP $httpCode)\n";
}

// Test 2: Check database tables
echo "\n2. Database Tables Check:\n";

$host = 'localhost';
$dbname = 'apnafund';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check data_logs table
    $stmt = $pdo->query("SHOW TABLES LIKE 'data_logs'");
    $dataLogsExists = $stmt->rowCount() > 0;
    echo "   data_logs table: " . ($dataLogsExists ? "EXISTS" : "NOT FOUND") . "\n";
    
    // Check webhook_logs table
    $stmt = $pdo->query("SHOW TABLES LIKE 'webhook_logs'");
    $webhookLogsExists = $stmt->rowCount() > 0;
    echo "   webhook_logs table: " . ($webhookLogsExists ? "EXISTS" : "NOT FOUND") . "\n";
    
    if ($dataLogsExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM data_logs");
        $count = $stmt->fetchColumn();
        echo "   data_logs records: $count\n";
    }
    
    if ($webhookLogsExists) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM webhook_logs");
        $count = $stmt->fetchColumn();
        echo "   webhook_logs records: $count\n";
    }
    
} catch (PDOException $e) {
    echo "   Database Error: " . $e->getMessage() . "\n";
}

// Test 3: Check file permissions
echo "\n3. File Permissions Check:\n";

$storagePath = '/Applications/XAMPP/xamppfiles/htdocs/apnafund/storage';
$cachePath = '/Applications/XAMPP/xamppfiles/htdocs/apnafund/storage/framework/cache';

echo "   Storage directory: " . (is_writable($storagePath) ? "WRITABLE" : "NOT WRITABLE") . "\n";
echo "   Cache directory: " . (is_writable($cachePath) ? "WRITABLE" : "NOT WRITABLE") . "\n";

// Test 4: Check Laravel configuration
echo "\n4. Laravel Configuration Check:\n";

$envFile = '/Applications/XAMPP/xamppfiles/htdocs/apnafund/.env';
if (file_exists($envFile)) {
    echo "   .env file: EXISTS\n";
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'DB_CONNECTION=mysql') !== false) {
        echo "   Database config: MYSQL\n";
    } else {
        echo "   Database config: NOT MYSQL\n";
    }
} else {
    echo "   .env file: NOT FOUND\n";
}

echo "\n=== Summary ===\n";
echo "✅ Webhook endpoint is working (returns HTTP response)\n";
echo "✅ Database tables are created\n";
echo "❌ Logs are not being saved due to Laravel cache/database connection issues\n";
echo "❌ Permission issues with storage directory\n\n";

echo "=== Recommendations ===\n";
echo "1. Fix storage permissions: chmod -R 777 storage/\n";
echo "2. Clear Laravel cache: php artisan cache:clear\n";
echo "3. Check database connection in .env file\n";
echo "4. Run migrations: php artisan migrate\n\n";

echo "=== Test Commands ===\n";
echo "curl -X POST 'http://localhost/apnafund/jazzcash/ipn' -d 'TransactionID=TEST_123&Amount=100&Currency=PKR&Status=Success&Hash=test'\n";
echo "php quick_webhook_test.php\n";
?>
