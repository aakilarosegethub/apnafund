#!/bin/bash

# Fund Create API - cURL Command with Dummy Data
# Replace YOUR_TOKEN_HERE with actual token from login API
# Replace /path/to/image.jpg with actual image file paths

curl --location 'http://localhost:8000/api/fundraise.php' \
--header 'Authorization: Bearer 1QkfbYgbSrFmduKyU6nSckxTnkSOqU61oIa9KikV9a38bd24' \
--header 'Accept: application/json' \
--form 'cat_id="1"' \
--form 'title="Help Save Little Emma - Medical Treatment Fund"' \
--form 'fund_for="Medical Treatment"' \
--form 'fund_amt="75000"' \
--form 'fund_story="Emma is a 5-year-old girl who was recently diagnosed with a rare heart condition. She needs urgent surgery and ongoing medical treatment. Her family is struggling to cover the medical expenses. Please help us raise funds for Emma'\''s treatment so she can live a healthy and happy life. Your support means the world to us. Every contribution, no matter how small, will help save Emma'\''s life."' \
--form 'exp_date="2024-12-31"' \
--form 'full_address="123 Health Care Street, Medical City, State 12345"' \
--form 'lats="40.7128"' \
--form 'longs="-74.0060"' \
--form 'patient_title="Emma Johnson"' \
--form 'patient_diagnosis="Congenital Heart Disease - Requires immediate surgical intervention and post-operative care. Estimated treatment cost: $75,000 including surgery, medication, and follow-up consultations."' \
--form 'fund_plan="1. Surgery costs: $50,000 (Cardiac surgery at Children Hospital)
2. Post-operative care: $15,000 (ICU, medication, monitoring)
3. Follow-up consultations: $5,000 (Monthly checkups for 12 months)
4. Medication: $5,000 (Prescription drugs for recovery period)"' \
--form 'status="Pending"' \
--form 'charity_id=""' \
--form 'fundsize="2"' \
--form 'fundpic0=@"images/fund_photo/dummy_fund_photo_1.jpg"' \
--form 'fundpic1=@"images/fund_photo/dummy_fund_photo_2.jpg"' \
--form 'petientsize="1"' \
--form 'petpic0=@"images/pet_photo/dummy_patient_photo.jpg"' \
--form 'certicatesize="1"' \
--form 'certpic0=@"images/fund_certificate/dummy_medical_certificate.jpg"'
