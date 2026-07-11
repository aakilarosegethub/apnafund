<?php

namespace App\Models;

use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;

/**
 * Payment gateway definition (`code`, automation flags); related {@see GatewayCurrency} rows define supported currencies.
 */
class Gateway extends Model
{
    use UniversalStatus;

    protected $hidden = [
        'gateway_parameters', 'extra',
    ];

    protected $casts = [
        'code' => 'string',
        'extra' => 'object',
        'input_form' => 'object',
        'supported_currencies' => 'object',
        'countries' => 'array',
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
        return $query->where(function ($q) use ($country) {
            $q->whereNull('countries')
                ->orWhereJsonContains('countries', $country);
        });
    }

    /**
     * Contribute / availability: `countries` JSON may list country names (e.g. Pakistan) and/or ISO codes (e.g. PKR).
     */
    public function scopeForGatewayRegion($query, ?string $country, ?string $localCurrencyCode = null)
    {
        if ($country === null || $country === '') {
            return $query;
        }

        $strictCode = resolveStrictCurrencyCodeForCountryName($country);
        $local = $localCurrencyCode !== null && $localCurrencyCode !== ''
            ? strtoupper(trim($localCurrencyCode))
            : '';
        $alpha2 = \App\Services\CurrencyService::resolveIsoAlpha2FromCountryLabel($country);

        return $query->where(function ($q) use ($country, $strictCode, $local, $alpha2) {
            $q->whereNull('countries')
                ->orWhereRaw('COALESCE(JSON_LENGTH(countries), 0) = 0')
                ->orWhereJsonContains('countries', $country);
            if ($strictCode !== null && $strictCode !== '') {
                $q->orWhereJsonContains('countries', $strictCode);
            }
            if ($local !== '') {
                $q->orWhereJsonContains('countries', $local);
            }
            if ($alpha2 !== null && $alpha2 !== '') {
                $q->orWhereJsonContains('countries', $alpha2);
            }
        });
    }
}
