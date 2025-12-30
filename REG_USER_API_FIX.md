# User Registration API - Setup & Fix Guide

## API Endpoint
**URL:** `http://localhost/apnafund/api/reg_user.php`  
**Method:** POST  
**Content-Type:** application/json

## Request Body
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "mobile": "1234567890",
    "password": "password123",
    "ccode": "+1"
}
```

## Response Format

### Success Response (200)
```json
{
    "UserLogin": {
        "id": "1",
        "name": "John Doe",
        "email": "john@example.com",
        "mobile": "1234567890",
        "ccode": "+1",
        "rdate": "2025-01-20 12:00:00",
        "status": "1"
    },
    "currency": "USD",
    "ResponseCode": "200",
    "Result": "true",
    "ResponseMsg": "Sign Up Done Successfully!"
}
```

### Error Responses

#### Missing Fields (401)
```json
{
    "ResponseCode": "401",
    "Result": "false",
    "ResponseMsg": "Something Went Wrong!"
}
```

#### Mobile Already Used (401)
```json
{
    "ResponseCode": "401",
    "Result": "false",
    "ResponseMsg": "Mobile Number Already Used!"
}
```

#### Email Already Used (401)
```json
{
    "ResponseCode": "401",
    "Result": "false",
    "ResponseMsg": "Email Address Already Used!"
}
```

## Database Fix Required

If you encounter the error: **"Field 'id' doesn't have a default value"**

This means the `tbl_user` table's `id` field is not set to AUTO_INCREMENT.

### Fix Method 1: Using phpMyAdmin
1. Open phpMyAdmin
2. Select your database (`gofund2`)
3. Click on `tbl_user` table
4. Go to "Structure" tab
5. Click "Change" on the `id` field
6. Check "A_I" (Auto Increment) checkbox
7. Click "Save"

### Fix Method 2: Using SQL
Run this SQL command in your database:
```sql
ALTER TABLE `tbl_user` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT;
```

Or if that doesn't work:
```sql
ALTER TABLE `tbl_user` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
```

### Fix Method 3: Using PHP Script
Run the provided script:
```bash
php fix_tbl_user_auto_increment.php
```

## Laravel Route

The API is already configured in Laravel:
- **Route:** `/api/reg_user.php`
- **Controller:** `App\Http\Controllers\Api\AuthController@register`
- **File:** `routes/api.php` (line 36)

## Testing the API

### Using cURL
```bash
curl -X POST 'http://localhost/apnafund/api/reg_user.php' \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "mobile": "1234567890",
    "password": "password123",
    "ccode": "+1"
}'
```

### Using Postman
1. Method: POST
2. URL: `http://localhost/apnafund/api/reg_user.php`
3. Headers: `Content-Type: application/json`
4. Body (raw JSON):
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "mobile": "1234567890",
    "password": "password123",
    "ccode": "+1"
}
```

## Features

✅ User registration with validation  
✅ Email uniqueness check  
✅ Mobile number uniqueness check  
✅ Country code support  
✅ Automatic status setting (status = 1)  
✅ Returns user data on success  
✅ Returns currency from settings  
✅ Error handling with try-catch  
✅ Proper JSON responses  

## Notes

- The API uses the old `tbl_user` table structure
- Password is stored in plain text (consider hashing in future)
- Mobile number validation includes country code
- Status is automatically set to 1 (active) on registration

