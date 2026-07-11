<?php

namespace App\Models;

use App\Models\Admins\SubCategory;
use App\Traits\Searchable;
use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Public campaign category (browse/filter); uses {@see UniversalStatus} and `active` scope.
 */
class Category extends Model
{
    use Searchable, UniversalStatus;

    /**
     * Get the campaigns for the category.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the subcategories for the category.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
