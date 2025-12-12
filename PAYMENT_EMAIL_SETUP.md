# Payment Success Email Templates

This implementation adds email notifications for successful payments, sending emails to both the admin and the user when a payment is completed.

## Features

- **Admin Email Notification**: Sends detailed payment information to admin when a payment is successful
- **User Email Notification**: Sends a thank you email with donation summary to the user
- **Professional Email Templates**: Beautiful, responsive HTML email templates
- **Dynamic Content**: Templates use shortcodes for dynamic content replacement

## Installation

1. **Run the migration** to add required columns to notification_templates table:
   ```bash
   php artisan migrate
   ```

2. **Run the seeder** to create the email templates:
   ```bash
   php artisan db:seed --class=PaymentSuccessEmailTemplateSeeder
   ```

3. **Test the templates** (optional):
   ```bash
   php test_payment_email_templates.php
   ```

## Email Templates Created

### 1. Admin Payment Success Template (`ADMIN_PAYMENT_SUCCESS`)
- **Subject**: "New Successful Payment Received - ApnaCrowdfunding"
- **Recipient**: Admin email (from site settings)
- **Content**: Detailed payment information including donor details, amount, campaign, transaction ID, etc.

### 2. User Payment Success Template (`USER_PAYMENT_SUCCESS`)
- **Subject**: "Payment Successful - Thank You for Your Donation!"
- **Recipient**: Donor's email
- **Content**: Thank you message with donation summary and campaign progress link

## Template Shortcodes

Both templates support the following shortcodes:

| Shortcode | Description | Example |
|-----------|-------------|---------|
| `{{full_name}}` | Donor's full name | "John Doe" |
| `{{email}}` | Donor's email | "john@example.com" |
| `{{campaign_name}}` | Campaign name | "Help Save Lives" |
| `{{amount}}` | Donation amount | "100.00" |
| `{{method_name}}` | Payment method | "Credit Card" |
| `{{trx}}` | Transaction ID | "TXN123456789" |
| `{{date}}` | Payment date | "Dec 19, 2024 10:30 AM" |
| `{{admin_url}}` | Admin panel URL | "https://admin.apnacrowdfunding.com/donations" |
| `{{campaign_url}}` | Campaign URL | "https://apnacrowdfunding.com/campaign/test" |
| `{{currency_symbol}}` | Currency symbol | "$" |

## How It Works

When a payment is successful, the system:

1. **Updates payment status** to success
2. **Updates campaign raised amount**
3. **Creates transaction records**
4. **Sends email to user** with donation confirmation
5. **Sends email to admin** with payment notification
6. **Creates admin notification** in the dashboard

## Code Changes Made

### 1. Migration File
- `database/migrations/2024_12_19_000000_add_columns_to_notification_templates_table.php`
- Adds required columns to notification_templates table

### 2. Seeder File
- `database/seeders/PaymentSuccessEmailTemplateSeeder.php`
- Creates the email templates with HTML content

### 3. PaymentController Updates
- `app/Http/Controllers/Gateway/PaymentController.php`
- Updated `campaignDataUpdate()` and `campaignDataUpdatejazz()` functions
- Added email notifications for both admin and user

## Admin Panel Management

The email templates can be managed through the admin panel:

1. Go to **Admin Panel > Notification > Templates**
2. Find "Admin Payment Success" and "User Payment Success" templates
3. Click **Edit** to modify subject, content, or status
4. Templates can be enabled/disabled independently

## Testing

To test the email functionality:

1. Make a test donation
2. Check if emails are sent to both admin and user
3. Verify email content and formatting
4. Check email logs in the database (if email logging is enabled)

## Customization

### Modifying Templates
- Edit templates through admin panel or directly in database
- Templates support full HTML/CSS for styling
- Use shortcodes for dynamic content

### Adding New Shortcodes
1. Update the template content to include new shortcodes
2. Update the PaymentController to pass the new shortcode values
3. Update the seeder if needed

### Email Provider Configuration
- Configure email settings in **Admin Panel > Notification > Email**
- Supports PHP Mail, SMTP, SendGrid, and Mailjet
- Test email functionality using the test email feature

## Troubleshooting

### Emails Not Sending
1. Check email configuration in admin panel
2. Verify email templates are active
3. Check email logs for errors
4. Test with a simple email first

### Template Not Found
1. Run the seeder: `php artisan db:seed --class=PaymentSuccessEmailTemplateSeeder`
2. Check if templates exist in database
3. Verify template names match exactly

### Shortcodes Not Replacing
1. Check shortcode syntax (use double curly braces: `{{shortcode}}`)
2. Verify shortcode names match exactly
3. Check if values are being passed in PaymentController

## Support

For issues or questions:
1. Check the test script output
2. Review email logs
3. Verify database template entries
4. Test email configuration separately
