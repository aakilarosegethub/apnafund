<?php
/**
 * Direct Webhook Test - Bypass Laravel cache issues
 * Ye script directly webhook controller ko call karta hai
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Create a simple request
$request = \Illuminate\Http\Request::create('/jazzcash/ipn', 'POST', [
    'TransactionID' => 'TEST_' . time(),
    'Amount' => '100.00',
    'Currency' => 'PKR',
    'Status' => 'Success',
    'Hash' => 'test_hash_' . time(),
    'MerchantID' => 'TEST_MERCHANT',
    'OrderID' => 'ORDER_' . time(),
]);

echo "=== Direct Webhook Test ===\n\n";

try {
    // Get the controller
    $controller = new \App\Http\Controllers\Gateway\JazzCash\IpnController();
    
    // Call the handle method directly
    $response = $controller->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
