<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use App\Support\PermissionHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin panel RBAC: allows a whitelist of routes for all admins; otherwise checks {@see PermissionHelper} permissions and logs unauthorized attempts.
 */
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

        $helper = app(PermissionHelper::class);
        if ($helper->isSuperAdmin($admin)) {
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
            app(ActivityLogger::class)->logUnauthorized($routeName, 'Sub-admin management restricted to super admin');
            return redirect()->route('admin.dashboard')->withToasts([['error', 'Only super admin can manage sub admins.']]);
        }
        if (str_starts_with($routeName, 'admin.roles')) {
            app(ActivityLogger::class)->logUnauthorized($routeName, 'Role management restricted to super admin');
            return redirect()->route('admin.dashboard')->withToasts([['error', 'Only super admin can manage roles.']]);
        }
        if (!$this->adminCanAccessRoute($admin, $routeName, $helper)) {
            app(ActivityLogger::class)->logUnauthorized($routeName, 'Permission denied for route');
            $toast[] = ['error', 'You do not have permission to access this page.'];
            return redirect()->route('admin.dashboard')->withToasts($toast);
        }

        return $next($request);
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

    /** Get list of route names the sub-admin is allowed to access (for debugging) */
    protected function getAllowedRoutesForAdmin($admin, PermissionHelper $helper): array
    {
        $allowed = [];
        $routeConfig = config('admin_route_permissions', []);
        $sorted = collect($routeConfig)->sortByDesc(fn ($_, $prefix) => strlen($prefix));

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            if (!$name || !str_starts_with($name, 'admin.')) {
                continue;
            }
            if (str_starts_with($name, 'admin.admin-users') || str_starts_with($name, 'admin.roles')) {
                continue;
            }
            if ($this->isAllowedForAll($name)) {
                $allowed[] = $name;
                continue;
            }
            foreach ($sorted as $prefix => $permissionKey) {
                if (str_starts_with($name, $prefix)) {
                    $keys = is_array($permissionKey) ? $permissionKey : [$permissionKey];
                    foreach ($keys as $key) {
                        if ($helper->hasPermission($admin, $key)) {
                            $allowed[] = $name;
                            break 2;
                        }
                    }
                    break;
                }
            }
            if ($admin->permissions && is_array($admin->permissions)) {
                $config = config('admin_permissions', []);
                foreach ($admin->permissions as $key) {
                    $patterns = $config[$key] ?? [];
                    foreach ($patterns as $pattern) {
                        if (str_ends_with($pattern, '*')) {
                            if (str_starts_with($name, rtrim($pattern, '*'))) {
                                $allowed[] = $name;
                                break 2;
                            }
                        } elseif ($name === $pattern) {
                            $allowed[] = $name;
                            break 2;
                        }
                    }
                }
            }
        }

        sort($allowed);
        return array_values(array_unique($allowed));
    }

    protected function adminCanAccessRoute($admin, string $routeName, PermissionHelper $helper): bool
    {
        // Sub-admin allowed permissions & routes (for debugging)
        $allowedPermissions = $admin->role_id && $admin->rbacRole
            ? $admin->rbacRole->permissions->pluck('key')->toArray()
            : (is_array($admin->permissions) ? $admin->permissions : []);
        $allowedRoutes = $this->getAllowedRoutesForAdmin($admin, $helper);
        $routeConfig = config('admin_route_permissions', []);
        // Match most specific prefix first (longer = more specific)
        $sorted = collect($routeConfig)->sortByDesc(fn ($_, $prefix) => strlen($prefix));
        
        foreach ($sorted as $prefix => $permissionKey) {
            if (str_starts_with($routeName, $prefix)) {
                $keys = is_array($permissionKey) ? $permissionKey : [$permissionKey];
                foreach ($keys as $key) {
                    if ($helper->hasPermission($admin, $key)) {
                        return true;
                    }
                }
                return false; // Matched prefix but no permission
            }
        }

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
