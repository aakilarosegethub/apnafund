<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Constants\ManageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Comment extends Model
{
    use Searchable;

    /**
     * Get the campaign that owns the comment.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user's type.
     */
    protected function userTypeBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->user_id) {
                    $html = '<span class="badge badge--success">' . trans('Registered') . '</span>';
                } else {
                    $html = '<span class="badge badge--primary">' . trans('Guest') . '</span>';
                }

                return $html;
            },
        );
    }

    /**
     * Scope a query to only include approved comments.
     */
    public function scopeApprove($query)
    {
        $query->where('status', ManageStatus::CAMPAIGN_COMMENT_APPROVED);
    }

    public function scopePending($query)
    {
        $query->where('status', ManageStatus::CAMPAIGN_COMMENT_PENDING);
    }
}
