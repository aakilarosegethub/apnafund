<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WebhookLog extends Model
{
    protected $fillable = [
        'webhook_type',
        'url',
        'method',
        'headers',
        'payload',
        'response_status',
        'response_body',
        'response_headers',
        'execution_time',
        'status',
        'error_message',
        'retry_count',
        'user_id',
        'campaign_id'
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'response_headers' => 'array',
        'execution_time' => 'decimal:3',
        'retry_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the webhook log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaign associated with the webhook log.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Scope a query to only include successful webhooks.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope a query to only include failed webhooks.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include pending webhooks.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include webhooks by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('webhook_type', $type);
    }

    /**
     * Get the status badge.
     */
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    'success' => '<span class="badge badge--success">Success</span>',
                    'failed' => '<span class="badge badge--danger">Failed</span>',
                    'pending' => '<span class="badge badge--warning">Pending</span>',
                    'retrying' => '<span class="badge badge--info">Retrying</span>',
                ];

                return $badges[$this->status] ?? '<span class="badge badge--secondary">Unknown</span>';
            },
        );
    }

    /**
     * Get the HTTP status badge.
     */
    protected function httpStatusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->response_status) {
                    return '<span class="badge badge--secondary">N/A</span>';
                }

                $status = $this->response_status;
                if ($status >= 200 && $status < 300) {
                    return '<span class="badge badge--success">' . $status . '</span>';
                } elseif ($status >= 400 && $status < 500) {
                    return '<span class="badge badge--warning">' . $status . '</span>';
                } elseif ($status >= 500) {
                    return '<span class="badge badge--danger">' . $status . '</span>';
                } else {
                    return '<span class="badge badge--info">' . $status . '</span>';
                }
            },
        );
    }

    /**
     * Get formatted execution time.
     */
    protected function formattedExecutionTime(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->execution_time) {
                    return 'N/A';
                }

                if ($this->execution_time < 1) {
                    return number_format($this->execution_time * 1000, 0) . 'ms';
                }

                return number_format($this->execution_time, 2) . 's';
            },
        );
    }

    /**
     * Get truncated payload for display.
     */
    protected function truncatedPayload(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->payload) {
                    return 'N/A';
                }

                $payload = is_array($this->payload) ? json_encode($this->payload, JSON_PRETTY_PRINT) : $this->payload;
                
                if (strlen($payload) > 200) {
                    return substr($payload, 0, 200) . '...';
                }

                return $payload;
            },
        );
    }

    /**
     * Get truncated response for display.
     */
    protected function truncatedResponse(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->response_body) {
                    return 'N/A';
                }

                $response = is_array($this->response_body) ? json_encode($this->response_body, JSON_PRETTY_PRINT) : $this->response_body;
                
                if (strlen($response) > 200) {
                    return substr($response, 0, 200) . '...';
                }

                return $response;
            },
        );
    }
}
