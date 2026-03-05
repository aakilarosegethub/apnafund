<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Failed;

class LogFailedAdminLogin
{
    public function handle(\Illuminate\Auth\Events\Failed $event): void
    {
        $guard = $event->guard ?? '';
        if ($guard !== 'admin') {
            return;
        }
        $identifier = $event->credentials['username'] ?? $event->credentials['email'] ?? 'unknown';
        app(ActivityLogger::class)->logAuth(
            ActivityLogger::ACTION_FAILED_LOGIN,
            $identifier,
            'Failed admin login attempt'
        );
    }
}
