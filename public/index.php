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

$request = Request::capture();

// Strip /apnafund prefix from URI if present
$requestUri = $request->server->get('REQUEST_URI', '');
if (strpos($requestUri, '/apnafund') === 0) {
    $newUri = preg_replace('#^/apnafund#', '', $requestUri);
    $request->server->set('REQUEST_URI', $newUri);
    // Also update PATH_INFO if it exists
    if ($request->server->has('PATH_INFO')) {
        $pathInfo = $request->server->get('PATH_INFO', '');
        if (strpos($pathInfo, '/apnafund') === 0) {
            $request->server->set('PATH_INFO', preg_replace('#^/apnafund#', '', $pathInfo));
        }
    }
}

$response = $kernel->handle($request)->send();

$kernel->terminate($request, $response);
