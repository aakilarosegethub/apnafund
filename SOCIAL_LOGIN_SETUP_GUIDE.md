# 🔐 Social Login Setup Guide - ApnaFund

Complete guide for setting up Facebook and Google social login in your ApnaFund application.

## 📋 Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Installation Steps](#installation-steps)
4. [Facebook Setup](#facebook-setup)
5. [Google Setup](#google-setup)
6. [Admin Configuration](#admin-configuration)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)
9. [Security Notes](#security-notes)

## 🎯 Overview

This guide will help you set up social login functionality for Facebook and Google in your ApnaFund application. Users will be able to login using their social media accounts instead of creating new accounts.

## ✅ Prerequisites

- Laravel 11.x
- PHP 8.2+
- Composer
- Admin access to your ApnaFund application
- Facebook Developer Account
- Google Cloud Console Account

## 🚀 Installation Steps

### Step 1: Install Dependencies

```bash
# Install Laravel Socialite package
composer require laravel/socialite

# Run migration to add social login fields
php artisan migrate
```

### Step 2: Configure Environment Variables

Add these variables to your `.env` file:

```env
# Facebook Configuration
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI=https://yourdomain.com/user/auth/facebook/callback

# Google Configuration
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/user/auth/google/callback
```

### Step 3: Clear Configuration Cache

```bash
php artisan config:clear
php artisan cache:clear
```

## 📘 Facebook Setup

### Step 1: Create Facebook App

1. **Go to Facebook Developers**
   - Visit: https://developers.facebook.com/
   - Click "My Apps" → "Create App"

2. **Choose App Type**
   - Select "Consumer" or "Business"
   - Enter App Name: "ApnaFund Social Login"
   - Enter Contact Email
   - Click "Create App"

3. **Add Facebook Login Product**
   - In your app dashboard, click "Add Product"
   - Find "Facebook Login" and click "Set Up"

### Step 2: Configure Facebook Login

1. **Basic Settings**
   - Go to "Facebook Login" → "Settings"
   - Add Valid OAuth Redirect URIs:
     ```
     https://yourdomain.com/user/auth/facebook/callback
     http://localhost:8000/user/auth/facebook/callback (for development)
     ```

2. **App Review (Optional)**
   - For production, submit for review
   - For development, add test users

### Step 3: Get Credentials

1. **App ID and Secret**
   - Go to "Settings" → "Basic"
   - Copy "App ID" → `FACEBOOK_CLIENT_ID`
   - Copy "App Secret" → `FACEBOOK_CLIENT_SECRET`

2. **Required Permissions**
   - `email` - User's email address
   - `public_profile` - Basic profile information

## 🔍 Google Setup

### Step 1: Create Google Cloud Project

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/
   - Click "Select a project" → "New Project"

2. **Create Project**
   - Project Name: "ApnaFund Social Login"
   - Click "Create"

### Step 2: Enable APIs

1. **Enable Google+ API**
   - Go to "APIs & Services" → "Library"
   - Search for "Google+ API"
   - Click "Enable"

2. **Enable Google Identity API**
   - Search for "Google Identity"
   - Click "Enable"

### Step 3: Create OAuth Credentials

1. **Go to Credentials**
   - Navigate to "APIs & Services" → "Credentials"
   - Click "Create Credentials" → "OAuth 2.0 Client IDs"

2. **Configure OAuth Consent Screen**
   - If prompted, configure OAuth consent screen
   - Choose "External" user type
   - Fill required information:
     - App Name: "ApnaFund"
     - User Support Email: your-email@domain.com
     - Developer Contact: your-email@domain.com

3. **Create OAuth Client**
   - Application Type: "Web application"
   - Name: "ApnaFund Web Client"
   - Authorized Redirect URIs:
     ```
     https://yourdomain.com/user/auth/google/callback
     http://localhost:8000/user/auth/google/callback (for development)
     ```

### Step 4: Get Credentials

1. **Copy Credentials**
   - After creation, you'll see Client ID and Client Secret
   - Copy Client ID → `GOOGLE_CLIENT_ID`
   - Copy Client Secret → `GOOGLE_CLIENT_SECRET`

## ⚙️ Admin Configuration

### Step 1: Access Admin Panel

1. **Login to Admin**
   - Go to: `https://yourdomain.com/admin`
   - Login with admin credentials

2. **Navigate to Settings**
   - Go to "Site Settings" → "Social Login"

### Step 2: Configure Facebook

1. **Enable Facebook Login**
   - Toggle "Enable Facebook Login" to ON
   - Enter Facebook App ID
   - Enter Facebook App Secret
   - Click "Test Configuration" to verify

### Step 3: Configure Google

1. **Enable Google Login**
   - Toggle "Enable Google Login" to ON
   - Enter Google Client ID
   - Enter Google Client Secret
   - Click "Test Configuration" to verify

### Step 4: Save Settings

1. **Save Configuration**
   - Click "Save Settings"
   - Settings will be automatically updated in `.env` file

## 🧪 Testing

### Step 1: Test Facebook Login

1. **Go to Login Page**
   - Visit: `https://yourdomain.com/login`
   - You should see Facebook login button

2. **Test Login Flow**
   - Click "Facebook" button
   - You'll be redirected to Facebook
   - Authorize the app
   - You'll be redirected back and logged in

### Step 2: Test Google Login

1. **Test Google Login**
   - Click "Google" button on login page
   - You'll be redirected to Google
   - Choose Google account
   - Authorize the app
   - You'll be redirected back and logged in

### Step 3: Verify User Creation

1. **Check User in Admin**
   - Go to Admin → Users
   - Look for new user created via social login
   - Verify user details are populated correctly

## 🔧 Troubleshooting

### Common Issues

#### 1. "Invalid redirect URI" Error

**Problem**: Facebook/Google shows "Invalid redirect URI" error

**Solution**:
- Check redirect URI in Facebook/Google console
- Ensure it matches exactly: `https://yourdomain.com/user/auth/facebook/callback`
- For development, also add: `http://localhost:8000/user/auth/facebook/callback`

#### 2. "App not found" Error

**Problem**: Facebook/Google shows "App not found" error

**Solution**:
- Verify App ID/Client ID is correct
- Check if app is published (for production)
- Ensure app is not in development mode restrictions

#### 3. Social Login Buttons Not Showing

**Problem**: Social login buttons don't appear on login page

**Solution**:
- Check if social login is enabled in admin settings
- Verify credentials are saved correctly
- Clear browser cache
- Check if there are any JavaScript errors

#### 4. "Configuration test failed" Error

**Problem**: Admin panel shows configuration test failed

**Solution**:
- Verify credentials are correct
- Check if redirect URIs are properly configured
- Ensure app has required permissions
- Check server logs for detailed error messages

### Debug Steps

1. **Check Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verify Environment Variables**
   ```bash
   php artisan config:show services.facebook
   php artisan config:show services.google
   ```

3. **Test Configuration**
   - Use admin panel "Test Configuration" buttons
   - Check for specific error messages

## 🔒 Security Notes

### Important Security Considerations

1. **Environment Variables**
   - Never commit `.env` file to version control
   - Use strong, unique secrets
   - Rotate secrets regularly

2. **Redirect URIs**
   - Only add trusted domains to redirect URIs
   - Use HTTPS in production
   - Remove development URIs in production

3. **App Permissions**
   - Only request necessary permissions
   - Regularly review and remove unused permissions
   - Monitor app usage and access

4. **User Data**
   - Social login users are automatically verified
   - Store minimal required data
   - Implement proper data retention policies

### Production Checklist

- [ ] Use HTTPS for all redirect URIs
- [ ] Remove development/test redirect URIs
- [ ] Set strong, unique secrets
- [ ] Enable app review for Facebook (if required)
- [ ] Configure proper OAuth consent screen for Google
- [ ] Test login flow thoroughly
- [ ] Monitor logs for any issues
- [ ] Set up proper error handling

## 📞 Support

If you encounter any issues:

1. Check this documentation first
2. Review server logs
3. Test with different browsers
4. Verify all configurations are correct
5. Contact support if issues persist

## 🎉 Success!

Once configured correctly, users will be able to:

- Login with Facebook account
- Login with Google account
- Automatically create accounts if they don't exist
- Access all features as regular users

The social login system is now fully integrated with your ApnaFund application!

---

**Last Updated**: January 2024  
**Version**: 1.0  
**Compatible with**: ApnaFund v1.0+
