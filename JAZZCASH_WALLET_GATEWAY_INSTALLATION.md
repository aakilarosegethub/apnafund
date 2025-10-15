# JazzCash Wallet Payment Gateway Installation Guide

## Overview
This guide will help you install and configure the new JazzCash Wallet Payment Gateway with the following features:
- Phone number and CNIC last 6 digits input form
- AJAX-based payment processing
- Real-time payment status updates
- Admin panel configuration for merchant credentials

## Installation Steps

### 1. Database Setup
Run the following SQL script in your database to add the gateway:

```sql
-- Copy and paste the contents of jazzcash_wallet_gateway_setup.sql
-- Or run: mysql -u your_username -p your_database < jazzcash_wallet_gateway_setup.sql
```

**OR** use the seeder:

```bash
php artisan db:seed --class=JazzCashWalletGatewaySeeder
```

### 2. Files Created
The following files have been created for the new gateway:

- **Controller**: `app/Http/Controllers/Gateway/JazzCashWallet/ProcessController.php`
- **Payment View**: `resources/views/user/payment/jazzcash_wallet.blade.php`
- **Seeder**: `database/seeders/JazzCashWalletGatewaySeeder.php`
- **Routes**: Added to `routes/ipn.php`
- **SQL Setup**: `jazzcash_wallet_gateway_setup.sql`

### 3. Configuration
After running the SQL script, you can configure the gateway through the admin panel:

1. Go to Admin Panel → Payment Gateways
2. Find "JazzCash Wallet Payment" in the list
3. Click "Configure" or "Edit"
4. Fill in the following fields:
   - **Merchant ID**: Your JazzCash merchant ID (e.g., MC303131)
   - **Password**: Your JazzCash password (e.g., tghs3du82x)
   - **Integrity Salt**: Your JazzCash integrity salt/secret key (e.g., tcd21z0w1s)
   - **Sandbox Mode**: Check this box to enable sandbox/testing mode

### 4. Gateway Features
- **Phone Number Validation**: Validates Pakistani mobile numbers (03XXXXXXXXX format)
- **CNIC Validation**: Validates last 6 digits of CNIC
- **AJAX Processing**: Real-time payment processing without page reload
- **Sandbox Support**: Toggle between live and test mode
- **PKR Currency**: Supports Pakistani Rupee only
- **Security**: Hash verification for payment validation
- **Country Support**: Available in Pakistan only
- **Admin Configuration**: Easy setup through admin panel

### 5. User Experience
When users select JazzCash Wallet as payment method:

1. They see a form asking for:
   - Phone number (JazzCash registered number)
   - CNIC last 6 digits
2. After submitting, the payment is processed via AJAX
3. Real-time feedback is shown to the user
4. Success/failure messages are displayed immediately

### 6. Testing
1. Enable sandbox mode for testing
2. Use test credentials provided by JazzCash
3. Test the complete payment flow with:
   - Valid phone number (03XXXXXXXXX)
   - Valid CNIC last 6 digits (6 numbers)
4. Verify IPN (Instant Payment Notification) is working

### 7. Production Setup
1. Disable sandbox mode
2. Enter your live production credentials from JazzCash
3. Test with real transactions
4. Monitor payment logs for any issues

### 8. API Endpoints
- **Sandbox**: `https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction`
- **Production**: `https://jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction`

### 9. Security Features
- Secure hash generation using HMAC-SHA256
- Transaction reference validation
- CNIC and phone number validation
- CSRF protection on all forms

### 10. Troubleshooting
- Ensure phone number format is correct (03XXXXXXXXX)
- Verify CNIC last 6 digits are exactly 6 numbers
- Check merchant credentials in admin panel
- Verify sandbox/live mode settings
- Check server logs for API errors

## Support
For technical support or issues with this gateway implementation, please check:
1. JazzCash API documentation
2. Server error logs
3. Payment gateway configuration in admin panel
4. Network connectivity to JazzCash servers
