<?php

namespace App\Models;

use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;

class PayoutBank extends Model
{
    use UniversalStatus;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the campaigns for this payout bank.
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
