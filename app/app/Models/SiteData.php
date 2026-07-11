<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CMS key/value content blocks (`data_key`, JSON `data_info`) for editable page sections.
 */
class SiteData extends Model
{
    protected $fillable = [
        'data_key',
        'data_info',
    ];

    protected $casts = [
        'data_info' => 'array',
    ];
}
