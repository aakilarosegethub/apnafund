@extends($activeTheme . 'layouts.green-home')

@section('custom-css')
<style>
    body {
        background: #f7f7f7;
    }

    .ks-forgot-wrapper {
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 16px;
        margin-top: 80px;
    }

    .ks-forgot-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        padding: 32px 28px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .ks-top-link {
        text-align: center;
        font-size: 14px;
        margin-bottom: 16px;
    }

    .ks-top-link a {
        color: #16a34a;
        text-decoration: none;
        font-weight: 500;
    }

    .ks-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #222;
    }

    .ks-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 24px;
        line-height: 1.5;
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
        box-sizing: border-box;
    }

    .ks-form-group input:focus {
        outline: none;
        border-color: #16a34a;
        background: #ffffff;
    }

    .ks-submit {
        width: 100%;
        padding: 12px;
        background: #16a34a;
        color: #fff;
        border: none;
        font-size: 15px;
        font-weight: 600;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 12px;
        position: relative;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .ks-submit:hover:not(:disabled) {
        background: #15803d;
    }

    .ks-submit:disabled {
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

    .ks-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .ks-success {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .ks-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .ks-footer a {
        color: #16a34a;
        font-weight: 600;
        text-decoration: none;
    }

    .ks-footer a:hover {
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="ks-forgot-wrapper">
    <div class="ks-forgot-card">
        <div class="ks-top-link">
            Remember your password? <a href="{{ route('user.login.form') }}">Log in</a>
        </div>

        <div class="ks-title">Forgot Password?</div>
        <div class="ks-subtitle">Enter your email address or username and we'll send you a verification code to reset your password.</div>

        <!-- Error/Success Messages -->
        @if ($errors->any())
            <div class="ks-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="ks-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="ks-error">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('user/password/forgot') }}" method="POST" id="forgotForm">
            @csrf

            <div class="ks-form-group">
                <input type="text" name="value" id="value" placeholder="Email address or username" value="{{ old('value') }}" required>
            </div>

            <x-captcha />

            <button type="submit" class="ks-submit" id="forgotBtn">
                <span class="btn-loader"></span>
                <span class="btn-text">Send Verification Code</span>
            </button>
        </form>

        <div class="ks-footer">
            <a href="{{ route('user.login.form') }}">← Back to Login</a>
        </div>
    </div>
</div>
@endsection

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forgotForm = document.getElementById('forgotForm');
    const forgotBtn = document.getElementById('forgotBtn');

    if (forgotForm && forgotBtn) {
        forgotForm.addEventListener('submit', function(e) {
            // Show loader
            forgotBtn.classList.add('btn-loading');
            forgotBtn.disabled = true;
        });
    }
});
</script>
@endpush
