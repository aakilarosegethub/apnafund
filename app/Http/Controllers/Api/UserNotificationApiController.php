<?php

namespace App\Http\Controllers\Api;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserNotificationApiController extends BaseApiController
{
    /**
     * Paginated in-app notifications for the authenticated user (user_notifications table).
     */
    public function list(Request $request): JsonResponse
    {
        $uid = $this->getUserId($request);
        if (empty($uid)) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Unauthorized! Please login first.',
            ], 401);
        }

        if (!Schema::hasTable('user_notifications')) {
            return response()->json([
                'ResponseCode' => '200',
                'Result' => 'true',
                'ResponseMsg' => 'No notifications.',
                'notificationdata' => [],
                'notifications' => [],
                'unread_count' => 0,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => (int) getPaginate(),
                'total' => 0,
            ]);
        }

        $data = $this->getRequestData($request);
        $perPage = isset($data['per_page']) ? min(100, max(1, (int) $data['per_page'])) : (int) getPaginate();
        $page = isset($data['page']) ? max(1, (int) $data['page']) : 1;

        $paginator = UserNotification::query()
            ->where('user_id', $uid)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        $unreadCount = UserNotification::query()
            ->where('user_id', $uid)
            ->whereNull('read_at')
            ->count();

        $items = $paginator->getCollection()->map(function (UserNotification $n) {
            return [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'click_url' => $n->click_url,
                'read_at' => $n->read_at?->toIso8601String(),
                'is_read' => $n->read_at !== null,
                'created_at' => $n->created_at?->toIso8601String(),
            ];
        })->values()->all();

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Notification list loaded successfully.',
            'notificationdata' => $items,
            'notifications' => $items,
            'unread_count' => $unreadCount,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ]);
    }
}
