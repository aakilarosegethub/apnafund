<?php

namespace App\Observers;

use App\Models\User;

class UserObserver extends AdminActivityObserver
{
    public function created(User $user): void
    {
        if ($this->shouldLog()) {
            $this->logger->logModelEvent(ActivityLogger::ACTION_CREATED, $user);
        }
    }

    public function updated(User $user): void
    {
        if ($this->shouldLog()) {
            $this->logger->log(
                ActivityLogger::ACTION_UPDATED,
                $this->getModuleName($user),
                (string) $user->getKey(),
                $user->getOriginal(),
                $user->getAttributes(),
            );
        }
    }

    public function deleted(User $user): void
    {
        if ($this->shouldLog()) {
            $this->logger->logModelEvent(ActivityLogger::ACTION_DELETED, $user, $user->getOriginal());
        }
    }
}
