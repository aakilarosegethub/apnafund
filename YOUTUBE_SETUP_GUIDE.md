# 🎥 YouTube Automatic Upload Setup Guide - ApnaCrowdfunding

## 📋 Overview
This guide will help you set up **YouTube automatic video upload** functionality in your ApnaCrowdfunding application. When users upload videos during campaign creation, they will be automatically uploaded to YouTube for better streaming performance.

## 🚀 Features

### ✅ **What You'll Get:**
- **Automatic YouTube Upload** - Videos uploaded to your YouTube channel
- **Better Performance** - Videos stream from YouTube CDN
- **Unlimited Storage** - No server storage limitations
- **Professional Streaming** - YouTube's optimized video delivery
- **Easy Management** - Videos appear in your YouTube channel

## 🔧 Step-by-Step Setup

### **Step 1: Google Cloud Console Setup**

#### 1.1 Create Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Sign in with your Google account
3. Click "Select a project" → "New Project"
4. **Project Name:** `ApnaCrowdfunding YouTube Integration`
5. Click "Create"

#### 1.2 Enable YouTube Data API
1. In the Google Cloud Console, go to "APIs & Services" → "Library"
2. Search for "**YouTube Data API v3**"
3. Click on it and press "**Enable**"
4. Wait for the API to be enabled

#### 1.3 Create OAuth 2.0 Credentials
1. Go to "APIs & Services" → "**Credentials**"
2. Click "**Create Credentials**" → "**OAuth 2.0 Client IDs**"
3. If prompted, configure the OAuth consent screen:
   - **User Type:** External
   - **App Name:** ApnaCrowdfunding YouTube Upload
   - **User Support Email:** Your email
   - **Developer Contact:** Your email
   - Click "Save and Continue"
   - Add your domain to authorized domains
   - Click "Save and Continue"
   - Click "Save and Continue" (skip scopes for now)
   - Click "Save and Continue" (skip test users for now)

4. **Application Type:** Web application
5. **Name:** `ApnaCrowdfunding YouTube Upload`
6. **Authorized redirect URIs:** 
   ```
   https://yourdomain.com/youtube/callback
   ```
   (Replace `yourdomain.com` with your actual domain)
7. Click "**Create**"

#### 1.4 Download Credentials
1. Click on the created OAuth client
2. Click "**Download JSON**"
3. Save the file as `youtube-credentials.json`

### **Step 2: File Setup**

#### 2.1 Upload Credentials File
```bash
# Copy the downloaded file to your project
cp youtube-credentials.json /Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding/storage/app/
```

#### 2.2 Set File Permissions
```bash
chmod 644 /Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding/storage/app/youtube-credentials.json
```

### **Step 3: Environment Configuration**

#### 3.1 Add to .env File
Add these variables to your `.env` file:

```env
# YouTube API Configuration
YOUTUBE_CLIENT_ID=your_client_id_here
YOUTUBE_CLIENT_SECRET=your_client_secret_here
YOUTUBE_REDIRECT_URI=https://yourdomain.com/youtube/callback
YOUTUBE_CREDENTIALS_PATH=/Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding/storage/app/youtube-credentials.json

# YouTube OAuth Tokens (will be set after authorization)
YOUTUBE_ACCESS_TOKEN=
YOUTUBE_REFRESH_TOKEN=
```

#### 3.2 Get Client ID and Secret
1. Open the downloaded `youtube-credentials.json` file
2. Copy the `client_id` value to `YOUTUBE_CLIENT_ID`
3. Copy the `client_secret` value to `YOUTUBE_CLIENT_SECRET`
4. Update `YOUTUBE_REDIRECT_URI` with your actual domain

### **Step 4: Authorization**

#### 4.1 Access Admin Panel
1. Go to your admin panel: `https://yourdomain.com/admin/youtube`
2. You should see the YouTube configuration page

#### 4.2 Authorize YouTube
1. Click "**Authorize YouTube**" button
2. You'll be redirected to Google's authorization page
3. Sign in with the Google account that owns the YouTube channel
4. Grant all requested permissions
5. You'll be redirected back to your admin panel

#### 4.3 Verify Configuration
- All status indicators should show green checkmarks
- You should see "YouTube Integration is Configured!" message

## 🎯 How It Works

### **User Experience:**
1. User creates a new campaign
2. In the video section, user uploads a video file
3. User checks "🚀 Auto-upload to YouTube" checkbox
4. System automatically uploads video to YouTube
5. YouTube URL is stored in database
6. Video streams from YouTube (faster loading)

### **Technical Flow:**
```
User Upload → Temporary Storage → YouTube API → YouTube Channel → Database URL
```

## 📁 File Structure

```
apnacrowdfunding/
├── app/
│   ├── Services/
│   │   └── YouTubeUploadService.php     # Main YouTube service
│   ├── Http/Controllers/
│   │   ├── User/CampaignController.php  # Updated with YouTube upload
│   │   └── Admin/YouTubeController.php  # Admin configuration
├── config/
│   └── services.php                     # YouTube configuration
├── storage/app/
│   └── youtube-credentials.json         # Google OAuth credentials
├── resources/views/
│   ├── admin/youtube/index.blade.php    # Admin configuration page
│   └── themes/apnacrowdfunding/user/campaign/
│       ├── new.blade.php                # Campaign creation form
│       └── edit.blade.php               # Campaign edit form
└── YOUTUBE_SETUP_GUIDE.md               # This documentation
```

## 🔒 Security & Privacy

### **Video Privacy Settings:**
- Videos are uploaded as **"unlisted"** by default
- Only people with the link can view them
- Videos don't appear in YouTube search results
- You can change this in `YouTubeUploadService.php`

### **Access Control:**
- Only authenticated users can upload videos
- YouTube API has rate limits (10,000 units/day)
- Each video upload costs ~1,600 units

## 🛠️ Troubleshooting

### **Common Issues:**

#### 1. "YouTube not configured" Error
**Solution:**
- Check if `youtube-credentials.json` exists in `storage/app/`
- Verify file permissions (644)
- Ensure all environment variables are set
- Check if the JSON file is valid

#### 2. "Access token expired" Error
**Solution:**
- The system automatically refreshes tokens
- If persistent, re-authorize via admin panel
- Check if refresh token is properly set

#### 3. "Quota exceeded" Error
**Solution:**
- YouTube API has daily limits (10,000 units)
- Each video upload costs ~1,600 units
- Wait 24 hours or request quota increase
- Monitor usage in Google Cloud Console

#### 4. "Video upload failed" Error
**Solution:**
- Check video file format (MP4, AVI, MOV supported)
- Ensure file size is under 128GB
- Check internet connection
- Verify YouTube channel permissions

#### 5. "Invalid redirect URI" Error
**Solution:**
- Ensure redirect URI in Google Console matches your domain
- Check if `YOUTUBE_REDIRECT_URI` in .env is correct
- Make sure your domain is added to authorized domains

### **Debug Mode:**
Enable detailed logging by adding to `.env`:
```env
LOG_LEVEL=debug
```

Check logs in: `storage/logs/laravel.log`

## 📊 API Quotas & Limits

### **YouTube Data API v3 Limits:**
- **Daily Quota:** 10,000 units (default)
- **Video Upload:** ~1,600 units per video
- **Daily Video Limit:** ~6 videos per day

### **Request Quota Increase:**
1. Go to Google Cloud Console
2. Navigate to "APIs & Services" → "Quotas"
3. Search for "YouTube Data API v3"
4. Click "Edit Quotas"
5. Request increase (up to 1,000,000 units/day)

## 🎨 Customization Options

### **Video Settings:**
Edit `app/Services/YouTubeUploadService.php` to customize:
- Video title format
- Description template
- Tags
- Privacy status
- Category

### **Form Styling:**
Modify the checkbox styling in:
- `resources/views/themes/apnacrowdfunding/user/campaign/new.blade.php`
- `resources/views/themes/apnacrowdfunding/user/campaign/edit.blade.php`

## 📈 Performance Benefits

### **Before YouTube Integration:**
- Videos stored on server
- High bandwidth usage
- Slow loading times
- Storage space issues
- Mobile performance issues

### **After YouTube Integration:**
- Videos streamed from YouTube CDN
- 90% less bandwidth usage
- Faster loading times
- Unlimited storage
- Better mobile experience
- Professional video player

## 🔄 Maintenance

### **Regular Tasks:**
1. **Monitor API Quotas** - Check daily usage in Google Cloud Console
2. **Update Tokens** - System handles this automatically
3. **Check Logs** - Monitor for errors in `storage/logs/laravel.log`
4. **Backup Credentials** - Keep credentials file safe

### **Updates:**
- Keep Google API Client library updated
- Monitor YouTube API changes
- Update service class as needed

## 📞 Support

### **If You Need Help:**
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify all configuration steps
3. Test with a small video file first
4. Check YouTube API status: https://status.youtube.com/

### **Useful Links:**
- [YouTube Data API Documentation](https://developers.google.com/youtube/v3)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Laravel Documentation](https://laravel.com/docs)
- [OAuth 2.0 Guide](https://developers.google.com/identity/protocols/oauth2)

## 🎉 Congratulations!

Your ApnaCrowdfunding application now has **professional YouTube integration**! Users can upload videos that automatically go to YouTube for better performance and unlimited storage.

### **Next Steps:**
1. Follow the configuration steps above
2. Test with a sample video
3. Monitor the first few uploads
4. Enjoy faster, more reliable video streaming! 🚀

---

## 📝 Quick Reference

### **Important URLs:**
- **Admin Panel:** `/admin/youtube`
- **Authorization:** `/admin/youtube/auth`
- **Callback:** `/youtube/callback`

### **Environment Variables:**
```env
YOUTUBE_CLIENT_ID=your_client_id
YOUTUBE_CLIENT_SECRET=your_client_secret
YOUTUBE_REDIRECT_URI=https://yourdomain.com/youtube/callback
YOUTUBE_CREDENTIALS_PATH=/path/to/youtube-credentials.json
YOUTUBE_ACCESS_TOKEN=auto_generated
YOUTUBE_REFRESH_TOKEN=auto_generated
```

### **File Locations:**
- **Credentials:** `storage/app/youtube-credentials.json`
- **Service:** `app/Services/YouTubeUploadService.php`
- **Admin Controller:** `app/Http/Controllers/Admin/YouTubeController.php`
- **Admin View:** `resources/views/admin/youtube/index.blade.php`

---

**Happy Uploading! 🎥✨**
