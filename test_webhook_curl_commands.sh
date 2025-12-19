#!/bin/bash

# Webhook Logging Test Commands
# Ye script test karta hai ke webhook data properly database mein store ho raha hai

echo "=== Webhook Logging Test Commands ==="
echo ""

# Base URL - change this to your actual domain
BASE_URL="http://localhost/apnacrowdfunding"

echo "1. Testing JazzCash IPN with Success Status..."
curl -X POST "$BASE_URL/jazzcash/ipn" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "User-Agent: WebhookTestScript/1.0" \
  -d "TransactionID=TEST_$(date +%s)" \
  -d "Amount=100.00" \
  -d "Currency=PKR" \
  -d "Status=Success" \
  -d "Hash=test_hash_$(date +%s)" \
  -d "MerchantID=TEST_MERCHANT" \
  -d "OrderID=ORDER_$(date +%s)" \
  -d "ResponseCode=000" \
  -d "ResponseMessage=Transaction Successful" \
  -d "TransactionDate=$(date '+%Y-%m-%d %H:%M:%S')" \
  -d "TransactionTime=$(date +%s)" \
  -v

echo -e "\n\n2. Testing JazzCash IPN with Failed Status..."
curl -X POST "$BASE_URL/jazzcash/ipn" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "User-Agent: WebhookTestScript/1.0" \
  -d "TransactionID=FAILED_$(date +%s)" \
  -d "Amount=50.00" \
  -d "Currency=PKR" \
  -d "Status=Failed" \
  -d "Hash=test_hash_failed_$(date +%s)" \
  -d "MerchantID=TEST_MERCHANT" \
  -d "OrderID=ORDER_FAILED_$(date +%s)" \
  -d "ResponseCode=001" \
  -d "ResponseMessage=Transaction Failed" \
  -d "TransactionDate=$(date '+%Y-%m-%d %H:%M:%S')" \
  -d "TransactionTime=$(date +%s)" \
  -v

echo -e "\n\n3. Testing with Missing TransactionID (should fail)..."
curl -X POST "$BASE_URL/jazzcash/ipn" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "User-Agent: WebhookTestScript/1.0" \
  -d "Amount=200.00" \
  -d "Currency=PKR" \
  -d "Status=Success" \
  -d "Hash=test_hash_no_txn_$(date +%s)" \
  -v

echo -e "\n\n4. Testing with GET method..."
curl -X GET "$BASE_URL/jazzcash/ipn?TransactionID=GET_TEST_$(date +%s)&Amount=75.00&Currency=PKR&Status=Success&Hash=test_hash_get_$(date +%s)" \
  -H "User-Agent: WebhookTestScript/1.0" \
  -v

echo -e "\n\n5. Testing with PUT method..."
curl -X PUT "$BASE_URL/jazzcash/ipn" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "User-Agent: WebhookTestScript/1.0" \
  -d "TransactionID=PUT_TEST_$(date +%s)" \
  -d "Amount=150.00" \
  -d "Currency=PKR" \
  -d "Status=Success" \
  -d "Hash=test_hash_put_$(date +%s)" \
  -v

echo -e "\n\n=== Test Complete ==="
echo "Check your database for webhook logs:"
echo "1. data_logs table: SELECT * FROM data_logs ORDER BY created_at DESC LIMIT 10;"
echo "2. webhook_logs table: SELECT * FROM webhook_logs ORDER BY created_at DESC LIMIT 10;"
echo "3. Laravel logs: tail -f storage/logs/laravel.log"
echo ""
echo "Admin panel: $BASE_URL/admin/webhook-logs"
