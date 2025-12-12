# JazzCash Mobile Wallet Gateway Installation Guide

## Overview
This guide will help you install and configure the **JazzCash Mobile Wallet Gateway** for your ApnaCrowdfunding application. JazzCash is Pakistan's leading mobile wallet service, and this gateway allows users to make payments using their JazzCash mobile wallet.

## Features
- ✅ **Mobile Wallet Payment** - JazzCash mobile wallet integration
- ✅ **Form Submission** - Redirects to JazzCash payment form
- ✅ **Sandbox Mode** - Toggle between test and production environments
- ✅ **PKR & USD Support** - Primary currencies for Pakistan market
- ✅ **Secure Hash Verification** - SHA256 hash validation for security
- ✅ **Admin Panel Integration** - Easy configuration through admin interface
- ✅ **Country Restriction** - Available in Pakistan only

## Installation Steps

### 1. Database Setup
Run the following SQL script in your database to add the JazzCash gateway:

```sql
-- Copy and paste the contents of jazzcash_gateway_setup.sql
-- Or run: mysql -u your_username -p your_database < jazzcash_gateway_setup.sql
```

### 2. Files Created
The following files have been created for the JazzCash gateway:

- **Controller**: `app/Http/Controllers/Gateway/JazzCash/ProcessController.php`
- **Payment View**: `resources/views/user/payment/jazzcash.blade.php`
- **Seeder**: `database/seeders/JazzCashGatewaySeeder.php`
- **Route**: Added to `routes/ipn.php`
- **SQL Setup**: `jazzcash_gateway_setup.sql`

### 3. Configuration Fields
The JazzCash gateway includes the following configuration fields:

| Field | Description | Required |
|-------|-------------|----------|
| **Merchant ID** | Your JazzCash merchant ID | Yes |
| **Password** | Your JazzCash account password | Yes |
| **Hash Key** | Secret key for hash generation | Yes |
| **Return URL** | Static return URL for redirects | Yes |
| **Sandbox Mode** | Checkbox to enable/disable test mode | No |

### 4. Admin Panel Configuration
After running the SQL script:

1. Go to **Admin Panel → Payment Methods → Automated**
2. Find **"JazzCash Mobile Wallet"** in the list
3. Click **"Configure"** or **"Edit"**
4. Fill in the configuration fields:
   - **Merchant ID**: Your JazzCash merchant ID
   - **Password**: Your JazzCash account password
   - **Hash Key**: Your secret hash key from JazzCash
   - **Return URL**: Your static return URL (e.g., `https://yoursite.com/payment/return`)
   - **Sandbox Mode**: Check to enable testing mode

### 5. JazzCash Mobile Wallet Features
This gateway implements JazzCash mobile wallet integration with:

- **Mobile Wallet Payment**: Users pay using their JazzCash mobile wallet
- **Form Submission**: Redirects to JazzCash payment form
- **Standard Parameters**: MerchantID, Password, HashKey, ReturnURL
- **Payment Data**: Amount, Currency, TransactionID, Description
- **Customer Info**: CustomerName, CustomerEmail, CustomerPhone
- **Security**: SHA256 hash verification
- **Wallet Type**: JazzCash specific mobile wallet

### 6. Supported Currencies
- **PKR** - Pakistani Rupee (Primary currency, Rate: 1.00, Symbol: Rs)
- **USD** - US Dollar (Rate: 280.00, Symbol: $)

### 7. Payment Limits
- **PKR**: Minimum 50 Rs, Maximum 100,000 Rs
- **USD**: Minimum $1, Maximum $1,000
- **Charges**: 1.50% per transaction

### 8. Testing Process

#### Sandbox Testing:
1. Enable **Sandbox Mode** in admin panel
2. Use JazzCash test credentials
3. Test with small amounts first
4. Verify payment flow and form submission

#### Production Setup:
1. Disable **Sandbox Mode**
2. Enter live JazzCash credentials
3. Update return URL to production domain
4. Test with small amounts before going live

### 9. User Experience Flow
1. User selects **JazzCash** as payment method
2. System redirects to JazzCash payment form
3. User enters JazzCash mobile wallet details
4. Payment is processed through JazzCash
5. User is redirected back to your site
6. Payment status is updated in your system

### 10. Security Features
- **SSL Encryption**: All data transmitted over HTTPS
- **Hash Verification**: SHA256 hash validation for all transactions
- **Mobile Wallet Security**: JazzCash's built-in security features
- **Sandbox Isolation**: Test environment separate from production

### 11. Integration Benefits
- **Pakistan Market**: Perfect for Pakistani users
- **Mobile First**: Optimized for mobile payments
- **Easy Integration**: Simple form submission process
- **Trusted Brand**: JazzCash is a trusted name in Pakistan
- **Low Charges**: Competitive transaction fees

### 12. Troubleshooting

#### Common Issues:
1. **Hash Verification Failed**: Check if Hash Key is correct
2. **Payment Not Processing**: Verify Merchant ID and Password
3. **Return URL Issues**: Ensure URL is static and accessible
4. **Form Not Loading**: Check JazzCash API endpoint

#### Debug Steps:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database entries in `gateways` and `gateway_currencies` tables
3. Test with sandbox mode first
4. Check IPN endpoint: `/ipn/jazzcash`

### 13. File Structure
```
app/Http/Controllers/Gateway/JazzCash/
├── ProcessController.php          # Main payment processing logic

resources/views/user/payment/
├── jazzcash.blade.php            # JazzCash payment form template

database/seeders/
├── JazzCashGatewaySeeder.php     # Database seeder

routes/
├── ipn.php                       # IPN route added

SQL Files:
├── jazzcash_gateway_setup.sql    # Manual database setup
```

### 14. JazzCash Specific Features
- **Mobile Wallet Integration**: Direct integration with JazzCash mobile app
- **Pakistan Focused**: Optimized for Pakistani market
- **Form Submission**: Seamless redirect to JazzCash payment form
- **Real-time Processing**: Instant payment confirmation
- **User Friendly**: Familiar JazzCash interface for users

### 15. Support
If you encounter any issues:
1. Check the installation guide above
2. Verify all configuration fields are correct
3. Test with sandbox mode first
4. Check Laravel logs for errors
5. Ensure your JazzCash credentials are valid
6. Contact JazzCash support for API-related issues

## Next Steps
1. Run the SQL script to add the gateway
2. Configure through admin panel
3. Test with sandbox mode
4. Go live with production credentials
5. Monitor payment transactions

The JazzCash Mobile Wallet Gateway is now ready to use and will provide a seamless payment experience for your Pakistani users!

