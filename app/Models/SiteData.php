<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteData extends Model
{
    protected $fillable = [
        'data_key',
        'data_info'
    ];

    protected $casts = [
        'data_info' => 'object'
    ];
}
