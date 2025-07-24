<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use UniversalStatus, Searchable;

    /**
     * Get the campaigns for the category.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
