<?php

namespace App\Listeners;

use App\Models\Admin;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;

class LogAdminLogin
{
    public function handle(Login $event): void
    {
        if (($event->guard ?? '') !== 'admin' || ! $event->user instanceof Admin) {
            return;
        }
        app(ActivityLogger::class)->logAuth(
            ActivityLogger::ACTION_LOGIN,
            $event->user->username,
            'Admin logged in successfully'
        );
    }
}
