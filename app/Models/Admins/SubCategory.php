<?php

namespace App\Models\Admins;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Traits\Searchable;

class SubCategory extends Model
{
    use HasFactory, Searchable;

    protected $table = 'sub_categories';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'status',
    ];

    /**
     * Get the parent category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

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
