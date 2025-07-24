<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, $guard = 'admin'): Response
    {
        if (auth()->guard($guard)->check()) {
            return to_route('admin.dashboard');
        }

        return $next($request);
    }
}
