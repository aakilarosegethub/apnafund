# Production Deployment Guide (Mac → Linux)

This document covers deployment of the Laravel application from Mac (case-insensitive filesystem) to Linux (case-sensitive, e.g., Hostinger).

## Pre-Deployment Checklist

1. **Clear all caches before deploying**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```

2. **Run migrations**
   ```bash
   php artisan migrate --force
   ```

3. **Build frontend assets**
   ```bash
   npm run build
   ```

## Production Commands (Run on Server After Deploy)

```bash
# Enter maintenance mode during deployment
php artisan down

# Deploy your files (via git pull, FTP, etc.)

# Run migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear and rebuild caches if needed
# php artisan optimize

# Exit maintenance mode
php artisan up
```

## Server Validation Commands (Run After Deploy)

Run these commands on the server to validate the setup:

```bash
# 1. Verify application boots
php artisan about

# 2. Verify middleware is registered (should list admin.permission)
php artisan route:list --path=admin 2>&1 | head -5

# 3. Build route cache (must succeed)
php artisan route:clear && php artisan route:cache && echo "Route cache OK"

# 4. Build config cache
php artisan config:cache && echo "Config cache OK"

# 5. Verify storage permissions
ls -la storage/framework/cache 2>/dev/null && echo "Storage writable"

# 6. Run migrations (dry-check)
php artisan migrate:status
```

Quick one-liner for full validation:
```bash
php artisan about && php artisan route:cache && php artisan config:cache && echo "✓ Production setup OK"
```

## Route Caching

This project is **safe for route caching**. Admin routes use controller references; all admin routes are protected by `admin` and `admin.permission` middleware.

```bash
php artisan route:cache
```

To clear route cache during development:
```bash
php artisan route:clear
```

**Note:** If `route:cache` fails with "Unable to prepare route for serialization", run `php artisan route:clear` and avoid route caching until closure-based routes are converted to controllers.

## Middleware & RBAC

- **AdminPermission** (`App\Http\Middleware\AdminPermission::class`): Registered as `admin.permission` in `bootstrap/app.php` (Laravel 11) and `app/Http/Kernel.php`
- **Admin routes**: All `/admin/*` routes (except login, logout, forgot password) use `['admin', 'admin.permission']` middleware
- **Admin login routes**: Use `admin.guest` middleware

## Case Sensitivity (Mac vs Linux)

- **Middleware**: `AdminLogedIn.php` and `AdminNotLogedIn.php` use PascalCase to match Linux filesystem requirements
- **Controllers**: Ensure all class names match file names exactly (e.g., `AdminPermission` in `AdminPermission.php`)
- **Namespaces**: Verify `App\Http\Controllers\Admin\*` and `App\Http\Middleware\*` match exactly

## Key Route Names (for Blade templates)

| Route Name | Purpose |
|------------|---------|
| admin.user.index | User management list |
| admin.user.active | Active users |
| admin.user.email.unconfirmed | Email unconfirmed users |
| admin.user.details | User details (use `details` not `detail`) |
| admin.donations.done | Completed donations |
| admin.registration.steps.* | Registration steps management |
| admin.notification.welcome.update | Welcome email template |

## Troubleshooting

### Missing routes
- Run `php artisan route:clear` then `php artisan route:cache`
- Verify route names in Blade: use `route('admin.user.index')` not hardcoded paths

### Middleware not found (admin.permission)
- **Laravel 11**: Check `bootstrap/app.php` → `$middleware->alias(['admin.permission' => ...])`
- **Legacy**: Check `app/Http/Kernel.php` → `$routeMiddleware`
- AdminPermission class: `App\Http\Middleware\AdminPermission`

### Maintenance mode stuck
- Delete `storage/framework/down` file manually if `php artisan up` fails
- Or: `php artisan up --refresh`
