@extends($activeTheme . 'layouts.green-home')

@section('custom-css')
<style>
    body {
        background: #f7f7f7;
    }

    .ks-signup-wrapper {
        min-height: calc(100vh - 180px);
        display: flex;
        justify-content: center;
        padding: 60px 16px;
        margin-top: 80px;
    }

    .ks-signup-card {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        padding: 28px;
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
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 22px;
        color: #222;
    }

    .ks-form-group {
        margin-bottom: 16px;
    }

    .ks-form-group input {
        width: 100%;
        padding: 12px 14px;
        font-size: 15px;
        border: 1px solid #dcdcdc;
        border-radius: 3px;
        box-sizing: border-box;
    }

    .ks-form-group input:focus {
        outline: none;
        border-color: #16a34a;
    }

    .ks-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 14px;
        margin-bottom: 12px;
        color: #444;
    }

    .ks-checkbox input {
        margin-top: 3px;
    }

    .ks-submit {
        width: 100%;
        padding: 12px;
        background: #16a34a;
        color: #fff;
        border: none;
        font-size: 15px;
        font-weight: 600;
        border-radius: 3px;
        cursor: pointer;
        margin-top: 12px;
        position: relative;
        transition: background 0.3s;
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
        margin-right: 8px;
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

    .ks-policy {
        font-size: 13px;
        color: #666;
        margin-top: 14px;
        line-height: 1.5;
    }

    .ks-policy a {
        color: #16a34a;
        text-decoration: none;
    }

    .ks-divider {
        text-align: center;
        margin: 20px 0;
        position: relative;
    }

    .ks-divider::before {
        content: '';
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

    .ks-social {
        width: 100%;
        padding: 12px;
        border-radius: 3px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
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

    /* OTP Form Styles */
    .ks-otp-form {
        display: none;
    }

    .ks-otp-form.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ks-signup-form.hidden {
        display: none;
    }

    .ks-otp-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #222;
    }

    .ks-otp-message {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .ks-otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 20px;
    }

    .ks-otp-input {
        width: 45px;
        height: 45px;
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        border: 2px solid #dcdcdc;
        border-radius: 4px;
    }

    .ks-otp-input:focus {
        outline: none;
        border-color: #16a34a;
    }

    .ks-resend-link {
        text-align: center;
        margin-top: 16px;
        font-size: 14px;
    }

    .ks-resend-link a {
        color: #16a34a;
        text-decoration: none;
        font-weight: 500;
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
</style>
@endsection

@section('content')
<div class="ks-signup-wrapper">
    <div class="ks-signup-card">
        <div class="ks-top-link">
            Have an account? <a href="{{ route('user.login') }}">Log in</a>
        </div>

        <!-- Error/Success Messages -->
        <div id="messageContainer"></div>

        <!-- Signup Form -->
        <div class="ks-signup-form" id="signupForm">
            <h1 class="ks-title">Sign up</h1>

            <form id="registerForm">
                @csrf

                <div class="ks-form-group">
                    <input type="text" name="name" id="name" placeholder="Name" required>
                </div>

                <div class="ks-form-group">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>

                <div class="ks-form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>

                <div class="ks-checkbox">
                    <input type="checkbox" id="news">
                    <label for="news">
                        Send me a weekly mix of handpicked projects, plus occasional {{ bs('site_name') ?? 'ApnaFund' }} news
                    </label>
                </div>

                <div class="ks-checkbox">
                    <input type="checkbox" id="research">
                    <label for="research">
                        Contact me about participating in {{ bs('site_name') ?? 'ApnaFund' }} research
                    </label>
                </div>

                <x-captcha />

                <button type="submit" class="ks-submit" id="submitBtn">
                    <span class="btn-loader"></span>
                    <span class="btn-text">Create account</span>
                </button>

                <div class="ks-policy">
                    By signing up, you agree to our
                    <a href="#">Privacy Policy</a>,
                    <a href="#">Cookie Policy</a> and
                    <a href="#">Terms of Use</a>.
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

                @if($facebookEnabled)
                    <a href="{{ route('user.social.facebook') }}" class="ks-social ks-facebook">
                        <i class="fab fa-facebook-f"></i> Continue with Facebook
                    </a>
                @endif

                @if($googleEnabled)
                    <a href="{{ route('user.social.google') }}" class="ks-social ks-google">
                        <i class="fab fa-google"></i> Continue with Google
                    </a>
                @endif
            @endif
        </div>

        <!-- OTP Form -->
        <div class="ks-otp-form" id="otpForm">
            <div class="ks-title">Verify Email</div>
            <div class="ks-otp-message" id="otpMessage">
                We've sent a verification code to <strong id="otpEmail"></strong>
            </div>

            <form id="verifyOtpForm">
                @csrf
                <input type="hidden" id="otpEmailHidden" name="email">

                <div class="ks-otp-inputs">
                    <input type="text" class="ks-otp-input" maxlength="1" id="otp1" required>
                    <input type="text" class="ks-otp-input" maxlength="1" id="otp2" required>
                    <input type="text" class="ks-otp-input" maxlength="1" id="otp3" required>
                    <input type="text" class="ks-otp-input" maxlength="1" id="otp4" required>
                    <input type="text" class="ks-otp-input" maxlength="1" id="otp5" required>
                    <input type="text" class="ks-otp-input" maxlength="1" id="otp6" required>
                </div>

                <button type="submit" class="ks-submit" id="verifyBtn">
                    <span class="btn-loader"></span>
                    <span class="btn-text">Verify & Create Account</span>
                </button>

                <div class="ks-resend-link">
                    Didn't receive the code? <a href="#" id="resendOtp">Resend</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const otpForm = document.getElementById('otpForm');
    const signupForm = document.getElementById('signupForm');
    const submitBtn = document.getElementById('submitBtn');
    const verifyBtn = document.getElementById('verifyBtn');
    const messageContainer = document.getElementById('messageContainer');
    
    let userEmail = '';
    let sessionData = null;

    // Show message
    function showMessage(message, type = 'error') {
        messageContainer.innerHTML = `<div class="ks-${type}">${message}</div>`;
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }

    // Button loader
    function setButtonLoading(button, loading) {
        if (loading) {
            button.classList.add('btn-loading');
            button.disabled = true;
        } else {
            button.classList.remove('btn-loading');
            button.disabled = false;
        }
    }

    // OTP Input handling
    const otpInputs = ['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6'];
    otpInputs.forEach((id, index) => {
        const input = document.getElementById(id);
        input.addEventListener('input', function(e) {
            const value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value;
            
            if (value && index < otpInputs.length - 1) {
                document.getElementById(otpInputs[index + 1]).focus();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                document.getElementById(otpInputs[index - 1]).focus();
            }
        });
    });

    // Register Form Submit
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!name || !email || !password) {
            showMessage('Please fill all required fields');
            return;
        }

        userEmail = email;
        setButtonLoading(submitBtn, true);

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('firstname', 'User');
        formData.append('lastname', 'User');
        formData.append('username', 'user' + Date.now());
        formData.append('country', 'Pakistan');
        formData.append('mobile', '03000000000');
        formData.append('mobile_code', '92');
        formData.append('country_code', 'PK');

        // Send OTP request
        fetch('{{ route("user.otp.send") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Check if response is OK (status 200-299)
            if (!response.ok) {
                // If response is not JSON, handle HTML error pages
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Request failed');
                    });
                } else {
                    // For HTML error pages (like 419), show generic error
                    if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    throw new Error('Request failed with status ' + response.status);
                }
            }
            return response.json();
        })
        .then(data => {
            setButtonLoading(submitBtn, false);
            
            if (data.success) {
                document.getElementById('otpEmail').textContent = email;
                document.getElementById('otpEmailHidden').value = email;
                
                // Hide signup form, show OTP form
                signupForm.classList.add('hidden');
                otpForm.classList.add('active');
                
                // Focus first OTP input
                document.getElementById('otp1').focus();
                
                showMessage(data.message || 'OTP sent successfully!', 'success');
            } else {
                showMessage(data.message || 'Failed to send OTP. Please try again.');
            }
        })
        .catch(error => {
            setButtonLoading(submitBtn, false);
            const errorMessage = error.message || 'An error occurred. Please try again.';
            showMessage(errorMessage);
            console.error('Error:', error);
        });
    });

    // Verify OTP Form Submit
    document.getElementById('verifyOtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get OTP code
        const otpCode = otpInputs.map(id => document.getElementById(id).value).join('');
        
        if (otpCode.length !== 6) {
            showMessage('Please enter the complete 6-digit code');
            return;
        }

        setButtonLoading(verifyBtn, true);

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('otp', otpCode);
        formData.append('email', userEmail);

        // Verify OTP and register
        fetch('{{ route("user.otp.verify") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Verification failed');
                    });
                } else {
                    if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    throw new Error('Request failed with status ' + response.status);
                }
            }
            return response.json();
        })
        .then(data => {
            setButtonLoading(verifyBtn, false);
            
            if (data.success) {
                showMessage('Account created successfully! Redirecting...', 'success');
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("home") }}';
                }, 2000);
            } else {
                showMessage(data.message || 'Invalid OTP. Please try again.');
                // Clear OTP inputs
                otpInputs.forEach(id => document.getElementById(id).value = '');
                document.getElementById('otp1').focus();
            }
        })
        .catch(error => {
            setButtonLoading(verifyBtn, false);
            const errorMessage = error.message || 'An error occurred. Please try again.';
            showMessage(errorMessage);
            console.error('Error:', error);
        });
    });

    // Resend OTP
    document.getElementById('resendOtp').addEventListener('click', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const email = userEmail;
        const password = document.getElementById('password').value;

        setButtonLoading(verifyBtn, true);

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('email', email);

        fetch('{{ route("user.otp.resend") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Resend failed');
                    });
                } else {
                    if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    throw new Error('Request failed with status ' + response.status);
                }
            }
            return response.json();
        })
        .then(data => {
            setButtonLoading(verifyBtn, false);
            
            if (data.success) {
                showMessage('OTP resent successfully!', 'success');
                otpInputs.forEach(id => document.getElementById(id).value = '');
                document.getElementById('otp1').focus();
            } else {
                showMessage(data.message || 'Failed to resend OTP. Please try again.');
            }
        })
        .catch(error => {
            setButtonLoading(verifyBtn, false);
            const errorMessage = error.message || 'An error occurred. Please try again.';
            showMessage(errorMessage);
            console.error('Error:', error);
        });
    });
});
</script>
@endpush
