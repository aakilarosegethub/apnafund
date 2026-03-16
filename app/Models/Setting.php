<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['email_from', 'mail_config', 'sms_from', 'email_template', 'sms_body'];
    protected $casts = ['mail_config' => 'object','sms_config' => 'object','universal_shortcodes' => 'object'];
    protected $hidden = ['email_template','mail_config','sms_config','system_info'];

    public function scopeSiteName($query, $pageTitle)
    {
        $pageTitle = empty($pageTitle) ? '' : ' | ' . $pageTitle;
        return $this->site_name . $pageTitle;
    }

    /**
     * Site currency - TCUR > IP-detected > DB. When TCUR not set, uses session(user_detected_currency) from IP.
     */
    public function getSiteCurAttribute($value)
    {
        return config('app.currency') ?: (session('user_detected_currency') ?: ($value ?? 'USD'));
    }

    /**
     * Currency symbol - TCUR > IP-detected > DB. When TCUR not set, uses IP-detected currency symbol.
     */
    public function getCurSymAttribute($value)
    {
        $tcur = config('app.currency') ?: session('user_detected_currency');
        if ($tcur) {
            $map = [
                'USD' => '$', 'PKR' => 'Rs', 'EUR' => '€', 'GBP' => '£',
                'INR' => '₹', 'SAR' => '﷼', 'AED' => 'د.إ', 'TRY' => '₺',
                'CAD' => 'C$', 'AUD' => 'A$', 'NZD' => 'NZ$', 'SEK' => 'kr',
                'NOK' => 'kr', 'DKK' => 'kr', 'CHF' => 'CHF', 'JPY' => '¥',
                'CNY' => '¥', 'HKD' => 'HK$', 'SGD' => 'S$', 'MYR' => 'RM',
                'BDT' => '৳', 'IDR' => 'Rp', 'THB' => '฿', 'PHP' => '₱',
                'ZAR' => 'R', 'NGN' => '₦', 'KES' => 'KSh', 'EGP' => 'E£',
                'BRL' => 'R$', 'MXN' => '$', 'RUB' => '₽',
            ];
            return $map[strtoupper(trim($tcur))] ?? $value ?? '$';
        }
        return $value ?? '$';
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function(){
            cache()->forget('setting');
        });
    }
}
