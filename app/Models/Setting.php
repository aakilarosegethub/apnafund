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
     * Currency symbol - TCUR > IP-detected (from DB cache) > DB. When TCUR not set, uses session user_detected_symbol.
     */
    public function getCurSymAttribute($value)
    {
        if (config('app.currency')) {
            return \App\Services\CurrencyService::getSymbolForCode(config('app.currency'));
        }
        if (session('user_detected_symbol')) {
            return session('user_detected_symbol');
        }
        $tcur = session('user_detected_currency');
        if ($tcur) {
            return \App\Services\CurrencyService::getSymbolForCode($tcur);
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
