<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserNotificationController extends Controller
{
    public function unreadCount(): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json(['count' => 0]);
        }

        if (! Schema::hasTable('user_notifications')) {
            return response()->json(['count' => 0]);
        }

        $count = UserNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function index()
    {
        $pageTitle = __('Notifications');
        $notifications = UserNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(getPaginate());

        return view(activeTheme().'user.page.notifications', compact('pageTitle', 'notifications'));
    }

    public function open(UserNotification $notification)
    {
        abort_unless((int) $notification->user_id === (int) auth()->id(), 403);

        if ($notification->read_at === null) {
            $notification->read_at = now();
            $notification->save();
        }

        $target = trim((string) ($notification->click_url ?? ''));
        if ($target === '') {
            return redirect()->route('user.notifications.index');
        }

        return redirect()->to($target);
    }

    public function markAllRead(Request $request)
    {
        UserNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->withToasts([['success', __('Marked as read')]]);
    }
}
