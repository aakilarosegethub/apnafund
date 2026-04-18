<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Singleton-style site settings row (mail/SMS, branding, toggles); accessed widely via `bs()`.
 */
class Setting extends Model
{
    protected $fillable = ['email_from', 'mail_config', 'sms_from', 'email_template', 'sms_body', 'site_email'];
    protected $casts = ['mail_config' => 'object','sms_config' => 'object','universal_shortcodes' => 'object'];
    protected $hidden = ['email_template','mail_config','sms_config','system_info'];

    public function scopeSiteName($query, $pageTitle)
    {
        $pageTitle = empty($pageTitle) ? '' : ' | ' . $pageTitle;
        return $this->site_name . $pageTitle;
    }

    /**
     * Site currency for display - TCUR > session > DB.
     */
    public function getSiteCurAttribute($value)
    {
        if (config('app.currency')) {
            return config('app.currency');
        }

        return session('user_detected_currency') ?: ($value ?? 'USD');
    }

    /**
     * Currency symbol for display - TCUR > session > DB.
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
