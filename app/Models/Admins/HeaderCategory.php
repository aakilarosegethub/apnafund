<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;

class HeaderCategory extends Model
{
    use HasFactory, Searchable;

    protected $table = 'header_categories';

    protected $fillable = [
        'label',
        'slug',
        'sort_order',
        'status',
    ];

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->status == 'active') {
            return '<span class="badge badge--success">Active</span>';
        }
        return '<span class="badge badge--danger">Inactive</span>';
    }
}
