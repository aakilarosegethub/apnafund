<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !($user instanceof \App\Models\Admin)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}
