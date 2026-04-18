<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * For logged-in users: requires email verification (`ec`) before continuing; used on sensitive user routes.
 */
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
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email (complete account authorization) before continuing.',
                    'requires_authorization' => true,
                    'redirect_url' => route('user.authorization'),
                ], 403);
            }

            return to_route('user.authorization');
        }

        abort(403);
    }
}
