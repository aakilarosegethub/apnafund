<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\Searchable;
use App\Constants\ManageStatus;
use App\Traits\UniversalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use Searchable, UniversalStatus;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'video',
        'youtube_url',
        'location',
        'gallery',
        'preferred_amounts',
        'goal_amount',
        'raised_amount',
        'start_date',
        'end_date',
        'status',
        'featured'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'gallery'           => 'array',
        'preferred_amounts' => 'array',
        'goal_amount'       => 'decimal:2',
        'raised_amount'     => 'decimal:2',
        'start_date'        => 'datetime:Y-m-d',
        'end_date'          => 'datetime:Y-m-d',
    ];

    /**
     * Get the user that owns the campaign.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the campaign.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the comments for the campaign.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', ManageStatus::CAMPAIGN_COMMENT_APPROVED)->orderByDesc('id');
    }

    /**
     * Get the deposits for the campaign.
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'campaign_id', 'id');
    }

    /**
     * Get the rewards for the campaign.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    /**
     * Scope a query to only include pending campaigns.
     */
    public function scopePending($query): void
    {
        $query->where('campaigns.status', ManageStatus::CAMPAIGN_PENDING);
    }

    /**
     * Scope a query to only include approved campaigns.
     */
    public function scopeApprove($query): void
    {
        $query->where('campaigns.status', ManageStatus::CAMPAIGN_APPROVED);
    }

    /**
     * Scope a query to only include rejected campaigns.
     */
    public function scopeReject($query): void
    {
        $query->where('campaigns.status', ManageStatus::CAMPAIGN_REJECTED);
    }

    public function scopeRunning($query): void
    {
        $query->where('start_date', '<', now())->where('end_date', '>', now());
    }

    public function scopeUpcoming($query): void
    {
        $query->where('start_date', '>', now());
    }

    public function scopeExpired($query): void
    {
        $query->where('end_date', '<', now());
    }

    /**
     * Scope a query to only include featured campaigns.
     */
    public function scopeFeatured($query): void
    {
        $query->where('featured', ManageStatus::YES);
    }

    public function scopeCommonQuery($query): void
    {
        $query->whereHas('category', fn ($innerQuery) => $innerQuery->active())->whereHas('user', fn ($innerQuery) => $innerQuery->active());
    }

    /**
     * Scope a query to only include campaigns that meet certain criteria.
     */
    public function scopeCampaignCheck($query): void
    {
        $query->commonQuery()->running();
    }

    /**
     * Scope a query to only include upcoming campaigns that meet certain criteria.
     */
    public function scopeUpcomingCheck($query): void
    {
        $query->commonQuery()->upcoming();
    }

    /**
     * Get the approval's status.
     */
    protected function approvalStatusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status == ManageStatus::CAMPAIGN_PENDING) {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                } else if ($this->status == ManageStatus::CAMPAIGN_APPROVED) {
                    $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
                } else {
                    $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
                }

                return $html;
            },
        );
    }

    /**
     * Get the campaign's status.
     */
    protected function campaignStatusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status == ManageStatus::CAMPAIGN_PENDING || $this->status == ManageStatus::CAMPAIGN_REJECTED) {
                    $html = '<span class="badge badge--warning">' . trans('N/A') . '</span>';
                } else if ($this->isRunning()) {
                    $html = '<span class="badge badge--success">' . trans('Running') . '</span>';
                } else if ($this->isExpired()) {
                    $html = '<span class="badge badge--danger">' . trans('Expired') . '</span>';
                } else {
                    $html = '<span class="badge badge--info">' . trans('Upcoming') . '</span>';
                }

                return $html;
            },
        );
    }

    /**
     * Get the campaign's featured status.
     */
    protected function featuredStatusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->featured) {
                    $html = '<span class="badge badge--success">' . trans('Yes') . '</span>';
                } else {
                    $html = '<span class="badge badge--warning">' . trans('No') . '</span>';
                }

                return $html;
            },
        );
    }

    /**
     * Check if the campaign is expired. (custom method)
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Check if the campaign is running. (custom method)
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return Carbon::today()->betweenIncluded($this->start_date, $this->end_date);
    }

    /**
     * Get the raised amount with fallback to calculated amount from deposits.
     */
    protected function raisedAmount(): Attribute
    {
        return Attribute::make(
            get: function () {
                // If raised_amount is set and greater than 0, use it
                if ($this->attributes['raised_amount'] && $this->attributes['raised_amount'] > 0) {
                    return $this->attributes['raised_amount'];
                }
                
                // Otherwise, calculate from deposits
                return $this->deposits()
                    ->where('status', ManageStatus::PAYMENT_SUCCESS)
                    ->sum('amount');
            },
        );
    }

    /**
     * Get the donors count.
     */
    protected function donorsCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->deposits()
                    ->where('status', ManageStatus::PAYMENT_SUCCESS)
                    ->distinct('user_id')
                    ->count('user_id');
            },
        );
    }
}
