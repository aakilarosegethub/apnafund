<?php

namespace App\Listeners;

use App\Models\Admin;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Logout;

class LogAdminLogout
{
    public function handle(Logout $event): void
    {
        if (($event->guard ?? '') !== 'admin' || !$event->user instanceof Admin) {
            return;
        }
        app(ActivityLogger::class)->logAuth(
            ActivityLogger::ACTION_LOGOUT,
            $event->user->username,
            'Admin logged out'
        );
    }
}
