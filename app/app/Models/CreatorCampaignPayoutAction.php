<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorCampaignPayoutAction extends Model
{
    protected $fillable = [
        'creator_campaign_payout_id',
        'admin_id',
        'action_type',
        'amount',
        'notes',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    public function payout(): BelongsTo
    {
        return $this->belongsTo(CreatorCampaignPayout::class, 'creator_campaign_payout_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
