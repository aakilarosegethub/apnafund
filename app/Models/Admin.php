<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'status', 'permissions',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status'      => 'integer',
        'permissions' => 'array',
    ];

    public function isSuperAdmin(): bool
    {
        $p = $this->permissions;
        return $p === null || (is_array($p) && in_array('*', $p));
    }

    public function hasPermission(string $key): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return is_array($this->permissions) && in_array($key, $this->permissions);
    }
}
