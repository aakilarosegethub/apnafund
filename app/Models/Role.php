<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_super_admin',
        'is_protected',
        'dashboard_widgets',
    ];

    protected $casts = [
        'is_super_admin'   => 'boolean',
        'is_protected'     => 'boolean',
        'dashboard_widgets' => 'array',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id');
    }

    public function hasPermission(string $key): bool
    {
        if ($this->is_super_admin) {
            return true;
        }
        return $this->permissions()->where('key', $key)->exists();
    }

    public function canDelete(): bool
    {
        return !$this->is_protected;
    }
}
