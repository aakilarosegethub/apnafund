<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreatorCampaignPayout extends Model
{
    protected $fillable = [
        'campaign_id',
        'success_marked_at',
        'total_raised',
        'platform_fee_type',
        'platform_fee_value',
        'platform_fee_amount',
        'marketing_fee_percent',
        'marketing_fee_amount',
        'chargeback_withholding_percent',
        'chargeback_withholding_amount',
        'fulfillment_withholding_percent',
        'fulfillment_withholding_amount',
        'net_payable_amount',
        'withheld_total_amount',
        'released_withheld_amount',
        'total_paid_amount',
        'payout_status',
        'fulfillment_status',
        'fulfillment_released_at',
        'created_by',
    ];

    protected $casts = [
        'success_marked_at' => 'datetime',
        'fulfillment_released_at' => 'datetime',
        'total_raised' => 'decimal:2',
        'platform_fee_value' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'marketing_fee_percent' => 'decimal:2',
        'marketing_fee_amount' => 'decimal:2',
        'chargeback_withholding_percent' => 'decimal:2',
        'chargeback_withholding_amount' => 'decimal:2',
        'fulfillment_withholding_percent' => 'decimal:2',
        'fulfillment_withholding_amount' => 'decimal:2',
        'net_payable_amount' => 'decimal:2',
        'withheld_total_amount' => 'decimal:2',
        'released_withheld_amount' => 'decimal:2',
        'total_paid_amount' => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(CreatorCampaignPayoutAction::class);
    }

    public function availableForPayout(): float
    {
        $totalPayable = (float) $this->net_payable_amount + (float) $this->released_withheld_amount;
        $remaining = $totalPayable - (float) $this->total_paid_amount;

        return $remaining > 0 ? round($remaining, 2) : 0.0;
    }

    public function remainingWithheldBalance(): float
    {
        $remaining = (float) $this->withheld_total_amount - (float) $this->released_withheld_amount;

        return $remaining > 0 ? round($remaining, 2) : 0.0;
    }
}
