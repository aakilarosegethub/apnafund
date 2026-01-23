#!/bin/bash

# Fund Create API - cURL Command WITHOUT File Uploads
# Replace YOUR_TOKEN_HERE with actual token from login API
# This command doesn't include image uploads

curl --location 'http://localhost:8000/api/fundraise.php' \
--header 'Authorization: Bearer 1QkfbYgbSrFmduKyU6nSckxTnkSOqU61oIa9KikV9a38bd24' \
--header 'Accept: application/json' \
--form 'cat_id="1"' \
--form 'title="Emergency Medical Fund for John Smith"' \
--form 'fund_for="Critical Surgery"' \
--form 'fund_amt="60000"' \
--form 'fund_story="John Smith, a 35-year-old father of two, urgently needs life-saving surgery. He was diagnosed with a serious medical condition last month and requires immediate treatment. The family has exhausted all their savings and needs help to cover the surgery costs. Your generous contribution will help save John'\''s life and bring him back to his loving family."' \
--form 'exp_date="2024-12-31"' \
--form 'full_address="456 Hospital Avenue, Health District, City, State 67890"' \
--form 'lats="34.0522"' \
--form 'longs="-118.2437"' \
--form 'patient_title="John Smith"' \
--form 'patient_diagnosis="Acute condition requiring immediate surgical intervention. Treatment includes: Surgery, ICU stay (5 days), medications, and follow-up care. Total estimated cost: $60,000."' \
--form 'fund_plan="Funds will be used for: 1. Surgical procedure costs 2. Hospital stay and ICU charges 3. Post-operative medications 4. Follow-up medical consultations"' \
--form 'status="Pending"' \
--form 'charity_id=""' \
--form 'fundsize="0"' \
--form 'petientsize="0"' \
--form 'certicatesize="0"'
