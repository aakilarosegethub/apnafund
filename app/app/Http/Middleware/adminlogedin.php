<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Legacy session check: requires `admin` key in session (older admin auth); redirects to `admin/login` if missing.
 */
class AdminLogedIn
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Session::has('admin')) {
            return redirect('admin/login');
        }

        return $next($request);
    }
}
