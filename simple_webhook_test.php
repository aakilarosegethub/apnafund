<?php
/**
 * Simple Webhook Test - Direct HTTP Test
 * Ye script webhook ko test karta hai without database dependency
 */

echo "=== Simple Webhook Test ===\n\n";

// Test data
$testData = [
    'TransactionID' => 'TEST_' . time(),
    'Amount' => '100.00',
    'Currency' => 'PKR',
    'Status' => 'Success',
    'Hash' => 'test_hash_' . time(),
    'MerchantID' => 'TEST_MERCHANT',
    'OrderID' => 'ORDER_' . time(),
];

$url = 'http://localhost/apnafund/jazzcash/ipn';

echo "1. Testing JazzCash IPN Webhook...\n";
echo "URL: $url\n";
echo "Data: " . http_build_query($testData) . "\n\n";

// Make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'User-Agent: WebhookTestScript/1.0'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "Error: $error\n";
} else {
    echo "Response: " . substr($response, 0, 500) . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Check if webhook is working by looking at the response above.\n";
echo "If you see HTML content, the webhook is being processed.\n";
echo "If you see 'Transaction not found', that's expected since we're using a test transaction ID.\n";
?>
