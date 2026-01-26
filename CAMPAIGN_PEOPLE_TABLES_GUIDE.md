# Campaign People/Collaborators Feature - Tables & Issues

## 📱 Live URL
```
https://apnacrowdfunding.com/user/campaign/edit/demo-campaihgn/people
```

## 🗄️ Database Tables Involved

### 1️⃣ Main Table: `campaign_collaborators`
```sql
CREATE TABLE `campaign_collaborators` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_collaborators_campaign_id_user_id_unique` (`campaign_id`, `user_id`),
  CONSTRAINT `campaign_collaborators_campaign_id_foreign` FOREIGN KEY (`campaign_id`) 
    REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `campaign_collaborators_user_id_foreign` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

### 2️⃣ Related Tables

#### `campaigns` (Main campaign data)
- `id` - Campaign ID
- `user_id` - Campaign owner (creator)
- `slug` - Campaign slug
- `name` - Campaign name
- `status` - Campaign status
- All other campaign fields

#### `users` (User accounts)
- `id` - User ID
- `username` - Username
- `email` - Email
- `firstname` - First name
- `lastname` - Last name
- `status` - User status (1 = active)
- `image` - Profile image

---

## 🔧 Feature Functionality

### People/Collaborators Tab Features:

1. **View Collaborators**
   - List all collaborators for the campaign
   - Shows user details (name, email, image)

2. **Add Collaborator**
   - Search users by username/email/name
   - Add user as collaborator
   - Owner-only feature

3. **Remove Collaborator**
   - Delete collaborator from campaign
   - Owner-only feature

---

## 🛣️ Routes

```php
// In routes/user.php
Route::post('collaborators/add/{slug}', 'addCollaborator')->name('collaborators.add');
Route::delete('collaborators/remove/{slug}/{userId}', 'removeCollaborator')->name('collaborators.remove');
Route::get('collaborators/search', 'searchUsers')->name('collaborators.search');
```

---

## 📂 Files Involved

### Controllers
- `app/Http/Controllers/User/CampaignController.php`
  - Line 15: `use App\Models\CampaignCollaborator;`
  - Line 514-515: Load collaborators for people section
  - Line 1579-1655: `addCollaborator()` function
  - Line 1660-1704: `removeCollaborator()` function
  - Line 1709+: `searchUsers()` function

### Models
- `app/Models/CampaignCollaborator.php` - Collaborator model
- `app/Models/Campaign.php` - Line 135: `collaborators()` relationship
- `app/Models/User.php` - User model

### Views (Theme-based)
- `resources/views/themes/green/user/campaign/edit.blade.php`
- `resources/views/themes/primary/user/campaign/edit.blade.php`
- `resources/views/themes/apnafund/user/campaign/edit.blade.php`

---

## ⚠️ Common Issues & Solutions

### Issue 1: Table Not Exists
**Error**: `Table 'campaign_collaborators' doesn't exist`

**Solution**: Run SQL script
```bash
mysql -u username -p database_name < create_campaign_collaborators_table.sql
```

### Issue 2: Relationships Not Working
**Check**:
1. Campaign model has `collaborators()` relationship
2. CampaignCollaborator model has `campaign()` and `user()` relationships

### Issue 3: Routes Not Working
**Check**:
1. Routes are defined in `routes/user.php`
2. User is authenticated
3. User is campaign owner (for add/remove)

### Issue 4: View Not Loading
**Check**:
1. Collaborators variable is passed to view (line 518 in controller)
2. Correct theme folder is being used
3. Section is set to 'people'

### Issue 5: Foreign Key Constraints
**Check**:
1. `campaigns` table exists with `id` field
2. `users` table exists with `id` field
3. Data integrity is maintained

---

## 🔍 Check Live Server

### Step 1: Verify Table Exists
```sql
-- Check if table exists
SHOW TABLES LIKE 'campaign_collaborators';

-- Check table structure
DESCRIBE campaign_collaborators;

-- Check data
SELECT * FROM campaign_collaborators LIMIT 10;
```

### Step 2: Check Foreign Keys
```sql
-- Check foreign key constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'campaign_collaborators'
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

### Step 3: Check Campaign Data
```sql
-- Check if demo campaign exists
SELECT id, user_id, slug, name 
FROM campaigns 
WHERE slug LIKE '%demo%' 
OR slug = 'demo-campaihgn';
```

### Step 4: Test Relationships
```sql
-- Check if collaborators exist for campaign
SELECT 
    cc.*,
    c.name as campaign_name,
    u.username,
    u.email
FROM campaign_collaborators cc
JOIN campaigns c ON cc.campaign_id = c.id
JOIN users u ON cc.user_id = u.id
WHERE c.slug = 'demo-campaihgn';
```

---

## 🐛 Debugging Steps

### 1. Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### 2. Check Database Connection
```php
// In tinker
php artisan tinker
>>> \DB::connection()->getPdo();
>>> \App\Models\CampaignCollaborator::count();
```

### 3. Test Controller Methods
```php
php artisan tinker
>>> $campaign = \App\Models\Campaign::where('slug', 'demo-campaihgn')->first();
>>> $campaign->collaborators;
>>> $campaign->collaborators()->with('user')->get();
```

### 4. Check Routes
```bash
php artisan route:list | grep collaborator
```

Expected output:
```
POST   user/campaign/collaborators/add/{slug}
DELETE user/campaign/collaborators/remove/{slug}/{userId}
GET    user/campaign/collaborators/search
```

---

## 📋 SQL Fix Script

Create file: `fix_campaign_collaborators_live.sql`

```sql
-- ========================================
-- FIX: Campaign Collaborators Table
-- Live Server Fix
-- ========================================

-- Drop table if exists (careful!)
-- DROP TABLE IF EXISTS campaign_collaborators;

-- Create table
CREATE TABLE IF NOT EXISTS `campaign_collaborators` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_collaborators_campaign_id_user_id_unique` (`campaign_id`, `user_id`),
  KEY `campaign_collaborators_campaign_id_foreign` (`campaign_id`),
  KEY `campaign_collaborators_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign keys (if not already added)
ALTER TABLE `campaign_collaborators`
  ADD CONSTRAINT `campaign_collaborators_campaign_id_foreign` 
    FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE;

ALTER TABLE `campaign_collaborators`
  ADD CONSTRAINT `campaign_collaborators_user_id_foreign` 
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Verify table
SELECT COUNT(*) as table_exists 
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name = 'campaign_collaborators';

-- Check structure
DESCRIBE campaign_collaborators;
```

---

## ✅ Checklist

Live server pr check karen:

- [ ] **Table Exists**: `campaign_collaborators` table hai?
- [ ] **Foreign Keys**: Constraints properly set hain?
- [ ] **Campaign Exists**: Demo campaign database mai hai?
- [ ] **User Access**: Logged in user campaign owner hai?
- [ ] **Routes Working**: Collaborator routes accessible hain?
- [ ] **Model Loaded**: CampaignCollaborator model auto-loaded hai?
- [ ] **Relationship**: Campaign model mai `collaborators()` method hai?
- [ ] **View File**: Correct theme ka edit.blade.php file hai?
- [ ] **JavaScript**: Frontend JS collaborator functionality k liye hai?
- [ ] **Permissions**: Owner-only checks kaam kar rahe hain?

---

## 🚀 Quick Fix Commands

```bash
# 1. Check table on live server
mysql -u username -p database_name -e "DESCRIBE campaign_collaborators;"

# 2. Create table if missing
mysql -u username -p database_name < create_campaign_collaborators_table.sql

# 3. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Check routes
php artisan route:list | grep collaborator

# 5. Test in tinker
php artisan tinker
>>> \App\Models\CampaignCollaborator::count()
```

---

## 📞 Support Files

Yeh files check karen:
- `create_campaign_collaborators_table.sql` (already exists in project)
- `app/Models/CampaignCollaborator.php` (already exists)
- Controller methods (already implemented)
- Routes (already defined)

---

**Agar koi specific error aa raha hai to wo batayen, mai us ka solution dunga!** 🔧
