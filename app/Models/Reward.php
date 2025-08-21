<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Reward extends Model
{
    protected $fillable = [
        'campaign_id',
        'title',
        'description',
        'minimum_amount',
        'quantity',
        'claimed_count',
        'image',
        'type',
        'color_theme',
        'terms_conditions',
        'is_active'
    ];

    protected $casts = [
        'minimum_amount' => 'decimal:2',
        'quantity' => 'integer',
        'claimed_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the campaign that owns the reward.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Scope a query to only include active rewards.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if reward is available (not sold out).
     */
    public function isAvailable(): bool
    {
        if ($this->quantity === null) {
            return true; // Unlimited quantity
        }
        return $this->claimed_count < $this->quantity;
    }

    /**
     * Get remaining quantity.
     */
    public function getRemainingQuantity(): ?int
    {
        if ($this->quantity === null) {
            return null; // Unlimited
        }
        return max(0, $this->quantity - $this->claimed_count);
    }

    /**
     * Get the reward image URL.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->image) {
                    return getImage(getFilePath('reward') . '/' . $this->image, getThumbSize('reward'));
                }
                return asset('assets/images/default-reward.png');
            }
        );
    }
}
