# Google OAuth 401 Unauthorized Fix Guide

## Problem
You're getting a 401 Unauthorized error with "invalid_client" when trying to exchange the authorization code for an access token.

## Root Cause
The Google OAuth client is not properly configured in Google Cloud Console, or the credentials are incorrect.

## Solution Steps

### Step 1: Go to Google Cloud Console
1. Visit: https://console.cloud.google.com/
2. Select your project (or create a new one if needed)

### Step 2: Enable Google+ API
1. Go to "APIs & Services" > "Library"
2. Search for "Google+ API" and enable it
3. Also search for "Google Identity" and enable it

### Step 3: Configure OAuth Consent Screen
1. Go to "APIs & Services" > "OAuth consent screen"
2. Choose "External" user type
3. Fill in the required information:
   - App name: "ApnaFund"
   - User support email: your email
   - Developer contact: your email
4. Add scopes:
   - `../auth/userinfo.email`
   - `../auth/userinfo.profile`
   - `openid`
5. Add test users (your email addresses)

### Step 4: Create OAuth 2.0 Credentials
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. Choose "Web application" as the application type
4. Set the name: "ApnaFund Web Client"
5. Add authorized redirect URIs:
   - `http://localhost:8000/user/auth/google/callback`
   - `https://yourdomain.com/user/auth/google/callback` (for production)
6. Click "Create"

### Step 5: Update Your .env File
Replace the current Google credentials with the new ones:

```env
GOOGLE_CLIENT_ID=your_new_client_id_here
GOOGLE_CLIENT_SECRET=your_new_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/user/auth/google/callback
```

### Step 6: Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 7: Test the OAuth Flow
1. Visit: http://localhost:8000/user/auth/google
2. You should be redirected to Google
3. After authorization, you should be redirected back and logged in

## Common Issues

### Issue 1: "Invalid redirect URI"
- Make sure the redirect URI in Google Cloud Console exactly matches: `http://localhost:8000/user/auth/google/callback`
- Check for typos and ensure it's using `http` not `https` for localhost

### Issue 2: "App not verified"
- This is normal for development
- Click "Advanced" > "Go to ApnaFund (unsafe)" to proceed

### Issue 3: "Access blocked"
- Make sure you've added your email as a test user in OAuth consent screen

## Verification Steps

1. Check that the client ID starts with a number (not a string)
2. Check that the client secret starts with "GOCSPX-"
3. Verify the redirect URI is exactly: `http://localhost:8000/user/auth/google/callback`
4. Make sure the OAuth consent screen is configured
5. Ensure the APIs are enabled

## Current Configuration (for reference)
- Client ID: 306554305922-ra98g8p54lbv8mu275oee7qf17afj8e2.apps.googleusercontent.com
- Client Secret: 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
- Redirect URI: http://localhost:8000/user/auth/google/callback

If these credentials are not working, you need to create new ones following the steps above.
