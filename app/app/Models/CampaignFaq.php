<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FAQ entry displayed on a campaign detail page.
 */
class CampaignFaq extends Model
{
    protected $fillable = [
        'campaign_id',
        'question',
        'answer',
        'order',
    ];

    /**
     * Get the campaign that owns the FAQ.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
