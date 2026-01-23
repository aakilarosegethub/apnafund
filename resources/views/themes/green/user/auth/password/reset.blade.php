@extends($activeTheme . 'layouts.green-home')

@section('custom-css')
<style>
    body {
        background: #f7f7f7;
    }

    .ks-reset-wrapper {
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 16px;
        margin-top: 80px;
    }

    .ks-reset-card {
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

    .ks-form-group {
        margin-bottom: 18px;
        position: relative;
    }

    .ks-form-group input {
        width: 100%;
        padding: 12px 14px;
        padding-right: 40px;
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

    .ks-password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        font-size: 16px;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ks-password-toggle:hover {
        color: #16a34a;
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
<div class="ks-reset-wrapper">
    <div class="ks-reset-card">
        <div class="ks-top-link">
            Remember your password? <a href="{{ route('user.login.form') }}">Log in</a>
        </div>

        <div class="ks-title">Reset Password</div>
        <div class="ks-subtitle">Enter your new password below</div>

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
            Resetting password for <strong>{{ showEmailAddress($email) }}</strong>
        </div>

        <form action="{{ route('user.password.reset') }}" method="POST" id="resetForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="code" value="{{ $verCode }}">

            <div class="ks-form-group">
                <input type="password" name="password" id="password" placeholder="New Password" required>
                <button type="button" class="ks-password-toggle" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <div class="ks-form-group">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required>
                <button type="button" class="ks-password-toggle" onclick="togglePassword('password_confirmation')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <button type="submit" class="ks-submit" id="resetBtn">
                <span class="btn-loader"></span>
                <span class="btn-text">Reset Password</span>
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
    const resetForm = document.getElementById('resetForm');
    const resetBtn = document.getElementById('resetBtn');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    // Password toggle functionality
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // Confirm password validation
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.style.borderColor = '#dc3545';
        } else {
            this.style.borderColor = '#16a34a';
        }
    });

    // Form submission
    if (resetForm && resetBtn) {
        resetForm.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }

            // Show loader
            resetBtn.classList.add('btn-loading');
            resetBtn.disabled = true;
        });
    }
});
</script>
@endpush
