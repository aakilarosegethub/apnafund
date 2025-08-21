@extends($activeTheme . 'layouts.app')

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
    .login-page-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
    }

    /* Left Side - Illustration */
    .login-illustration {
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

    /* Right Side - Login Form */
    .login-form-section {
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

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .login-subtitle {
        color: #666;
        font-size: 1rem;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fff;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #05ce78;
        box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        transform: translateY(-2px);
    }

    .input-group {
        position: relative;
    }

    .input-group .form-control {
        padding-right: 50px;
    }

    .input-group-text {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        z-index: 10;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }

    .input-group-text:hover {
        color: #05ce78;
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

    /* Form Check */
    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        justify-content: space-between;
    }

    .form-check-left {
        display: flex;
        align-items: center;
    }

    .form-check-input {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        border: 2px solid #e9ecef;
        border-radius: 4px;
        cursor: pointer;
        accent-color: #05ce78;
    }

    .form-check-label {
        color: #666;
        font-size: 0.9rem;
        cursor: pointer;
    }

    /* Links */
    .forgot-password {
        text-align: right;
    }

    .forgot-password a {
        color: #05ce78;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .forgot-password a:hover {
        color: #04a85f;
        text-decoration: underline;
    }

    .login-footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .login-footer a {
        color: #05ce78;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .login-footer a:hover {
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
        .login-page-container {
            flex-direction: column;
            padding: 20px;
        }

        .login-illustration {
            max-width: 100%;
            margin-bottom: 30px;
        }

        .login-form-section {
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
        .login-form-section {
            padding: 30px 20px;
        }

        .login-title {
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



<div class="login-page-container">
    <!-- Left Side - Illustration -->
    <div class="login-illustration">
        <div class="illustration-container">
            <div class="decoration decoration-1">
                <i class="fas fa-home"></i>
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
                <h1 class="illustration-title">ApnaFund</h1>
                <p class="illustration-subtitle">Secure & Easy Fundraising Platform</p>
                <div style="font-size: 4rem; margin-top: 20px;">
                    <i class="fas fa-handshake" style="color: rgba(255,255,255,0.8);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-form-section">
        <div class="login-header">
            <h2 class="login-title">{{ __(@$loginContent->data_info->form_heading) ?: 'Sign in to your account' }}</h2>
            <p class="login-subtitle">Welcome back! Please enter your details</p>
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

        <!-- Login Form -->
        <form action="{{ route('user.login') }}" method="POST" class="verify-gcaptcha" id="loginForm">
            @csrf
            <div class="form-group">
                <label for="username" class="form-label">@lang('Username or Email Address')</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username or email" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">@lang('Password')</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="input-group-text" id="togglePassword">
                        <i class="fas fa-eye" id="passwordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-check">
                <div class="form-check-left">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember" @checked(old('remember'))>
                    <label class="form-check-label" for="remember">
                        @lang('Remember me')
                    </label>
                </div>
                <div class="forgot-password">
                    <a href="{{ route('user.password.request.form') }}">@lang('Forgot Your Password?')</a>
                </div>
            </div>

            <x-captcha />

            <button type="submit" class="btn-theme" id="loginBtn">
                <span class="loading-spinner" id="loadingSpinner"></span>
                <span id="loginBtnText">{{ __(@$loginContent->data_info->submit_button_text) ?: 'Log In' }}</span>
            </button>
        </form>

        <div class="login-footer">
            <p>@lang('Don\'t have any account?') <a href="{{ route('user.register') }}">@lang('Create Account')</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginBtnText = document.getElementById('loginBtnText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        passwordIcon.classList.toggle('fa-eye');
        passwordIcon.classList.toggle('fa-eye-slash');
    });

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        // Show loading state
        loginBtn.disabled = true;
        loginBtnText.style.display = 'none';
        loadingSpinner.style.display = 'inline-block';
    });

    // Add interactive effects
    const formInputs = document.querySelectorAll('.form-control');
    formInputs.forEach(input => {
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
            loginForm.dispatchEvent(new Event('submit'));
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
