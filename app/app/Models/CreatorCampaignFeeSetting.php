<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreatorCampaignFeeSetting extends Model
{
    protected $fillable = [
        'platform_fee_type',
        'platform_fee_value',
        'marketing_fee_percent',
        'chargeback_withholding_percent',
        'fulfillment_withholding_percent',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'platform_fee_value' => 'decimal:2',
        'marketing_fee_percent' => 'decimal:2',
        'chargeback_withholding_percent' => 'decimal:2',
        'fulfillment_withholding_percent' => 'decimal:2',
    ];
}
