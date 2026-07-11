<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Staff account for the admin panel; may have `role_id` and JSON `permissions` or use RBAC relations.
 */
class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'status', 'permissions', 'role_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status' => 'integer',
        'permissions' => 'array',
    ];

    public function rbacRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        if ($this->role_id) {
            return (bool) ($this->rbacRole?->is_super_admin ?? false);
        }
        $p = $this->permissions;

        return $p === null || (is_array($p) && in_array('*', $p));
    }

    public function hasPermission(string $key): bool
    {
        return app(\App\Support\PermissionHelper::class)->hasPermission($this, $key);
    }
}
