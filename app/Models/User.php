<?php

namespace App\Models;

use App\Constants\ManageStatus;
use App\Traits\Searchable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

/**
 * End-user account (backer and/or campaign creator) with Sanctum API tokens.
 *
 * **Mass assignment:** see `$fillable` (profile, business, OAuth, verification images, etc.). **Hidden:** secrets and wallet fields.
 *
 * **Relationships:** `deposits`, `withdrawals`, `transactions`, `campaigns`, `comments`, `appNotifications`, `pushDevices`, …
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, Searchable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'firstname', 'lastname', 'username', 'mobile', 'whatsapp', 'country_code', 'country_name', 'address', 'business_type', 'business_name', 'business_description', 'industry', 'funding_amount', 'fund_usage', 'campaign_duration', 'phone', 'phone_verified_at', 'last_login_at', 'provider', 'provider_id', 'avatar', 'status', 'ec', 'sc', 'tc', 'terms_accepted_at',
        'ver_code', 'ver_code_send_at', 'cnic_front_image', 'cnic_back_image',
        'failed_login_attempts', 'blocked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token', 'ver_code', 'balance', 'kyc_data'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'address'           => 'object',
        'kyc_data'          => 'object',
        'ver_code_send_at'  => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'terms_accepted_at' => 'datetime',
        'blocked_until'     => 'datetime',
    ];

    /**
     * Whether this account still needs to accept the Terms of Use.
     * Only social (OAuth) sign-ups are gated; classic registrations accept
     * terms inside the registration form itself.
     */
    public function needsTermsAcceptance(): bool
    {
        return !empty($this->provider) && is_null($this->terms_accepted_at);
    }

    /**
     * Get the user's full name.
     */
    public function fullname(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=' , ManageStatus::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', ManageStatus::PAYMENT_INITIATE);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    /**
     * Get the campaigns for the user.
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the comments for the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * In-app notifications for creators (header bell). Not Laravel's database notifications.
     */
    public function appNotifications()
    {
        return $this->hasMany(UserNotification::class)->latest();
    }

    public function pushDevices()
    {
        return $this->hasMany(UserPushDevice::class);
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', ManageStatus::ACTIVE)->where('ec', ManageStatus::VERIFIED)->where('sc', ManageStatus::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', ManageStatus::INACTIVE);
    }

    public function scopeEmailUnconfirmed($query)
    {
        return $query->where('ec', ManageStatus::UNVERIFIED);
    }

    public function scopeMobileUnconfirmed($query)
    {
        return $query->where('sc', ManageStatus::UNVERIFIED);
    }

    public function scopeKycUnconfirmed($query)
    {
        return $query->where('kc', ManageStatus::UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kc', ManageStatus::PENDING);
    }

    public function scopePhoneVerified($query)
    {
        return $query->whereNotNull('phone_verified_at');
    }

    /**
     * Send email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        // Send welcome email to user
        $this->notify(new \App\Notifications\WelcomeNotification($this));
    }
}
