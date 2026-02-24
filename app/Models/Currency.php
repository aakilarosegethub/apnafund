<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'rate_to_usd',
        'source',
    ];

    protected $casts = [
        'rate_to_usd' => 'float',
    ];
}
