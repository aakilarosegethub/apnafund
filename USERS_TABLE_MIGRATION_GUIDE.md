# Users Table Migration Guide

## Current Situation

1. **Code Updated**: `AuthController` ab `users` table use karta hai (instead of `tbl_user`)
2. **Field Mapping**: 
   - `ccode` → `country_code`
   - `rdate` → `created_at` (Laravel automatically)
   - `wallet` → `balance`
3. **Database Issue**: `users` table `gofund2` database mein exist nahi karti

## Solution Options

### Option 1: Run Migrations (Recommended)

```bash
php artisan migrate --force
```

Lekin pehle check karein ki `users` table already exist to nahi karti:

```sql
-- Check if users table exists
SHOW TABLES LIKE 'users';

-- If exists, check structure
DESCRIBE users;
```

### Option 2: Create Users Table Manually

Agar migrations run nahi ho sakti, to manually table create karein:

```sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `address` json DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `kc` tinyint(1) DEFAULT 0,
  `ec` tinyint(1) DEFAULT 0,
  `sc` tinyint(1) DEFAULT 0,
  `ts` tinyint(1) DEFAULT 0,
  `tc` tinyint(1) DEFAULT 0,
  `ref_by` bigint(20) UNSIGNED DEFAULT 0,
  `ver_code` varchar(255) DEFAULT NULL,
  `ver_code_send_at` timestamp NULL DEFAULT NULL,
  `balance` decimal(18,8) DEFAULT 0.00000000,
  `kyc_data` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Option 3: Use tbl_user Table (If it exists)

Agar `tbl_user` table exist karti hai, to code ko revert karke `tbl_user` use kar sakte hain.

## Code Changes Made

### AuthController.php
- All `tbl_user` references changed to `users`
- Field mapping: `ccode` → `country_code`
- Response mapping: `country_code as ccode`, `created_at as rdate`, `balance as wallet`

### Gofund.php
- `insertDataId_Api` method updated to handle both `users` and `tbl_user` tables

## Testing

After creating the table, test the API:

```bash
curl -X POST 'http://localhost/apnafund/api/reg_user.php' \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "mobile": "1234567890",
    "password": "password123",
    "ccode": "+1"
}'
```

## Next Steps

1. Check if `users` table exists in `gofund2` database
2. If not, run migrations or create table manually
3. Test the API endpoint
4. If `tbl_user` table exists and you want to use it, let me know and I'll revert the changes

