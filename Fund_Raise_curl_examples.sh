#!/bin/bash
# Fund Raise API - cURL examples
# Set your base URL (e.g. http://127.0.0.1:8000 or https://yoursite.com)
BASE_URL="${BASE_URL:-http://127.0.0.1:8000}"

echo "=== 1. Gateway List (GET) ==="
curl -s -X GET "${BASE_URL}/api/gateways" \
  -H "Accept: application/json" | jq .

echo -e "\n=== 2. Gateway List by country (GET) ==="
curl -s -X GET "${BASE_URL}/api/gateways?country=Pakistan" \
  -H "Accept: application/json" | jq .

echo -e "\n=== 3. Payment Gateway List - Legacy (GET) ==="
curl -s -X GET "${BASE_URL}/api/paymentgateway.php" \
  -H "Accept: application/json" | jq .

echo -e "\n=== 4. Get Payment Webview URL (POST) ==="
curl -s -X POST "${BASE_URL}/api/payment/webview-url" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "gateway_id": 1,
    "amount": 500,
    "campaign_slug": "your-campaign-slug",
    "full_name": "Donor Name",
    "email": "donor@example.com",
    "country": "Pakistan",
    "currency": "PKR",
    "phone": "03001234567",
    "anonymous": "0"
  }' | jq .

# Optional: use gateway code instead of gateway_id
# curl -s -X POST "${BASE_URL}/api/payment/webview-url" \
#   -H "Content-Type: application/json" \
#   -H "Accept: application/json" \
#   -d '{"gateway":"101","amount":1000,"campaign_slug":"your-campaign-slug","full_name":"Donor Name","email":"donor@example.com","country":"Pakistan","currency":"PKR"}' | jq .
