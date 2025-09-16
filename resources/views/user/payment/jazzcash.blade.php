@extends('themes.' . $activeTemplate . 'layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="text-center">
                            <img src="{{ asset('assets/images/jazzcash-logo.png') }}" alt="JazzCash" class="mb-3" style="height: 50px;">
                            <h4>@lang('JazzCash Mobile Wallet Payment')</h4>
                            <p class="text-muted">@lang('Secure mobile wallet payment processing')</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="payment-info">
                                    <h5><i class="ti ti-credit-card text-primary"></i> @lang('Payment Details')</h5>
                                    <div class="info-item">
                                        <span class="label">@lang('Amount'):</span>
                                        <span class="value">{{ $data['val']['Amount'] }} {{ $data['val']['Currency'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">@lang('Transaction ID'):</span>
                                        <span class="value">{{ $data['val']['TransactionID'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="label">@lang('Description'):</span>
                                        <span class="value">{{ $data['val']['Description'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wallet-info">
                                    <h5><i class="ti ti-device-mobile text-success"></i> @lang('JazzCash Wallet')</h5>
                                    <div class="wallet-features">
                                        <div class="feature-item">
                                            <i class="ti ti-shield-check text-success"></i>
                                            <span>@lang('Secure Mobile Payment')</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="ti ti-clock text-primary"></i>
                                            <span>@lang('Instant Processing')</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="ti ti-phone text-info"></i>
                                            <span>@lang('Mobile Wallet')</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">@lang('Redirecting...')</span>
                            </div>
                            <p class="mt-3">@lang('Redirecting to JazzCash mobile wallet...')</p>
                            <p class="text-muted small">@lang('Please do not close this window or refresh the page.')</p>
                            
                            <div class="countdown-timer mt-3">
                                <div class="timer-circle">
                                    <span id="countdown">3</span>
                                </div>
                                <p class="timer-text">@lang('seconds remaining')</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="jazzcashPaymentForm" action="{{ $data['url'] }}" method="{{ $data['method'] }}" style="display: none;">
        @foreach($data['val'] as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let countdown = 3;
            const countdownElement = document.getElementById('countdown');
            const timerText = document.querySelector('.timer-text');
            
            const timer = setInterval(function() {
                countdownElement.textContent = countdown;
                countdown--;
                
                if (countdown < 0) {
                    clearInterval(timer);
                    document.getElementById('jazzcashPaymentForm').submit();
                }
            }, 1000);
        });
    </script>

    <style>
        .payment-info, .wallet-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .label {
            font-weight: 600;
            color: #6c757d;
        }
        
        .value {
            color: #495057;
            font-weight: 500;
        }
        
        .wallet-features {
            margin-top: 15px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
        }
        
        .feature-item i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 15px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 15px 15px 0 0;
        }
        
        .countdown-timer {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .timer-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .timer-text {
            margin: 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endsection

