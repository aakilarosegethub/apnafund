
<header class="simple-header">
    <div class="container header-flex">

        <!-- Left (optional menu / kept for balance) -->
        <div class="header-left">
            {{-- Yahan aap future left dropdown / menu ka icon rakh sakte hain --}}
        </div>

        <!-- Center Logo (dynamic from settings) -->
        <div class="header-center">
            <a href="{{ route('home') }}" class="d-inline-block">
                <img
                    src="{{ getSiteLogo('light') }}"
                    alt="{{ bs('site_name') ?? 'Apna Crowdfunding' }}"
                >
            </a>
        </div>

        <!-- Right User (dynamic: image + links) -->
        <div class="header-right">
            @php
                $user = auth()->user();
                $userName = $user ? ($user->fullname ?? $user->name ?? null) : null;
                $initials = 'U';

                if ($userName) {
                    $parts = explode(' ', $userName);
                    $initials = '';
                    foreach ($parts as $part) {
                        if (! empty($part)) {
                            $initials .= mb_substr($part, 0, 1);
                        }
                    }
                    $initials = strtoupper($initials);
                }
            @endphp

            @if($user)
                <div class="d-flex align-items-center gap-2">
                <x-creator-notification-bell />
                <div class="user-wrapper">
                    <div class="user-avatar" id="userAvatar">
                        @if(!empty($user->image))
                            <img
                                src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}"
                                alt="{{ $userName }}"
                            >
                        @else
                            {{ $initials }}
                        @endif
                    </div>

                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ route('user.dashboard') }}">Dashboard</a>
                        <a href="{{ route('user.profile') }}">Profile</a>
                        <a href="{{ route('user.logout') }}">Logout</a>
                    </div>
                </div>
                </div>
            @else
                <div class="user-wrapper">
                    <a href="{{ route('user.login.form') }}" class="btn btn-sm btn-outline-success">
                        Login
                    </a>
                </div>
            @endif
        </div>

    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var avatar = document.getElementById('userAvatar');
    var dropdown = document.getElementById('userDropdown');

    if (avatar && dropdown) {
        avatar.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener('click', function () {
            dropdown.classList.remove('show');
        });
    }
});
</script>
