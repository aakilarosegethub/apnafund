# MWallet Payment Gateway Installation Guide

## Overview
This guide will help you install and configure the MWallet Payment Gateway with the following configuration fields:
- Merchant ID
- API Key  
- Secret Key
- Sandbox Mode (checkbox)

## Installation Steps

### 1. Database Setup
Run the following SQL script in your database to add the gateway:

```sql
-- Copy and paste the contents of mwallet_gateway_setup.sql
-- Or run: mysql -u your_username -p your_database < mwallet_gateway_setup.sql
```

Alternatively, you can use the seeder:

```bash
php artisan db:seed --class=MWalletGatewaySeeder
```

### 2. Files Created
The following files have been created for the new gateway:

- **Controller**: `app/Http/Controllers/Gateway/MWallet/ProcessController.php`
- **Payment View**: `resources/views/user/payment/redirect.blade.php` (shared)
- **Seeder**: `database/seeders/MWalletGatewaySeeder.php`
- **Route**: Added to `routes/ipn.php`
- **SQL Setup**: `mwallet_gateway_setup.sql`

### 3. Configuration
After running the SQL script or seeder, you can configure the gateway through the admin panel:

1. Go to Admin Panel → Payment Methods → Automated
2. Find "MWallet Payment Gateway" in the list
3. Click "Configure" or "Edit"
4. Fill in the following fields:
   - **Merchant ID**: Your merchant ID from MWallet
   - **API Key**: Your API key from MWallet dashboard
   - **Secret Key**: Your secret key from MWallet dashboard
   - **Sandbox Mode**: Check this box to enable sandbox/testing mode

### 4. Gateway Features
- **Sandbox Support**: Toggle between live and test mode
- **Multiple Currencies**: Supports USD, PKR, EUR, GBP
- **Security**: Signature verification for payment validation
- **Country Support**: Available in multiple countries including Pakistan, US, UK, etc.
- **Admin Configuration**: Easy setup through admin panel
- **Webhook Support**: Automatic payment verification via webhooks

### 5. MWallet Dashboard Configuration
1. Log in to your MWallet merchant dashboard
2. Go to API Settings or Webhook Configuration
3. Set the webhook URL to: `https://yourdomain.com/ipn/mwallet`
4. Enable webhook notifications for payment status updates

### 6. Testing
1. Enable sandbox mode for testing
2. Use test credentials provided by MWallet
3. Test the complete payment flow
4. Verify webhook notifications are working

### 7. Production Setup
1. Disable sandbox mode
2. Enter your live production credentials
3. Update webhook URL to production domain
4. Test with small amounts first

## API Endpoints
- **Sandbox**: `https://sandbox.mwallet.com.pk/api/v2`
- **Production**: `https://api.mwallet.com.pk/api/v2`

## Supported Countries
Pakistan, United States, United Kingdom, Canada, Australia, Germany, France, Italy, Spain, Netherlands, Belgium, Austria, Switzerland, Sweden, Norway, Denmark, Finland, Ireland, Portugal, Greece, Luxembourg, Cyprus, Malta, Slovenia, Slovakia, Czech Republic, Hungary, Poland, Lithuania, Latvia, Estonia, Bulgaria, Romania, Croatia

## Troubleshooting
- Ensure webhook URL is accessible from MWallet servers
- Check that all required fields are filled in admin panel
- Verify API credentials are correct
- Check server logs for any error messages
- Test with sandbox mode first before going live

## Support
For MWallet API support, contact MWallet support team or refer to their official documentation.


