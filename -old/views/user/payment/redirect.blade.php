@extends('themes.' . $activeTemplate . 'layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">@lang('Redirecting to Payment Gateway')</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                        <p class="mt-3">@lang('Please wait while we redirect you to the payment gateway...')</p>
                        <p class="text-muted">@lang('Do not close this window or refresh the page.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="paymentForm" action="{{ $data['url'] }}" method="{{ $data['method'] }}" style="display: none;">
        @foreach($data['val'] as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit the form after a short delay
            setTimeout(function() {
                document.getElementById('paymentForm').submit();
            }, 2000);
        });
    </script>
@endsection
