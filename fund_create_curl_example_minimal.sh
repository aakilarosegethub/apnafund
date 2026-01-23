#!/bin/bash

# Fund Create API - Minimal cURL Command (Required Fields Only)
# Replace YOUR_TOKEN_HERE with actual token from login API

curl --location 'http://localhost:8000/api/fundraise.php' \
--header 'Authorization: Bearer 1QkfbYgbSrFmduKyU6nSckxTnkSOqU61oIa9KikV9a38bd24' \
--header 'Accept: application/json' \
--form 'cat_id="1"' \
--form 'title="Help Save a Life - Medical Fund"' \
--form 'fund_for="Medical Emergency"' \
--form 'fund_amt="50000"' \
--form 'fund_story="Urgent medical treatment needed for critical patient."' \
--form 'status="Pending"'
