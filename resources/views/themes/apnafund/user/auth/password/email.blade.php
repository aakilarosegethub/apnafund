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
    .forgot-page-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        position: relative;
    }

    /* Left Side - Illustration */
    .forgot-illustration {
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

    /* Right Side - Forgot Password Form */
    .forgot-form-section {
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

    .forgot-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .forgot-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .forgot-subtitle {
        color: #666;
        font-size: 1rem;
        line-height: 1.6;
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
    .back-to-login {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .back-to-login a {
        color: #05ce78;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .back-to-login a:hover {
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
        .forgot-page-container {
            flex-direction: column;
            padding: 20px;
        }

        .forgot-illustration {
            max-width: 100%;
            margin-bottom: 30px;
        }

        .forgot-form-section {
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
        .forgot-form-section {
            padding: 30px 20px;
        }

        .forgot-title {
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

<div class="forgot-page-container">
    <!-- Left Side - Illustration -->
    <div class="forgot-illustration">
        <div class="illustration-container">
            <div class="decoration decoration-1">
                <i class="fas fa-key"></i>
            </div>
            <div class="decoration decoration-2">
                <i class="fas fa-lock"></i>
            </div>
            <div class="decoration decoration-3">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="decoration decoration-4">
                <i class="fas fa-shield-alt"></i>
            </div>
            
            <div class="illustration-content">
                <h1 class="illustration-title">Reset Password</h1>
                <p class="illustration-subtitle">Secure Password Recovery</p>
                <div style="font-size: 4rem; margin-top: 20px;">
                    <i class="fas fa-unlock-alt" style="color: rgba(255,255,255,0.8);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side - Forgot Password Form -->
    <div class="forgot-form-section">
        <div class="forgot-header">
            <h2 class="forgot-title">{{ __(@$forgotPasswordContent->data_info->form_heading) ?: 'Forgot Your Password?' }}</h2>
            <p class="forgot-subtitle">Enter your email address and we'll send you a link to reset your password.</p>
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

        <!-- Forgot Password Form -->
        <form action="{{ url('user/password/forgot') }}" method="POST" class="verify-gcaptcha" id="forgotForm">
            @csrf
            <div class="form-group">
                <label for="value" class="form-label">@lang('Username or Email Address')</label>
                <input type="text" class="form-control" id="value" name="value" value="{{ old('value') }}" placeholder="Enter your username or email" required>
            </div>

            <x-captcha />

            <button type="submit" class="btn-theme" id="forgotBtn">
                <span class="loading-spinner" id="loadingSpinner"></span>
                <span id="forgotBtnText">{{ __(@$forgotPasswordContent->data_info->submit_button_text) ?: 'Send Reset Link' }}</span>
            </button>
        </form>

        <div class="back-to-login">
            <p>Remember your password? <a href="{{ route('user.login.form') }}">@lang('Back to Login')</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forgotForm = document.getElementById('forgotForm');
    const forgotBtn = document.getElementById('forgotBtn');
    const forgotBtnText = document.getElementById('forgotBtnText');
    const loadingSpinner = document.getElementById('loadingSpinner');

    // Function to refresh CSRF token
    function refreshCsrfToken() {
        return fetch('/csrf-token', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', data.token);
            }
            // Update form CSRF token
            const csrfInput = forgotForm.querySelector('input[name="_token"]');
            if (csrfInput) {
                csrfInput.value = data.token;
            }
            return data.token;
        })
        .catch(error => {
            console.error('Error refreshing CSRF token:', error);
            return null;
        });
    }

    // Refresh CSRF token on page load and periodically
    refreshCsrfToken();
    setInterval(refreshCsrfToken, 5 * 60 * 1000); // Refresh every 5 minutes

    // Handle form submission with CSRF token refresh
    forgotForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default submission
        
        // Show loading state
        forgotBtn.disabled = true;
        forgotBtnText.style.display = 'none';
        loadingSpinner.style.display = 'inline-block';
        
        // Refresh CSRF token before submitting
        refreshCsrfToken().then(() => {
            // Submit form after token is refreshed
            forgotForm.submit();
        }).catch(() => {
            // If refresh fails, submit anyway (might work if token is still valid)
            forgotForm.submit();
        });
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
            forgotForm.dispatchEvent(new Event('submit'));
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
