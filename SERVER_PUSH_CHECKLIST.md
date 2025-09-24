# 🚀 Server Push Checklist - ApnaFund

## 📋 Files to Push to Live Server

### 🔧 Core Social Login Files (REQUIRED)

1. **Controllers:**
   - `app/Http/Controllers/User/Auth/SocialLoginController.php` ✅
   - `app/Http/Controllers/Admin/SocialLoginSettingController.php` ✅

2. **Configuration:**
   - `config/services.php` ✅

3. **Routes:**
   - `routes/user.php` ✅
   - `routes/admin.php` ✅

4. **Models:**
   - `app/Models/User.php` ✅

5. **Views:**
   - `resources/views/themes/apnafund/user/auth/login.blade.php` ✅
   - `resources/views/admin/setting/social_login.blade.php` ✅
   - `resources/views/admin/partials/sidebar.blade.php` ✅

6. **Database Migration:**
   - `database/migrations/2024_01_15_000000_add_social_login_fields_to_users_table.php` ✅

### 🛠️ Admin User Management Files (REQUIRED)

7. **Controllers:**
   - `app/Http/Controllers/Admin/UserController.php` ✅

8. **Views:**
   - `resources/views/admin/user/delete-all-users.blade.php` ✅

### 🧪 Test & Fix Files (OPTIONAL)

9. **Test Files:**
   - `test_social_login.php` (Optional - for testing)
   - `test_social_final.php` (Optional - for testing)
   - `debug_social_login.php` (Optional - for debugging)
   - `fix_social_login.php` (Optional - for fixing)
   - `fix_social_login_live.php` (Optional - for live server fixes)

10. **Documentation:**
    - `SOCIAL_LOGIN_SETUP_GUIDE.md` (Optional - for reference)
    - `test_delete_users.php` (Optional - for testing)

## 🎯 Priority Order for Push

### **HIGH PRIORITY (Must Push)**
```
1. app/Http/Controllers/User/Auth/SocialLoginController.php
2. app/Http/Controllers/Admin/SocialLoginSettingController.php
3. config/services.php
4. routes/user.php
5. routes/admin.php
6. app/Models/User.php
7. resources/views/themes/apnafund/user/auth/login.blade.php
8. resources/views/admin/setting/social_login.blade.php
9. resources/views/admin/partials/sidebar.blade.php
10. database/migrations/2024_01_15_000000_add_social_login_fields_to_users_table.php
11. app/Http/Controllers/Admin/UserController.php
12. resources/views/admin/user/delete-all-users.blade.php
```

### **MEDIUM PRIORITY (Recommended)**
```
13. fix_social_login_live.php (For error handling)
14. test_social_final.php (For testing)
```

### **LOW PRIORITY (Optional)**
```
15. SOCIAL_LOGIN_SETUP_GUIDE.md (Documentation)
16. test_social_login.php (Testing)
17. debug_social_login.php (Debugging)
18. test_delete_users.php (Testing)
```

## 🚀 Quick Push Commands

### **Option 1: Push All Core Files**
```bash
# Core social login files
scp app/Http/Controllers/User/Auth/SocialLoginController.php user@server:/path/to/app/Http/Controllers/User/Auth/
scp app/Http/Controllers/Admin/SocialLoginSettingController.php user@server:/path/to/app/Http/Controllers/Admin/
scp config/services.php user@server:/path/to/config/
scp routes/user.php user@server:/path/to/routes/
scp routes/admin.php user@server:/path/to/routes/
scp app/Models/User.php user@server:/path/to/app/Models/
scp resources/views/themes/apnafund/user/auth/login.blade.php user@server:/path/to/resources/views/themes/apnafund/user/auth/
scp resources/views/admin/setting/social_login.blade.php user@server:/path/to/resources/views/admin/setting/
scp resources/views/admin/partials/sidebar.blade.php user@server:/path/to/resources/views/admin/partials/
scp database/migrations/2024_01_15_000000_add_social_login_fields_to_users_table.php user@server:/path/to/database/migrations/
scp app/Http/Controllers/Admin/UserController.php user@server:/path/to/app/Http/Controllers/Admin/
scp resources/views/admin/user/delete-all-users.blade.php user@server:/path/to/resources/views/admin/user/
```

### **Option 2: Git Push (Recommended)**
```bash
git add .
git commit -m "Add social login and admin user management features"
git push origin main
```

## ⚠️ Important Notes

### **Before Push:**
1. ✅ Test all functionality locally
2. ✅ Backup live server database
3. ✅ Check .env file for credentials
4. ✅ Verify file permissions

### **After Push:**
1. ✅ Run: `php artisan migrate`
2. ✅ Run: `php artisan config:clear`
3. ✅ Run: `php artisan cache:clear`
4. ✅ Test social login functionality
5. ✅ Test admin user deletion

## 🔧 Environment Variables to Set

Add these to your live server `.env` file:
```env
# Social Login (Optional - can be disabled)
FACEBOOK_CLIENT_ID=your_facebook_app_id
FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
FACEBOOK_REDIRECT_URI=https://yourdomain.com/user/auth/facebook/callback
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/user/auth/google/callback
```

## 🎯 Final Checklist

- [ ] All core files pushed
- [ ] Database migration run
- [ ] Cache cleared
- [ ] Social login tested
- [ ] Admin user deletion tested
- [ ] Error handling verified
- [ ] Live server stable

## 📞 Support

If you encounter any issues:
1. Check `fix_social_login_live.php` for solutions
2. Verify all files are uploaded correctly
3. Check server logs for errors
4. Test functionality step by step

---

**Total Files to Push: 12 Core Files + 6 Optional Files**
**Estimated Time: 10-15 minutes**
**Risk Level: Low (with proper testing)**
