<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignDocumentField extends Model
{
    protected $fillable = [
        'field_key',
        'label',
        'is_required',
        'is_active',
        'is_global',
        'countries',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'countries' => 'array',
        'sort_order' => 'integer',
    ];
}
