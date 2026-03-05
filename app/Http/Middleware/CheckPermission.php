<?php

namespace App\Http\Middleware;

use App\Support\PermissionHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login.form');
        }

        $helper = app(PermissionHelper::class);
        if ($helper->isSuperAdmin($admin)) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($helper->hasPermission($admin, $permission)) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'You do not have permission to perform this action.'], 403);
        }

        $toast[] = ['error', 'You do not have permission to access this page.'];
        return redirect()->route('admin.dashboard')->withToasts($toast);
    }
}
