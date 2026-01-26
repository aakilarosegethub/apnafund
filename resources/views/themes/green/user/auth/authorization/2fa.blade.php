@extends($activeTheme . 'layouts.green-home')

@section('custom-css')
<style>
    body {
        background: #f7f7f7;
    }

    .ks-verify-wrapper {
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 16px;
        margin-top: 80px;
    }

    .ks-verify-card {
        width: 100%;
        max-width: 460px;
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
        margin-bottom: 12px;
        color: #222;
        text-align: center;
    }

    .ks-info-text {
        font-size: 14px;
        color: #666;
        margin-bottom: 24px;
        line-height: 1.6;
        text-align: center;
    }

    .ks-form-group {
        margin-bottom: 18px;
    }

    .verification-code {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 24px;
    }

    .verification-code input {
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        border: 1px solid #dcdcdc;
        border-radius: 4px;
        background: #eef4ff;
    }

    .verification-code input:focus {
        outline: none;
        border-color: #05ce78;
        background: #ffffff;
    }

    .ks-verify-btn {
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

    .ks-verify-btn:hover:not(:disabled) {
        background: #04b56a;
    }

    .ks-verify-btn:disabled {
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

    .alert-danger {
        background: #fde8e8;
        border: 1px solid #f5b5b5;
        padding: 10px 12px;
        font-size: 14px;
        color: #9b1c1c;
        margin-bottom: 16px;
        border-radius: 4px;
    }

    .alert-success {
        background: #d1fae5;
        border: 1px solid #6ee7b7;
        padding: 10px 12px;
        font-size: 14px;
        color: #047857;
        margin-bottom: 16px;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="ks-verify-wrapper">
    <div class="ks-verify-card">

        <div class="ks-logo">
            <h1>{{ bs('site_name') ?? 'ApnaFund' }}</h1>
        </div>

        <h2 class="ks-title">2FA Verification</h2>

        <div class="ks-info-text">
            Enter the verification code from your authenticator app
        </div>

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('user.go2fa.verify') }}" method="POST" class="verification-code-form" id="verifyForm">
            @csrf
            
            <div class="verification-code">
                @include('partials.verificationCode')
            </div>

            <button type="submit" class="ks-verify-btn" id="verifyBtn">
                <span class="btn-loader"></span>
                <span class="btn-text">Verify</span>
            </button>
        </form>

    </div>
</div>
@endsection

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const verifyForm = document.getElementById('verifyForm');
    const verifyBtn = document.getElementById('verifyBtn');

    if (verifyForm && verifyBtn) {
        verifyForm.addEventListener('submit', function(e) {
            // Show loader
            verifyBtn.classList.add('btn-loading');
            verifyBtn.disabled = true;
        });
    }
});
</script>
@endpush
