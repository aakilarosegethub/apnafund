
@extends($activeTheme . 'layouts.green-home')

@section('custom-css')
<style>
    body {
        background: #f7f7f7;
    }

    .ks-login-wrapper {
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 16px;
        margin-top: 80px;
    }

    .ks-login-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        padding: 32px 28px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .ks-logo {
        text-align: center;
        margin-bottom: 20px;
    }

    .ks-logo h1 {
        font-size: 28px;
        font-weight: 800;
        color: #05ce78;
        margin: 0;
        letter-spacing: -1px;
    }

    .ks-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #222;
    }

    .ks-form-group {
        margin-bottom: 18px;
    }

    .ks-form-group input {
        width: 100%;
        padding: 12px 14px;
        font-size: 15px;
        border: 1px solid #dcdcdc;
        border-radius: 4px;
        background: #eef4ff;
    }

    .ks-form-group input:focus {
        outline: none;
        border-color: #05ce78;
        background: #ffffff;
    }

    .ks-forgot {
        display: inline-block;
        font-size: 14px;
        color: #2752ff;
        margin: 6px 0 16px;
        text-decoration: none;
    }

    .ks-forgot:hover {
        text-decoration: underline;
    }

    .ks-login-btn {
        width: 100%;
        padding: 12px;
        background: #05ce78;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        position: relative;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .ks-login-btn:hover:not(:disabled) {
        background: #04b56a;
    }

    .ks-login-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .btn-loader {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .btn-loading .btn-loader {
        display: inline-block;
    }

    .btn-loading .btn-text {
        opacity: 0.7;
    }

    .ks-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 14px 0;
        font-size: 14px;
    }

    .ks-divider {
        text-align: center;
        margin: 22px 0;
        position: relative;
    }

    .ks-divider::before {
        content: "";
        height: 1px;
        background: #e5e5e5;
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
    }

    .ks-divider span {
        background: #fff;
        padding: 0 10px;
        font-size: 14px;
        color: #666;
        position: relative;
    }

    .ks-social-btn {
        width: 100%;
        padding: 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .ks-apple {
        background: #000;
        color: #fff;
    }

    .ks-facebook {
        background: #3b5998;
        color: #fff;
    }

    .ks-google {
        background: #db4437;
        color: #fff;
    }

    .ks-social-info {
        font-size: 13px;
        color: #666;
        margin-top: 12px;
        line-height: 1.5;
    }

    .ks-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .ks-footer a {
        color: #2752ff;
        font-weight: 600;
        text-decoration: none;
    }

    .ks-footer a:hover {
        text-decoration: underline;
    }

    .alert-danger {
        background: #fde8e8;
        border: 1px solid #f5b5b5;
        padding: 10px 12px;
        font-size: 14px;
        color: #9b1c1c;
        margin-bottom: 16px;
        border-radius: 4px;
    }
    span{
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="ks-login-wrapper">
    <div class="ks-login-card">

        <div class="ks-logo">
            <h1>{{ bs('site_name') ?? 'ApnaFund' }}</h1>
        </div>

        <h2 class="ks-title">Log in</h2>

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('user.login') }}">
            @csrf

            <div class="ks-form-group">
                <input type="text" name="username" placeholder="Email address or username"
                       value="{{ old('username') }}" required>
            </div>

            <div class="ks-form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <a href="{{ route('user.password.request.form') }}" class="ks-forgot">
                Forgot your password?
            </a>

            <button type="submit" class="ks-login-btn" id="loginBtn">
                <span class="btn-loader"></span>
                <span class="btn-text">Log in</span>
            </button>

            <div class="ks-remember">
                <input type="checkbox" name="remember" id="remember" checked>
                <label for="remember">Remember me</label>
            </div>
        </form>

        @php
            $facebookEnabled = !empty(config('services.facebook.client_id')) && 
                               !empty(config('services.facebook.client_secret')) && 
                               config('services.facebook.client_id') !== 'disabled';
            $googleEnabled = !empty(config('services.google.client_id')) && 
                             !empty(config('services.google.client_secret')) && 
                             config('services.google.client_id') !== 'disabled';
        @endphp

        @if($facebookEnabled || $googleEnabled)
            <div class="ks-divider">
                <span>or</span>
            </div>

            <div style="display: flex; gap: 10px; flex-direction: column;">
                @if($facebookEnabled)
                    <a href="{{ route('user.social.facebook') }}" class="ks-social-btn ks-facebook">
                        <i class="fab fa-facebook-f"></i> Continue with Facebook
                    </a>
                @endif

                @if($googleEnabled)
                    <a href="{{ route('user.social.google') }}" class="ks-social-btn ks-google">
                        <i class="fab fa-google"></i> Continue with Google
                    </a>
                @endif
            </div>
        @endif

        <div class="ks-footer">
            New to {{ bs('site_name') ?? 'ApnaFund' }}?
            <a href="{{ route('user.register') }}">Sign up</a>
        </div>

    </div>
</div>
@endsection

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[action="{{ route("user.login") }}"]');
    const loginBtn = document.getElementById('loginBtn');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function(e) {
            // Show loader
            loginBtn.classList.add('btn-loading');
            loginBtn.disabled = true;
        });
    }
});
</script>
@endpush
