# RBAC & Activity Logging Integration Guide

## Overview

This document describes the RBAC (Role-Based Access Control) and Activity Logging system integrated into the ApnaFund admin panel.

---

## Phase 1 — System Analysis Summary

### Existing Authorization
- **Admin model**: `permissions` JSON column; `isSuperAdmin()`, `hasPermission()`
- **Middleware**: `AdminPermission` – route-based checks via `config/admin_permissions.php`
- **Helper**: `admin_can($key)` – checks permission keys
- **Super Admin**: `permissions === null` or `['*']`
- **Sub-admin management**: Super admin only (unchanged)

### Legacy Compatibility
- Admins **without** `role_id`: Use legacy `permissions` JSON + `admin_permissions` route mapping
- Admins **with** `role_id`: Use RBAC (roles + permissions)
- `admin_can()` works with both legacy keys and RBAC permission keys

---

## Phase 2 — RBAC Structure

### New Tables
- **roles**: id, name, slug, description, is_super_admin, is_protected
- **permissions**: id, key (module.action), module, action, description
- **role_permissions**: role_id, permission_id
- **admins**: added `role_id` (nullable, FK to roles)

### Permission Format
`module.action` — examples:
- `users.view`, `users.create`, `users.update`, `users.delete`
- `admin_users.view`, `campaigns.view`, `settings.manage`
- `activity_logs.view`, `roles.view`, `roles.manage`

### New Components
- **PermissionHelper** (`app/Support/PermissionHelper.php`): `hasPermission($admin, $key)`, Super Admin bypass
- **CheckPermission** middleware: `permission:users.view` for granular checks
- **Legacy map**: Old keys (seo, blog, users, etc.) mapped to new permissions for backward compatibility

### Usage Examples
```php
// In routes - granular permission middleware
Route::get('users', [UserController::class, 'index'])
    ->middleware(['admin', 'admin.permission', 'permission:users.view']);

// In Blade - existing helper works with both legacy and RBAC
@if(admin_can('users.view'))
    ...
@endif
@if(admin_can('users'))  {{-- legacy key, mapped to users.view --}}
    ...
@endif

// Programmatic check
app(PermissionHelper::class)->hasPermission(auth()->guard('admin')->user(), 'users.view');

// Manual activity logging in a controller
app(ActivityLogger::class)->log('custom_action', 'my_module', $recordId, $old, $new, 'Description');
```

---

## Phase 3 — Activity Logging

### Table
`admin_activity_logs`: user_id, role_id, action_type, module_name, record_id, old_data, new_data, ip, user_agent, url, method, description, timestamps

### Service
**ActivityLogger** (`app/Services/ActivityLogger.php`)
- `log($actionType, $moduleName, $recordId, $oldData, $newData, $description)`
- `logModelEvent($action, $model, $oldData)`
- `logAuth($action, $identifier, $description)`
- `logUnauthorized($route, $description)`

### Action Types
- `created`, `updated`, `deleted`
- `login`, `logout`, `failed_login`, `unauthorized`

### Model Observers
- **UserObserver**, **AdminObserver**, **CampaignObserver** — log create/update/delete when an admin is authenticated

### Auth Events
- **LogAdminLogin**: On Login (guard admin)
- **LogAdminLogout**: On Logout
- **LogFailedAdminLogin**: On Failed (guard admin)

### Logging Unauthorized Access
`AdminPermission` middleware logs unauthorized attempts via `ActivityLogger::logUnauthorized()`.

---

## Phase 4 — Safe Integration

- No existing routes removed or broken
- RBAC changes are additive; legacy `permissions` JSON still works
- Super Admin: `role?.is_super_admin` or legacy `permissions` null/`['*']`
- Super Admin and protected roles cannot be deleted
- Sub-admin and role management restricted to Super Admin

---

## Phase 5 — Admin UI

### Role Management (`/admin/roles`)
- **Index**: List roles with admin count
- **Create**: Add role and assign permissions (grouped by module)
- **Edit**: Update role and permissions (Super Admin role is read-only for permissions)
- **Delete**: Allowed only for non-protected roles with no assigned admins

### Activity Logs (`/admin/activity-logs`)
- **Index**: Filterable table (action, module, date range)
- **Detail modal**: Old vs new data comparison (read-only)

---

## Installation

```bash
# Run migrations
php artisan migrate

# Seed RBAC (roles, permissions, assign Super Admin to existing full-access admins)
php artisan db:seed --class=RbacSeeder
```

---

## Refactoring Strategy

1. **Do not change** existing business logic in controllers.
2. **Add** `permission:module.action` middleware to routes gradually.
3. Keep using `admin_can()` in Blade; it now delegates to PermissionHelper.
4. **Replace** any direct `if ($user->role == 'admin')` with `middleware('permission:...')` or `admin_can()`.
5. **Remove** old custom permission checks only when fully replaced by RBAC.

---

## Folder Structure

```
app/
├── Http/
│   ├── Middleware/
│   │   ├── AdminPermission.php    # Refactored for RBAC + logging
│   │   └── CheckPermission.php    # permission:key middleware
│   └── Controllers/Admin/
│       ├── RoleController.php
│       └── ActivityLogController.php
├── Listeners/
│   ├── LogAdminLogin.php
│   ├── LogAdminLogout.php
│   └── LogFailedAdminLogin.php
├── Models/
│   ├── Admin.php          # role_id, role(), hasPermission()
│   ├── Role.php
│   ├── Permission.php
│   └── AdminActivityLog.php
├── Observers/
│   ├── AdminActivityObserver.php
│   ├── UserObserver.php
│   ├── AdminObserver.php
│   └── CampaignObserver.php
├── Services/
│   └── ActivityLogger.php
└── Support/
    └── PermissionHelper.php

config/
├── admin_permissions.php   # Legacy route mapping (unchanged)
└── admin_route_permissions.php  # Route -> RBAC permission mapping
```
