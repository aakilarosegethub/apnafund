<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FX row: ISO code and `rate_to_usd` for {@see CurrencyService} conversions.
 */
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
