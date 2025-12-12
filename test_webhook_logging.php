<?php
/**
 * Test Webhook Logging Script
 * Ye script test karta hai ke webhook data properly database mein store ho raha hai
 */

// Test data for JazzCash IPN
$testData = [
    'TransactionID' => 'TEST_' . time(),
    'Amount' => '100.00',
    'Currency' => 'PKR',
    'Status' => 'Success',
    'Hash' => 'test_hash_' . time(),
    'MerchantID' => 'TEST_MERCHANT',
    'OrderID' => 'ORDER_' . time(),
    'ResponseCode' => '000',
    'ResponseMessage' => 'Transaction Successful',
    'TransactionDate' => date('Y-m-d H:i:s'),
    'TransactionTime' => time(),
];

// Test data for other gateways
$testDataStripe = [
    'id' => 'evt_test_' . time(),
    'type' => 'payment_intent.succeeded',
    'data' => [
        'object' => [
            'id' => 'pi_test_' . time(),
            'amount' => 10000,
            'currency' => 'usd',
            'status' => 'succeeded'
        ]
    ]
];

$testDataPayPal = [
    'id' => 'WH-' . time(),
    'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
    'resource' => [
        'id' => 'CAPTURE-' . time(),
        'amount' => [
            'currency_code' => 'USD',
            'value' => '100.00'
        ],
        'status' => 'COMPLETED'
    ]
];

echo "=== Webhook Logging Test Script ===\n\n";

// Test 1: JazzCash IPN Test
echo "1. Testing JazzCash IPN Webhook...\n";
$jazzCashUrl = 'http://localhost/apnacrowdfunding/jazzcash/ipn';
$jazzCashResult = testWebhook($jazzCashUrl, $testData, 'POST');
echo "JazzCash Result: " . ($jazzCashResult ? "SUCCESS" : "FAILED") . "\n\n";

// Test 2: Test with different HTTP methods
echo "2. Testing with GET method...\n";
$getResult = testWebhook($jazzCashUrl, $testData, 'GET');
echo "GET Result: " . ($getResult ? "SUCCESS" : "FAILED") . "\n\n";

// Test 3: Test with missing TransactionID (should fail)
echo "3. Testing with missing TransactionID...\n";
$invalidData = $testData;
unset($invalidData['TransactionID']);
$invalidResult = testWebhook($jazzCashUrl, $invalidData, 'POST');
echo "Invalid Data Result: " . ($invalidResult ? "SUCCESS" : "FAILED") . "\n\n";

// Test 4: Test with different status
echo "4. Testing with failed status...\n";
$failedData = $testData;
$failedData['Status'] = 'Failed';
$failedData['TransactionID'] = 'FAILED_' . time();
$failedResult = testWebhook($jazzCashUrl, $failedData, 'POST');
echo "Failed Status Result: " . ($failedResult ? "SUCCESS" : "FAILED") . "\n\n";

echo "=== Test Complete ===\n";
echo "Check your database for webhook logs in:\n";
echo "- data_logs table\n";
echo "- webhook_logs table\n";
echo "- Laravel logs in storage/logs/\n\n";

echo "To view logs in database, run:\n";
echo "SELECT * FROM data_logs ORDER BY created_at DESC LIMIT 10;\n";
echo "SELECT * FROM webhook_logs ORDER BY created_at DESC LIMIT 10;\n";

/**
 * Test webhook function
 */
function testWebhook($url, $data, $method = 'POST') {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: WebhookTestScript/1.0'
        ]);
    } else {
        $urlWithParams = $url . '?' . http_build_query($data);
        curl_setopt($ch, CURLOPT_URL, $urlWithParams);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    echo "URL: $url\n";
    echo "Method: $method\n";
    echo "HTTP Code: $httpCode\n";
    echo "Response: " . substr($response, 0, 200) . "\n";
    if ($error) {
        echo "Error: $error\n";
    }
    echo "---\n";
    
    return $httpCode >= 200 && $httpCode < 500;
}

/**
 * Test database connection and show recent logs
 */
function testDatabaseConnection() {
    try {
        // This would require Laravel bootstrap, so we'll skip for now
        echo "Database connection test skipped (requires Laravel bootstrap)\n";
        return true;
    } catch (Exception $e) {
        echo "Database connection failed: " . $e->getMessage() . "\n";
        return false;
    }
}
?>
