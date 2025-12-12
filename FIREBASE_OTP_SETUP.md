# Firebase Phone Authentication (OTP) Setup Guide

This guide explains how to set up Firebase Phone Authentication (OTP) for your Laravel application.

## Prerequisites

1. Firebase project created
2. Firebase Authentication enabled
3. Phone authentication provider enabled in Firebase Console
4. Service account key downloaded

## Environment Configuration

Add the following variables to your `.env` file:

```env
# Firebase Configuration
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_PRIVATE_KEY_ID=your-private-key-id
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYour private key here\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-xxxxx@your-project-id.iam.gserviceaccount.com
FIREBASE_CLIENT_ID=your-client-id
FIREBASE_CLIENT_CERT_URL=https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-xxxxx%40your-project-id.iam.gserviceaccount.com

# Firebase Auth Settings
FIREBASE_VERIFY_PHONE_NUMBER=true
FIREBASE_PHONE_AUTH_TIMEOUT=60
FIREBASE_MAX_OTP_ATTEMPTS=3
FIREBASE_DATABASE_ID=(default)
FIREBASE_COLLECTION_PREFIX=apnacrowdfunding
```

## Firebase Console Setup

### 1. Enable Phone Authentication

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project
3. Navigate to Authentication > Sign-in method
4. Enable "Phone" provider
5. Configure test phone numbers for development

### 2. Create Service Account

1. Go to Project Settings > Service Accounts
2. Click "Generate new private key"
3. Download the JSON file
4. Extract the values for your `.env` file

### 3. Configure Phone Authentication

1. In Authentication > Sign-in method > Phone
2. Enable "Phone number sign-in"
3. Add your domain to authorized domains
4. Configure reCAPTCHA settings if needed

## Installation Steps

### 1. Install Dependencies

```bash
composer require kreait/firebase-php
```

### 2. Run Migration

```bash
php artisan migrate
```

### 3. Register Middleware

Add to `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... other middleware
    'phone.verified' => \App\Http\Middleware\RequirePhoneVerification::class,
];
```

## Usage

### Basic OTP Flow

1. **Send OTP**: `POST /otp/send`
   ```json
   {
       "phone_number": "+1234567890"
   }
   ```

2. **Verify OTP**: `POST /otp/verify`
   ```json
   {
       "otp_code": "123456"
   }
   ```

### Routes Available

- `GET /otp-login` - Show OTP login form
- `POST /otp/send` - Send OTP to phone
- `POST /otp/verify` - Verify OTP code
- `POST /otp/resend` - Resend OTP
- `POST /otp/check-phone` - Check if phone exists

### Using Middleware

Protect routes that require phone verification:

```php
Route::middleware(['auth', 'phone.verified'])->group(function () {
    // Protected routes
});
```

## Security Best Practices

### 1. Environment Security

- **Never commit service account keys to version control**
- Store Firebase credentials in environment variables only
- Use different service accounts for different environments
- Rotate service account keys regularly

### 2. Rate Limiting

Implement rate limiting for OTP requests:

```php
// In routes/web.php
Route::post('otp/send', 'OTPController@sendOTP')
    ->middleware('throttle:5,1'); // 5 requests per minute
```

### 3. Input Validation

- Validate phone number format before sending OTP
- Sanitize all user inputs
- Implement proper error handling

### 4. Session Security

- Use secure session configuration
- Implement session timeout for OTP verification
- Clear OTP session data after successful verification

### 5. Logging and Monitoring

- Log all OTP attempts (successful and failed)
- Monitor for suspicious activity
- Implement alerting for unusual patterns

### 6. Firebase Security Rules

Configure Firestore security rules:

```javascript
rules_version = '2';
service cloud.firestore {
  match /databases/{database}/documents {
    match /users/{userId} {
      allow read, write: if request.auth != null && request.auth.uid == userId;
    }
  }
}
```

### 7. Phone Number Validation

- Implement proper phone number format validation
- Use E.164 format for international compatibility
- Validate country codes

### 8. Error Handling

- Don't expose sensitive information in error messages
- Log detailed errors server-side
- Provide user-friendly error messages

## Testing

### 1. Test Phone Numbers

Add test phone numbers in Firebase Console:
- Go to Authentication > Sign-in method > Phone
- Add test phone numbers and OTP codes

### 2. Unit Tests

```php
// Example test
public function test_can_send_otp()
{
    $response = $this->postJson('/otp/send', [
        'phone_number' => '+1234567890'
    ]);
    
    $response->assertStatus(200)
             ->assertJson(['success' => true]);
}
```

## Troubleshooting

### Common Issues

1. **Invalid phone number format**
   - Ensure phone numbers are in E.164 format (+1234567890)
   - Validate country codes

2. **Firebase service account errors**
   - Verify all environment variables are set correctly
   - Check service account permissions

3. **OTP verification fails**
   - Ensure OTP is entered within timeout period
   - Check Firebase project configuration

4. **Rate limiting issues**
   - Implement proper rate limiting
   - Monitor Firebase quotas

### Debug Mode

Enable debug logging in `config/firebase.php`:

```php
'debug' => env('FIREBASE_DEBUG', false),
```

## Production Considerations

1. **Monitoring**: Set up Firebase monitoring and alerts
2. **Scaling**: Configure Firebase quotas and limits
3. **Backup**: Implement proper backup strategies
4. **Compliance**: Ensure GDPR/CCPA compliance for phone data
5. **Costs**: Monitor Firebase usage and costs

## Support

For issues related to:
- Firebase setup: [Firebase Documentation](https://firebase.google.com/docs)
- Laravel integration: Check application logs
- Phone authentication: [Firebase Auth Documentation](https://firebase.google.com/docs/auth)
