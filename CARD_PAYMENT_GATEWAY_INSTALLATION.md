# Card Payment Gateway v1.1 Installation Guide

## Overview
This guide will help you install and configure the new Card Payment Gateway that follows the **Page Redirection v1.1 template for Card Payments** specification. This gateway is designed to work alongside your existing payment gateways without interfering with them.

## Features
- ✅ **Page Redirection v1.1 Template** - Follows the standard card payment redirection protocol
- ✅ **Sandbox Mode** - Toggle between test and production environments
- ✅ **Multiple Currencies** - Supports USD, EUR, GBP, PKR, CAD, AUD
- ✅ **Secure Hash Verification** - SHA256 hash validation for security
- ✅ **Admin Panel Integration** - Easy configuration through admin interface
- ✅ **Non-Intrusive** - Won't affect existing payment gateways

## Installation Steps

### 1. Database Setup
Run the following SQL script in your database to add the CardPayment gateway:

```sql
-- Copy and paste the contents of card_payment_gateway_setup.sql
-- Or run: mysql -u your_username -p your_database < card_payment_gateway_setup.sql
```

### 2. Files Created
The following files have been created for the CardPayment gateway:

- **Controller**: `app/Http/Controllers/Gateway/CardPayment/ProcessController.php`
- **Payment View**: `resources/views/user/payment/cardpayment.blade.php`
- **Seeder**: `database/seeders/CardPaymentGatewaySeeder.php`
- **Route**: Added to `routes/ipn.php`
- **SQL Setup**: `card_payment_gateway_setup.sql`

### 3. Configuration Fields
The gateway includes the following configuration fields:

| Field | Description | Required |
|-------|-------------|----------|
| **Merchant ID** | Your merchant ID from payment provider | Yes |
| **Password** | Your account password | Yes |
| **Hash Key** | Secret key for hash generation | Yes |
| **Return URL** | Static return URL for redirects | Yes |
| **Sandbox Mode** | Checkbox to enable/disable test mode | No |

### 4. Admin Panel Configuration
After running the SQL script:

1. Go to **Admin Panel → Payment Methods → Automated**
2. Find **"Card Payment Gateway v1.1"** in the list
3. Click **"Configure"** or **"Edit"**
4. Fill in the configuration fields:
   - **Merchant ID**: Your payment provider merchant ID
   - **Password**: Your account password
   - **Hash Key**: Your secret hash key
   - **Return URL**: Your static return URL (e.g., `https://yoursite.com/payment/return`)
   - **Sandbox Mode**: Check to enable testing mode

### 5. Page Redirection v1.1 Features
This gateway implements the Page Redirection v1.1 template with:

- **Standard Parameters**: MerchantID, Password, HashKey, ReturnURL
- **Payment Data**: Amount, Currency, TransactionID, Description
- **Customer Info**: CustomerName, CustomerEmail, CustomerPhone
- **Security**: SHA256 hash verification
- **Version Control**: Version 1.1 specification compliance

### 6. Supported Currencies
- **USD** - US Dollar (Rate: 1.00, Symbol: $)
- **PKR** - Pakistani Rupee (Rate: 280.00, Symbol: Rs)
- **EUR** - Euro
- **GBP** - British Pound
- **CAD** - Canadian Dollar
- **AUD** - Australian Dollar

### 7. Testing Process

#### Sandbox Testing:
1. Enable **Sandbox Mode** in admin panel
2. Use test credentials from your payment provider
3. Test with small amounts first
4. Verify payment flow and IPN responses

#### Production Setup:
1. Disable **Sandbox Mode**
2. Enter live production credentials
3. Update return URL to production domain
4. Test with small amounts before going live

### 8. Security Features
- **SSL Encryption**: All data transmitted over HTTPS
- **Hash Verification**: SHA256 hash validation for all transactions
- **PCI DSS Compliance**: Follows card payment security standards
- **Sandbox Isolation**: Test environment separate from production

### 9. Integration Benefits
- **Non-Intrusive**: Doesn't modify existing gateways
- **Standard Compliant**: Follows Page Redirection v1.1 template
- **Admin Friendly**: Easy configuration through admin panel
- **Multi-Currency**: Supports multiple currencies out of the box
- **Scalable**: Can handle high transaction volumes

### 10. Troubleshooting

#### Common Issues:
1. **Hash Verification Failed**: Check if Hash Key is correct
2. **Payment Not Processing**: Verify Merchant ID and Password
3. **Return URL Issues**: Ensure URL is static and accessible
4. **Currency Not Supported**: Check supported currencies list

#### Debug Steps:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database entries in `gateways` and `gateway_currencies` tables
3. Test with sandbox mode first
4. Check IPN endpoint: `/ipn/card-payment`

### 11. File Structure
```
app/Http/Controllers/Gateway/CardPayment/
├── ProcessController.php          # Main payment processing logic

resources/views/user/payment/
├── cardpayment.blade.php          # Payment form template

database/seeders/
├── CardPaymentGatewaySeeder.php   # Database seeder

routes/
├── ipn.php                        # IPN route added

SQL Files:
├── card_payment_gateway_setup.sql # Manual database setup
```

### 12. Support
If you encounter any issues:
1. Check the installation guide above
2. Verify all configuration fields are correct
3. Test with sandbox mode first
4. Check Laravel logs for errors
5. Ensure your payment provider credentials are valid

## Next Steps
1. Run the SQL script to add the gateway
2. Configure through admin panel
3. Test with sandbox mode
4. Go live with production credentials

The Card Payment Gateway v1.1 is now ready to use and will work seamlessly with your existing payment infrastructure!
