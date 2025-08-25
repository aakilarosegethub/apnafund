<?php

namespace App\Models;

use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    use UniversalStatus;

    protected $hidden = [
        'gateway_parameters', 'extra'
    ];

    protected $casts = [
        'code'                 => 'string',
        'extra'                => 'object',
        'input_form'           => 'object',
        'supported_currencies' => 'object',
        'countries'            => 'array'
    ];

    public function currencies()
    {
        return $this->hasMany(GatewayCurrency::class, 'method_code', 'code');
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function singleCurrency()
    {
        return $this->hasOne(GatewayCurrency::class, 'method_code', 'code')->orderBy('id', 'desc');
    }

    public function scopeAutomated($query)
    {
        return $query->where('code', '<', 1000);
    }

    public function scopeManual($query)
    {
        return $query->where('code', '>=', 1000);
    }

    /**
     * Check if gateway is available for a specific country
     */
    public function isAvailableForCountry($country)
    {
        // If no countries are set, gateway is available for all countries
        if (empty($this->countries)) {
            return true;
        }
        
        return in_array($country, $this->countries);
    }

    /**
     * Scope to filter gateways by country
     */
    public function scopeForCountry($query, $country)
    {
        return $query->where(function($q) use ($country) {
            $q->whereNull('countries')
              ->orWhereJsonContains('countries', $country);
        });
    }
}
