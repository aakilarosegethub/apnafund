<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'to_email',
        'to_name',
        'from_email',
        'from_name',
        'subject',
        'body',
        'template_name',
        'email_type',
        'status',
        'provider',
        'provider_response',
        'error_message',
        'user_id',
        'ip_address',
        'user_agent',
        'sent_at'
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at' => 'datetime'
    ];

    /**
     * Get the user that this email was sent to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the email type badge color
     */
    public function getTypeBadgeAttribute(): string
    {
        return match($this->email_type) {
            'welcome' => 'success',
            'verification' => 'warning',
            'notification' => 'info',
            'password_reset' => 'danger',
            'general' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'sent' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get truncated body for preview
     */
    public function getBodyPreviewAttribute(): string
    {
        return strlen($this->body) > 200 
            ? substr(strip_tags($this->body), 0, 200) . '...' 
            : strip_tags($this->body);
    }

    /**
     * Scope for filtering by email type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('email_type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for recent emails
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
