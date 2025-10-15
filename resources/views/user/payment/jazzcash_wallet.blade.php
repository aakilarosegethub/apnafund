@php
    $activeTemplate = 'themes.apnafund.';
@endphp
@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center">@lang('JazzCash Wallet Payment')</h4>
                </div>
                <div class="card-body">
                    <div class="payment-info mb-4">
                        <div class="row">
                            <div class="col-sm-6">
                                <p><strong>@lang('Amount'):</strong> {{ showAmount($payment->amount) }} {{ $payment->currency }}</p>
                            </div>
                            <div class="col-sm-6">
                                <p><strong>@lang('Transaction ID'):</strong> {{ $payment->transaction_id }}</p>
                            </div>
                        </div>
                    </div>

                    <form id="jazzcash-wallet-form">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $payment->transaction_id }}">
                        
                        <div class="form-group mb-3">
                            <label for="phone_number" class="form-label">@lang('Phone Number') <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   placeholder="03XXXXXXXXX" 
                                   pattern="03[0-9]{9}" 
                                   maxlength="11" 
                                   required>
                            <small class="form-text text-muted">@lang('Enter your JazzCash registered phone number (e.g., 03123456789)')</small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="cnic_last_6" class="form-label">@lang('CNIC Last 6 Digits') <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="cnic_last_6" 
                                   name="cnic_last_6" 
                                   placeholder="123456" 
                                   pattern="[0-9]{6}" 
                                   maxlength="6" 
                                   required>
                            <small class="form-text text-muted">@lang('Enter the last 6 digits of your CNIC')</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <span class="btn-text">@lang('Process Payment')</span>
                                <span class="btn-loading d-none">
                                    <i class="fas fa-spinner fa-spin"></i> @lang('Processing...')
                                </span>
                            </button>
                        </div>
                    </form>

                    <div id="payment-result" class="mt-4 d-none">
                        <div class="alert" id="result-alert">
                            <div id="result-message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
$(document).ready(function() {
    // Format phone number input
    $('#phone_number').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 0 && !value.startsWith('03')) {
            value = '03' + value;
        }
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        $(this).val(value);
    });

    // Format CNIC input (only numbers)
    $('#cnic_last_6').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        $(this).val(value);
    });

    // Handle form submission
    $('#jazzcash-wallet-form').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submit-btn');
        const btnText = submitBtn.find('.btn-text');
        const btnLoading = submitBtn.find('.btn-loading');
        const resultDiv = $('#payment-result');
        const resultAlert = $('#result-alert');
        const resultMessage = $('#result-message');
        
        // Validate form
        const phoneNumber = $('#phone_number').val();
        const cnicLast6 = $('#cnic_last_6').val();
        
        if (!phoneNumber || !phoneNumber.match(/^03[0-9]{9}$/)) {
            showResult('error', '@lang("Please enter a valid phone number starting with 03")');
            return;
        }
        
        if (!cnicLast6 || !cnicLast6.match(/^[0-9]{6}$/)) {
            showResult('error', '@lang("Please enter exactly 6 digits for CNIC")');
            return;
        }
        
        // Show loading state
        submitBtn.prop('disabled', true);
        btnText.addClass('d-none');
        btnLoading.removeClass('d-none');
        resultDiv.addClass('d-none');
        
        // Make AJAX request
        $.ajax({
            url: '{{ route("Jazzcashwallet.process") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showResult('success', response.message);
                    // Redirect to success page after 3 seconds
                    setTimeout(function() {
                        window.location.href = '{{ route("user.deposit.index") }}';
                    }, 3000);
                } else {
                    showResult('error', response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = '@lang("An error occurred while processing your payment")';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showResult('error', errorMessage);
            },
            complete: function() {
                // Hide loading state
                submitBtn.prop('disabled', false);
                btnText.removeClass('d-none');
                btnLoading.addClass('d-none');
            }
        });
    });
    
    function showResult(type, message) {
        const resultDiv = $('#payment-result');
        const resultAlert = $('#result-alert');
        const resultMessage = $('#result-message');
        
        resultAlert.removeClass('alert-success alert-danger');
        
        if (type === 'success') {
            resultAlert.addClass('alert-success');
            resultMessage.html('<i class="fas fa-check-circle"></i> ' + message);
        } else {
            resultAlert.addClass('alert-danger');
            resultMessage.html('<i class="fas fa-exclamation-circle"></i> ' + message);
        }
        
        resultDiv.removeClass('d-none');
        resultDiv[0].scrollIntoView({ behavior: 'smooth' });
    }
});
</script>
@endpush
