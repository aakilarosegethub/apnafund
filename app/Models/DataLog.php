<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint',
        'method',
        'request_data',
        'headers',
        'ip_address',
        'user_agent',
        'raw_input',
        'transaction_id',
        'status',
        'response'
    ];

    protected $casts = [
        'request_data' => 'array',
        'headers' => 'array',
    ];

    /**
     * Log incoming request data
     */
    public static function logRequest($endpoint, $method, $request, $response = null, $transactionId = null)
    {
        $data = [
            'endpoint' => $endpoint,
            'method' => $method,
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'raw_input' => $request->getContent(),
            'transaction_id' => $transactionId,
            'status' => 'received',
            'response' => $response
        ];

        return self::create($data);
    }

    /**
     * Update log status
     */
    public function updateStatus($status, $response = null)
    {
        $this->update([
            'status' => $status,
            'response' => $response
        ]);
    }
}
