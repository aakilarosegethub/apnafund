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
        overflow-y: auto;
    }

    @media (max-width: 576px) {
        .ks-signup-wrapper {
            align-items: flex-start;
            padding-bottom: 120px;
        }
        .ks-otp-inputs {
            flex-wrap: wrap;
        }
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

    .ks-form-group input.ks-input-invalid {
        border-color: #dc2626;
    }

    .ks-form-group input.ks-input-valid {
        border-color: #16a34a;
    }

    .ks-field-hint {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        line-height: 1.4;
        color: #666;
    }

    .ks-field-hint.ks-field-hint-error {
        color: #dc2626 !important;
        font-weight: 500;
    }

    .ks-field-hint.ks-field-hint-ok {
        color: #16a34a !important;
    }

    .ks-password-strength {
        margin-top: 8px;
    }

    .ks-strength-track {
        height: 4px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 6px;
    }

    .ks-strength-fill {
        display: block;
        height: 100%;
        width: 0;
        border-radius: 999px;
        transition: width 0.2s ease, background-color 0.2s ease;
    }

    .ks-strength-fill.weak { width: 33%; background: #dc2626; }
    .ks-strength-fill.medium { width: 66%; background: #f59e0b; }
    .ks-strength-fill.strong { width: 100%; background: #16a34a; }

    .ks-strength-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .ks-strength-label.weak { color: #dc2626; }
    .ks-strength-label.medium { color: #d97706; }
    .ks-strength-label.strong { color: #16a34a; }

    .ks-password-rules {
        list-style: none;
        margin: 8px 0 0;
        padding: 0;
        font-size: 12px;
        line-height: 1.5;
    }

    .ks-password-rules li {
        color: #6b7280;
        margin-bottom: 2px;
    }

    .ks-password-rules li.met {
        color: #16a34a;
    }

    .ks-password-rules li.unmet {
        color: #dc2626;
    }

    .ks-form-errors {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 12px 14px;
        border-radius: 4px;
        margin-bottom: 16px;
        font-size: 14px;
        line-height: 1.5;
    }

    .ks-form-errors ul {
        margin: 0;
        padding-left: 18px;
    }

    .ks-form-errors li {
        color: #991b1b !important;
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

    .ks-linkedin {
        background: #0a66c2;
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
    span{
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="ks-signup-wrapper">
    <div class="ks-signup-card">
        <div class="ks-top-link">
            Have an account? <a href="{{ route('user.login.form') }}">Log in</a>
        </div>

        <!-- Error/Success Messages -->
        <div id="messageContainer"></div>
        <div id="formErrors" class="ks-form-errors" style="display:none;" role="alert" aria-live="polite"></div>

        <!-- Signup Form -->
        <div class="ks-signup-form" id="signupForm">
            <h1 class="ks-title">Sign up</h1>

            <form id="registerForm">
                @csrf

                <div class="ks-form-group">
                    <input type="text" name="name" id="name" placeholder="Name" required maxlength="{{ registrationNameMaxLength() }}" pattern=".*\p{L}.*" title="Name must include at least one letter and be at most {{ registrationNameMaxLength() }} characters" autocomplete="name">
                    <small class="ks-field-hint" id="nameHint">Maximum {{ registrationNameMaxLength() }} characters</small>
                </div>

                <div class="ks-form-group">
                    <input type="email" name="email" id="email" placeholder="Email" required maxlength="191" autocomplete="email">
                    <small class="ks-field-hint" id="emailHint"></small>
                </div>

                <div class="ks-form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required autocomplete="new-password">
                    <small class="ks-field-hint" id="passwordHint">{{ registrationPasswordMinLength() }}–{{ registrationPasswordMaxLength() }} characters with uppercase, lowercase, number, and symbol</small>
                    <div class="ks-password-strength" id="passwordStrength" style="display: none;">
                        <div class="ks-strength-track">
                            <span class="ks-strength-fill" id="strengthFill"></span>
                        </div>
                        <span class="ks-strength-label" id="strengthLabel">Weak</span>
                    </div>
                    <ul class="ks-password-rules" id="passwordRulesList" style="display: none;"></ul>
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

                <button type="submit" class="ks-submit" id="submitBtn" disabled>
                    <span class="btn-loader"></span>
                    <span class="btn-text">Create account</span>
                </button>

                <div class="ks-policy">
                    By signing up, you agree to our
                    <a href="{{ route('policy.pages', ['privacy-policy', 11]) }}" target="_blank" rel="noopener">Privacy Policy</a>,
                    <a href="{{ route('cookie.policy') }}" target="_blank" rel="noopener">Cookie Policy</a> and
                    <a href="{{ url('policy/terms-of-use/12') }}" target="_blank" rel="noopener">Terms of Use</a>.
                </div>
            </form>

            @php
                $facebookEnabled = !empty(config('services.facebook.client_id')) && 
                                   !empty(config('services.facebook.client_secret')) && 
                                   config('services.facebook.client_id') !== 'disabled';
                $googleEnabled = !empty(config('services.google.client_id')) && 
                                 !empty(config('services.google.client_secret')) && 
                                 config('services.google.client_id') !== 'disabled';
                $linkedinEnabled = !empty(config('services.linkedin.client_id')) && 
                                  !empty(config('services.linkedin.client_secret')) && 
                                  config('services.linkedin.client_id') !== 'disabled';
            @endphp

            @if($facebookEnabled || $googleEnabled || $linkedinEnabled)
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

                @if($linkedinEnabled)
                    <a href="{{ route('user.social.linkedin') }}" class="ks-social ks-linkedin">
                        <i class="fab fa-linkedin-in"></i> Continue with LinkedIn
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const otpForm = document.getElementById('otpForm');
    const signupForm = document.getElementById('signupForm');
    const submitBtn = document.getElementById('submitBtn');
    const verifyBtn = document.getElementById('verifyBtn');
    const messageContainer = document.getElementById('messageContainer');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const nameHint = document.getElementById('nameHint');
    const emailHint = document.getElementById('emailHint');
    const passwordHint = document.getElementById('passwordHint');
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthFill = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    const passwordRulesList = document.getElementById('passwordRulesList');
    const formErrorsBox = document.getElementById('formErrors');

    const NAME_MAX = {{ registrationNameMaxLength() }};
    const PASSWORD_MIN = {{ registrationPasswordMinLength() }};
    const PASSWORD_MAX = {{ registrationPasswordMaxLength() }};
    const EMAIL_MAX = 191;
    const WEAK_PASSWORDS = @json(\App\Constants\WeakPasswords::listForFrontend());
    const WEAK_PASSWORD_BASES = ['password', 'passw0rd', 'qwerty', 'abc', 'admin', 'welcome', 'letmein', 'login', 'hello'];
    const PASSWORD_RULE_LABELS = {
        length: `At least ${PASSWORD_MIN} characters (max ${PASSWORD_MAX})`,
        upper: 'At least one uppercase letter',
        lower: 'At least one lowercase letter',
        number: 'At least one number',
        special: 'At least one special character',
        common: 'Not a common or easily guessed password',
        identity: 'Must not contain your email or name'
    };

    function enforceInputMaxLength(input, maxLength, hintEl, label) {
        if (!input) {
            return;
        }

        const trimToMax = function() {
            if (input.value.length > maxLength) {
                input.value = input.value.slice(0, maxLength);
                if (hintEl) {
                    setFieldState(input, hintEl, label + ' must not exceed ' + maxLength + ' characters.', false);
                }
                updateSubmitState(true);
            }
        };

        input.addEventListener('input', trimToMax);
        input.addEventListener('paste', function() {
            setTimeout(trimToMax, 0);
        });
    }

    // Name/email only — password uses validate-only (no silent trim) so >72 shows error
    enforceInputMaxLength(nameInput, NAME_MAX, nameHint, 'Name');
    enforceInputMaxLength(emailInput, EMAIL_MAX, emailHint, 'Email');

    function extractSignupErrorMessage(data, fallback) {
        if (data && data.errors) {
            const fieldOrder = ['name', 'email', 'password'];
            for (let i = 0; i < fieldOrder.length; i++) {
                const field = fieldOrder[i];
                if (data.errors[field] && data.errors[field][0]) {
                    return data.errors[field][0];
                }
            }
        }

        return (data && data.message) ? data.message : fallback;
    }
    
    let userEmail = '';
    let sessionData = null;
    const touched = { name: false, email: false, password: false };

    function setFieldState(input, hintEl, message, isValid) {
        input.classList.remove('ks-input-invalid', 'ks-input-valid');
        hintEl.classList.remove('ks-field-hint-error', 'ks-field-hint-ok');
        hintEl.style.display = message ? 'block' : 'none';

        if (!message) {
            hintEl.textContent = '';
            return;
        }

        hintEl.textContent = message;
        if (isValid) {
            input.classList.add('ks-input-valid');
            hintEl.classList.add('ks-field-hint-ok');
        } else {
            input.classList.add('ks-input-invalid');
            hintEl.classList.add('ks-field-hint-error');
        }
    }

    function getNameError(value, showEmptyHint) {
        const trimmed = (value || '').trim();
        const length = trimmed.length;

        if (!trimmed) {
            return showEmptyHint ? 'Name is required.' : null;
        }
        if (length > NAME_MAX) {
            return `Name must not exceed ${NAME_MAX} characters (${length}/${NAME_MAX}).`;
        }
        if (!/\p{L}/u.test(trimmed)) {
            return 'Name must include at least one letter.';
        }

        return null;
    }

    function getEmailError(value, showEmptyHint) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

        if (!value) {
            return showEmptyHint ? 'Email is required.' : null;
        }
        if (value.length > EMAIL_MAX) {
            return `Email must not exceed ${EMAIL_MAX} characters (${value.length}/${EMAIL_MAX}).`;
        }
        if (!emailPattern.test(value)) {
            return 'Please enter a valid email address.';
        }

        return null;
    }

    function isSequentialDigits(value) {
        if (!/^\d+$/.test(value) || value.length < 6) {
            return false;
        }

        let ascending = true;
        let descending = true;

        for (let i = 1; i < value.length; i++) {
            if (parseInt(value[i], 10) !== parseInt(value[i - 1], 10) + 1) {
                ascending = false;
            }
            if (parseInt(value[i], 10) !== parseInt(value[i - 1], 10) - 1) {
                descending = false;
            }
        }

        return ascending || descending;
    }

    function isSequentialLetters(value) {
        if (!/^[a-z]+$/i.test(value) || value.length < 6) {
            return false;
        }

        const lower = value.toLowerCase();
        let ascending = true;
        let descending = true;

        for (let i = 1; i < lower.length; i++) {
            if (lower.charCodeAt(i) !== lower.charCodeAt(i - 1) + 1) {
                ascending = false;
            }
            if (lower.charCodeAt(i) !== lower.charCodeAt(i - 1) - 1) {
                descending = false;
            }
        }

        return ascending || descending;
    }

    function isTooCommonPassword(value) {
        const lower = (value || '').toLowerCase();

        if (!lower) {
            return false;
        }

        if (WEAK_PASSWORDS.includes(lower)) {
            return true;
        }

        for (let i = 0; i < WEAK_PASSWORD_BASES.length; i++) {
            const base = WEAK_PASSWORD_BASES[i];
            if (lower === base || new RegExp('^' + base.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\d*$').test(lower)) {
                return true;
            }
        }

        if (/^\d{8,}$/.test(lower)) {
            return true;
        }

        if (/^[a-z]{8,}$/i.test(lower)) {
            return true;
        }

        if (/^(.)\1{3,}$/.test(value)) {
            return true;
        }

        if (isSequentialDigits(lower) || isSequentialLetters(lower)) {
            return true;
        }

        return false;
    }

    function passwordContainsUserIdentifier(value, email, name) {
        const lower = (value || '').toLowerCase();
        const emailValue = (email || '').trim().toLowerCase();

        if (emailValue) {
            if (lower.includes(emailValue)) {
                return 'Password must not contain your email address.';
            }

            const localPart = emailValue.split('@')[0] || '';
            if (localPart.length >= 3 && lower.includes(localPart)) {
                return 'Password must not contain your email address.';
            }
        }

        if (name) {
            const parts = name.trim().split(/\s+/);
            for (let i = 0; i < parts.length; i++) {
                const part = parts[i].toLowerCase();
                if (part.length >= 3 && lower.includes(part)) {
                    return 'Password must not contain your name.';
                }
            }
        }

        return null;
    }

    function analyzePassword(value, email, name) {
        const length = value.length;
        const identityError = passwordContainsUserIdentifier(value, email, name);
        const rules = {
            length: length >= PASSWORD_MIN && length <= PASSWORD_MAX,
            upper: /[A-Z]/.test(value),
            lower: /[a-z]/.test(value),
            number: /[0-9]/.test(value),
            special: /[^A-Za-z0-9]/.test(value),
            common: value.length > 0 && !isTooCommonPassword(value),
            identity: !identityError
        };

        const errors = [];

        if (!value) {
            errors.push('Password is required.');
        } else {
            if (length > PASSWORD_MAX) {
                errors.push(`Password must not exceed ${PASSWORD_MAX} characters.`);
            }
            if (length < PASSWORD_MIN) {
                errors.push(`Password must be at least ${PASSWORD_MIN} characters long.`);
            }
            if (!rules.upper) {
                errors.push('Password must contain at least one uppercase letter.');
            }
            if (!rules.lower) {
                errors.push('Password must contain at least one lowercase letter.');
            }
            if (!rules.number) {
                errors.push('Password must contain at least one number.');
            }
            if (!rules.special) {
                errors.push('Password must contain at least one special character.');
            }
            if (!rules.common) {
                errors.push('Password is too common. Please choose a stronger password.');
            }
            if (identityError) {
                errors.push(identityError);
            }
        }

        let strength = 'weak';
        if (errors.length === 0) {
            let score = 0;
            if (length >= 10) score++;
            if (length >= 12) score++;
            if (rules.special && rules.number) score++;
            if (rules.upper && rules.lower) score++;
            strength = score >= 3 ? 'strong' : 'medium';
        }

        return { errors, rules, strength };
    }

    function getPasswordError(value, showEmptyHint) {
        const email = emailInput ? emailInput.value.trim() : '';
        const name = nameInput ? nameInput.value.trim() : '';
        const result = analyzePassword(value, email, name);

        if (!value) {
            return showEmptyHint ? 'Password is required.' : null;
        }

        return result.errors.length ? result.errors[0] : null;
    }

    function renderPasswordStrengthUI(value) {
        const email = emailInput ? emailInput.value.trim() : '';
        const name = nameInput ? nameInput.value.trim() : '';
        const result = analyzePassword(value, email, name);
        const hasInput = value.length > 0;

        passwordStrength.style.display = hasInput ? 'block' : 'none';
        passwordRulesList.style.display = hasInput ? 'block' : 'none';

        if (!hasInput) {
            strengthFill.className = 'ks-strength-fill';
            strengthLabel.className = 'ks-strength-label';
            strengthLabel.textContent = 'Weak';
            passwordRulesList.innerHTML = '';
            return;
        }

        strengthFill.className = 'ks-strength-fill ' + result.strength;
        strengthLabel.className = 'ks-strength-label ' + result.strength;
        strengthLabel.textContent = result.strength;

        passwordRulesList.innerHTML = Object.keys(PASSWORD_RULE_LABELS).map(function(key) {
            const met = result.rules[key];
            const className = met ? 'met' : 'unmet';
            const icon = met ? '&#10003;' : '&#10007;';
            return '<li class="' + className + '">' + icon + ' ' + PASSWORD_RULE_LABELS[key] + '</li>';
        }).join('');
    }

    function validateNameField(showEmptyHint) {
        const value = nameInput.value.trim();
        const error = getNameError(nameInput.value, showEmptyHint);

        if (error) {
            setFieldState(nameInput, nameHint, error, false);
            return false;
        }

        if (value) {
            setFieldState(nameInput, nameHint, `${value.length}/${NAME_MAX} characters`, true);
        } else if (!showEmptyHint) {
            setFieldState(nameInput, nameHint, `Maximum ${NAME_MAX} characters`, true);
        } else {
            setFieldState(nameInput, nameHint, 'Name is required.', false);
            return false;
        }

        return true;
    }

    function validateEmailField(showEmptyHint) {
        const value = emailInput.value.trim();
        const error = getEmailError(value, showEmptyHint);

        if (error) {
            setFieldState(emailInput, emailHint, error, false);
            return false;
        }

        setFieldState(emailInput, emailHint, value ? 'Email looks good.' : '', true);
        return true;
    }

    function validatePasswordField(showEmptyHint) {
        const value = passwordInput.value;
        const error = getPasswordError(value, showEmptyHint);

        renderPasswordStrengthUI(value);

        if (error) {
            setFieldState(passwordInput, passwordHint, error, false);
            return false;
        }

        if (value) {
            const strength = analyzePassword(value, emailInput.value.trim(), nameInput.value.trim()).strength;
            setFieldState(passwordInput, passwordHint, 'Password strength: ' + strength.charAt(0).toUpperCase() + strength.slice(1), true);
        } else if (!showEmptyHint) {
            setFieldState(passwordInput, passwordHint, `${PASSWORD_MIN}–${PASSWORD_MAX} characters with uppercase, lowercase, number, and symbol`, true);
        } else {
            setFieldState(passwordInput, passwordHint, 'Password is required.', false);
            return false;
        }

        return true;
    }

    function isRegistrationFormValidStrict() {
        return !getNameError(nameInput.value, true)
            && !getEmailError(emailInput.value.trim(), true)
            && !getPasswordError(passwordInput.value, true);
    }

    function showFormErrors(forceAll) {
        const errors = [];

        const nameError = getNameError(nameInput.value, forceAll || touched.name);
        const emailError = getEmailError(emailInput.value.trim(), forceAll || touched.email);
        const passwordAnalysis = analyzePassword(
            passwordInput.value,
            emailInput.value.trim(),
            nameInput.value.trim()
        );

        if (nameError) errors.push(nameError);
        if (emailError) errors.push(emailError);
        if (forceAll || touched.password) {
            passwordAnalysis.errors.forEach(function(err) {
                errors.push(err);
            });
        }

        if (errors.length) {
            formErrorsBox.innerHTML = '<ul>' + errors.map(function(err) {
                return '<li>' + err + '</li>';
            }).join('') + '</ul>';
            formErrorsBox.style.display = 'block';
        } else {
            formErrorsBox.innerHTML = '';
            formErrorsBox.style.display = 'none';
        }
    }

    function updateSubmitState(forceAll) {
        const showHints = forceAll === true;
        const passwordTooLong = passwordInput.value.length > PASSWORD_MAX;
        const passwordHasValue = passwordInput.value.length > 0;

        if (showHints || touched.name) {
            validateNameField(showHints || touched.name);
        }
        if (showHints || touched.email) {
            validateEmailField(showHints || touched.email);
        }
        if (showHints || touched.password || passwordTooLong || passwordHasValue) {
            validatePasswordField(showHints || touched.password || passwordTooLong);
        }

        showFormErrors(showHints || passwordTooLong);
        submitBtn.disabled = !isRegistrationFormValidStrict();
    }

    nameInput.addEventListener('keyup', function() {
        touched.name = true;
        updateSubmitState(false);
    });
    nameInput.addEventListener('input', function() {
        touched.name = true;
        updateSubmitState(false);
    });
    nameInput.addEventListener('blur', function() {
        touched.name = true;
        updateSubmitState(true);
    });

    emailInput.addEventListener('keyup', function() {
        touched.email = true;
        updateSubmitState(false);
    });
    emailInput.addEventListener('input', function() {
        touched.email = true;
        updateSubmitState(false);
    });
    emailInput.addEventListener('blur', function() {
        touched.email = true;
        updateSubmitState(true);
    });

    passwordInput.addEventListener('keyup', function() {
        touched.password = true;
        updateSubmitState(false);
    });
    passwordInput.addEventListener('input', function() {
        touched.password = true;
        updateSubmitState(passwordInput.value.length > PASSWORD_MAX);
    });
    passwordInput.addEventListener('paste', function() {
        setTimeout(function() {
            touched.password = true;
            updateSubmitState(true);
        }, 0);
    });
    passwordInput.addEventListener('blur', function() {
        touched.password = true;
        updateSubmitState(true);
    });

    updateSubmitState(false);

    // Show message
    function showMessage(message, type = 'error', autoHide = true) {
        formErrorsBox.style.display = 'none';
        messageContainer.innerHTML = `<div class="ks-${type}">${message}</div>`;
        if (autoHide) {
            setTimeout(() => {
                messageContainer.innerHTML = '';
            }, 5000);
        }
    }

    // Button loader
    function setButtonLoading(button, loading) {
        if (loading) {
            button.classList.add('btn-loading');
            button.disabled = true;
        } else {
            button.classList.remove('btn-loading');
            if (button === submitBtn) {
                updateSubmitState(false);
            } else {
                button.disabled = false;
            }
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

        touched.name = true;
        touched.email = true;
        touched.password = true;
        updateSubmitState(true);

        if (!isRegistrationFormValidStrict()) {
            messageContainer.innerHTML = '<div class="ks-error">Please fix the errors below before continuing.</div>';
            const firstInvalid = document.querySelector('.ks-input-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            submitBtn.disabled = true;
            return;
        }

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value;

        messageContainer.innerHTML = '';
        formErrorsBox.style.display = 'none';

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

        if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.getResponse === 'function') {
            const captchaToken = grecaptcha.getResponse();
            if (!captchaToken) {
                setButtonLoading(submitBtn, false);
                showMessage('Please complete the captcha verification.');
                return;
            }
            formData.append('g-recaptcha-response', captchaToken);
        }
        fetch('{{ route("user.otp.send") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type') || '';
            const isJson = contentType.includes('application/json');

            if (!response.ok) {
                if (isJson) {
                    return response.json().then(data => {
                        throw new Error(extractSignupErrorMessage(data, 'Request failed'));
                    });
                }

                if (response.status === 419) {
                    throw new Error('Session expired. Please refresh the page and try again.');
                }

                throw new Error('Request failed with status ' + response.status);
            }

            if (!isJson) {
                throw new Error('Unexpected server response. Please try again.');
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
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', '{{ route("user.home") }}');
                }
                setTimeout(() => {
                    window.location.replace(data.redirect || '{{ route("user.home") }}');
                }, 1500);
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
@endsection
