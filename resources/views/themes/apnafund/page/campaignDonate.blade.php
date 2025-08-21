
@extends($activeTheme . 'layouts.frontend')
@section('style')
<style>
        body {
            background: #f8f9fa;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            line-height: 1.6;
            color: #333;
        }

        /* Header */
        .main-header {
            background: #05ce78;
            padding: 15px 0;
            margin: 0;
            border: none;
        }

        .main-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        .logo {
            text-align: center;
        }

        .logo-img {
            height: 35px;
            width: auto;
        }

        .signin-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .signin-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        /* Main Container */
        .payment-container {
            max-width: 40.9375rem;
            margin: 40px auto;
            padding: 0 20px;
            width: 100%;
        }

        .payment-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            padding: 40px 35px;
            margin-bottom: 30px;
            border: 1px solid #f0f0f0;
            width: 100%;
            box-sizing: border-box;
        }

        /* Fundraiser Info */
        .fundraiser-info {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .fundraiser-image {
            width: 100px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .fundraiser-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .fundraiser-details h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #232323;
        }

        .fundraiser-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        /* Donation Amount */
        .donation-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #232323;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        .amount-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 25px;
            width: 100%;
        }

        .amount-btn {
            padding: 12px 8px;
            border: 2px solid #e9ecef;
            background: #fff;
            border-radius: 10px;
            font-weight: 600;
            color: #333;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.95rem;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
        }

        .amount-btn:hover {
            border-color: #05ce78;
            background: #f8fffe;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(5, 206, 120, 0.15);
        }

        .amount-btn.selected {
            border-color: #05ce78;
            background: #05ce78;
            color: #fff;
            box-shadow: 0 2px 8px rgba(5, 206, 120, 0.3);
        }

        .custom-amount {
            position: relative;
            margin-bottom: 20px;
        }

        .custom-amount input {
            width: 100%;
            padding: 20px 20px 20px 65px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 3.5rem;
            font-weight: 900;
            color: #232323;
            background: #fff;
            text-align: right;
            box-sizing: border-box;
            line-height: 1.2;
        }

        .custom-amount input:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        .custom-amount input::placeholder {
            color: #999;
            font-weight: 400;
        }

        .currency-symbol {
            position: absolute;
            left: 22px;
            top: 40%;
            transform: translateY(-50%);
            font-weight: 900;
            color: #232323;
            font-size: 1.6rem;
            line-height: 1;
        }

        .currency-label {
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(10px);
            font-size: 0.65rem;
            color: #232323;
            font-weight: 400;
            line-height: 1;
        }

        /* Tip Section */
        .tip-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .tip-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .tip-slider {
            width: 100%;
            margin-bottom: 15px;
            -webkit-appearance: none;
            appearance: none;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            outline: none;
        }

        .tip-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #05ce78;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tip-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #05ce78;
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tip-value {
            display: block;
            width: 100px;
            margin: 0 auto;
            padding: 10px 16px;
            background: #fff;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-weight: 600;
            color: #232323;
            font-size: 1rem;
            min-width: 80px;
            text-align: center;
        }

        .custom-tip-link {
            color: #05ce78;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .custom-tip-link:hover {
            text-decoration: underline;
        }

        /* Payment Methods */
        .payment-methods {
            margin-bottom: 30px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
        }

        .payment-option:hover {
            border-color: #05ce78;
        }

        .payment-option.selected {
            border-color: #05ce78;
            background: #f8fffe;
        }

        .payment-radio {
            width: 20px;
            height: 20px;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            margin-right: 15px;
            position: relative;
        }

        .payment-option.selected .payment-radio {
            border-color: #05ce78;
            background: #05ce78;
        }

        .payment-option.selected .payment-radio::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
        }

        .payment-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-logo {
            width: 24px;
            height: 24px;
            background: #f0f0f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        .payment-text {
            font-weight: 500;
            color: #232323;
        }

        /* Checkboxes */
        .checkbox-section {
            margin-bottom: 30px;
        }

        .checkbox-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            width: 100%;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #e9ecef;
            border-radius: 3px;
            margin-right: 12px;
            margin-top: 2px;
            cursor: pointer;
            position: relative;
        }

        .checkbox.checked {
            background: #05ce78;
            border-color: #05ce78;
        }

        .checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-label {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
        }

        .info-icon {
            color: #05ce78;
            margin-left: 5px;
            font-size: 12px;
        }

        /* Summary */
        .donation-summary {
            margin-bottom: 30px;
            width: 100%;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            width: 100%;
        }

        .summary-label {
            color: #666;
            font-size: 0.9rem;
        }

        .summary-value {
            font-weight: 600;
            color: #232323;
        }

        .summary-total {
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
            margin-top: 10px;
        }

        .summary-total .summary-label,
        .summary-total .summary-value {
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Payment Button */
        .payment-button {
            width: 100%;
            padding: 18px 20px;
            background: #232323;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
            box-sizing: border-box;
            min-height: 60px;
        }

        .payment-button:hover {
            background: #1a1a1a;
        }

        .payment-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Legal Text */
        .legal-text {
            font-size: 0.8rem;
            color: #999;
            text-align: center;
            line-height: 1.4;
        }

        .legal-text a {
            color: #05ce78;
            text-decoration: none;
        }

        .legal-text a:hover {
            text-decoration: underline;
        }

        /* Protection Section */
        .protection-section {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
        }

        .protection-icon {
            width: 50px;
            height: 50px;
            background: #05ce78;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #fff;
            font-size: 20px;
        }

        .protection-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #232323;
            margin-bottom: 10px;
        }

        .protection-text {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .protection-text a {
            color: #05ce78;
            text-decoration: none;
        }

        .protection-text a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .payment-container {
                margin: 20px auto;
                padding: 0 15px;
                max-width: 100%;
            }

            .payment-card {
                padding: 30px 25px;
            }

            .amount-buttons {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .amount-btn {
                padding: 10px 6px;
                font-size: 0.9rem;
                min-height: 40px;
            }

            .custom-amount input {
                padding: 18px 18px 18px 55px;
                font-size: 1.3rem;
            }

            .currency-symbol {
                left: 20px;
                font-size: 1.3rem;
            }

            .currency-label {
                left: 20px;
                font-size: 0.6rem;
                transform: translateY(8px);
            }
        }

        @media (max-width: 480px) {
            .payment-container {
                padding: 0 10px;
            }

            .payment-card {
                padding: 25px 20px;
            }

            .amount-buttons {
                grid-template-columns: repeat(2, 1fr);
                gap: 6px;
            }

            .amount-btn {
                padding: 8px 4px;
                font-size: 0.85rem;
                min-height: 35px;
            }
        }

        /* Form Fields */
        .form--label {
            display: block;
            font-weight: 600;
            color: #232323;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form--label.required::after {
            content: ' *';
            color: #dc3545;
        }

        .form--control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #232323;
            background: #fff;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form--control:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        .form--control::placeholder {
            color: #999;
        }

        .input--group {
            display: flex;
            align-items: center;
        }

        .input-group-text {
            padding: 12px 16px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .input--group .form--control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .col-lg-3 {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 0 10px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .g-3 > * {
            margin-bottom: 1rem;
        }

        /* Responsive Form Fields */
        @media (max-width: 768px) {
            .col-lg-3,
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
@endsection

@section('frontend')
<!--New design-->
<!-- Main Content -->
<div class="payment-container">
        <form action="{{ route('user.deposit.insert', $campaignData->slug) }}" method="POST" id="donationForm">
            @csrf
            <input type="hidden" name="currency">
            <input type="hidden" name="gateway">
            <input type="hidden" name="amount" id="amountInput">

            @auth
                <input type="hidden" name="country" value="{{ @$authUser->country_name }}">
            @endauth
            
            <div class="payment-card">
            <!-- Fundraiser Info -->
            <div class="fundraiser-info">
                <div class="fundraiser-image">
                <img src="{{ getImage(getFilePath('campaign') . '/' . $campaignData->image) }}" alt="{{ $campaignData->name }}" class="campaign-image mb-3">
                </div>
                <div class="fundraiser-details">
                <h3>@lang('You\'re supporting') <strong>{{ $campaignData->name }}</strong></h3>
                <p class="campaign-description">{{ strLimit($campaignData->description, 100) }}</p>
                </div>
            </div>

            <!-- Donation Amount -->
            <div class="donation-section">
                <h3 class="section-title">Enter your donation</h3>
                <div class="amount-buttons">
                    @if($campaignData->preferred_amounts && is_array($campaignData->preferred_amounts))
                        @foreach ($campaignData->preferred_amounts as $preferredAmount)
                            <button type="button" class="amount-btn" data-amount="{{ $preferredAmount }}">
                                {{ $setting->cur_sym . $preferredAmount }}
                            </button>
                        @endforeach
                    @else
                        <button type="button" class="amount-btn" data-amount="50">{{ $setting->cur_sym }}50</button>
                        <button type="button" class="amount-btn" data-amount="100">{{ $setting->cur_sym }}100</button>
                        <button type="button" class="amount-btn" data-amount="200">{{ $setting->cur_sym }}200</button>
                        <button type="button" class="amount-btn" data-amount="300">{{ $setting->cur_sym }}300</button>
                        <button type="button" class="amount-btn" data-amount="500">{{ $setting->cur_sym }}500</button>
                        <button type="button" class="amount-btn" data-amount="1000">{{ $setting->cur_sym }}1,000</button>
                    @endif
                </div>
                <div class="custom-amount">
                    <span class="currency-symbol">{{ $setting->cur_sym }}</span>
                    <input type="text" id="customAmount" placeholder="0.00" min="0" step="0.01">
                    <span class="currency-label">{{ strtoupper($setting->site_cur) }}</span>
                </div>
            </div>

            <!-- Tip Section -->
            <div class="tip-section">
                <h3 class="section-title">@lang('Tip') {{ $setting->site_name }} @lang('services')</h3>
                <p class="tip-description">
                    {{ $setting->site_name }} @lang('has a 0% platform fee for organizers.') {{ $setting->site_name }} @lang('will continue offering its services thanks to donors who will leave an optional amount here:')
                </p>
                <input type="range" class="tip-slider" id="tipSlider" min="0" max="25" value="17.5" step="0.5">
                <div style="text-align: center; margin-bottom: 10px;">
                    <span class="tip-value" id="tipValue">17.5%</span>
                </div>
                <div style="text-align: center;">
                    <a href="#" class="custom-tip-link" id="customTipLink">@lang('Enter custom tip')</a>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="payment-methods">
                <h3 class="section-title">@lang('Payment method')</h3>
                @if($gatewayCurrencies && count($gatewayCurrencies) > 0)
                    @foreach ($gatewayCurrencies as $gatewayCurrency)
                        <div class="payment-option" data-method="{{ $gatewayCurrency->method_code }}" data-currency="{{ $gatewayCurrency->currency }}">
                            <div class="payment-radio"></div>
                            <div class="payment-info">
                                <div class="payment-logo">{{ strtoupper(substr($gatewayCurrency->method->name, 0, 2)) }}</div>
                                <span class="payment-text">{{ __($gatewayCurrency->method->name) }} ({{ strtoupper($gatewayCurrency->currency) }})</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        @lang('No payment gateways are currently available.')
                    </div>
                @endif
            </div>

            <!-- Personal Information -->
            <div id="personalInfoSection">
                <h3 class="section-title">@lang('Personal Information')</h3>
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <label class="form--label required">@lang('Full Name')</label>
                        @if ($authUser)
                            <input type="text" class="form--control" name="full_name" value="{{ old('full_name', @$authUser->fullname) }}" placeholder="@lang('Enter your full name')" @readonly(@$authUser) required>
                        @else
                            <input type="text" class="form--control" name="full_name" value="{{ old('full_name') }}" placeholder="@lang('Enter your full name')" required>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form--label required">@lang('Email')</label>
                        @if ($authUser)
                            <input type="email" class="form--control" name="email" value="{{ old('email', @$authUser->email) }}" placeholder="@lang('Enter your email')" @readonly(@$authUser) required>
                        @else
                            <input type="email" class="form--control" name="email" value="{{ old('email') }}" placeholder="@lang('Enter your email')" required>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form--label required">@lang('Phone')</label>
                        <input type="hidden" name="mobile_code">
                        
                        @if ($authUser)
                            <input type="text" class="form--control" name="phone" value="{{ old('phone', @$authUser->mobile) }}" placeholder="@lang('+0123 456 789')" @readonly(@$authUser) required>
                        @else
                            <div class="input--group">
                                <span class="input-group-text input-group-text-light mobile-code"></span>
                                <input type="number" class="form--control checkUser" name="phone" value="{{ old('phone') }}" required>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form--label required">@lang('Country')</label>
                        @if ($authUser)
                            <input type="text" class="form--control" name="country" value="{{ old('country', @$authUser->country_name) }}" @readonly(@$authUser) required>
                        @else
                            <select class="form--control" name="country" required>
                                <option value="">@lang('Select Country')</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->country }}" @selected(old('country') == $country->country)>
                                        {{ $country->country }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="checkbox-section">
                <div class="checkbox-item">
                    <div class="checkbox" id="privacyCheckbox"></div>
                    <label class="checkbox-label">
                        @lang('Don\'t display my name publicly on the fundraiser.')
                        <i class="fas fa-info-circle info-icon"></i>
                    </label>
                </div>
                <div class="checkbox-item">
                    <div class="checkbox checked" id="marketingCheckbox"></div>
                    <label class="checkbox-label">
                        @lang('Get occasional marketing updates from') {{ $setting->site_name }}. @lang('You may unsubscribe at any time.')
                    </label>
                </div>
            </div>

            <!-- Donation Summary -->
            <div class="donation-summary">
                <div class="summary-item">
                    <span class="summary-label">@lang('Your donation')</span>
                    <span class="summary-value" id="donationAmount">{{ $setting->cur_sym }}0.00</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">{{ $setting->site_name }} @lang('tip')</span>
                    <span class="summary-value" id="tipAmount">{{ $setting->cur_sym }}0.00</span>
                </div>
                <div class="summary-item summary-total">
                    <span class="summary-label">@lang('Total due today')</span>
                    <span class="summary-value" id="totalAmount">{{ $setting->cur_sym }}0.00</span>
                </div>
            </div>

            <!-- Payment Button -->
            <button type="submit" class="payment-button" id="paymentButton" disabled>
                <i class="ti ti-heart"></i>
                @lang('Contribute Now')
            </button>

            <!-- Legal Text -->
            <p class="legal-text">
                @lang('By clicking \'Contribute Now\', you agree to') {{ $setting->site_name }}'s 
                <a href="#">@lang('Terms of Service')</a>
                @lang('and') 
                <a href="#">@lang('Privacy Notice')</a>. 
                @lang('Learn more about') <a href="#">@lang('pricing and fees')</a>.
            </p>
        </div>
        </form>

        <!-- Protection Section -->
        <div class="protection-section">
            <div class="protection-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="protection-title">{{ $setting->site_name }} @lang('protects your donation')</h3>
            <p class="protection-text">
                @lang('We guarantee you a full refund for up to a year in the rare case that fraud occurs. See our') 
                <a href="#">{{ $setting->site_name }} @lang('Giving Guarantee')</a>.
            </p>
        </div>
    </div>
<!-- Main Content -->
<div class="payment-container d-none">
        <div class="payment-card">
            <!-- Fundraiser Info -->
            <div class="fundraiser-info">
                <div class="fundraiser-image">
                    <img src="{{ getImage(getFilePath('campaign') . '/' . $campaignData->image) }}" alt="{{ $campaignData->name }}">
                </div>
                <div class="fundraiser-details">
                    <h3>@lang('You\'re supporting') <strong>{{ $campaignData->name }}</strong></h3>
                    <p>{{ strLimit($campaignData->description, 80) }}</p>
                </div>
            </div>

            <!-- Donation Form -->
            <form action="{{ route('user.deposit.insert', $campaignData->slug) }}" method="POST" id="donationForm">
                @csrf
                <input type="hidden" name="currency">

                @auth
                    <input type="hidden" name="country" value="{{ @$authUser->country_name }}">
                @endauth

                <!-- Donation Amount -->
                <div class="donation-section">
                    <h3 class="section-title">@lang('Enter your donation')</h3>
                    <div class="amount-buttons">
                        @if($campaignData->preferred_amounts && is_array($campaignData->preferred_amounts))
                            @foreach ($campaignData->preferred_amounts as $preferredAmount)
                                <button type="button" class="amount-btn" data-amount="{{ $preferredAmount }}">
                                    {{ $setting->cur_sym . $preferredAmount }}
                                </button>
                            @endforeach
                        @else
                            <button type="button" class="amount-btn" data-amount="50">{{ $setting->cur_sym }}50</button>
                            <button type="button" class="amount-btn" data-amount="100">{{ $setting->cur_sym }}100</button>
                            <button type="button" class="amount-btn" data-amount="200">{{ $setting->cur_sym }}200</button>
                            <button type="button" class="amount-btn" data-amount="300">{{ $setting->cur_sym }}300</button>
                            <button type="button" class="amount-btn" data-amount="500">{{ $setting->cur_sym }}500</button>
                            <button type="button" class="amount-btn" data-amount="1000">{{ $setting->cur_sym }}1,000</button>
                        @endif
                    </div>
                    <div class="custom-amount">
                        <span class="currency-symbol">{{ $setting->cur_sym }}</span>
                        <input type="number" step="any" min="0" id="donationAmount" name="amount" value="{{ old('amount') }}" placeholder="0.00" required>
                        <span class="currency-label">{{ strtoupper($setting->site_cur) }}</span>
                    </div>
                </div>

                <!-- Tip Section -->
                <div class="tip-section">
                    <h3 class="section-title">@lang('Tip') {{ $setting->site_name }} @lang('services')</h3>
                    <p class="tip-description">
                        {{ $setting->site_name }} @lang('has a 0% platform fee for organizers.') {{ $setting->site_name }} @lang('will continue offering its services thanks to donors who will leave an optional amount here:')
                    </p>
                    <input type="range" class="tip-slider" id="tipSlider" min="0" max="25" value="0" step="0.5">
                    <div style="text-align: center; margin-bottom: 10px;">
                        <span class="tip-value" id="tipValue">0%</span>
                    </div>
                    <div style="text-align: center;">
                        <a href="#" class="custom-tip-link" id="customTipLink">@lang('Enter custom tip')</a>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="payment-methods">
                    <h3 class="section-title">@lang('Payment method')</h3>
                    @if($gatewayCurrencies && count($gatewayCurrencies) > 0)
                        @foreach ($gatewayCurrencies as $gatewayCurrency)
                            <div class="payment-option" data-method="{{ $gatewayCurrency->method_code }}" data-currency="{{ $gatewayCurrency->currency }}">
                                <div class="payment-radio"></div>
                                <div class="payment-info">
                                    <div class="payment-logo">{{ strtoupper(substr($gatewayCurrency->method->name, 0, 2)) }}</div>
                                    <span class="payment-text">{{ __($gatewayCurrency->method->name) }} ({{ strtoupper($gatewayCurrency->currency) }})</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            @lang('No payment gateways are currently available.')
                        </div>
                    @endif
                </div>

                <!-- Personal Information -->
                <div id="personalInfoSection">
                    <h3 class="section-title">@lang('Personal Information')</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form--label required">@lang('Full Name')</label>
                            @if ($authUser)
                                <input type="text" class="form--control" name="full_name" value="{{ old('full_name', @$authUser->fullname) }}" placeholder="@lang('Enter your full name')" @readonly(@$authUser) required>
                            @else
                                <input type="text" class="form--control" name="full_name" value="{{ old('full_name') }}" placeholder="@lang('Enter your full name')" required>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form--label required">@lang('Email')</label>
                            @if ($authUser)
                                <input type="email" class="form--control" name="email" value="{{ old('email', @$authUser->email) }}" placeholder="@lang('Enter your email')" @readonly(@$authUser) required>
                            @else
                                <input type="email" class="form--control" name="email" value="{{ old('email') }}" placeholder="@lang('Enter your email')" required>
                            @endif
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form--label required">@lang('Phone')</label>
                            <input type="hidden" name="mobile_code">
                            
                            @if ($authUser)
                                <input type="text" class="form--control" name="phone" value="{{ old('phone', @$authUser->mobile) }}" placeholder="@lang('+0123 456 789')" @readonly(@$authUser) required>
                            @else
                                <div class="input--group">
                                    <span class="input-group-text input-group-text-light mobile-code"></span>
                                    <input type="number" class="form--control checkUser" name="phone" value="{{ old('phone') }}" required>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form--label required">@lang('Country')</label>
                            @if ($authUser)
                                <input type="text" class="form--control" name="country" value="{{ old('country', @$authUser->country_name) }}" @readonly(@$authUser) required>
                            @else
                                <select class="form--control" name="country" required>
                                    <option value="">@lang('Select Country')</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->country }}" @selected(old('country') == $country->country)>
                                            {{ $country->country }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Checkboxes -->
                <div class="checkbox-section">
                    <div class="checkbox-item">
                        <div class="checkbox" id="privacyCheckbox"></div>
                        <label class="checkbox-label">
                            @lang('Don\'t display my name publicly on the fundraiser.')
                            <i class="fas fa-info-circle info-icon"></i>
                        </label>
                    </div>
                    <div class="checkbox-item">
                        <div class="checkbox checked" id="marketingCheckbox"></div>
                        <label class="checkbox-label">
                            @lang('Get occasional marketing updates from') {{ $setting->site_name }}. @lang('You may unsubscribe at any time.')
                        </label>
                    </div>
                </div>

                <!-- Donation Summary -->
                <div class="donation-summary">
                    <div class="summary-item">
                        <span class="summary-label">@lang('Your donation')</span>
                        <span class="summary-value" id="summaryDonationAmount">{{ $setting->cur_sym }}0.00</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">{{ $setting->site_name }} @lang('tip')</span>
                        <span class="summary-value" id="summaryTipAmount">{{ $setting->cur_sym }}0.00</span>
                    </div>
                    <div class="summary-item summary-total">
                        <span class="summary-label">@lang('Total due today')</span>
                        <span class="summary-value" id="summaryTotalAmount">{{ $setting->cur_sym }}0.00</span>
                    </div>
                </div>

                <!-- Payment Button -->
                <button type="submit" class="payment-button" id="donateBtn" disabled>
                    <i class="ti ti-heart"></i>
                    @lang('Donate Now')
                </button>

                <!-- Legal Text -->
                <p class="legal-text">
                    @lang('By clicking \'Donate Now\', you agree to') {{ $setting->site_name }}'s 
                    <a href="#">@lang('Terms of Service')</a>
                    @lang('and') 
                    <a href="#">@lang('Privacy Notice')</a>. 
                    @lang('Learn more about') <a href="#">@lang('pricing and fees')</a>.
                </p>
            </form>
        </div>

        <!-- Protection Section -->
        <div class="protection-section">
            <div class="protection-icon">
                <i class="ti ti-shield-check"></i>
            </div>
            <h3 class="protection-title">{{ $setting->site_name }} @lang('protects your donation')</h3>
            <p class="protection-text">
                @lang('We guarantee you a full refund for up to a year in the rare case that fraud occurs. See our') 
                <a href="#">{{ $setting->site_name }} @lang('Giving Guarantee')</a>.
            </p>
        </div>
    </div>
<!---New design end-->
    <div class="donation-page pt-120 pb-60">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="donation-form-card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card custom--card">
                            <div class="card-header">
                                <h3 class="title">@lang('Make a Donation to') "{{ $campaignData->name }}"</h3>
                                <p class="text-muted">@lang('Your contribution will make a difference')</p>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('user.deposit.insert', $campaignData->slug) }}" method="POST" id="donationForm">
                                    @csrf
                                    <input type="hidden" name="currency">

                                    @auth
                                        <input type="hidden" name="country" value="{{ @$authUser->country_name }}">
                                    @endauth

                                    <!-- Anonymous Donation Option -->
                                    <div class="form-group mb-4">
                                        <div class="form--check">
                                            <input type="checkbox" class="form-check-input" name="anonymousDonation" @checked(old('anonymousDonation')) id="anonymousDonation">
                                            <label class="form-check-label" for="anonymousDonation">
                                                @lang('Donate as anonymous')
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group anonymous-alert-text d-none mb-4">
                                        <div class="alert alert-info" role="alert">
                                            @lang('We require your information even if you choose to donate anonymously. However, rest assured that your details will not be displayed anywhere in our system.')
                                        </div>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form--label required">@lang('Full Name')</label>
                                            @if ($authUser)
                                                <input type="text" class="form--control" name="full_name" value="{{ old('full_name', @$authUser->fullname) }}" placeholder="@lang('Enter your full name')" @readonly(@$authUser) required>
                                            @else
                                                <input type="text" class="form--control" name="full_name" value="{{ old('full_name') }}" placeholder="@lang('Enter your full name')" required>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label required">@lang('Email')</label>
                                            @if ($authUser)
                                                <input type="email" class="form--control" name="email" value="{{ old('email', @$authUser->email) }}" placeholder="@lang('Enter your email')" @readonly(@$authUser) required>
                                            @else
                                                <input type="email" class="form--control" name="email" value="{{ old('email') }}" placeholder="@lang('Enter your email')" required>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form--label required">@lang('Phone')</label>
                                            <input type="hidden" name="mobile_code">
                                            
                                            @if ($authUser)
                                                <input type="text" class="form--control" name="phone" value="{{ old('phone', @$authUser->mobile) }}" placeholder="@lang('+0123 456 789')" @readonly(@$authUser) required>
                                            @else
                                                <div class="input--group">
                                                    <span class="input-group-text input-group-text-light mobile-code"></span>
                                                    <input type="number" class="form--control checkUser" name="phone" value="{{ old('phone') }}" required>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label required">@lang('Country')</label>
                                            @if ($authUser)
                                                <input type="text" class="form--control" name="country" value="{{ old('country', @$authUser->country_name) }}" @readonly(@$authUser) required>
                                            @else
                                                <select class="form--control" name="country" required>
                                                    <option value="">@lang('Select Country')</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->country }}" @selected(old('country') == $country->country)>
                                                            {{ $country->country }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Amount Selection -->
                                    <div class="amount-selection mb-4">
                                        <h4 class="section-title">@lang('Choose an amount')</h4>
                                        
                                        <div class="form-group mb-3">
                                            <div class="input--group">
                                                <span class="input-group-text">{{ $setting->cur_sym }}</span>
                                                <input type="number" step="any" min="0" class="form--control" id="donationAmount" name="amount" value="{{ old('amount') }}" placeholder="0" readonly required>
                                            </div>
                                        </div>

                                        <div class="amount-options">
                                            <div class="row g-2">
                                                @if($campaignData->preferred_amounts && is_array($campaignData->preferred_amounts))
                                                    @foreach ($campaignData->preferred_amounts as $preferredAmount)
                                                        <div class="col-md-3 col-sm-6">
                                                            <div class="form--radio amount-option">
                                                                <input type="radio" class="form-check-input" id="{{ 'donationAmount_' . $loop->iteration }}" name="donationAmount" data-amount="{{ $preferredAmount }}">
                                                                <label class="form-check-label" for="{{ 'donationAmount_' . $loop->iteration }}">
                                                                    {{ $setting->cur_sym . $preferredAmount }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="form--radio amount-option">
                                                        <input type="radio" class="form-check-input" id="customDonationAmount" name="donationAmount">
                                                        <label class="form-check-label" for="customDonationAmount">
                                                            @lang('Custom')
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Gateway Selection -->
                                    <div class="payment-gateway mb-4">
                                        <h4 class="section-title">@lang('Select Payment Method')</h4>
                                        <div class="gateway-options">
                                            @if($gatewayCurrencies && count($gatewayCurrencies) > 0)
                                                @foreach ($gatewayCurrencies as $gatewayCurrency)
                                                    <div class="form--radio gateway-option">
                                                        <input type="radio" class="form-check-input" id="gateway_{{ $gatewayCurrency->id }}" name="gateway" value="{{ $gatewayCurrency->method_code }}" data-currency="{{ $gatewayCurrency->currency }}" required>
                                                        <label class="form-check-label" for="gateway_{{ $gatewayCurrency->id }}">
                                                            <div class="gateway-info">
                                                                <span class="gateway-name">{{ __($gatewayCurrency->method->name) }}</span>
                                                                <span class="gateway-currency">({{ strtoupper($gatewayCurrency->currency) }})</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="alert alert-warning">
                                                    @lang('No payment gateways are currently available.')
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Payment Preview -->
                                    <div class="payment-preview d-none mb-4">
                                        <h4 class="section-title">@lang('Payment Summary')</h4>
                                        <div class="preview-details">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>@lang('Donation Amount')</span>
                                                    <span><span class="amount fw-bold">0</span> {{ __($setting->site_cur) }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>@lang('Limit')</span>
                                                    <span><span class="min fw-bold">0</span> {{ __($setting->site_cur) }} - <span class="max fw-bold">0</span> {{ __($setting->site_cur) }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>@lang('Charge')</span>
                                                    <span><span class="charge fw-bold">0</span> {{ __($setting->site_cur) }}</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>@lang('Payable')</span>
                                                    <span><span class="payable fw-bold">0</span> {{ __($setting->site_cur) }}</span>
                                                </li>
                                                <li class="list-group-item justify-content-between d-none rate-element"></li>
                                                <li class="list-group-item justify-content-between d-none in-site-cur">
                                                    <span>@lang('In') <span class="method_currency"></span></span>
                                                    <span class="final_amount fw-bold">0</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn--base w-100 btn-lg" id="donateBtn" disabled>
                                        <i class="ti ti-heart me-2"></i>@lang('Donate Now')
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="campaign-summary" data-aos="fade-up" data-aos-duration="1500">
                        <!-- Campaign Info Card -->
                        <div class="post-sidebar__card mb-4">
                            <h3 class="post-sidebar__card__header">@lang('Campaign Summary')</h3>
                            <div class="post-sidebar__card__body">
                                <div class="campaign-info">
                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $campaignData->image) }}" alt="{{ $campaignData->name }}" class="campaign-image mb-3">
                                    <h3>@lang('You\'re supporting') <strong>OKK {{ $campaignData->name }}</strong></h3>
                                    <p class="campaign-description">{{ strLimit($campaignData->description, 100) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Card -->
                        <div class="post-sidebar__card mb-4">
                            <h3 class="post-sidebar__card__header">@lang('Progress')</h3>
                            <div class="post-sidebar__card__body">
                                @php
                                    $percentage = donationPercentage($campaignData->goal_amount, $campaignData->raised_amount);
                                @endphp

                                <div class="progress custom--progress my-4" role="progressbar" aria-label="Campaign Progress" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" style="width: {{ $percentage }}%"><span class="progress-txt">{{ $percentage . '%' }}</span></div>
                                </div>
                                
                                <ul class="campaign-status">
                                    <li>
                                        <span><i class="ti ti-cash-register"></i> @lang('Goal'):</span> {{ $setting->cur_sym . showAmount(@$campaignData->goal_amount) }}
                                    </li>
                                    <li>
                                        <span><i class="ti ti-building-bank"></i> @lang('Raised'):</span> {{ $setting->cur_sym . showAmount(@$campaignData->raised_amount) }}
                                    </li>
                                    <li>
                                        <span><i class="ti ti-users"></i> @lang('Donors'):</span> {{ $campaignData->donors_count ?? 0 }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Time Left Card -->
                        <div class="post-sidebar__card">
                            <h3 class="post-sidebar__card__header">@lang('Time Left')</h3>
                            <div class="post-sidebar__card__body">
                                <div class="campaign__countdown" data-target-date="{{ showDateTime(@$campaignData->end_date, 'Y-m-d\TH:i:s') }}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            line-height: 1.6;
            color: #333;
        }

        /* Header */
        .main-header {
            background: #05ce78;
            padding: 15px 0;
            margin: 0;
            border: none;
        }

        .main-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        .logo {
            text-align: center;
        }

        .logo-img {
            height: 35px;
            width: auto;
        }

        .signin-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .signin-link:hover {
            color: #fff;
            text-decoration: underline;
        }

        /* Main Container */
        .payment-container {
            max-width: 40.9375rem;
            margin: 40px auto;
            padding: 0 20px;
            width: 100%;
        }

        .payment-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            padding: 40px 35px;
            margin-bottom: 30px;
            border: 1px solid #f0f0f0;
            width: 100%;
            box-sizing: border-box;
        }

        /* Fundraiser Info */
        .fundraiser-info {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .fundraiser-image {
            width: 100px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .fundraiser-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .fundraiser-details h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #232323;
        }

        .fundraiser-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        /* Donation Amount */
        .donation-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #232323;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }

        .amount-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 25px;
            width: 100%;
        }

        .amount-btn {
            padding: 12px 8px;
            border: 2px solid #e9ecef;
            background: #fff;
            border-radius: 10px;
            font-weight: 600;
            color: #333;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.95rem;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            box-sizing: border-box;
        }

        .amount-btn:hover {
            border-color: #05ce78;
            background: #f8fffe;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(5, 206, 120, 0.15);
        }

        .amount-btn.selected {
            border-color: #05ce78;
            background: #05ce78;
            color: #fff;
            box-shadow: 0 2px 8px rgba(5, 206, 120, 0.3);
        }

        .custom-amount {
            position: relative;
            margin-bottom: 20px;
        }

        .custom-amount input {
            width: 100%;
            padding: 20px 20px 20px 65px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 3.5rem;
            font-weight: 900;
            color: #232323;
            background: #fff;
            text-align: right;
            box-sizing: border-box;
            line-height: 1.2;
        }

        .custom-amount input:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        .custom-amount input::placeholder {
            color: #999;
            font-weight: 400;
        }

        .currency-symbol {
            position: absolute;
            left: 22px;
            top: 40%;
            transform: translateY(-50%);
            font-weight: 900;
            color: #232323;
            font-size: 1.6rem;
            line-height: 1;
        }

        .currency-label {
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(10px);
            font-size: 0.65rem;
            color: #232323;
            font-weight: 400;
            line-height: 1;
        }

        /* Tip Section */
        .tip-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .tip-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .tip-slider {
            width: 100%;
            margin-bottom: 15px;
            -webkit-appearance: none;
            appearance: none;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            outline: none;
        }

        .tip-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #05ce78;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tip-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #05ce78;
            border-radius: 50%;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tip-value {
            display: block;
            width: 100px;
            margin: 0 auto;
            padding: 10px 16px;
            background: #fff;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-weight: 600;
            color: #232323;
            font-size: 1rem;
            min-width: 80px;
            text-align: center;
        }

        .custom-tip-link {
            color: #05ce78;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .custom-tip-link:hover {
            text-decoration: underline;
        }

        /* Payment Methods */
        .payment-methods {
            margin-bottom: 30px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            box-sizing: border-box;
        }

        .payment-option:hover {
            border-color: #05ce78;
        }

        .payment-option.selected {
            border-color: #05ce78;
            background: #f8fffe;
        }

        .payment-radio {
            width: 20px;
            height: 20px;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            margin-right: 15px;
            position: relative;
        }

        .payment-option.selected .payment-radio {
            border-color: #05ce78;
            background: #05ce78;
        }

        .payment-option.selected .payment-radio::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
        }

        .payment-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-logo {
            width: 24px;
            height: 24px;
            background: #f0f0f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        .payment-text {
            font-weight: 500;
            color: #232323;
        }

        /* Checkboxes */
        .checkbox-section {
            margin-bottom: 30px;
        }

        .checkbox-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            width: 100%;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #e9ecef;
            border-radius: 3px;
            margin-right: 12px;
            margin-top: 2px;
            cursor: pointer;
            position: relative;
        }

        .checkbox.checked {
            background: #05ce78;
            border-color: #05ce78;
        }

        .checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-label {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
        }

        .info-icon {
            color: #05ce78;
            margin-left: 5px;
            font-size: 12px;
        }

        /* Summary */
        .donation-summary {
            margin-bottom: 30px;
            width: 100%;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            width: 100%;
        }

        .summary-label {
            color: #666;
            font-size: 0.9rem;
        }

        .summary-value {
            font-weight: 600;
            color: #232323;
        }

        .summary-total {
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
            margin-top: 10px;
        }

        .summary-total .summary-label,
        .summary-total .summary-value {
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Payment Button */
        .payment-button {
            width: 100%;
            padding: 18px 20px;
            background: #232323;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
            box-sizing: border-box;
            min-height: 60px;
        }

        .payment-button:hover {
            background: #1a1a1a;
        }

        .payment-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Legal Text */
        .legal-text {
            font-size: 0.8rem;
            color: #999;
            text-align: center;
            line-height: 1.4;
        }

        .legal-text a {
            color: #05ce78;
            text-decoration: none;
        }

        .legal-text a:hover {
            text-decoration: underline;
        }

        /* Protection Section */
        .protection-section {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
        }

        .protection-icon {
            width: 50px;
            height: 50px;
            background: #05ce78;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #fff;
            font-size: 20px;
        }

        .protection-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #232323;
            margin-bottom: 10px;
        }

        .protection-text {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .protection-text a {
            color: #05ce78;
            text-decoration: none;
        }

        .protection-text a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .payment-container {
                margin: 20px auto;
                padding: 0 15px;
                max-width: 100%;
            }

            .payment-card {
                padding: 30px 25px;
            }

            .amount-buttons {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .amount-btn {
                padding: 10px 6px;
                font-size: 0.9rem;
                min-height: 40px;
            }

            .custom-amount input {
                padding: 18px 18px 18px 55px;
                font-size: 1.3rem;
            }

            .currency-symbol {
                left: 20px;
                font-size: 1.3rem;
            }

            .currency-label {
                left: 20px;
                font-size: 0.6rem;
                transform: translateY(8px);
            }
        }

        @media (max-width: 480px) {
            .payment-container {
                padding: 0 10px;
            }

            .payment-card {
                padding: 25px 20px;
            }

            .amount-buttons {
                grid-template-columns: repeat(2, 1fr);
                gap: 6px;
            }

            .amount-btn {
                padding: 8px 4px;
                font-size: 0.85rem;
                min-height: 35px;
            }
        }

        /* Original styles for compatibility */
        .donation-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .donation-form-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }
        
        .campaign-summary {
            position: sticky;
            top: 20px;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .amount-options {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }
        
        .amount-option {
            background: white;
            border-radius: 8px;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .amount-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .gateway-options {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }
        
        .gateway-option {
            background: white;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .gateway-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .gateway-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .campaign-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .campaign-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .campaign-description {
            color: #666;
            font-size: 0.9rem;
        }
        
        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .payment-preview {
            background: #e8f5e8;
            border-radius: 10px;
            padding: 1rem;
            border: 2px solid #28a745;
        }
    </style>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            let selectedAmount = 0;
            let tipPercentage = 0;
            let selectedGateway = null;

            // Amount button selection
            $('.amount-btn').on('click', function() {
                $('.amount-btn').removeClass('selected');
                $(this).addClass('selected');
                
                const amount = parseFloat($(this).data('amount')) || 0;
                selectedAmount = amount;
                $('#customAmount').val(amount.toFixed(2));
                $('#amountInput').val(amount.toFixed(2));
                
                updateSummary();
                checkFormValidity();
            });

            // Custom amount input
            $('#customAmount').on('input', function() {
                const amount = parseFloat($(this).val()) || 0;
                selectedAmount = amount;
                $('#amountInput').val(amount.toFixed(2));
                
                // Remove selection from amount buttons when typing custom amount
                $('.amount-btn').removeClass('selected');
                
                updateSummary();
                checkFormValidity();
            });

            // Tip slider
            $('#tipSlider').on('input', function() {
                tipPercentage = parseFloat($(this).val()) || 0;
                $('#tipValue').text(tipPercentage + '%');
                updateSummary();
            });

            // Custom tip link
            $('#customTipLink').on('click', function(e) {
                e.preventDefault();
                const customTip = prompt('Enter custom tip percentage (0-25):', tipPercentage);
                if (customTip !== null && !isNaN(customTip) && customTip >= 0 && customTip <= 25) {
                    tipPercentage = parseFloat(customTip);
                    $('#tipSlider').val(tipPercentage);
                    $('#tipValue').text(tipPercentage + '%');
                    updateSummary();
                }
            });

            // Payment method selection
            $('.payment-option').on('click', function() {
                $('.payment-option').removeClass('selected');
                $(this).addClass('selected');
                
                selectedGateway = $(this).data('method');
                $('input[name="gateway"]').val(selectedGateway);
                $('input[name="currency"]').val($(this).data('currency'));
                
                checkFormValidity();
            });

            // Privacy checkbox
            $('#privacyCheckbox').on('click', function() {
                $(this).toggleClass('checked');
                const isAnonymous = $(this).hasClass('checked');
                
                if (isAnonymous) {
                    $('#personalInfoSection').slideUp();
                } else {
                    $('#personalInfoSection').slideDown();
                }
            });

            // Marketing checkbox
            $('#marketingCheckbox').on('click', function() {
                $(this).toggleClass('checked');
            });

            // Update summary calculations
            function updateSummary() {
                const tipAmount = (selectedAmount * tipPercentage) / 100;
                const totalAmount = selectedAmount + tipAmount;
                
                $('#donationAmount').text('{{ $setting->cur_sym }}' + selectedAmount.toFixed(2));
                $('#tipAmount').text('{{ $setting->cur_sym }}' + tipAmount.toFixed(2));
                $('#totalAmount').text('{{ $setting->cur_sym }}' + totalAmount.toFixed(2));
            }

            // Check if form is valid
            function checkFormValidity() {
                const amount = parseFloat($('#customAmount').val()) || 0;
                const hasSelectedGateway = selectedGateway !== null;
                
                if (amount > 0 && hasSelectedGateway) {
                    $('#paymentButton').prop('disabled', false);
                } else {
                    $('#paymentButton').prop('disabled', true);
                }
            }

            // Form submission
            $('#donationForm').on('submit', function(e) {
                const amount = parseFloat($('#customAmount').val()) || 0;
                const isAnonymous = $('#privacyCheckbox').hasClass('checked');
                
                if (amount <= 0) {
                    e.preventDefault();
                    alert('@lang("Please select a valid amount")');
                    return false;
                }
                
                if (!selectedGateway) {
                    e.preventDefault();
                    alert('@lang("Please select a payment method")');
                    return false;
                }

                // Add tip amount to form if tip is selected
                if (tipPercentage > 0) {
                    const tipAmount = (amount * tipPercentage) / 100;
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'tip_amount',
                        value: tipAmount.toFixed(2)
                    }).appendTo('#donationForm');
                }

                // Add anonymous flag if checked
                if (isAnonymous) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'anonymous_donation',
                        value: '1'
                    }).appendTo('#donationForm');
                }

                // Add marketing preference if checked
                if ($('#marketingCheckbox').hasClass('checked')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'marketing_updates',
                        value: '1'
                    }).appendTo('#donationForm');
                }

                // Update the amount input before submission
                $('#amountInput').val(amount.toFixed(2));
            });

            // Initialize form state
            updateSummary();
            checkFormValidity();
        });
    </script>
@endpush 