<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * CSRF verification for web routes; `api/*` and selected endpoints are excluded for token/JSON clients.
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'user/register-business',
        'api/verify-email',
        'api/*',  // Exclude all API routes from CSRF verification
    ];
}
