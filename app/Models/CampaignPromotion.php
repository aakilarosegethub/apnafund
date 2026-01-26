<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignPromotion extends Model
{
    protected $table = 'campaign_promotions';

    protected $fillable = [
        'campaign_id',
        'meta_campaign_id',
        'meta_adset_id',
        'meta_ad_id',
        'meta_creative_id',
        'status',
        'daily_budget',
        'error_message',
        'promoted_at',
    ];

    protected $casts = [
        'promoted_at' => 'datetime',
        'daily_budget' => 'decimal:2',
    ];

    /**
     * Get the campaign that owns the promotion
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
