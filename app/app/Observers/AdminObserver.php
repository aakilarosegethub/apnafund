<?php

namespace App\Observers;

use App\Models\Admin;
use App\Services\ActivityLogger;

class AdminObserver extends AdminActivityObserver
{
    public function created(Admin $admin): void
    {
        if ($this->shouldLog()) {
            $this->logger->log(
                ActivityLogger::ACTION_CREATED,
                'admin_users',
                (string) $admin->getKey(),
                null,
                $admin->makeHidden(['password', 'remember_token'])->toArray(),
            );
        }
    }

    public function updated(Admin $admin): void
    {
        if ($this->shouldLog()) {
            $changes = $admin->getChanges();
            unset($changes['updated_at'], $changes['password'], $changes['remember_token']);
            if (! empty($changes)) {
                $this->logger->log(
                    ActivityLogger::ACTION_UPDATED,
                    'admin_users',
                    (string) $admin->getKey(),
                    array_intersect_key($admin->getOriginal(), $changes),
                    $changes,
                );
            }
        }
    }

    public function deleted(Admin $admin): void
    {
        if ($this->shouldLog()) {
            $data = $admin->makeHidden(['password', 'remember_token'])->toArray();
            $this->logger->log(
                ActivityLogger::ACTION_DELETED,
                'admin_users',
                (string) $admin->getKey(),
                $data,
                null,
            );
        }
    }
}
