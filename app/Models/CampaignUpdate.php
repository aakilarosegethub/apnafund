<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Backer-facing update post (`slug`, `is_published`) with optional threaded comments.
 */
class CampaignUpdate extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
        'title',
        'content',
        'slug',
        'image',
        'is_published'
    ];

    /**
     * Get the campaign that owns the update.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the user that created the update.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the update.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'update_id')->where('status', \App\Constants\ManageStatus::CAMPAIGN_COMMENT_APPROVED)->orderByDesc('id');
    }

    /**
     * Get all comments for the update (including pending).
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'update_id')->orderByDesc('id');
    }
}
