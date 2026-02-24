<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorizationStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Email verification only (mobile/SMS and 2FA skipped)
            if ($user->status && $user->ec) {
                return $next($request);
            } else {
                return to_route('user.authorization');
            }
        }

        abort(403);
    }
}
