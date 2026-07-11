<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Persists admin audit events to {@see AdminActivityLog} (CRUD, auth, unauthorized access).
 */
class ActivityLogger
{
    public const ACTION_CREATED = 'created';

    public const ACTION_UPDATED = 'updated';

    public const ACTION_DELETED = 'deleted';

    public const ACTION_LOGIN = 'login';

    public const ACTION_LOGOUT = 'logout';

    public const ACTION_FAILED_LOGIN = 'failed_login';

    public const ACTION_UNAUTHORIZED = 'unauthorized';

    /**
     * @param  string  $actionType  One of {@see ActivityLogger} `ACTION_*` constants
     * @param  string|null  $moduleName  Logical module (e.g. `campaign`)
     * @param  string|null  $recordId  Primary key or identifier
     * @param  array<string, mixed>|null  $oldData  Snapshot before change
     * @param  array<string, mixed>|null  $newData  Snapshot after change
     */
    public function log(
        string $actionType,
        ?string $moduleName = null,
        ?string $recordId = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?string $description = null
    ): AdminActivityLog {
        $admin = $this->getAdmin();
        $request = request();

        return AdminActivityLog::create([
            'user_id' => $admin?->id,
            'role_id' => $admin?->role_id,
            'action_type' => $actionType,
            'module_name' => $moduleName,
            'record_id' => $recordId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'description' => $description,
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function logModelEvent(string $action, $model, ?array $oldData = null): AdminActivityLog
    {
        $newData = $action === self::ACTION_DELETED ? null : $model->toArray();

        return $this->log(
            $action,
            $this->getModuleName($model),
            (string) $model->getKey(),
            $oldData,
            $newData
        );
    }

    public function logAuth(string $action, ?string $identifier = null, ?string $description = null): AdminActivityLog
    {
        $request = request();
        $admin = $this->getAdmin();

        return AdminActivityLog::create([
            'user_id' => $admin?->id,
            'role_id' => $admin?->role_id,
            'action_type' => $action,
            'module_name' => 'auth',
            'record_id' => $identifier,
            'old_data' => null,
            'new_data' => $identifier ? ['identifier' => $identifier] : null,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'description' => $description,
        ]);
    }

    public function logUnauthorized(?string $route = null, ?string $description = null): AdminActivityLog
    {
        $admin = auth()->guard('admin')->user();
        $request = request();

        return AdminActivityLog::create([
            'user_id' => $admin?->id,
            'role_id' => $admin?->role_id,
            'action_type' => self::ACTION_UNAUTHORIZED,
            'module_name' => 'auth',
            'record_id' => $route,
            'old_data' => null,
            'new_data' => $route ? ['attempted_route' => $route] : null,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'description' => $description ?? 'Unauthorized access attempt',
        ]);
    }

    protected function getAdmin(): ?Admin
    {
        $user = Auth::guard('admin')->user();

        return $user instanceof Admin ? $user : null;
    }

    protected function getModuleName($model): string
    {
        $name = class_basename($model);

        return strtolower(Str::singular(Str::snake($name)));
    }
}
