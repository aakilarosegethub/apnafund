<?php

namespace App\Http\Middleware;

use App\Constants\ManageStatus;
use Closure;
use Illuminate\Http\Request;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (bs('site_maintenance') == ManageStatus::ACTIVE) {
            return to_route('maintenance');
        }

        return $next($request);
    }
}
