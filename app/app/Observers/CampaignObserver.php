<?php

namespace App\Observers;

use App\Models\Campaign;
use App\Services\ActivityLogger;

class CampaignObserver extends AdminActivityObserver
{
    public function created(Campaign $campaign): void
    {
        if ($this->shouldLog()) {
            $this->logger->logModelEvent(ActivityLogger::ACTION_CREATED, $campaign);
        }
    }

    public function updated(Campaign $campaign): void
    {
        if ($this->shouldLog()) {
            $changes = $campaign->getChanges();
            unset($changes['updated_at']);
            if (! empty($changes)) {
                $this->logger->log(
                    ActivityLogger::ACTION_UPDATED,
                    $this->getModuleName($campaign),
                    (string) $campaign->getKey(),
                    array_intersect_key($campaign->getOriginal(), $changes),
                    $changes,
                );
            }
        }
    }

    public function deleted(Campaign $campaign): void
    {
        if ($this->shouldLog()) {
            $this->logger->logModelEvent(ActivityLogger::ACTION_DELETED, $campaign, $campaign->toArray());
        }
    }
}
