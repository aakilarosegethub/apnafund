<?php
/**
 * Home API Endpoint
 * This file provides a standalone endpoint for the home API
 * It bootstraps Laravel and calls the HomeController
 */

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));

// Suppress deprecation warnings
if (defined('E_STRICT') && PHP_VERSION_ID < 80400) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
}

// Check if Laravel is in maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__.'/../bootstrap/app.php';

// Create a request from the current HTTP request
$request = Illuminate\Http\Request::capture();

// Handle the request through Laravel's kernel (this will use the route)
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request);
    
    // Send the response
    $response->send();
    
    // Terminate the kernel
    $kernel->terminate($request, $response);
} catch (\Exception $e) {
    // Handle errors gracefully
    http_response_code(500);
    header('Content-Type: application/json');
    
    // Log the error for debugging
    if (function_exists('error_log')) {
        error_log('Home API Error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
    }
    
    echo json_encode([
        "ResponseCode" => "500",
        "Result" => "false",
        "ResponseMsg" => "Internal Server Error: " . $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
