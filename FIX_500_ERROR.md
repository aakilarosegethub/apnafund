# Fix 500 Error - Step by Step

## Quick Fix (Run this first):

Browser mein yeh URL open karo:
```
http://localhost/apnacrowdfunding/QUICK_FIX.php
```

Ya phir terminal mein:
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding
/Applications/XAMPP/xamppfiles/bin/php QUICK_FIX.php
```

## Manual Fix Steps:

### Step 1: .env File Create Karo
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding
cp env_template.txt .env
```

### Step 2: APP_KEY Generate Karo (IMPORTANT!)
```bash
/Applications/XAMPP/xamppfiles/bin/php artisan key:generate
```

### Step 3: Cache Clear Karo
```bash
/Applications/XAMPP/xamppfiles/bin/php artisan config:clear
/Applications/XAMPP/xamppfiles/bin/php artisan cache:clear
/Applications/XAMPP/xamppfiles/bin/php artisan view:clear
/Applications/XAMPP/xamppfiles/bin/php artisan route:clear
```

### Step 4: Storage Permissions Fix Karo
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 5: Test Karo
Browser mein: `http://localhost/apnacrowdfunding/`

## Agar Abhi Bhi 500 Error Aaye:

1. **Check Apache Error Log:**
   - XAMPP Control Panel > Apache > Logs
   - Ya: `/Applications/XAMPP/xamppfiles/logs/error_log`

2. **Check Laravel Log:**
   ```bash
   tail -50 /Applications/XAMPP/xamppfiles/htdocs/apnacrowdfunding/storage/logs/laravel.log
   ```

3. **Test Error Script:**
   Browser mein: `http://localhost/apnacrowdfunding/test_error.php`

## Common Issues:

- ❌ **APP_KEY missing** → Run: `php artisan key:generate`
- ❌ **.env file missing** → Copy from `env_template.txt`
- ❌ **Vendor not installed** → Run: `composer install`
- ❌ **Storage not writable** → Run: `chmod -R 755 storage`

