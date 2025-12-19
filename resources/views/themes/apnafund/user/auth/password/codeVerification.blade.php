@php
    $activeTheme = 'themes.primary.';
    $activeThemeTrue = 'themes.primary.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('content')
<style>
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #e8f5e8 0%, #05ce78 50%, #04a85f 100%);
        position: relative;
        overflow-x: hidden;
    }

    /* Background Elements */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(circle at 20% 30%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    /* Main Container */
    .verification-page-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
    }

    /* Left Side - Illustration */
    .verification-illustration {
        flex: 1;
        max-width: 600px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
    }

    .illustration-container {
        position: relative;
        width: 100%;
        height: 500px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .illustration-content {
        text-align: center;
        color: white;
        position: relative;
        z-index: 2;
    }

    .illustration-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .illustration-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    /* Decorative Elements */
    .decoration {
        position: absolute;
        opacity: 0.1;
    }

    .decoration-1 {
        top: 10%;
        left: 10%;
        font-size: 3rem;
        color: white;
    }

    .decoration-2 {
        top: 20%;
        right: 15%;
        font-size: 2rem;
        color: white;
    }

    .decoration-3 {
        bottom: 20%;
        left: 15%;
        font-size: 2.5rem;
        color: white;
    }

    .decoration-4 {
        bottom: 10%;
        right: 10%;
        font-size: 2rem;
        color: white;
    }

    /* Right Side - Verification Form */
    .verification-form-section {
        flex: 1;
        max-width: 450px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-left: 40px;
        position: relative;
        z-index: 10;
    }

    .verification-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .verification-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .verification-subtitle {
        color: #666;
        font-size: 1rem;
        margin-bottom: 20px;
    }

    .email-info {
        background: rgba(5, 206, 120, 0.1);
        border: 1px solid rgba(5, 206, 120, 0.2);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 25px;
        text-align: center;
    }

    .email-info p {
        margin: 0;
        color: #05ce78;
        font-weight: 600;
    }

    .email-info b {
        color: #04a85f;
    }

    /* Verification Code Input Styles */
    .verification-code-container {
        margin-bottom: 25px;
    }

    .verification-code-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 20px;
    }

    .code-input {
        width: 50px;
        height: 60px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        background: #fff;
        transition: all 0.3s ease;
    }

    .code-input:focus {
        outline: none;
        border-color: #05ce78;
        box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        transform: translateY(-2px);
    }

    .code-input.filled {
        border-color: #05ce78;
        background: rgba(5, 206, 120, 0.05);
    }

    /* Button Styles */
    .btn-theme {
        background: linear-gradient(135deg, #05ce78 0%, #04a85f 100%);
        color: #fff;
        border: none;
        padding: 15px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(5, 206, 120, 0.3);
        width: 100%;
        margin-top: 10px;
    }

    .btn-theme:hover {
        background: linear-gradient(135deg, #04a85f 0%, #038c4f 100%);
        color: #fff;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(5, 206, 120, 0.4);
    }

    .btn-theme:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Links */
    .verification-footer {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .verification-footer a {
        color: #05ce78;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .verification-footer a:hover {
        color: #04a85f;
        text-decoration: underline;
    }

    .try-again-text {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }

    .try-again-text b {
        color: #333;
        font-weight: 600;
    }

    /* Alert Styles */
    .alert {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border-left: 4px solid #dc3545;
    }

    .alert-success {
        background: rgba(5, 206, 120, 0.1);
        color: #05ce78;
        border-left: 4px solid #05ce78;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid #fff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 10px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .verification-page-container {
            flex-direction: column;
            padding: 20px;
        }

        .verification-illustration {
            max-width: 100%;
            margin-bottom: 30px;
        }

        .verification-form-section {
            margin-left: 0;
            max-width: 100%;
        }

        .illustration-container {
            height: 300px;
        }

        .illustration-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .verification-form-section {
            padding: 30px 20px;
        }

        .verification-title {
            font-size: 1.5rem;
        }

        .illustration-container {
            height: 250px;
        }

        .illustration-title {
            font-size: 1.5rem;
        }

        .verification-code-inputs {
            gap: 8px;
        }

        .code-input {
            width: 45px;
            height: 55px;
            font-size: 1.3rem;
        }
    }

    /* Hide original account section */
    .account {
        display: none;
    }
</style>

<div class="verification-page-container">
    <!-- Left Side - Illustration -->
    <div class="verification-illustration">
        <div class="illustration-container">
            <div class="decoration decoration-1">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="decoration decoration-2">
                <i class="fas fa-lock"></i>
            </div>
            <div class="decoration decoration-3">
                <i class="fas fa-key"></i>
            </div>
            <div class="decoration decoration-4">
                <i class="fas fa-user-shield"></i>
            </div>
            
            <div class="illustration-content">
                <h1 class="illustration-title">ApnaCrowdfunding</h1>
                <p class="illustration-subtitle">Secure Password Verification</p>
                <div style="font-size: 4rem; margin-top: 20px;">
                    <i class="fas fa-mobile-alt" style="color: rgba(255,255,255,0.8);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Verification Form -->
    <div class="verification-form-section">
        <div class="verification-header">
            <h2 class="verification-title">{{ __(@$codeVerifyContent->data_info->form_heading) ?: 'Verify Your Code' }}</h2>
            <p class="verification-subtitle">Enter the 6-digit code sent to your email</p>
        </div>

        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0" style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Email Info -->
        <div class="email-info">
            <p>@lang('A six-digit verification code has been sent to') <b>{{ showEmailAddress($email) }}</b></p>
        </div>

        <!-- Verification Form -->
        <form action="{{ route('user.password.code.verification.form') }}" method="POST" class="verification-code-form" id="verificationForm">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div class="verification-code-container">
                <div class="verification-code-inputs">
                    <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" placeholder="*" class="code-input" autocomplete="off" required>
                    <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" placeholder="*" class="code-input" autocomplete="off" required>
                    <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" placeholder="*" class="code-input" autocomplete="off" required>
                    <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" placeholder="*" class="code-input" autocomplete="off" required>
                    <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" placeholder="*" class="code-input" autocomplete="off" required>
                    <input type="tel" name="code[]" maxlength="1" pattern="[0-9]" placeholder="*" class="code-input" autocomplete="off" required>
                </div>
            </div>

            <button type="submit" class="btn-theme" id="verifyBtn">
                <span class="loading-spinner" id="loadingSpinner"></span>
                <span id="verifyBtnText">{{ __(@$codeVerifyContent->data_info->submit_button_text) ?: 'Verify Code' }}</span>
            </button>
        </form>

        <div class="verification-footer">
            <p class="try-again-text">@lang('Please check including your') <b>@lang('spam')</b> @lang('folder. If not found, then you can')</p>
            <a href="{{ route('user.password.request.form') }}">@lang('Try Again')</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const verificationForm = document.getElementById('verificationForm');
    const verifyBtn = document.getElementById('verifyBtn');
    const verifyBtnText = document.getElementById('verifyBtnText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const codeInputs = document.querySelectorAll('.code-input');

    // Handle code input functionality
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = this.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                this.value = '';
                return;
            }

            // Add filled class for styling
            if (value.length > 0) {
                this.classList.add('filled');
            } else {
                this.classList.remove('filled');
            }

            // Auto-focus next input
            if (value.length === 1 && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            // Handle backspace
            if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                codeInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            
            if (pastedData.length === 6) {
                codeInputs.forEach((input, i) => {
                    input.value = pastedData[i] || '';
                    if (pastedData[i]) {
                        input.classList.add('filled');
                    } else {
                        input.classList.remove('filled');
                    }
                });
            }
        });
    });

    // Handle form submission
    verificationForm.addEventListener('submit', function(e) {
        // Check if all inputs are filled
        const allFilled = Array.from(codeInputs).every(input => input.value.length === 1);
        
        if (!allFilled) {
            e.preventDefault();
            alert('Please enter the complete 6-digit code');
            return;
        }

        // Show loading state
        verifyBtn.disabled = true;
        verifyBtnText.style.display = 'none';
        loadingSpinner.style.display = 'inline-block';
    });

    // Add interactive effects
    codeInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            verificationForm.dispatchEvent(new Event('submit'));
        }
    });

    // Add floating animation to decorations
    const decorations = document.querySelectorAll('.decoration');
    decorations.forEach((decoration, index) => {
        decoration.style.animation = `float ${3 + index * 0.5}s ease-in-out infinite`;
    });
});

// Add floating animation
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
`;
document.head.appendChild(style);
</script>
@endsection
