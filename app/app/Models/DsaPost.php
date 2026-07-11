<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DsaPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'feature_image',
        'excerpt',
        'status',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Boot the model - auto-generate slug from title on create.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    /**
     * Scope for active posts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
