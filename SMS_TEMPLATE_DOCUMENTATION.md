# SMS Template Settings - Documentation

## Overview
This document provides detailed information about SMS template configuration, extraction, and management for template ID 28 and other notification templates.

## Template URL
```
http://192.168.1.34:8000/admin/notification/template/edit/28
```

## How to Extract Template Settings

### Method 1: Using SQL Query
Run the provided SQL script to extract template data:

```bash
mysql -u your_username -p your_database < extract_sms_template_28.sql
```

Or execute directly in phpMyAdmin or MySQL console:

```sql
SELECT 
    id,
    act,
    name,
    subj as email_subject,
    email_body,
    email_status,
    sms_body,
    sms_status,
    shortcodes,
    created_at,
    updated_at
FROM notification_templates 
WHERE id = 28;
```

### Method 2: Using the Web Interface
1. Navigate to: `http://192.168.1.34:8000/admin/notification/template/edit/28`
2. Log in to admin panel
3. Click "View Shortcodes" to see available variables
4. Click "Export as JSON" button to download complete template configuration
5. Click "Copy Settings" to copy template details to clipboard

## Template Structure

### Database Table: `notification_templates`

| Field | Type | Description |
|-------|------|-------------|
| id | INT | Template ID (primary key) |
| act | VARCHAR | Action trigger name |
| name | VARCHAR | Template display name |
| subj | VARCHAR | Email subject line |
| email_body | TEXT | HTML email content |
| email_status | TINYINT | Email enabled (1) or disabled (0) |
| sms_body | TEXT | SMS message content |
| sms_status | TINYINT | SMS enabled (1) or disabled (0) |
| shortcodes | JSON | Available placeholder variables |

## SMS Configuration

### Global SMS Settings (basic_settings table)

```sql
SELECT 
    site_name,
    sms_from,
    sms_body as universal_sms_body,
    sa as sms_enabled,
    sms_config
FROM basic_settings 
LIMIT 1;
```

### SMS Gateway Configuration
The system supports three SMS gateways:
- **Nexmo**: API key and secret required
- **Twilio**: Account SID, Auth Token, and From number required  
- **Custom**: Custom API URL with headers and body parameters

### SMS Settings Location
- Navigate to: `Admin Panel → Notifications → SMS Settings`
- Configure gateway credentials
- Test SMS delivery before going live

## Shortcodes System

### Template-Specific Shortcodes
Each template has its own set of shortcodes based on the notification context.

Example shortcodes for different templates:
- `{{username}}` - User's username
- `{{amount}}` - Transaction amount
- `{{currency}}` - Currency symbol
- `{{campaign_name}}` - Campaign title
- `{{date}}` - Current date
- `{{time}}` - Current time

### Universal Shortcodes
Available in all templates:
- `@{{site_name}}` - Site name
- `@{{site_url}}` - Site URL
- `@{{site_email}}` - Site contact email
- `@{{site_phone}}` - Site phone number

## New UI Features

### 1. Enhanced Template Header
- Displays template ID and action name
- Gradient background for visual appeal
- Quick access to shortcodes with collapsible section

### 2. Improved Email Section
- Rich text editor with formatting toolbar
- Status toggle in header
- Info alerts with helpful tips
- Input validation

### 3. Advanced SMS Section
- **Character Counter**: Real-time character count with color coding
  - Green: 0-320 characters (1-2 messages)
  - Yellow: 321-480 characters (3 messages)
  - Red: 481-500 characters (4+ messages)

- **SMS Preview**: Live preview of SMS message
- **Message Counter**: Shows how many SMS parts will be sent
- **Quick Insert Buttons**: One-click shortcode insertion
- **500 character limit** with validation

### 4. Shortcode Management
- Click to copy any shortcode
- Visual feedback on copy
- Organized table with template and universal codes
- Search and filter capabilities

### 5. Export Features
- **Export as JSON**: Download complete template configuration
- **Copy Settings**: Copy template info to clipboard
- Includes metadata and timestamp

### 6. Form Actions
- **Save Changes**: Submit form with validation
- **Cancel**: Return to previous page
- **Reset**: Clear all changes and restore original values

## SMS Best Practices

### Message Length Guidelines
- **160 characters**: 1 SMS message
- **161-320 characters**: 2 SMS messages
- **321-480 characters**: 3 SMS messages
- **481+ characters**: 4+ SMS messages (not recommended)

### Writing Effective SMS Templates

1. **Be Concise**: Keep messages under 160 characters when possible
2. **Use Shortcodes**: Personalize with user-specific data
3. **Clear Call-to-Action**: Tell users what to do next
4. **Professional Tone**: Maintain brand voice
5. **Test First**: Always send test SMS before activation

### Example SMS Templates

#### Short Format (1 message)
```
Hi {{username}}, your donation of {{currency}}{{amount}} to {{campaign_name}} was successful. Thank you!
```

#### Medium Format (2 messages)
```
Dear {{username}}, your contribution of {{currency}}{{amount}} to {{campaign_name}} has been received. 
You'll receive a confirmation email shortly. Visit {{site_url}} for updates.
```

## API Routes

### Admin Routes (routes/admin.php)
```php
Route::controller(NotificationController::class)->prefix('notification')->name('notification.')->group(function(){
    Route::get('templates', 'templates')->name('templates');
    Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
    Route::post('template/update/{id}', 'templateUpdate')->name('template.update');
    Route::get('sms', 'sms')->name('sms');
    Route::post('sms/update', 'smsUpdate')->name('sms.update');
    Route::post('sms/test', 'testSMS')->name('sms.test');
});
```

## Controller Methods

### NotificationController Methods

#### templateEdit($id)
```php
// Loads template for editing
public function templateEdit($id) {
    $template = NotificationTemplate::findOrFail($id);
    $pageTitle = $template->name;
    $setting = bs(); // Basic settings helper
    return view('admin.notification.edit', compact('pageTitle', 'template', 'setting'));
}
```

#### templateUpdate($id)
```php
// Updates template with validation
public function templateUpdate($id) {
    $this->validate(request(), [
        'subject' => 'required|string|max:255',
        'email_body' => 'required',
        'sms_body' => 'required',
    ]);
    
    $template = NotificationTemplate::findOrFail($id);
    $template->subj = request('subject');
    $template->email_body = request('email_body');
    $template->email_status = request('email_status') ? 1 : 0;
    $template->sms_body = request('sms_body');
    $template->sms_status = request('sms_status') ? 1 : 0;
    $template->save();
    
    return back()->withToasts([['success', 'Template updated']]);
}
```

## Testing SMS Templates

### Using Test SMS Feature
1. Go to: `Admin Panel → Notifications → SMS Settings`
2. Click "Test SMS" tab
3. Enter mobile number (with country code)
4. Click "Send Test SMS"
5. Check SMS delivery status

### Manual Testing via Code
```php
use App\Notify\Sms;

$sms = new Sms();
$sms->mobile = '+1234567890';
$sms->message = 'Test message with {{shortcode}}';
$sms->receiverName = 'Test User';
$sms->subject = 'Test';
$sms->send();
```

## Troubleshooting

### Common Issues

1. **SMS Not Sending**
   - Check if SMS is enabled in basic settings (`sa` field)
   - Verify SMS gateway credentials
   - Check gateway balance/quota
   - Review error logs in `storage/logs/laravel.log`

2. **Shortcodes Not Replacing**
   - Ensure shortcode format is correct: `{{shortcode}}`
   - Check if shortcode exists in template's shortcode list
   - Verify data is passed to notification function

3. **Character Count Issues**
   - Unicode characters may use more bytes
   - Emojis count as multiple characters
   - Check actual SMS length with gateway

4. **Template Not Saving**
   - Check form validation errors
   - Verify CSRF token is valid
   - Check database connection
   - Review server error logs

## File Locations

### Views
```
resources/views/admin/notification/
├── edit.blade.php           (Template editor - UPDATED)
├── templates.blade.php      (Template list)
├── email.blade.php          (Email settings)
├── sms.blade.php            (SMS settings)
└── universalTemplate.blade.php (Universal template)
```

### Controllers
```
app/Http/Controllers/Admin/
└── NotificationController.php
```

### Models
```
app/Models/
└── NotificationTemplate.php
```

### SQL Scripts
```
/Applications/XAMPP/xamppfiles/htdocs/apnafund/
└── extract_sms_template_28.sql (Template extraction query)
```

## Security Considerations

1. **Admin Access Only**: Template editing requires admin authentication
2. **CSRF Protection**: All forms include CSRF tokens
3. **Input Validation**: Server-side validation on all inputs
4. **SQL Injection Prevention**: Using Eloquent ORM with parameterized queries
5. **XSS Protection**: Blade templating auto-escapes output

## Performance Optimization

1. **Caching**: Consider caching frequently used templates
2. **Queue SMS**: Use Laravel queues for bulk SMS sending
3. **Rate Limiting**: Implement rate limits to prevent spam
4. **Batch Processing**: Group SMS sends for efficiency

## Backup and Migration

### Export All Templates
```sql
SELECT * FROM notification_templates 
ORDER BY id 
INTO OUTFILE '/tmp/notification_templates_backup.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

### Import Templates
```sql
LOAD DATA INFILE '/tmp/notification_templates_backup.csv'
INTO TABLE notification_templates
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

## Support and Maintenance

For technical support or questions:
- Review Laravel logs: `storage/logs/laravel.log`
- Check gateway documentation
- Contact system administrator
- Review this documentation

## Version History

- **v2.0** (2026-01-24): Enhanced UI with advanced features
  - Added character counter and SMS preview
  - Implemented quick shortcode insertion
  - Added export functionality
  - Improved visual design
  
- **v1.0** (Initial): Basic template editing interface

---

**Last Updated**: January 24, 2026  
**Status**: Active  
**Maintained By**: Development Team
