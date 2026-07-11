<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Guest-only routes: if the given guard is already authenticated, redirect to the user home route.
 */
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (auth()->guard($guard)->check()) {
            return to_route('user.home');
        }

        return $next($request);
    }
}
