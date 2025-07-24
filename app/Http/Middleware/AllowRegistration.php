<?php

namespace App\Http\Middleware;

use App\Constants\ManageStatus;
use Closure;
use Illuminate\Http\Request;

class AllowRegistration
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
        if (bs('signup') == ManageStatus::INACTIVE) {
            $toast[] = ['info', 'We are not accepting registration at this moment'];

            return back()->withToasts($toast);
        }

        return $next($request);
    }
}
