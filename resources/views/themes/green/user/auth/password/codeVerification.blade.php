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
        max-width: 420px;
        background: #ffffff;
        padding: 32px 28px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
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
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .ks-email-info {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 24px;
        font-size: 14px;
        text-align: center;
    }

    .ks-email-info strong {
        color: #047857;
    }

    .ks-otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 24px;
    }

    .ks-otp-input {
        width: 45px;
        height: 45px;
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        border: 2px solid #dcdcdc;
        border-radius: 4px;
        background: #eef4ff;
    }

    .ks-otp-input:focus {
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
        color: #666;
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
<div class="ks-verify-wrapper">
    <div class="ks-verify-card">
        <div class="ks-title">Verify Your Code</div>
        <div class="ks-subtitle">Enter the 6-digit verification code sent to your email</div>

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

        <div class="ks-email-info">
            A verification code has been sent to <strong>{{ showEmailAddress($email) }}</strong>
        </div>

        <form action="{{ route('user.password.code.verification.form') }}" method="POST" id="verifyForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="ks-otp-inputs">
                <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" class="ks-otp-input" autocomplete="off" required>
                <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" class="ks-otp-input" autocomplete="off" required>
                <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" class="ks-otp-input" autocomplete="off" required>
                <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" class="ks-otp-input" autocomplete="off" required>
                <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" class="ks-otp-input" autocomplete="off" required>
                <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" class="ks-otp-input" autocomplete="off" required>
            </div>

            <button type="submit" class="ks-submit" id="verifyBtn">
                <span class="btn-loader"></span>
                <span class="btn-text">Verify Code</span>
            </button>
        </form>

        <div class="ks-footer">
            <p style="margin: 0 0 8px 0;">Didn't receive the code? Check your spam folder.</p>
            <a href="{{ route('user.password.request.form') }}">Try Again</a>
        </div>
    </div>
</div>
@endsection

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const verifyForm = document.getElementById('verifyForm');
    const verifyBtn = document.getElementById('verifyBtn');
    const otpInputs = document.querySelectorAll('.ks-otp-input');

    // OTP Input handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value;
            
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            
            if (pastedData.length === 6) {
                otpInputs.forEach((input, i) => {
                    input.value = pastedData[i] || '';
                });
                otpInputs[5].focus();
            }
        });
    });

    // Form submission
    if (verifyForm && verifyBtn) {
        verifyForm.addEventListener('submit', function(e) {
            // Check if all inputs are filled
            const allFilled = Array.from(otpInputs).every(input => input.value.length === 1);
            
            if (!allFilled) {
                e.preventDefault();
                alert('Please enter the complete 6-digit code');
                return;
            }

            // Show loader
            verifyBtn.classList.add('btn-loading');
            verifyBtn.disabled = true;
        });
    }
});
</script>
@endpush
