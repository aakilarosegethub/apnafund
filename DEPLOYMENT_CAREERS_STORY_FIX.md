# Careers & Story Page Fix - Deployment Checklist

## Problem
The careers page content was appearing at `/page/story` instead of `/apnacrowdfunding-careers` due to:
- Incorrect database slug (included domain: `apnacrowdfunding.com/careers`)
- Wrong template content in `story.blade.php`
- Missing dedicated careers template

## Local Environment Status
✅ Database updated (slug fixed)
✅ New careers template created
✅ Story template updated with proper content
✅ Routes updated
✅ Cache cleared
✅ Testing ready

## Files to Deploy to Live Server

### 1. Database Changes
**File**: `fix_careers_story_live_server.sql`
```bash
# Run on live server database
mysql -u [username] -p [database_name] < fix_careers_story_live_server.sql
```

### 2. Template Files (Upload to Live Server)
```
resources/views/themes/green/page/apnacrowdfunding-careers.blade.php (NEW)
resources/views/themes/green/page/story.blade.php (MODIFIED)
```

### 3. Route Configuration (Upload to Live Server)
```
routes/web.php (MODIFIED)
```

## Deployment Steps

### Step 1: Backup Live Server
```bash
# Backup database
mysqldump -u [username] -p [database] > backup_before_careers_fix_$(date +%Y%m%d).sql

# Backup affected files
cp routes/web.php routes/web.php.backup
cp resources/views/themes/green/page/story.blade.php resources/views/themes/green/page/story.blade.php.backup
```

### Step 2: Apply Database Changes
```bash
# Run the SQL fix
mysql -u [username] -p [database_name] < fix_careers_story_live_server.sql
```

### Step 3: Upload Template Files
Upload the following files to live server:
- `resources/views/themes/green/page/apnacrowdfunding-careers.blade.php` (NEW FILE)
- `resources/views/themes/green/page/story.blade.php` (REPLACE EXISTING)
- `routes/web.php` (REPLACE EXISTING)

### Step 4: Clear Cache on Live Server
```bash
cd /path/to/live/site
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan route:cache
```

### Step 5: Test URLs
Visit these URLs to verify:
- ✅ `https://apnacrowdfunding.com/apnacrowdfunding-careers` → Should show careers page
- ✅ `https://apnacrowdfunding.com/page/story` → Should show "Our Story" page
- ✅ `https://apnacrowdfunding.com/our-story` → Should show "Our Story" page

## Expected Results

| URL | Expected Content | Status |
|-----|------------------|--------|
| `/apnacrowdfunding-careers` | Careers/Jobs page with job openings | After Deploy |
| `/page/story` | Our Story - company history and mission | After Deploy |
| `/our-story` | Our Story - company history and mission | After Deploy |

## Rollback Plan (If Needed)

If anything goes wrong:

```bash
# Restore database
mysql -u [username] -p [database_name] < backup_before_careers_fix_[date].sql

# Restore files
cp routes/web.php.backup routes/web.php
cp resources/views/themes/green/page/story.blade.php.backup resources/views/themes/green/page/story.blade.php
rm resources/views/themes/green/page/apnacrowdfunding-careers.blade.php

# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

## Files Reference

### Created/Modified Files:
1. `fix_careers_story_live_server.sql` - Database fix for live server
2. `CAREERS_STORY_PAGE_FIX.md` - Detailed documentation
3. `resources/views/themes/green/page/apnacrowdfunding-careers.blade.php` - New careers template
4. `resources/views/themes/green/page/story.blade.php` - Updated story template
5. `routes/web.php` - Added careers and story routes

## Notes
- The issue was caused by entering full URL with domain as slug instead of just the slug
- Future pages should use slug format: `my-page-name` NOT `domain.com/my-page-name`
- Both templates are standalone and don't depend on dynamic page content from database
- SEO data can still be managed from admin panel

## Support
If you encounter any issues during deployment:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify file permissions (templates should be readable by web server)
4. Ensure database connection is working

---
**Deployment Date**: [To be filled]
**Deployed By**: [To be filled]
**Status**: Ready for Deployment
