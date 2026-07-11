<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Grants a non-owner {@see User} permission to edit a {@see Campaign}.
 */
class CampaignCollaborator extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
    ];

    /**
     * Get the campaign that owns the collaborator.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the user that is a collaborator.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
