<?php

namespace App\Models;

use App\Constants\ManageStatus;
use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;

class GatewayCurrency extends Model
{   
    use UniversalStatus;

    protected $fillable = [
        'name', 'currency', 'symbol', 'method_code', 'gateway_alias',
        'min_amount', 'max_amount', 'percent_charge', 'fixed_charge',
        'rate', 'gateway_parameter', 'status'
    ];

    protected $hidden = [
        'gateway_parameter'
    ];

    protected $casts = ['status' => 'boolean'];

    public function method()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function baseSymbol()
    {
        return $this->method->crypto == ManageStatus::ACTIVE ? '$' : $this->symbol;
    }
}
