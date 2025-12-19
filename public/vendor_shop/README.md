# Vendor Shop Plugin

A WordPress plugin that handles product webhooks and returns product URLs in response.

## Features

- **Webhook Handler**: Processes product data via webhooks
- **URL Generation**: Automatically generates product URLs
- **Admin Interface**: Easy-to-use admin dashboard
- **REST API**: Full REST API support
- **Product Management**: Create, update, and manage products
- **Real-time Testing**: Test webhooks directly from admin

## Installation

### Method 1: WordPress Admin (Recommended)

1. Download the plugin files
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload the `vendor-shop-plugin.php` file
4. Activate the plugin

### Method 2: FTP Upload

1. Upload the entire `vendor_shop` folder to `/wp-content/plugins/`
2. Go to **Plugins** in WordPress admin
3. Find "Vendor Shop Product Manager" and click **Activate**

### Method 3: Manual Installation

1. Create a folder named `vendor-shop` in `/wp-content/plugins/`
2. Upload all plugin files to this folder
3. Activate the plugin from WordPress admin

## Configuration

### Webhook Settings

1. Go to **Vendor Shop > Webhook Settings**
2. Enter your webhook URL (usually: `https://yourdomain.com/wp-json/vendor-shop/v1/webhook`)
3. Set a webhook secret for security
4. Save settings

### Testing Webhook

1. Go to **Vendor Shop > Webhook Settings**
2. Click **Test Webhook** button
3. Check the result to ensure everything is working

## API Endpoints

### Webhook Endpoint
```
POST /wp-json/vendor-shop/v1/webhook
```

**Request Body:**
```json
{
    "sku": "PRODUCT-001",
    "name": "Test Product",
    "regular_price": 29.99,
    "description": "Product description",
    "sale_price": 24.99,
    "stock_quantity": 100,
    "category": "Electronics",
    "tags": "test,product,electronics",
    "image_url": "https://example.com/image.jpg"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Product processed successfully",
    "data": {
        "id": "PRODUCT-001",
        "name": "Test Product",
        "sku": "PRODUCT-001",
        "slug": "test-product-product-001",
        "description": "Product description",
        "regular_price": 29.99,
        "sale_price": 24.99,
        "stock_quantity": 100,
        "category": "Electronics",
        "tags": "test,product,electronics",
        "image_url": "https://example.com/image.jpg",
        "url": "https://yourdomain.com/product/test-product-product-001",
        "product_url": "https://yourdomain.com/product/test-product-product-001",
        "permalink": "https://yourdomain.com/product/test-product-product-001",
        "admin_url": "https://yourdomain.com/wp-admin/post.php?post=PRODUCT-001&action=edit",
        "edit_url": "https://yourdomain.com/wp-admin/post.php?post=PRODUCT-001&action=edit",
        "status": "created",
        "created_at": "2024-01-01T00:00:00+00:00",
        "updated_at": "2024-01-01T00:00:00+00:00"
    }
}
```

### Get All Products
```
GET /wp-json/vendor-shop/v1/products
```

### Get Single Product
```
GET /wp-json/vendor-shop/v1/products/{id}
```

## Usage Examples

### cURL Example
```bash
curl -X POST "https://yourdomain.com/wp-json/vendor-shop/v1/webhook" \
  -H "Content-Type: application/json" \
  -d '{
    "sku": "TEST-003",
    "name": "Test Product 3",
    "regular_price": 39.99,
    "description": "Test product 3 description"
  }'
```

### JavaScript Example
```javascript
fetch('https://yourdomain.com/wp-json/vendor-shop/v1/webhook', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        sku: 'TEST-004',
        name: 'Test Product 4',
        regular_price: 49.99,
        description: 'Test product 4 description'
    })
})
.then(response => response.json())
.then(data => {
    console.log('Product URL:', data.data.url);
    console.log('Admin URL:', data.data.admin_url);
});
```

### PHP Example
```php
$data = [
    'sku' => 'TEST-005',
    'name' => 'Test Product 5',
    'regular_price' => 59.99,
    'description' => 'Test product 5 description'
];

$response = wp_remote_post('https://yourdomain.com/wp-json/vendor-shop/v1/webhook', [
    'headers' => [
        'Content-Type' => 'application/json',
    ],
    'body' => json_encode($data)
]);

$body = wp_remote_retrieve_body($response);
$result = json_decode($body, true);

if ($result['success']) {
    echo 'Product URL: ' . $result['data']['url'];
}
```

## File Structure

```
vendor_shop/
├── vendor-shop-plugin.php          # Main plugin file
├── webhook-handler.php             # Standalone webhook handler
├── assets/
│   ├── vendor-shop.css            # Plugin styles
│   └── vendor-shop.js             # Plugin JavaScript
└── README.md                       # This file
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Security Features

- Input sanitization
- Output escaping
- Nonce verification
- Capability checks
- SQL injection prevention
- XSS protection

## Troubleshooting

### Common Issues

1. **Webhook not working**: Check if the plugin is activated and the URL is correct
2. **Permission denied**: Ensure the webhook endpoint is accessible
3. **JSON errors**: Verify the request format and content-type header
4. **URL not generated**: Check if the product data is valid

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Log Files

Check the following log files for errors:
- `/wp-content/debug.log`
- Server error logs
- Plugin-specific logs

## Support

For support and questions:
- Check the WordPress admin dashboard for error messages
- Review the debug logs
- Contact the plugin developer

## Changelog

### Version 1.0.0
- Initial release
- Webhook handler
- URL generation
- Admin interface
- REST API support

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by ApnaCrowdfunding Team.
