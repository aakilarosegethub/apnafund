<?php
/**
 * New Home API Endpoint using Laravel Controller
 * This file acts as a bridge between direct PHP API calls and Laravel controller
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Get request data
$requestData = json_decode(file_get_contents('php://input'), true);
if (empty($requestData)) {
    $requestData = $_POST;
}

// Create request
$request = Illuminate\Http\Request::create(
    '/api/home',
    'POST',
    $requestData,
    $_COOKIE,
    $_FILES,
    $_SERVER
);

// Handle request through Laravel
$response = $kernel->handle($request);

// Send response
$response->send();

$kernel->terminate($request, $response);

