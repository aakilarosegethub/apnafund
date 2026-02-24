#!/bin/bash
# Signup API with CNIC front & back images (multipart form-data)
# Run: php artisan migrate (once) to add cnic_front_image, cnic_back_image columns to users table.

BASE_URL="${BASE_URL:-http://localhost:8000}"

# Replace with actual image file paths on your machine
CNIC_FRONT="/path/to/cnic_front.jpg"
CNIC_BACK="/path/to/cnic_back.jpg"

curl --location "${BASE_URL}/api/reg_user.php" \
  --header 'Accept: application/json' \
  --form "name=Test User" \
  --form "email=testuser@example.com" \
  --form "mobile=03001234567" \
  --form "ccode=PK" \
  --form "password=Secret123!" \
  --form "cnic_front_image=@${CNIC_FRONT}" \
  --form "cnic_back_image=@${CNIC_BACK}"

# Example with real files (run from project root, use two sample images):
# curl --location "http://localhost:8000/api/reg_user.php" \
#   --header 'Accept: application/json' \
#   --form "name=Donor Name" \
#   --form "email=donor@example.com" \
#   --form "mobile=03001234567" \
#   --form "ccode=PK" \
#   --form "password=MyPass123!" \
#   --form "cnic_front_image=@/path/to/cnic_front.jpg" \
#   --form "cnic_back_image=@/path/to/cnic_back.jpg"
