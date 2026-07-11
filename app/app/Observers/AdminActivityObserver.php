<?php

namespace App\Observers;

use App\Services\ActivityLogger;

abstract class AdminActivityObserver
{
    protected ActivityLogger $logger;

    public function __construct(ActivityLogger $logger)
    {
        $this->logger = $logger;
    }

    protected function shouldLog(): bool
    {
        return auth()->guard('admin')->check();
    }

    protected function getModuleName($model): string
    {
        return strtolower(\Illuminate\Support\Str::singular(\Illuminate\Support\Str::snake(class_basename($model))));
    }
}
