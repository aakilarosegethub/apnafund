<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Suppress deprecation warnings (E_STRICT deprecated in PHP 8.4+)
if (defined('E_STRICT') && PHP_VERSION_ID < 80400) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
