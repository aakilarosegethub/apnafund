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
        color: #dc2626;
        text-align: center;
    }

    .ks-ban-icon {
        text-align: center;
        margin-bottom: 20px;
        font-size: 60px;
        color: #dc2626;
    }

    .ks-ban-message {
        background: #fde8e8;
        border: 1px solid #f5b5b5;
        padding: 16px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .ks-ban-label {
        font-size: 14px;
        font-weight: 600;
        color: #9b1c1c;
        margin-bottom: 8px;
    }

    .ks-ban-reason {
        font-size: 15px;
        color: #222;
        line-height: 1.6;
    }

    .ks-contact-section {
        text-align: center;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e5e5;
    }

    .ks-contact-text {
        font-size: 14px;
        color: #666;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .ks-home-btn {
        display: inline-block;
        padding: 10px 24px;
        background: #05ce78;
        color: #fff;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.3s;
    }

    .ks-home-btn:hover {
        background: #04b56a;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="ks-verify-wrapper">
    <div class="ks-verify-card">

        <div class="ks-logo">
            <h1>{{ bs('site_name') ?? 'ApnaFund' }}</h1>
        </div>

        <div class="ks-ban-icon">
            <i class="fas fa-ban"></i>
        </div>

        <h2 class="ks-title">Account Suspended</h2>

        <div class="ks-ban-message">
            <div class="ks-ban-label">Reason for suspension:</div>
            <div class="ks-ban-reason">{{ $user->ban_reason }}</div>
        </div>

        <div class="ks-contact-section">
            <p class="ks-contact-text">
                If you believe this is a mistake, please contact our support team for assistance.
            </p>
            <a href="{{ route('home') }}" class="ks-home-btn">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>

    </div>
</div>
@endsection
