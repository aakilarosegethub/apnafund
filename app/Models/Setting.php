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

    protected static function boot()
    {
        parent::boot();

        static::saved(function(){
            cache()->forget('setting');
        });
    }
}
