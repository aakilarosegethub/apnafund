# Fix 500 Error - Users Table Missing

## Problem
```
ResponseCode: 500
Error: Table 'gofund2.users' doesn't exist
```

## Solution

### Option 1: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `gofund2`
3. Click on "SQL" tab
4. Copy and paste this SQL:

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
  `address` text DEFAULT NULL,
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
  `kyc_data` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

5. Click "Go"

### Option 2: Using MySQL Command Line

```bash
mysql -u root -p gofund2 < create_users_table.sql
```

### Option 3: Using Laravel Migrations

```bash
php artisan migrate --force
```

**Note:** If you get "Table already exists" error, mark the migration as run:
```bash
php artisan migrate:status
# Then manually insert into migrations table if needed
```

### Option 4: Import SQL File

1. Open phpMyAdmin
2. Select `gofund2` database
3. Click "Import" tab
4. Choose file: `create_users_table.sql`
5. Click "Go"

## Verify Table Creation

After creating the table, verify it exists:

```sql
SHOW TABLES LIKE 'users';
DESCRIBE users;
```

## Test API

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

## Expected Response (Success)

```json
{
    "UserLogin": {
        "id": "1",
        "name": "Test User",
        "email": "test@example.com",
        "mobile": "1234567890",
        "ccode": "+1",
        "status": "1",
        "rdate": "2025-12-27 16:50:00",
        "wallet": "0"
    },
    "currency": "$",
    "ResponseCode": "200",
    "Result": "true",
    "ResponseMsg": "Sign Up Done Successfully!"
}
```

## Files Created

- `create_users_table.sql` - SQL script to create table
- `create_users_table.php` - PHP script to create table (if MySQL connection works)

