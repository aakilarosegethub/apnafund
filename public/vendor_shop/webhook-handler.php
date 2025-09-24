<?php
/**
 * Vendor Shop Webhook Handler
 * Handles product webhooks and returns product URLs in response
 */

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.',
        'data' => null
    ]);
    exit;
}

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Validate JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    $required_fields = ['sku', 'name', 'regular_price'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Required field '{$field}' is missing or empty");
        }
    }
    
    // Extract product data
    $sku = htmlspecialchars(strip_tags($data['sku']));
    $name = htmlspecialchars(strip_tags($data['name']));
    $regular_price = floatval($data['regular_price']);
    $description = isset($data['description']) ? htmlspecialchars(strip_tags($data['description'])) : '';
    $sale_price = isset($data['sale_price']) ? floatval($data['sale_price']) : null;
    $stock_quantity = isset($data['stock_quantity']) ? intval($data['stock_quantity']) : null;
    $category = isset($data['category']) ? htmlspecialchars(strip_tags($data['category'])) : '';
    $tags = isset($data['tags']) ? htmlspecialchars(strip_tags($data['tags'])) : '';
    $image_url = isset($data['image_url']) ? filter_var($data['image_url'], FILTER_SANITIZE_URL) : '';
    
    // Generate product slug
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name . '-' . $sku));
    $slug = trim($slug, '-');
    
    // Create product URL (adjust domain as needed)
    $base_url = 'https://yourdomain.com'; // Change this to your actual domain
    $product_url = $base_url . '/product/' . $slug;
    $admin_url = $base_url . '/wp-admin/post.php?post=' . $sku . '&action=edit';
    
    // Prepare response data
    $response_data = [
        'id' => $sku,
        'name' => $name,
        'sku' => $sku,
        'slug' => $slug,
        'description' => $description,
        'regular_price' => $regular_price,
        'sale_price' => $sale_price,
        'stock_quantity' => $stock_quantity,
        'category' => $category,
        'tags' => $tags,
        'image_url' => $image_url,
        'url' => $product_url,
        'product_url' => $product_url,
        'permalink' => $product_url,
        'admin_url' => $admin_url,
        'edit_url' => $admin_url,
        'status' => 'created',
        'created_at' => date('c'),
        'updated_at' => date('c')
    ];
    
    // Log the webhook data (optional)
    error_log('Vendor Shop Webhook: ' . json_encode($data));
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Product processed successfully',
        'data' => $response_data
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Vendor Shop Webhook Error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => null
    ]);
}
?>