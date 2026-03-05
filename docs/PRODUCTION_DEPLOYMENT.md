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

## Route Caching

This project is **safe for route caching**. Always use route names in Blade templates rather than hardcoded URLs.

```bash
php artisan route:cache
```

To clear route cache during development:
```bash
php artisan route:clear
```

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

### Middleware not found
- Check `app/Http/Kernel.php` and `bootstrap/app.php` for alias registration
- AdminPermission is at `App\Http\Middleware\AdminPermission::class`

### Maintenance mode stuck
- Delete `storage/framework/down` file manually if `php artisan up` fails
- Or: `php artisan up --refresh`
