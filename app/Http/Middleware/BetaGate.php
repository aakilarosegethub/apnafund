<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Optional beta landing flow: when `BETA_GATE_ENABLED` is true, visitors without `apnafund_beta_seen` cookie
 * are redirected to `/beta` until they accept (see `routes/web.php`).
 */
class BetaGate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if beta gate is enabled via environment variable
        // Set BETA_GATE_ENABLED=false in .env to disable beta gate
        $betaGateEnabled = env('BETA_GATE_ENABLED', 'false');
        
        // If beta gate is disabled, skip all checks
        if (strtolower($betaGateEnabled) === 'false' || $betaGateEnabled === false) {
            return $next($request);
        }

        // If cookie already set, allow normal flow
        if (Cookie::get('apnafund_beta_seen')) {
            return $next($request);
        }

        // Allow accessing the beta page and accept route without redirect loop
        if ($request->is('beta') || $request->is('beta/start')) {
            return $next($request);
        }

        // For all other front routes, send first-time visitors to beta page
        return redirect()->route('beta.page');
    }
}

