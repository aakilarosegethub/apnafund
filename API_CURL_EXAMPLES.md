# API CURL Examples

## Base URL
```
http://192.168.1.43:8000
```

---

## 1. User Registration API
**Endpoint:** `/reg_user.php`  
**Method:** `POST`

### Request
```bash
curl -X POST "http://192.168.1.43:8000/reg_user.php" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "mobile": "1234567890",
    "password": "password123",
    "ccode": "US"
  }'
```

### Response (Success - Email Verification Required)
```json
{
  "UserLogin": {
    "id": "1",
    "firstname": "John",
    "lastname": "Doe",
    "name": "John Doe",
    "email": "john.doe@example.com",
    "mobile": "1234567890",
    "ccode": "US",
    "status": "1",
    "rdate": "2024-01-15 10:30:00",
    "wallet": "0",
    "profile_pic": null,
    "ec": "0"
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "currency": "USD",
  "email_verified": false,
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "Sign Up Done Successfully! Please verify your email. OTP has been sent to your email."
}
```

### Response (Success - Email Auto Verified)
```json
{
  "UserLogin": {
    "id": "1",
    "firstname": "John",
    "lastname": "Doe",
    "name": "John Doe",
    "email": "john.doe@example.com",
    "mobile": "1234567890",
    "ccode": "US",
    "status": "1",
    "rdate": "2024-01-15 10:30:00",
    "wallet": "0",
    "profile_pic": null,
    "ec": "1"
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "currency": "USD",
  "email_verified": true,
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "Sign Up Done Successfully!"
}
```

### Response (Error - Email Already Exists)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Email Address Already Used!"
}
```

### Response (Error - Mobile Already Exists)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Mobile Number Already Used!"
}
```

---

## 2. Verify Email OTP API
**Endpoint:** `/verify_email_otp.php`  
**Method:** `POST`

### Request
```bash
curl -X POST "http://192.168.1.43:8000/verify_email_otp.php" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "code": "123456"
  }'
```

### Response (Success)
```json
{
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "Email verified successfully!"
}
```

### Response (Error - Invalid Code)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Invalid verification code!"
}
```

### Response (Error - Code Expired)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Verification code has expired. Please request a new one."
}
```

### Response (Error - User Not Found)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "User not found!"
}
```

### Response (Error - Missing Parameters)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Code and Email are required!"
}
```

---

## 3. Complete Registration Flow Example

### Step 1: Register User
```bash
curl -X POST "http://192.168.1.43:8000/reg_user.php" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "email": "jane.smith@example.com",
    "mobile": "9876543210",
    "password": "securepass123",
    "ccode": "US"
  }'
```

**Response:** OTP sent to email (if email verification is enabled)

### Step 2: Verify Email OTP
```bash
curl -X POST "http://192.168.1.43:8000/verify_email_otp.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jane.smith@example.com",
    "code": "456789"
  }'
```

**Response:** Email verified successfully + Welcome email sent

---

## 4. Using Form Data (Alternative)

### Registration (Form Data)
```bash
curl -X POST "http://192.168.1.43:8000/reg_user.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "name=John Doe" \
  -d "email=john@example.com" \
  -d "mobile=1234567890" \
  -d "password=password123" \
  -d "ccode=US"
```

### Verify OTP (Form Data)
```bash
curl -X POST "http://192.168.1.43:8000/verify_email_otp.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=john@example.com" \
  -d "code=123456"
```

---

## 5. Testing with Postman

### Registration Request
- **Method:** POST
- **URL:** `http://192.168.1.43:8000/reg_user.php`
- **Headers:**
  - `Content-Type: application/json`
- **Body (raw JSON):**
```json
{
  "name": "Test User",
  "email": "test@example.com",
  "mobile": "1234567890",
  "password": "test123",
  "ccode": "US"
}
```

### Verify OTP Request
- **Method:** POST
- **URL:** `http://192.168.1.43:8000/verify_email_otp.php`
- **Headers:**
  - `Content-Type: application/json`
- **Body (raw JSON):**
```json
{
  "email": "test@example.com",
  "code": "123456"
}
```

---

---

## 6. Resend Mobile OTP API
**Endpoint:** `/resend_mobile_otp.php`  
**Method:** `POST`

### Request (Email Only - User will be found by email)
```bash
curl -X POST "http://192.168.1.43:8000/api/resend_mobile_otp.php" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com"
  }'
```

### Response (Success - SMS Only)
```json
{
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "OTP has been resent successfully to SMS (+911234567890)",
  "mobile": "+911234567890",
  "email": null,
  "sent_via": {
    "sms": true,
    "email": false
  }
}
```

### Response (Success - SMS + Email)
```json
{
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "OTP has been resent successfully to SMS (+911234567890) and Email (user@example.com)",
  "mobile": "+911234567890",
  "email": "user@example.com",
  "sent_via": {
    "sms": true,
    "email": true
  }
}
```

### Response (Error - Rate Limit)
```json
{
  "ResponseCode": "429",
  "Result": "false",
  "ResponseMsg": "Please wait 45 seconds before requesting a new OTP."
}
```

### Response (Error - User Not Found)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "User not found with this email!"
}
```

### Response (Error - Missing Parameters)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Email is required!"
}
```

---

## 7. Verify Mobile OTP API
**Endpoint:** `/verify_mobile_otp.php`  
**Method:** `POST`

### Request
```bash
curl -X POST "http://192.168.1.43:8000/api/verify_mobile_otp.php" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "mobile": "1234567890",
    "ccode": "US",
    "code": "123456"
  }'
```

### Response (Success)
```json
{
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "Mobile number verified successfully!"
}
```

### Response (Error - Invalid Code)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Invalid verification code!"
}
```

### Response (Error - Code Expired)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Verification code has expired. Please request a new one."
}
```

### Response (Error - User Not Found)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "User not found!"
}
```

### Response (Error - Missing Parameters)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Code, mobile number and country code are required!"
}
```

---

## 8. Mobile OTP Registration Flow Example

### Step 1: Register User
```bash
curl -X POST "http://192.168.1.43:8000/api/reg_user.php" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Rajesh Kumar",
    "email": "rajesh@example.com",
    "mobile": "9876543210",
    "password": "securepass123",
    "ccode": "IN"
  }'
```

**Response:** User registered, OTP sent to mobile (if SMS verification is enabled)

### Step 2: Resend Mobile OTP (if not received)
```bash
curl -X POST "http://192.168.1.43:8000/api/resend_mobile_otp.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "rajesh@example.com"
  }'
```

**Response:** New OTP sent to mobile (SMS) and email automatically

### Step 3: Verify Mobile OTP
```bash
curl -X POST "http://192.168.1.43:8000/api/verify_mobile_otp.php" \
  -H "Content-Type: application/json" \
  -d '{
    "mobile": "9876543210",
    "ccode": "IN",
    "code": "456789"
  }'
```

**Response:** Mobile verified successfully

---

## 9. Using Form Data (Mobile OTP)

### Resend Mobile OTP (Form Data)
    ```bash
    curl -X POST "http://192.168.1.43:8000/api/resend_mobile_otp.php" \
      -H "Content-Type: application/x-www-form-urlencoded" \
      -d "email=rajesh@example.com"
```

### Verify Mobile OTP (Form Data)
```bash
curl -X POST "http://192.168.1.43:8000/api/verify_mobile_otp.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "mobile=9876543210" \
  -d "ccode=IN" \
  -d "code=123456"
```

---

## Notes:
- **Email OTP:**
  - OTP expires after **2 minutes**
  - OTP is **6 digits** (e.g., 123456)
  - Email verification is based on admin settings (`ec` setting)
  - If `ec` is enabled, OTP will be sent automatically after registration
  - Welcome email is sent automatically after successful verification

- **Mobile OTP:**
  - OTP expires after **10 minutes**
  - OTP is **6 digits** (e.g., 123456)
  - Rate limiting: Minimum **60 seconds** gap between resend requests
  - Mobile verification is based on admin settings (`sc` setting)
  - **Only email is required** - User will be found by email address
  - OTP automatically sent to both SMS (if mobile exists) and Email
  - SMS sent using `SVER_CODE` template
  - Email sent using `EVER_CODE` template
  - If user has mobile number, OTP will be sent to both SMS and Email
  - If user doesn't have mobile, OTP will be sent to Email only
  - All endpoints support both `GET` and `POST` methods

---

## 10. Password Reset Flow (3 APIs)

### API 1: Send Password Reset OTP
**Endpoint:** `/send_password_reset_otp.php`  
**Method:** `POST`

#### Request
```bash
curl -X POST "http://192.168.1.43:8000/api/send_password_reset_otp.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com"
  }'
```

#### Response (Success)
```json
{
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "OTP has been sent successfully to your email: user@example.com",
  "email": "user@example.com"
}
```

#### Response (Error - User Not Found)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "User not found with this email!"
}
```

#### Response (Error - Rate Limit)
```json
{
  "ResponseCode": "429",
  "Result": "false",
  "ResponseMsg": "Please wait 45 seconds before requesting a new OTP."
}
```

---

### API 2: Verify Password Reset OTP
**Endpoint:** `/verify_password_reset_otp.php`  
**Method:** `POST`

#### Request
```bash
curl -X POST "http://192.168.1.43:8000/api/verify_password_reset_otp.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "code": "123456"
  }'
```

#### Response (Success)
```json
{
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "OTP verified successfully!",
  "reset_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6",
  "email": "user@example.com"
}
```

#### Response (Error - Invalid OTP)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Invalid OTP code!"
}
```

#### Response (Error - OTP Expired)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "OTP has expired. Please request a new one."
}
```

---

### API 3: Reset Password
**Endpoint:** `/reset_password.php`  
**Method:** `POST`

#### Request
```bash
curl -X POST "http://192.168.1.43:8000/api/reset_password.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "newpassword123",
    "reset_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6"
  }'
```

#### Response (Success)
```json
{
  "ResponseCode": "200",
  "Result": "true",
  "ResponseMsg": "Password reset successfully! Email notification sent."
}
```

#### Response (Error - Invalid Token)
```json
{
  "ResponseCode": "401",
  "Result": "false",
  "ResponseMsg": "Invalid or expired reset token!"
}
```

---

### Complete Password Reset Flow Example

#### Step 1: Send OTP
```bash
curl -X POST "http://192.168.1.43:8000/api/send_password_reset_otp.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com"
  }'
```

**Response:** OTP sent to email

#### Step 2: Verify OTP
```bash
curl -X POST "http://192.168.1.43:8000/api/verify_password_reset_otp.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "code": "456789"
  }'
```

**Response:** Reset token received

#### Step 3: Reset Password
```bash
curl -X POST "http://192.168.1.43:8000/api/reset_password.php" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "newpassword123",
    "reset_token": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6"
  }'
```

**Response:** Password reset successfully

---

## Notes - Password Reset:
- **OTP Validity:** 10 minutes
- **Reset Token Validity:** 30 minutes (after OTP verification)
- **Rate Limiting:** Minimum 60 seconds gap between OTP requests
- **OTP Format:** 6 digits (e.g., 123456)
- **Email Required:** User must exist with the provided email
- **Password Hashing:** Password is automatically hashed before saving
- **Email Notification:** Confirmation email sent after successful password reset
- All endpoints support both `GET` and `POST` methods
