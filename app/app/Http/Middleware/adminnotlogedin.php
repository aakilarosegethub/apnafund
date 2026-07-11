<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Legacy session check: if `admin` session exists, redirect away from login to `admin/dashboard`.
 */
class AdminNotLogedIn
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::get('admin')) {
            return redirect('admin/dashboard');
        }

        return $next($request);
    }
}
