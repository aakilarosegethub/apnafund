# 🎥 YouTube Integration Guide - ApnaCrowdfunding

## 📋 Overview
This guide explains how to set up and configure YouTube automatic video upload functionality in your ApnaCrowdfunding application.

## 🚀 Features Implemented

### ✅ **Current Features:**
1. **Manual YouTube URL Input** - Users can paste existing YouTube video URLs
2. **Automatic YouTube Upload** - Videos are automatically uploaded to YouTube when checkbox is selected
3. **Local Video Upload** - Traditional file upload to server
4. **Smart Toggle System** - Users can choose between upload methods
5. **Live Preview** - YouTube videos show preview in forms
6. **Better Streaming** - YouTube videos load faster and use less server bandwidth

## 🔧 Configuration Steps

### **Step 1: Google Cloud Console Setup**

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/
   - Sign in with your Google account

2. **Create a New Project**
   - Click "Select a project" → "New Project"
   - Name: "ApnaCrowdfunding YouTube Integration"
   - Click "Create"

3. **Enable YouTube Data API v3**
   - Go to "APIs & Services" → "Library"
   - Search for "YouTube Data API v3"
   - Click on it and press "Enable"

4. **Create OAuth 2.0 Credentials**
   - Go to "APIs & Services" → "Credentials"
   - Click "Create Credentials" → "OAuth 2.0 Client IDs"
   - Application type: "Web application"
   - Name: "ApnaCrowdfunding YouTube Upload"
   - Authorized redirect URIs: `https://yourdomain.com/youtube/callback`
   - Click "Create"

5. **Download Credentials**
   - Click on the created OAuth client
   - Click "Download JSON"
   - Save as `youtube-credentials.json`

### **Step 2: File Setup**

1. **Upload Credentials File**
   ```bash
   # Place the downloaded file in storage/app/
   cp youtube-credentials.json /Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding/storage/app/
   ```

2. **Set File Permissions**
   ```bash
   chmod 644 /Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding/storage/app/youtube-credentials.json
   ```

### **Step 3: Environment Configuration**

Add these variables to your `.env` file:

```env
# YouTube API Configuration
YOUTUBE_CLIENT_ID=your_client_id_here
YOUTUBE_CLIENT_SECRET=your_client_secret_here
YOUTUBE_REDIRECT_URI=https://yourdomain.com/youtube/callback
YOUTUBE_CREDENTIALS_PATH=/Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding/storage/app/youtube-credentials.json

# YouTube OAuth Tokens (will be set after first authorization)
YOUTUBE_ACCESS_TOKEN=
YOUTUBE_REFRESH_TOKEN=
```

### **Step 4: OAuth Authorization**

1. **Create Authorization Route** (Add to `routes/web.php`):
   ```php
   Route::get('/youtube/auth', function() {
       $youtubeService = new \App\Services\YouTubeUploadService();
       return redirect($youtubeService->getAuthUrl());
   })->name('youtube.auth');

   Route::get('/youtube/callback', function(\Illuminate\Http\Request $request) {
       $youtubeService = new \App\Services\YouTubeUploadService();
       $accessToken = $youtubeService->handleCallback($request->get('code'));
       
       // Store tokens in .env or database
       // You can create a simple admin interface for this
       
       return redirect('/admin/youtube/success');
   })->name('youtube.callback');
   ```

2. **First Time Authorization**
   - Visit: `https://yourdomain.com/youtube/auth`
   - Sign in with the Google account that owns the YouTube channel
   - Grant permissions
   - Copy the access token and refresh token to your `.env` file

## 📁 File Structure

```
apnacrowdfunding/
├── app/
│   ├── Services/
│   │   └── YouTubeUploadService.php     # Main YouTube service
│   └── Http/Controllers/User/
│       └── CampaignController.php       # Updated with YouTube upload
├── config/
│   └── services.php                     # YouTube configuration
├── storage/app/
│   └── youtube-credentials.json         # Google OAuth credentials
├── resources/views/themes/apnacrowdfunding/user/campaign/
│   ├── new.blade.php                    # Campaign creation form
│   └── edit.blade.php                   # Campaign edit form
└── YOUTUBE_INTEGRATION_GUIDE.md         # This documentation
```

## 🎯 How It Works

### **User Experience:**
1. User creates/edits a campaign
2. In video section, user sees 3 options:
   - **Upload Video File** (traditional)
   - **Upload Video File + Auto-upload to YouTube** (new feature)
   - **YouTube URL** (paste existing URL)

### **Technical Flow:**
1. User selects "Auto-upload to YouTube" checkbox
2. Video file is uploaded to server temporarily
3. `YouTubeUploadService` uploads video to YouTube
4. YouTube returns video URL
5. URL is stored in database
6. Local file is deleted (optional)

## 🔒 Security & Privacy

### **Video Privacy Settings:**
- Videos are uploaded as **"unlisted"** by default
- Only people with the link can view them
- Videos don't appear in search results
- You can change this in `YouTubeUploadService.php`

### **Access Control:**
- Only authenticated users can upload videos
- YouTube API has rate limits (10,000 units/day)
- Each video upload costs ~1,600 units

## 🛠️ Troubleshooting

### **Common Issues:**

1. **"YouTube not configured" Error**
   - Check if `youtube-credentials.json` exists
   - Verify file permissions
   - Ensure all environment variables are set

2. **"Access token expired" Error**
   - Refresh token automatically handles this
   - If persistent, re-authorize via `/youtube/auth`

3. **"Quota exceeded" Error**
   - YouTube API has daily limits
   - Wait 24 hours or request quota increase

4. **"Video upload failed" Error**
   - Check video file format (MP4, AVI, MOV supported)
   - Ensure file size is under 128GB
   - Check internet connection

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
Edit `YouTubeUploadService.php` to customize:
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

### **After YouTube Integration:**
- Videos streamed from YouTube CDN
- 90% less bandwidth usage
- Faster loading times
- Unlimited storage
- Better mobile experience

## 🔄 Maintenance

### **Regular Tasks:**
1. **Monitor API Quotas** - Check daily usage
2. **Update Tokens** - Refresh tokens when needed
3. **Check Logs** - Monitor for errors
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

---

## 🎉 Congratulations!

Your ApnaCrowdfunding application now has **professional YouTube integration**! Users can upload videos that automatically go to YouTube for better performance and unlimited storage.

**Next Steps:**
1. Follow the configuration steps above
2. Test with a sample video
3. Monitor the first few uploads
4. Enjoy faster, more reliable video streaming! 🚀
