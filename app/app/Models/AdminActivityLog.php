<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'user_id',
        'role_id',
        'action_type',
        'module_name',
        'record_id',
        'old_data',
        'new_data',
        'ip',
        'user_agent',
        'url',
        'method',
        'description',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
