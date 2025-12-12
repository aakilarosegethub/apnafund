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
    .reset-page-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
    }

    /* Left Side - Illustration */
    .reset-illustration {
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

    /* Right Side - Reset Form */
    .reset-form-section {
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

    .reset-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .reset-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .reset-subtitle {
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

    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-control:focus {
        outline: none;
        border-color: #05ce78;
        box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        transform: translateY(-2px);
    }

    .password-input-container {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: #05ce78;
    }

    /* Password Requirements */
    .password-requirements {
        margin-top: 15px;
        padding: 15px;
        background: rgba(5, 206, 120, 0.05);
        border-radius: 10px;
        border: 1px solid rgba(5, 206, 120, 0.1);
    }

    .password-requirement {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .requirement-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 12px;
        font-weight: bold;
    }

    .requirement-icon.valid {
        background: #05ce78;
        color: white;
    }

    .requirement-icon.invalid {
        background: #dc3545;
        color: white;
    }

    .requirement-text {
        color: #666;
    }

    .password-strength {
        margin-top: 10px;
    }

    .password-strength-bar {
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
    }

    .password-strength-bar::after {
        content: '';
        height: 100%;
        background: linear-gradient(90deg, #dc3545 0%, #ffc107 50%, #05ce78 100%);
        width: 0%;
        transition: width 0.3s ease;
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
    .reset-footer {
        text-align: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .reset-footer a {
        color: #05ce78;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .reset-footer a:hover {
        color: #04a85f;
        text-decoration: underline;
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
        .reset-page-container {
            flex-direction: column;
            padding: 20px;
        }

        .reset-illustration {
            max-width: 100%;
            margin-bottom: 30px;
        }

        .reset-form-section {
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
        .reset-form-section {
            padding: 30px 20px;
        }

        .reset-title {
            font-size: 1.5rem;
        }

        .illustration-container {
            height: 250px;
        }

        .illustration-title {
            font-size: 1.5rem;
        }
    }

    /* Hide original account section */
    .account {
        display: none;
    }
</style>

<div class="reset-page-container">
    <!-- Left Side - Illustration -->
    <div class="reset-illustration">
        <div class="illustration-container">
            <div class="decoration decoration-1">
                <i class="fas fa-key"></i>
            </div>
            <div class="decoration decoration-2">
                <i class="fas fa-lock"></i>
            </div>
            <div class="decoration decoration-3">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="decoration decoration-4">
                <i class="fas fa-user-shield"></i>
                    </div>
            
            <div class="illustration-content">
                <h1 class="illustration-title">ApnaCrowdfunding</h1>
                <p class="illustration-subtitle">Reset Your Password</p>
                <div style="font-size: 4rem; margin-top: 20px;">
                    <i class="fas fa-key" style="color: rgba(255,255,255,0.8);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Reset Form -->
    <div class="reset-form-section">
        <div class="reset-header">
            <h2 class="reset-title">{{ __(@$passwordResetContent->data_info->form_heading) ?: 'Reset Password' }}</h2>
            <p class="reset-subtitle">Enter your new password below</p>
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
            <p>@lang('Resetting password for') <b>{{ showEmailAddress($email) }}</b></p>
                        </div>

        <!-- Reset Form -->
        <form action="{{ route('user.password.reset') }}" method="POST" class="reset-form" id="resetForm">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="code" value="{{ $verCode }}">
            
            <div class="form-group">
                <label class="form-label required">@lang('Password')</label>
                <div class="password-input-container">
                    <input type="password" class="form-control @if ($setting->strong_pass) secure-password @endif" name="password" id="password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                                    </div>
                                    @if ($setting->strong_pass)
                    <div class="password-requirements">
                                            <div class="password-requirement">
                            <span class="requirement-icon capital invalid">✗</span>
                                                <span class="requirement-text">@lang('At least one uppercase letter')</span>
                                            </div>
                                            <div class="password-requirement">
                            <span class="requirement-icon lower invalid">✗</span>
                                                <span class="requirement-text">@lang('At least one lowercase letter')</span>
                                            </div>
                                            <div class="password-requirement">
                            <span class="requirement-icon number invalid">✗</span>
                                                <span class="requirement-text">@lang('At least one number')</span>
                                            </div>
                                            <div class="password-requirement">
                            <span class="requirement-icon special invalid">✗</span>
                                                <span class="requirement-text">@lang('At least one special character')</span>
                                            </div>
                                            <div class="password-requirement">
                            <span class="requirement-icon minimum invalid">✗</span>
                                                <span class="requirement-text">@lang('At least 6 characters long')</span>
                                            </div>
                                        </div>
                                        <div class="password-strength">
                                            <div class="password-strength-bar"></div>
                                        </div>
                                    @endif
                                </div>

            <div class="form-group">
                <label class="form-label required">@lang('Confirm Password')</label>
                <div class="password-input-container">
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye"></i>
                                    </button>
                </div>
            </div>

            <button type="submit" class="btn-theme" id="resetBtn">
                <span class="loading-spinner" id="loadingSpinner"></span>
                <span id="resetBtnText">{{ __(@$passwordResetContent->data_info->submit_button_text) ?: 'Reset Password' }}</span>
            </button>
        </form>

        <div class="reset-footer">
            <p>@lang('Remember your password?') <a href="{{ route('user.login') }}">@lang('Sign In')</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resetForm = document.getElementById('resetForm');
    const resetBtn = document.getElementById('resetBtn');
    const resetBtnText = document.getElementById('resetBtnText');
    const loadingSpinner = document.getElementById('loadingSpinner');
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

    // Password validation
    @if ($setting->strong_pass)
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const requirements = {
            capital: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
            minimum: password.length >= 6
        };

        // Update requirement icons
        Object.keys(requirements).forEach(req => {
            const icon = document.querySelector(`.requirement-icon.${req}`);
            if (icon) {
                if (requirements[req]) {
                    icon.classList.remove('invalid');
                    icon.classList.add('valid');
                    icon.textContent = '✓';
                } else {
                    icon.classList.remove('valid');
                    icon.classList.add('invalid');
                    icon.textContent = '✗';
                }
            }
        });

        // Update strength bar
        const strengthBar = document.querySelector('.password-strength-bar');
        if (strengthBar) {
            const validCount = Object.values(requirements).filter(Boolean).length;
            const percentage = (validCount / Object.keys(requirements).length) * 100;
            strengthBar.style.setProperty('--width', percentage + '%');
        }
    });
    @endif

    // Confirm password validation
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.style.borderColor = '#dc3545';
        } else {
            this.style.borderColor = '#05ce78';
        }
    });

    // Form submission
    resetForm.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match');
            return;
        }

        // Show loading state
        resetBtn.disabled = true;
        resetBtnText.style.display = 'none';
        loadingSpinner.style.display = 'inline-block';
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
    
    .password-strength-bar::after {
        width: var(--width, 0%);
    }
`;
document.head.appendChild(style);
</script>
@endsection

@if ($setting->strong_pass)
    @push('page-style-lib')
        <link rel="stylesheet" href="{{ asset('assets/universal/css/strongPassword.css') }}">
    @endpush

    @push('page-script-lib')
        <script src="{{asset('assets/universal/js/strongPassword.js')}}"></script>
    @endpush
@endif
