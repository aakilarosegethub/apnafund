@extends('user.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ $pageTitle }}</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div id="phone-form">
                        <form id="otp-phone-form">
                            @csrf
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                       placeholder="+1234567890" required>
                                <div class="form-text">Enter your phone number with country code</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="send-otp-btn">
                                Send OTP
                            </button>
                        </form>
                    </div>

                    <div id="otp-form" style="display: none;">
                        <form id="otp-verify-form">
                            @csrf
                            <div class="mb-3">
                                <label for="otp_code" class="form-label">Enter OTP Code</label>
                                <input type="text" class="form-control" id="otp_code" name="otp_code" 
                                       placeholder="123456" maxlength="8" required>
                                <div class="form-text">Enter the 6-digit code sent to your phone</div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="verify-otp-btn">
                                    Verify OTP
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="resend-otp-btn">
                                    Resend OTP
                                </button>
                                <button type="button" class="btn btn-link" id="back-to-phone">
                                    Change Phone Number
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('user.login.form') }}" class="text-decoration-none">
                            Back to Email Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneForm = document.getElementById('otp-phone-form');
    const otpForm = document.getElementById('otp-verify-form');
    const phoneSection = document.getElementById('phone-form');
    const otpSection = document.getElementById('otp-form');
    const sendOtpBtn = document.getElementById('send-otp-btn');
    const verifyOtpBtn = document.getElementById('verify-otp-btn');
    const resendOtpBtn = document.getElementById('resend-otp-btn');
    const backToPhoneBtn = document.getElementById('back-to-phone');

    // Send OTP
    phoneForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phoneNumber = document.getElementById('phone_number').value;
        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = 'Sending...';

        fetch('{{ route("user.otp.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ phone_number: phoneNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                phoneSection.style.display = 'none';
                otpSection.style.display = 'block';
                showAlert('success', data.message);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred. Please try again.');
        })
        .finally(() => {
            sendOtpBtn.disabled = false;
            sendOtpBtn.textContent = 'Send OTP';
        });
    });

    // Verify OTP
    otpForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const otpCode = document.getElementById('otp_code').value;
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'Verifying...';

        fetch('{{ route("user.otp.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ otp_code: otpCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred. Please try again.');
        })
        .finally(() => {
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.textContent = 'Verify OTP';
        });
    });

    // Resend OTP
    resendOtpBtn.addEventListener('click', function() {
        resendOtpBtn.disabled = true;
        resendOtpBtn.textContent = 'Resending...';

        fetch('{{ route("user.otp.resend") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred. Please try again.');
        })
        .finally(() => {
            resendOtpBtn.disabled = false;
            resendOtpBtn.textContent = 'Resend OTP';
        });
    });

    // Back to phone form
    backToPhoneBtn.addEventListener('click', function() {
        otpSection.style.display = 'none';
        phoneSection.style.display = 'block';
        document.getElementById('otp_code').value = '';
    });

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.card-body');
        const existingAlert = container.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        container.insertBefore(alertDiv, container.firstChild);
    }
});
</script>
@endsection
