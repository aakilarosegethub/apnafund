<?php

namespace App\Models\Admins;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Searchable;

class FooterCategory extends Model
{
    use HasFactory, Searchable;

    protected $table = 'footer_categories';

    protected $fillable = [
        'label',
        'slug',
        'category_id',
        'category_ids',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'category_ids' => 'array',
    ];

    /**
     * Get the campaign category (single – backward compat).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get category IDs for filtering campaigns (multi or single).
     */
    public function getCategoryIdsForFilter(): array
    {
        $ids = $this->category_ids;
        if (is_array($ids) && count($ids) > 0) {
            return array_map('intval', $ids);
        }
        if ($this->category_id) {
            return [(int) $this->category_id];
        }
        return [];
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
