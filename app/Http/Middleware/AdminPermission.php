<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    protected array $allowedForAll = [
        'admin.dashboard',
        'admin.profile',
        'admin.password.update',
        'admin.system.notification.*',
        'admin.cache.clear',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login.form');
        }

        if ($this->isSuperAdmin($admin)) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if (!$routeName) {
            return $next($request);
        }

        if ($this->isAllowedForAll($routeName)) {
            return $next($request);
        }

        if (str_starts_with($routeName, 'admin.admin-users')) {
            return redirect()->route('admin.dashboard')->withToasts([['error', 'Only super admin can manage sub admins.']]);
        }

        if (!$this->adminCanAccessRoute($admin, $routeName)) {
            $toast[] = ['error', 'You do not have permission to access this page.'];
            return redirect()->route('admin.dashboard')->withToasts($toast);
        }

        return $next($request);
    }

    protected function isSuperAdmin($admin): bool
    {
        $p = $admin->permissions;
        return $p === null || (is_array($p) && (empty($p) === false && in_array('*', $p)));
    }

    protected function isAllowedForAll(string $routeName): bool
    {
        foreach ($this->allowedForAll as $pattern) {
            if (str_ends_with($pattern, '*')) {
                $prefix = rtrim($pattern, '*');
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            } elseif ($routeName === $pattern) {
                return true;
            }
        }
        return false;
    }

    protected function adminCanAccessRoute($admin, string $routeName): bool
    {
        $permissions = $admin->permissions;
        if (!is_array($permissions)) {
            return false;
        }

        $config = config('admin_permissions', []);
        foreach ($permissions as $key) {
            $patterns = $config[$key] ?? [];
            foreach ($patterns as $pattern) {
                if (str_ends_with($pattern, '*')) {
                    $prefix = rtrim($pattern, '*');
                    if (str_starts_with($routeName, $prefix)) {
                        return true;
                    }
                } elseif ($routeName === $pattern) {
                    return true;
                }
            }
        }
        return false;
    }
}
