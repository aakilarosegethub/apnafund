<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Unauthenticated redirect: web users go to `user.login`; JSON/API requests get a 401 without redirect.
 */
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API routes, return null to throw AuthenticationException (handled by Handler)
        if ($request->is('api/*') || $request->expectsJson()) {
            return null;
        }
        
        // For web routes, try to find login route or return null
        try {
            return route('user.login');
        } catch (\Exception $e) {
            // If route doesn't exist, return null (will be handled by Handler)
            return null;
        }
    }
}
