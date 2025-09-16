@php

$url = $data->url;
$method = $data->method;
$data = $data->val;
$amount = $data->pp_Amount;
$currency = $data->pp_TxnCurrency;
$transaction_id = $data->pp_TxnRefNo;
$description = $data->pp_Description;
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">@lang('Card Payment - Page Redirection v1.1')</h4>
                    <p class="text-center text-muted">@lang('Secure card payment processing')</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-info">
                                <h5>@lang('Payment Details')</h5>
                                <p><strong>@lang('Amount'):</strong> {{ $amount }} {{ $currency }}</p>
                                <p><strong>@lang('Transaction ID'):</strong> {{ $transaction_id }}</p>
                                <p><strong>@lang('Description'):</strong> {{ $description }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="security-info">
                                <h5>@lang('Security Features')</h5>
                                <ul class="list-unstyled">
                                    <li><i class="ti ti-shield-check text-success"></i> @lang('SSL Encrypted')</li>
                                    <li><i class="ti ti-lock text-primary"></i> @lang('PCI DSS Compliant')</li>
                                    <li><i class="ti ti-credit-card text-info"></i> @lang('Card Payments v1.1')</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">@lang('Redirecting...')</span>
                        </div>
                        <p class="mt-3">@lang('Redirecting to secure payment gateway...')</p>
                        <p class="text-muted small">@lang('Please do not close this window or refresh the page.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<form id="cardPaymentForm" action="{{ $url }}" method="{{ $method }}" style="display: none;">
    @foreach($data as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show countdown before redirect
    let countdown = 3;
    const countdownElement = document.querySelector('.mt-3');
    
    const timer = setInterval(function() {
        countdownElement.textContent = `Redirecting to secure payment gateway in ${countdown} seconds...`;
        countdown--;
        
        if (countdown < 0) {
            clearInterval(timer);
            document.getElementById('cardPaymentForm').submit();
        }
    }, 1000);
});
</script>

<style>
.payment-info, .security-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.security-info ul li {
    padding: 5px 0;
}

.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}
</style>
@endsection
