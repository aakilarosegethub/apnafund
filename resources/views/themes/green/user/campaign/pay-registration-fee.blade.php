@php
    $activeTheme = activeTheme();
    $setting = bs();
    $displayCurrencyCode = $setting->site_cur ?? 'USD';
    $displayCurrencySymbol = $setting->cur_sym ?? '$';
    $feeInDisplayCurrency = $feeAmountUsd;
@endphp
@extends($activeTheme . 'layouts.blank')
@section('custom-css')
<style>
    body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #f8f9fa; }
    .pay-fee-container { max-width: 520px; margin: 40px auto; padding: 0 20px; }
    .pay-fee-card { background: #fff; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.08); padding: 32px; margin-bottom: 24px; border: 1px solid #f0f0f0; }
    .pay-fee-card h2 { font-size: 1.25rem; margin-bottom: 8px; color: #232323; }
    .pay-fee-card .campaign-name { font-weight: 600; color: #05ce78; }
    .fee-amount { font-size: 2rem; font-weight: 700; color: #232323; margin: 20px 0; }
    .payment-option { display: flex; align-items: center; padding: 14px; border: 2px solid #e9ecef; border-radius: 8px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; }
    .payment-option:hover, .payment-option.selected { border-color: #05ce78; background: #f8fffe; }
    .payment-radio { width: 20px; height: 20px; border: 2px solid #e9ecef; border-radius: 50%; margin-right: 12px; }
    .payment-option.selected .payment-radio { border-color: #05ce78; background: #05ce78; }
    .payment-text { font-weight: 500; color: #232323; }
    .btn-pay { width: 100%; padding: 16px; background: #05ce78; color: #fff; border: none; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; margin-top: 16px; }
    .btn-pay:hover { background: #04b868; color: #fff; }
    .btn-pay:disabled { background: #ccc; cursor: not-allowed; }
    .back-link { display: inline-block; margin-bottom: 16px; color: #05ce78; text-decoration: none; font-weight: 500; }
    .back-link:hover { color: #04b868; text-decoration: underline; }
    .alert-warning { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 12px; margin-bottom: 20px; font-size: 0.9rem; }
</style>
@endsection
@section('frontend')
<div class="pay-fee-container">
    <a href="{{ route('user.campaign.edit', $campaign->slug) }}" class="back-link">&larr; @lang('Back to project')</a>

    <div class="pay-fee-card">
        <h2>@lang('Pay Registration Fee')</h2>
        <p class="mb-0">@lang('Project'): <span class="campaign-name">{{ $campaign->name }}</span></p>
        <p class="fee-amount">{{ $displayCurrencySymbol }}{{ number_format($feeInDisplayCurrency, 2) }} <small class="text-muted" style="font-size: 0.6em;">({{ strtoupper($displayCurrencyCode) }})</small></p>

        <form action="{{ route('user.campaign.pay.registration.fee.submit', $campaign->slug) }}" method="POST" id="registrationFeeForm">
            @csrf
            <input type="hidden" name="gateway" id="gatewayInput">
            <input type="hidden" name="currency" id="currencyInput">

            @if($gatewayCurrencies && $gatewayCurrencies->count() > 0)
                <p class="mb-2"><strong>@lang('Select payment method')</strong></p>
                @foreach($gatewayCurrencies as $gatewayCurrency)
                    <div class="payment-option" data-method="{{ $gatewayCurrency->method_code }}" data-currency="{{ $gatewayCurrency->currency }}">
                        <div class="payment-radio"></div>
                        <span class="payment-text">{{ __($gatewayCurrency->method->name) }} ({{ strtoupper($gatewayCurrency->currency) }})</span>
                    </div>
                @endforeach
                <button type="submit" class="btn btn-pay" id="submitBtn" disabled>@lang('Pay') {{ $displayCurrencySymbol }}{{ number_format($feeInDisplayCurrency, 2) }}</button>
            @else
                <div class="alert alert-warning">
                    @lang('No payment methods available for your country.') @lang('Please contact support.')
                </div>
            @endif
        </form>
    </div>
</div>

@endsection
@section('script')
@if($gatewayCurrencies && $gatewayCurrencies->count() > 0)
<script>
document.querySelectorAll('.payment-option').forEach(function(el) {
    el.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(function(o) { o.classList.remove('selected'); });
        this.classList.add('selected');
        document.getElementById('gatewayInput').value = this.dataset.method;
        document.getElementById('currencyInput').value = this.dataset.currency;
        document.getElementById('submitBtn').disabled = false;
    });
});
</script>
@endif
@endsection
