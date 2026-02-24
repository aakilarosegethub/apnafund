#!/bin/bash
# Fund Update API – campaign_id, title, content, image ke sath
# 1. YOUR_TOKEN: Login API se mila hua token yahan paste karo
# 2. Image path apni file se replace karo (path mein quotes MAT use karo)

# IMPORTANT: Form values mein quotes mat lagao – campaign_id=77, na ki campaign_id="77"
# Image path: image=@/path/to/file.jpg (quotes mat lagao path ke around)

curl --location 'http://localhost:8001/api/fund_update.php' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer YOUR_TOKEN' \
  --form 'campaign_id=77' \
  --form 'title=Update title' \
  --form 'content=Update content – min 30 chars.' \
  --form 'image=@/Users/apple/Desktop/336730351_605486167764771_8669250532853093731_n.jpg'

# Example with your token:
# curl --location 'http://localhost:8001/api/fund_update.php' \
#   --header 'Accept: application/json' \
#   --header 'Authorization: Bearer 33|YW8Qt2bj3gTNaKNmXi1T4murveAH8VkQ6eaQrPbi093577b1' \
#   --form 'campaign_id=77' \
#   --form 'title=Update title' \
#   --form 'content=Update content – min 30 chars.' \
#   --form 'image=@/Users/apple/Desktop/336730351_605486167764771_8669250532853093731_n.jpg'
