<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces a first-time social (Google/Facebook/LinkedIn) account to confirm the
 * Terms of Use before reaching any authenticated user page.
 *
 * Without this gate a freshly created social user — who is logged in immediately
 * after the OAuth callback — could simply type /user/dashboard and skip the
 * acceptance screen. The check is based on the persisted users.terms_accepted_at
 * column so it survives session loss and direct navigation.
 *
 * Classic (email/password) registrations accept terms inside their own form, so
 * they have provider = null and are never affected here.
 */
class EnsureTermsAccepted
{
    /**
     * Route names that must stay reachable while terms are still pending,
     * otherwise the redirect would loop or trap the user.
     */
    private const EXEMPT_ROUTES = [
        'user.terms.accept.form',
        'user.terms.accept',
        'user.logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Guests are handled by the normal auth middleware; nothing to gate here.
        if (! $user || ! method_exists($user, 'needsTermsAcceptance') || ! $user->needsTermsAcceptance()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName && in_array($routeName, self::EXEMPT_ROUTES, true)) {
            return $next($request);
        }

        // Don't hijack API/JSON or non-GET requests with a redirect.
        if ($request->is('api/*') || $request->expectsJson()) {
            return $next($request);
        }

        return redirect()->route('user.terms.accept.form');
    }
}
