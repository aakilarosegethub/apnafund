<?php

namespace App\Http\Middleware;

use App\Constants\ManageStatus;
use Closure;
use Illuminate\Http\Request;

/**
 * When site setting `signup` is inactive, blocks registration routes with a toast message.
 */
class AllowRegistration
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (bs('signup') == ManageStatus::INACTIVE) {
            $toast[] = ['info', 'We are not accepting registration at this moment'];

            return back()->withToasts($toast);
        }

        return $next($request);
    }
}
