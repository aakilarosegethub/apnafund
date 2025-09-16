# Custom Payment Gateway Installation Guide

## Overview
This guide will help you install and configure the new Custom Payment Gateway with the following configuration fields:
- Merchant ID
- Password  
- Hash Key
- Return URL
- Sandbox Mode (checkbox)

## Installation Steps

### 1. Database Setup
Run the following SQL script in your database to add the gateway:

```sql
-- Copy and paste the contents of custom_gateway_setup.sql
-- Or run: mysql -u your_username -p your_database < custom_gateway_setup.sql
```

### 2. Files Created
The following files have been created for the new gateway:

- **Controller**: `app/Http/Controllers/Gateway/CustomGateway/ProcessController.php`
- **Payment View**: `resources/views/user/payment/redirect.blade.php`
- **Seeder**: `database/seeders/CustomGatewaySeeder.php`
- **Route**: Added to `routes/ipn.php`

### 3. Configuration
After running the SQL script, you can configure the gateway through the admin panel:

1. Go to Admin Panel → Payment Gateways
2. Find "Custom Payment Gateway" in the list
3. Click "Configure" or "Edit"
4. Fill in the following fields:
   - **Merchant ID**: Your merchant ID from the payment provider
   - **Password**: Your password from the payment provider
   - **Hash Key**: Your hash key/integrity salt from the payment provider
   - **Return URL**: Your return URL (must be static)
   - **Sandbox Mode**: Check this box to enable sandbox/testing mode

### 4. Gateway Features
- **Sandbox Support**: Toggle between live and test mode
- **Multiple Currencies**: Supports USD, EUR, GBP, PKR
- **Security**: Hash verification for payment validation
- **Country Support**: Available in all countries by default
- **Admin Configuration**: Easy setup through admin panel

### 5. Testing
1. Enable sandbox mode for testing
2. Use test credentials provided by your payment provider
3. Test the complete payment flow
4. Verify IPN (Instant Payment Notification) is working

### 6. Production Setup
1. Disable sandbox mode
2. Enter your live production credentials
3. Update return URL to your production domain
4. Test with small amounts first

## Configuration Fields Explained

### Merchant ID
- Your unique merchant identifier from the payment provider
- Required for all transactions

### Password
- Your account password from the payment provider
- Used for authentication

### Hash Key
- Secret key for generating secure hashes
- Used to verify payment authenticity

### Return URL
- URL where users are redirected after payment
- Must be a static URL (e.g., https://yoursite.com/payment/return)

### Sandbox Mode
- Checkbox to enable/disable test mode
- When enabled, uses test environment
- When disabled, uses live/production environment

## Support
If you encounter any issues:
1. Check the Laravel logs in `storage/logs/laravel.log`
2. Verify all configuration fields are filled correctly
3. Ensure your payment provider credentials are valid
4. Test with sandbox mode first

## Security Notes
- Never commit your production credentials to version control
- Use environment variables for sensitive data in production
- Regularly rotate your API keys and passwords
- Monitor payment logs for any suspicious activity
