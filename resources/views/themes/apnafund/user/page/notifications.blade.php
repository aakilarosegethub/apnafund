@php
    $activeTheme = activeTheme();
@endphp
@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <h2 class="mb-0">@lang('Notifications')</h2>
                <form action="{{ route('user.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">@lang('Mark all as read')</button>
                </form>
            </div>

            <div class="card custom--card">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($notifications as $n)
                            <li class="list-group-item {{ $n->read_at ? '' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div class="flex-grow-1">
                                        <a href="{{ route('user.notifications.open', $n) }}" class="text-decoration-none text-dark fw-semibold d-block">
                                            {{ $n->title }}
                                            @if(!$n->read_at)
                                                <span class="badge bg-success ms-1">@lang('New')</span>
                                            @endif
                                        </a>
                                        @if($n->body)
                                            <p class="mb-1 mt-1 text-muted small">{{ $n->body }}</p>
                                        @endif
                                        <small class="text-muted">{{ $n->created_at?->diffForHumans() }}</small>
                                    </div>
                                    <a href="{{ route('user.notifications.open', $n) }}" class="btn btn--base btn-sm shrink-0">@lang('Open')</a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-5 text-muted">@lang('No notifications yet')</li>
                        @endforelse
                    </ul>
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($notifications) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
