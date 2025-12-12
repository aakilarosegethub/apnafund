# Firebase OTP Setup Guide (हिंदी में)

## Step 1: Firebase Console में Project Setup

1. **Firebase Console खोलें**: https://console.firebase.google.com/
2. **Project बनाएं** (या existing project select करें)
3. **Authentication Enable करें**:
   - Left menu से "Authentication" पर click करें
   - "Get started" button click करें
   - "Sign-in method" tab पर जाएं
   - "Phone" provider को enable करें
   - Test phone numbers add करें (development के लिए)

## Step 2: Service Account Key Download करें

1. **Project Settings** पर जाएं (gear icon)
2. **"Service Accounts"** tab select करें
3. **"Generate new private key"** button click करें
4. JSON file download होगी - इसे save करें

## Step 3: JSON File से Values Extract करें

Download की गई JSON file खोलें और इन values को note करें:

```json
{
  "type": "service_account",
  "project_id": "your-project-id",           // ← यह FIREBASE_PROJECT_ID है
  "private_key_id": "abc123...",             // ← यह FIREBASE_PRIVATE_KEY_ID है
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",  // ← यह FIREBASE_PRIVATE_KEY है
  "client_email": "firebase-adminsdk-xxxxx@your-project-id.iam.gserviceaccount.com",  // ← यह FIREBASE_CLIENT_EMAIL है
  "client_id": "123456789",                  // ← यह FIREBASE_CLIENT_ID है
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-xxxxx%40your-project-id.iam.gserviceaccount.com"  // ← यह FIREBASE_CLIENT_CERT_URL है
}
```

## Step 4: .env File में Settings Add करें

`.env` file खोलें और नीचे दी गई settings add करें:

```env
# Firebase Configuration
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_PRIVATE_KEY_ID=your-private-key-id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYour private key here\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-xxxxx@your-project-id.iam.gserviceaccount.com
FIREBASE_CLIENT_ID=your-client-id
FIREBASE_CLIENT_CERT_URL=https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-xxxxx%40your-project-id.iam.gserviceaccount.com

# Firebase Auth Settings
FIREBASE_VERIFY_PHONE_NUMBER=true
FIREBASE_PHONE_AUTH_TIMEOUT=60
FIREBASE_MAX_OTP_ATTEMPTS=3
FIREBASE_DATABASE_ID=(default)
FIREBASE_COLLECTION_PREFIX=apnacrowdfunding
```

### Important Notes:

1. **FIREBASE_PRIVATE_KEY**: 
   - JSON file में `private_key` value को copy करें
   - पूरी key को quotes में रखें
   - `\n` characters को maintain करें

2. **FIREBASE_CLIENT_CERT_URL**:
   - JSON file में `client_x509_cert_url` value को copy करें
   - URL encoding maintain करें (%40 = @)

## Step 5: Composer Package Check करें

Firebase PHP package install होना चाहिए:

```bash
composer require kreait/firebase-php
```

## Step 6: Config Clear करें

Settings add करने के बाद:

```bash
php artisan config:clear
php artisan cache:clear
```

## Step 7: Test करें

1. Registration page पर जाएं
2. Phone number के साथ signup करें
3. OTP phone पर receive होना चाहिए
4. OTP verify करें

## Troubleshooting

### अगर OTP नहीं आ रहा:
1. Firebase Console में Phone Authentication enable है या नहीं check करें
2. Phone number format सही है या नहीं check करें (+country_code + number)
3. Service account key सही है या नहीं verify करें
4. `.env` file में सभी values correctly set हैं या नहीं check करें

### अगर Error आ रहा है:
1. `storage/logs/laravel.log` file check करें
2. Firebase Console में quotas check करें
3. Service account permissions verify करें

## Security Tips

1. **कभी भी service account key को Git में commit न करें**
2. Production और Development के लिए अलग-अलग service accounts use करें
3. Service account keys को regularly rotate करें
4. `.env` file को `.gitignore` में रखें

