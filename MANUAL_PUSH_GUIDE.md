# 🚀 Manual Push Guide - ApnaFund

## 📋 Files Ready for Server Upload

Main ne aapke liye complete list bana di hai jo files server par push karni hain:

### ✅ **CORE FILES (Must Upload) - 12 Files:**

1. **`app/Http/Controllers/User/Auth/SocialLoginController.php`** ✅
2. **`app/Http/Controllers/Admin/SocialLoginSettingController.php`** ✅  
3. **`config/services.php`** ✅
4. **`routes/user.php`** ✅
5. **`routes/admin.php`** ✅
6. **`app/Models/User.php`** ✅
7. **`resources/views/themes/apnafund/user/auth/login.blade.php`** ✅
8. **`resources/views/admin/setting/social_login.blade.php`** ✅
9. **`resources/views/admin/partials/sidebar.blade.php`** ✅
10. **`database/migrations/2024_01_15_000000_add_social_login_fields_to_users_table.php`** ✅
11. **`app/Http/Controllers/Admin/UserController.php`** ✅
12. **`resources/views/admin/user/delete-all-users.blade.php`** ✅

### 🛠️ **OPTIONAL FILES (Recommended) - 3 Files:**

13. **`fix_social_login_live.php`** (Error handling ke liye)
14. **`test_social_final.php`** (Testing ke liye)
15. **`SERVER_PUSH_CHECKLIST.md`** (Reference ke liye)

## 🚀 **UPLOAD METHODS:**

### **Method 1: cPanel File Manager (Easiest)**
1. **Login to cPanel**
2. **Open File Manager**
3. **Navigate to your website directory**
4. **Upload files maintaining directory structure**
5. **Set permissions: 644 for files, 755 for directories**

### **Method 2: FTP/SFTP Upload**
1. **Connect via FTP client (FileZilla, WinSCP)**
2. **Upload all files maintaining directory structure**
3. **Set proper file permissions**

### **Method 3: SSH/Terminal Upload**
```bash
# If you have SSH access
scp -r /path/to/local/files/* user@server:/path/to/website/
```

## ⚡ **AFTER UPLOAD - Run These Commands:**

```bash
# Run these on your live server:
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 🔧 **Environment Variables to Add:**

Add these to your `.env` file on live server:
```env
# Social Login (Optional - can be disabled)
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI=https://yourdomain.com/user/auth/facebook/callback
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/user/auth/google/callback
```

## 🧪 **Testing After Upload:**

1. **Test Login Page:** Visit `/login` - should work without errors
2. **Test Admin Panel:** Visit `/admin` - should work normally
3. **Test Social Login:** Buttons should be hidden if credentials not set
4. **Test User Deletion:** Admin → Users → Delete All Users
5. **Check Error Logs:** Look for any PHP errors

## 🚨 **Important Notes:**

- **Backup:** Always backup your live server before uploading
- **Permissions:** Set correct file permissions (644 for files, 755 for directories)
- **Database:** Run migration to add social login fields
- **Cache:** Clear all caches after upload
- **Testing:** Test all functionality before going live

## 📞 **Support:**

If you encounter any issues:
1. Check `fix_social_login_live.php` for solutions
2. Verify all files are uploaded correctly
3. Check server error logs
4. Test functionality step by step

---

**Total Files to Upload: 12 Core + 3 Optional = 15 Files**
**Estimated Time: 15-20 minutes**
**Risk Level: Low (with proper testing)**

## 🎯 **Quick Start:**

1. **Upload 12 core files first**
2. **Run migration and clear cache**
3. **Test basic functionality**
4. **Upload optional files if needed**
5. **Test social login features**

**Status: Ready to Push! 🚀**
