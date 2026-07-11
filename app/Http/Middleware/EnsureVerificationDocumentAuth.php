<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures CNIC / campaign verification documents are never served to guests.
 * Applied in addition to route-level auth middleware (defense in depth).
 */
class EnsureVerificationDocumentAuth
{
    public function handle(Request $request, Closure $next, string $context = 'web'): Response
    {
        return match ($context) {
            'admin' => $this->ensureAdmin($request, $next),
            'api' => $this->ensureApiUser($request, $next),
            default => $this->ensureWebUser($request, $next),
        };
    }

    private function ensureWebUser(Request $request, Closure $next): Response
    {
        if (! auth('web')->check()) {
            return redirect()->guest(route('user.login'));
        }

        return $next($request);
    }

    private function ensureAdmin(Request $request, Closure $next): Response
    {
        if (! auth('admin')->check()) {
            abort(403, 'Admin authentication required.');
        }

        return $next($request);
    }

    private function ensureApiUser(Request $request, Closure $next): Response
    {
        if (! $request->user('sanctum')) {
            abort(401, 'Authentication required.');
        }

        return $next($request);
    }
}
