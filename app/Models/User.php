<?php

namespace App\Models;

use App\Constants\ManageStatus;
use App\Traits\Searchable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password',
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
        'ver_code_send_at'  => 'datetime'
    ];

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
}
