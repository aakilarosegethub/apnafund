# LinkedIn Login Integration Guide

This guide provides step-by-step instructions to integrate LinkedIn OAuth login into your ApnaFund application.

---

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [LinkedIn Developer Portal Setup](#linkedin-developer-portal-setup)
3. [Admin Panel Configuration](#admin-panel-configuration)
4. [Environment Variables](#environment-variables)
5. [Redirect URI Configuration](#redirect-uri-configuration)
6. [Testing the Integration](#testing-the-integration)
7. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- Admin access to ApnaFund
- A LinkedIn account
- Your application URL (e.g., `https://yourdomain.com` or `http://localhost:8000` for local development)

---

## LinkedIn Developer Portal Setup

### Step 1: Create a LinkedIn Developer Account
1. Go to [LinkedIn Developer Portal](https://www.linkedin.com/developers/apps)
2. Sign in with your LinkedIn account
3. Click **Create app**

### Step 2: Create a New App
1. Select **Create new app**
2. Fill in the required details:
   - **App name**: Your app name (e.g., ApnaFund)
   - **LinkedIn Page**: Select or create a LinkedIn Company Page (required)
   - **Privacy policy URL**: Your website's privacy policy URL
   - **App logo**: Optional - upload your app logo
3. Accept the terms and click **Create app**

### Step 3: Add Sign In Product
1. In your app dashboard, go to the **Products** tab
2. Find **Sign In with LinkedIn using OpenID Connect**
3. Click **Request access** (or **Add product** if already available)
4. Wait for approval if required (usually instant for basic access)

### Step 4: Get Your Credentials
1. Go to the **Auth** tab in your app dashboard
2. Note down:
   - **Client ID** (Application ID)
   - **Client Secret** (click **Generate** if not visible, then copy it)

### Step 5: Configure Redirect URLs
1. In the **Auth** tab, find **Authorized redirect URLs**
2. Click **Add redirect URL**
3. Add your callback URL based on your environment:
   - **Production**: `https://yourdomain.com/user/auth/linkedin/callback`
   - **Local development**: `http://localhost:8000/user/auth/linkedin/callback`
   - **Custom port**: `http://192.168.1.104:8000/user/auth/linkedin/callback` (use your actual URL)
4. Click **Update** to save

---

## Admin Panel Configuration

### Step 1: Access Social Login Settings
1. Log in to your ApnaFund **Admin Panel**
2. Go to **Setting** (or **Settings**)
3. Navigate to **Social Login Settings**

### Step 2: Enable LinkedIn Login
1. Find the **LinkedIn Login** section
2. Toggle **Enable LinkedIn Login** to ON

### Step 3: Enter Credentials
1. **LinkedIn Client ID**: Paste the Client ID from your LinkedIn app
2. **LinkedIn Client Secret**: Paste the Client Secret from your LinkedIn app

### Step 4: Verify Redirect URI
- The **Redirect URI** field is read-only and displays: `{YOUR_APP_URL}/user/auth/linkedin/callback`
- Ensure this exactly matches the URL you added in the LinkedIn Developer Portal

### Step 5: Test Configuration
1. Click **Test Configuration** button
2. You should see: "LinkedIn configuration is valid"
3. If you see an error, double-check your Client ID and Client Secret

### Step 6: Save Settings
1. Click **Save Settings**
2. Your credentials are now stored and updated in the `.env` file automatically

---

## Environment Variables

The following variables are used for LinkedIn integration. They are auto-updated when you save from the Admin Panel, but you can also set them manually in `.env`:

```
LINKEDIN_CLIENT_ID=your_client_id_here
LINKEDIN_CLIENT_SECRET=your_client_secret_here
LINKEDIN_REDIRECT_URI=https://yourdomain.com/user/auth/linkedin/callback
```

**Important**: After changing `.env`, run `php artisan config:clear` to clear the config cache.

---

## Redirect URI Configuration

The redirect URI **must exactly match** in both places:

| Location | Format |
|----------|--------|
| LinkedIn Developer Portal | `{APP_URL}/user/auth/linkedin/callback` |
| Your Application | Same URL |

**Examples:**
- Production: `https://www.apnacrowdfunding.com/user/auth/linkedin/callback`
- Development: `http://localhost:8000/user/auth/linkedin/callback`
- Local network: `http://192.168.1.104:8000/user/auth/linkedin/callback`

---

## Testing the Integration

### Step 1: Verify Admin Settings
- Ensure LinkedIn is enabled and credentials are saved
- Test configuration shows success

### Step 2: Test on Frontend
1. Log out if you are logged in
2. Go to the **Login** page
3. You should see a **LinkedIn** or **Continue with LinkedIn** button
4. Click the button

### Step 3: Expected Flow
1. You are redirected to LinkedIn's authorization page
2. Sign in with LinkedIn (if not already signed in)
3. Approve the app permissions
4. You are redirected back to your app and logged in

### Step 4: Verify User Creation
- On first login, a new user account is created
- Email from LinkedIn profile is used
- User is logged in and redirected to the dashboard

---

## Troubleshooting

### LinkedIn button not showing
- **Cause**: Credentials not configured or LinkedIn disabled
- **Fix**: Enable LinkedIn in Admin Panel and add valid Client ID & Secret

### "LinkedIn login is not configured"
- **Cause**: Empty or invalid credentials in config
- **Fix**: Save credentials in Admin Panel, then run `php artisan config:clear`

### "unauthorized_scope_error" or scope errors
- **Cause**: Old LinkedIn OAuth API - LinkedIn migrated to OpenID Connect
- **Fix**: Ensure you're using "Sign In with LinkedIn using OpenID Connect" product

### Redirect URI mismatch
- **Cause**: URL in LinkedIn app doesn't match your app URL
- **Fix**: Add the exact redirect URL to LinkedIn Developer Portal. Check `APP_URL` in `.env`

### "LinkedIn account email is required"
- **Cause**: User's LinkedIn account has no public email or hasn't granted email permission
- **Fix**: User must ensure their LinkedIn profile has an email and grants permission

### Config cache issues
- Run: `php artisan config:clear`
- Restart your web server if needed

---

## Security Notes

1. **Never** commit your `LINKEDIN_CLIENT_SECRET` to version control
2. Keep `.env` in `.gitignore`
3. Use different apps for development and production
4. Rotate Client Secret if you suspect it has been compromised

---

## Support

For LinkedIn API documentation, visit: [LinkedIn Developer Documentation](https://learn.microsoft.com/en-us/linkedin/)

---

*Last updated: March 2025*
