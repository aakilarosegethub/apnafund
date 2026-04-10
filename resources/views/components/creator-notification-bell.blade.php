@props(['variant' => 'light', 'instance' => 'main'])
@auth
@php
    $hasNotifTable = \Illuminate\Support\Facades\Schema::hasTable('user_notifications');
    $cnUid = 'cn_' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($instance ?? 'main'));
    $creatorNotifPreview = collect();
    $creatorNotifUnread = 0;
    if ($hasNotifTable) {
        $creatorNotifPreview = \App\Models\UserNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();
        $creatorNotifUnread = \App\Models\UserNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }
    $unreadCountUrl = $hasNotifTable ? route('user.notifications.unread-count') : '';
@endphp
@if($hasNotifTable)
<style>
    .creator-notif-wrap { position: relative; display: inline-flex; align-items: center; margin-right: 12px; }
    .creator-notif-btn {
        position: relative;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 2px solid rgba(0,0,0,0.08);
        background: #fff;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .creator-notif-btn:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.12); transform: scale(1.03); }
    .creator-notif-btn--on-dark { border-color: rgba(255,255,255,0.45); background: rgba(255,255,255,0.12); color: #fff; }
    .creator-notif-btn--on-dark:hover { background: rgba(255,255,255,0.22); }
    .creator-notif-btn--on-dark .creator-notif-badge { border: 2px solid #1a1a1a; }
    .creator-notif-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        font-size: 11px;
        font-weight: 800;
        line-height: 20px;
        text-align: center;
        background: #dc3545;
        color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.45);
        z-index: 5;
        pointer-events: none;
        border: 2px solid #fff;
    }
    .creator-notif-btn--on-dark .creator-notif-badge { border-color: rgba(5, 206, 120, 0.95); }
    .creator-notif-badge.is-hidden { display: none !important; }
    .creator-notif-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: min(360px, 92vw);
        max-height: 420px;
        overflow: hidden;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        z-index: 1002;
        border: 1px solid #eee;
    }
    .creator-notif-dropdown.show { display: block; }
    .creator-notif-head {
        padding: 12px 16px;
        font-weight: 600;
        font-size: 15px;
        border-bottom: 1px solid #eee;
        color: #222;
    }
    .creator-notif-list { max-height: 280px; overflow-y: auto; }
    .creator-notif-item {
        display: block;
        padding: 12px 16px;
        border-bottom: 1px solid #f3f3f3;
        text-decoration: none;
        color: #333;
        transition: background 0.15s;
    }
    .creator-notif-item:hover { background: #f8f9fa; }
    .creator-notif-item--unread { background: #f0fdf4; }
    .creator-notif-item-title { font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #111; }
    .creator-notif-item-body { font-size: 12px; color: #666; line-height: 1.4; }
    .creator-notif-item-time { font-size: 11px; color: #999; margin-top: 6px; }
    .creator-notif-empty { padding: 24px 16px; text-align: center; color: #888; font-size: 14px; }
    .creator-notif-footer {
        padding: 10px 12px;
        border-top: 1px solid #eee;
        text-align: center;
    }
    .creator-notif-footer .btn { font-size: 13px; font-weight: 600; }
</style>
<div class="creator-notif-wrap" id="{{ $cnUid }}_root" data-unread-url="{{ $unreadCountUrl }}">
    <button type="button" class="creator-notif-btn {{ ($variant ?? 'light') === 'on-dark' ? 'creator-notif-btn--on-dark' : '' }}" id="{{ $cnUid }}_bell" aria-expanded="false" aria-label="@lang('Notifications')">
        <i class="fas fa-bell"></i>
        <span class="creator-notif-badge {{ $creatorNotifUnread < 1 ? 'is-hidden' : '' }}" id="{{ $cnUid }}_badge" aria-hidden="{{ $creatorNotifUnread < 1 ? 'true' : 'false' }}">{{ $creatorNotifUnread > 99 ? '99+' : (int) $creatorNotifUnread }}</span>
    </button>
    <div class="creator-notif-dropdown" id="{{ $cnUid }}_dropdown">
        <div class="creator-notif-head">@lang('Notifications')</div>
        <div class="creator-notif-list">
            @forelse($creatorNotifPreview as $n)
                <a href="{{ route('user.notifications.open', $n) }}" class="creator-notif-item {{ $n->read_at ? '' : 'creator-notif-item--unread' }}">
                    <div class="creator-notif-item-title">{{ $n->title }}</div>
                    @if($n->body)
                        <div class="creator-notif-item-body">{{ \Illuminate\Support\Str::limit($n->body, 120) }}</div>
                    @endif
                    <div class="creator-notif-item-time">{{ $n->created_at?->diffForHumans() }}</div>
                </a>
            @empty
                <div class="creator-notif-empty">@lang('No notifications yet')</div>
            @endforelse
        </div>
        <div class="creator-notif-footer">
            <a href="{{ route('user.notifications.index') }}" class="btn btn-success btn-sm w-100">@lang('See all')</a>
        </div>
    </div>
</div>
<script>
(function () {
    var root = document.getElementById(@json($cnUid . '_root'));
    var bell = document.getElementById(@json($cnUid . '_bell'));
    var panel = document.getElementById(@json($cnUid . '_dropdown'));
    var badge = document.getElementById(@json($cnUid . '_badge'));
    if (!root || !bell || !panel) return;

    function setUnreadCount(count) {
        var n = parseInt(count, 10) || 0;
        if (!badge) return;
        if (n < 1) {
            badge.classList.add('is-hidden');
            badge.setAttribute('aria-hidden', 'true');
            return;
        }
        badge.classList.remove('is-hidden');
        badge.setAttribute('aria-hidden', 'false');
        badge.textContent = n > 99 ? '99+' : String(n);
    }

    function fetchUnread() {
        var url = root.getAttribute('data-unread-url');
        if (!url) return;
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data && typeof data.count !== 'undefined') setUnreadCount(data.count);
        }).catch(function () {});
    }

    bell.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = panel.classList.toggle('show');
        bell.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) fetchUnread();
    });
    document.addEventListener('click', function () {
        panel.classList.remove('show');
        bell.setAttribute('aria-expanded', 'false');
    });
    panel.addEventListener('click', function (e) { e.stopPropagation(); });

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') fetchUnread();
    });
    setInterval(fetchUnread, 90000);
    fetchUnread();
})();
</script>
@endif
@endauth
