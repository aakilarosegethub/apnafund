
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
                <img src="{{ getImage(getFilePath('campaign') . '/' . $campaignData->image, getFileSize('campaign')) }}" alt="{{ $campaignData->name }}" class="campaign-image mb-3">
                </div>
                <div class="fundraiser-details">
                <h3>@lang('You\'re supporting') <strong>{{ $campaignData->name }}</strong></h3>
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

            <!-- Country Selection (Hidden by default) -->
            <div class="country-selection-section d-none" id="countrySelectionSection">
                <h3 class="section-title">@lang('Select Your Country')</h3>
                <p class="section-description">@lang('Choose your country to see available payment methods')</p>
                
                @if (!$authUser)
                    <div class="row g-3 mb-4">
                        <div class="col-lg-6">
                            <label class="form--label required">@lang('Country')</label>
                            @php
                                $detectedCountry = session('user_country') ?? detectUserCountry() ?? null;
                            @endphp
                            <select class="form--control" name="country" id="countrySelect">
                                <option value="">@lang('All Countries')</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->country }}" @selected(old('country') == $country->country)>
                                        {{ $country->country }}
                                        @if($detectedCountry == $country->country)
                                            ({{ count($gatewayCurrencies) }} @lang('methods available'))
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                
                <!-- Country Status Message -->
                @if($gatewayCurrencies && count($gatewayCurrencies) > 0)
                    @if(session('user_country'))
                        <div class="alert alert-success mb-3">
                            <i class="ti ti-check"></i>
                            @lang('Found') <strong>{{ count($gatewayCurrencies) }}</strong> @lang('payment methods available in') <strong>{{ session('user_country') }}</strong>
                            <a href="#" onclick="showCountrySelection()" class="text-decoration-underline ms-2">@lang('Change country')</a>
                        </div>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="ti ti-globe"></i>
                            @lang('Showing') <strong>{{ count($gatewayCurrencies) }}</strong> @lang('payment methods available in') <strong>@lang('All Countries')</strong>
                            <a href="#" onclick="showCountrySelection()" class="text-decoration-underline ms-2">@lang('Select specific country')</a>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning mb-3">
                        <i class="ti ti-alert-triangle"></i>
                        @lang('No payment methods available for') <strong>{{ session('user_country') ?? 'selected country' }}</strong>
                        <a href="#" onclick="showCountrySelection()" class="text-decoration-underline ms-2">@lang('Try different country')</a>
                    </div>
                @endif
            </div>

            <!-- Hidden Country Field for Form Submission -->
            <input type="hidden" name="selected_country" value="{{ session('user_country') ?? '' }}">

            <!-- Payment Methods (Only show if gateways available) -->
            @if($gatewayCurrencies && count($gatewayCurrencies) > 0)
                <div class="payment-methods">
                    <h3 class="section-title">@lang('Payment method')</h3>
                    
                    @foreach ($gatewayCurrencies as $gatewayCurrency)
                        <div class="payment-option" 
                             data-method="{{ $gatewayCurrency->method_code }}" 
                             data-currency="{{ $gatewayCurrency->currency }}"
                             data-gateway="{{ json_encode($gatewayCurrency) }}">
                            <div class="payment-radio"></div>
                            <div class="payment-info">
                                <div class="payment-logo">{{ strtoupper(substr($gatewayCurrency->method->name, 0, 2)) }}</div>
                                <span class="payment-text">{{ __($gatewayCurrency->method->name) }} ({{ strtoupper($gatewayCurrency->currency) }})</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

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
                            @php
                                $detectedCountry = detectUserCountry();
                                $countryCode = $detectedCountry ? getCountryCode($detectedCountry) : null;
                                $phonePlaceholder = $countryCode ? getPhonePlaceholder($countryCode) : '@lang("Enter your phone number")';
                            @endphp
                                                <div class="input--group">
                                                    <span class="input-group-text input-group-text-light mobile-code"></span>
                                <input type="tel" class="form--control checkUser phone-input" name="phone" value="{{ old('phone') }}" placeholder="{{ $phonePlaceholder }}" data-country-code="{{ $countryCode }}" required>
                                                </div>
                            <small class="form-text text-muted phone-help">@lang('Format will be applied based on your country')</small>
                                            @endif
                                        </div>
                    <div class="col-lg-3 col-md-6">
                                            <label class="form--label required">@lang('Country')</label>
                                            @if ($authUser)
                                                <input type="text" class="form--control" name="country" value="{{ old('country', @$authUser->country_name) }}" @readonly(@$authUser) required>
                                            @else
                            <input type="text" class="form--control" name="country_display" value="{{ session('user_country') ?? 'All Countries' }}" readonly>
                            <input type="hidden" name="country" value="{{ session('user_country') ?? '' }}">
                            @if(session('user_country'))
                                <small class="form-text text-muted">
                                    <a href="#" onclick="showCountrySelection()" class="text-decoration-underline">@lang('Change country')</a>
                                </small>
                            @endif
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
        
        .country-selection-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 2px solid #e9ecef;
        }
        
        .country-selection-section .section-description {
            color: #6c757d;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .highlight-dropdown {
            border: 2px solid #007bff !important;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3) !important;
            transition: all 0.3s ease;
        }
    </style>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            let selectedAmount = 0;
            let tipPercentage = 0;
            let selectedGateway = null;
            
            // Function to show country selection
            window.showCountrySelection = function() {
                // Show the country selection section
                $('#countrySelectionSection').removeClass('d-none').show();
                
                // Focus on the country dropdown and scroll to it
                $('#countrySelect').focus();
                $('html, body').animate({
                    scrollTop: $('#countrySelectionSection').offset().top - 100
                }, 500);
                
                // Add a visual highlight to the dropdown
                $('#countrySelect').addClass('highlight-dropdown');
                setTimeout(function() {
                    $('#countrySelect').removeClass('highlight-dropdown');
                }, 2000);
                
                console.log('Country selection function called');
            };

            // Function to change country
            window.changeCountry = function() {
                $('#countrySelect').focus();
                $('html, body').animate({
                    scrollTop: $('#countrySelect').offset().top - 100
                }, 500);
            };
            
            // Country selection change handler - simple approach
            $('#countrySelect').on('change', function() {
                const selectedCountry = $(this).val();
                console.log('Country changed to:', selectedCountry);
                
                // Show apply button
                if (!$('#applyCountryBtn').length) {
                    // $('#countrySelect').after('<button type="button" id="applyCountryBtn" class="btn btn-primary mt-2">Apply Country Selection</button>');
                }
            });
            
            // Apply country selection
            // Now handle country selection on change instead of button click
            $('#countrySelect').on('change', function() {
                const selectedCountry = $(this).val();

                // Remove apply button if it exists (no longer needed)
                $('#applyCountryBtn').remove();

                // Update the hidden country field for form submission
                $('input[name="selected_country"]').val(selectedCountry);

                if (selectedCountry) {
                    // Update session and reload page
                    $.ajax({
                        url: '{{ route("update.user.country") }}',
                        method: 'POST',
                        data: {
                            country: selectedCountry,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('Country updated, reloading page...');
                            window.location.reload();
                        },
                        error: function(xhr) {
                            console.log('Failed to update country in session');
                            alert('Failed to update country. Please try again.');
                        }
                    });
                } else {
                    // Clear country selection
                    $.ajax({
                        url: '{{ route("update.user.country") }}',
                        method: 'POST',
                        data: {
                            country: '',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('All Countries selected, reloading page...');
                            window.location.reload();
                        },
                        error: function(xhr) {
                            console.log('Failed to clear country in session');
                            alert('Failed to clear country. Please try again.');
                        }
                    });
                }
            });

            // Function to hide country selection section
            window.hideCountrySelection = function() {
                $('#countrySelectionSection').addClass('d-none').hide();
            };

            // Flag to prevent change event on page load
            let pageLoaded = false;
            
            // Initialize country selection section (hidden by default)
            $(document).ready(function() {
                // Hide country selection section by default
                $('#countrySelectionSection').addClass('d-none').hide();
                
                // Debug: Log country selection status
                console.log('Country selection section is hidden by default');
                console.log('Detected country:', '{{ $detectedCountry ?? "None" }}');
                console.log('Session country:', '{{ session("user_country") ?? "None" }}');
                console.log('Available gateways:', {{ count($gatewayCurrencies) }});
                
                // Test click handlers
                $('a[onclick*="showCountrySelection"]').on('click', function(e) {
                    console.log('Change country link clicked');
                    e.preventDefault();
                    showCountrySelection();
                });
                
                // Set flag after page is loaded
                setTimeout(function() {
                    pageLoaded = true;
                }, 1000);
            });

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



            // Initialize form state
            updateSummary();
            checkFormValidity();

            // Phone validation functions
            function isValidPhone(phone) {
                const cleanPhone = phone.replace(/\D/g, '');
                if (cleanPhone.length < 10) return false;
                if (cleanPhone.length > 15) return false;
                const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
                return phoneRegex.test(phone);
            }

            function formatPhoneNumber(phone, countryCode) {
                const cleanPhone = phone.replace(/\D/g, '');
                
                switch(countryCode) {
                    case 'PK': // Pakistan
                        if (cleanPhone.length === 11 && cleanPhone.startsWith('03')) {
                            return `+92 ${cleanPhone.slice(1,4)} ${cleanPhone.slice(4,7)} ${cleanPhone.slice(7)}`;
                        } else if (cleanPhone.length === 10 && cleanPhone.startsWith('3')) {
                            return `+92 ${cleanPhone.slice(0,3)} ${cleanPhone.slice(3,6)} ${cleanPhone.slice(6)}`;
                        } else if (cleanPhone.length === 12 && cleanPhone.startsWith('92')) {
                            return `+${cleanPhone.slice(0,2)} ${cleanPhone.slice(2,5)} ${cleanPhone.slice(5,8)} ${cleanPhone.slice(8)}`;
                        }
                        break;
                        
                    case 'US': // United States
                        if (cleanPhone.length === 10) {
                            return `(${cleanPhone.slice(0,3)}) ${cleanPhone.slice(3,6)}-${cleanPhone.slice(6)}`;
                        } else if (cleanPhone.length === 11 && cleanPhone.startsWith('1')) {
                            return `+1 (${cleanPhone.slice(1,4)}) ${cleanPhone.slice(4,7)}-${cleanPhone.slice(7)}`;
                        }
                        break;
                        
                    case 'GB': // United Kingdom
                        if (cleanPhone.length === 11 && cleanPhone.startsWith('0')) {
                            return `+44 ${cleanPhone.slice(1,5)} ${cleanPhone.slice(5,8)} ${cleanPhone.slice(8)}`;
                        } else if (cleanPhone.length === 12 && cleanPhone.startsWith('44')) {
                            return `+${cleanPhone.slice(0,2)} ${cleanPhone.slice(2,6)} ${cleanPhone.slice(6,9)} ${cleanPhone.slice(9)}`;
                        }
                        break;
                        
                    case 'IN': // India
                        if (cleanPhone.length === 10) {
                            return `+91 ${cleanPhone.slice(0,5)} ${cleanPhone.slice(5)}`;
                        } else if (cleanPhone.length === 12 && cleanPhone.startsWith('91')) {
                            return `+${cleanPhone.slice(0,2)} ${cleanPhone.slice(2,7)} ${cleanPhone.slice(7)}`;
                        }
                        break;
                        
                    default:
                        if (cleanPhone.length >= 10 && cleanPhone.length <= 15) {
                            return `+${cleanPhone}`;
                        }
                }
                return phone;
            }

            // Phone input validation
            const phoneInput = document.querySelector('.phone-input');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    value = value.replace(/[^\d\s\-\(\)\+]/g, '');
                    e.target.value = value;
                });
                
                phoneInput.addEventListener('blur', function(e) {
                    const countryCode = e.target.getAttribute('data-country-code');
                    if (e.target.value && isValidPhone(e.target.value)) {
                        e.target.style.borderColor = '#05ce78';
                        
                        if (countryCode) {
                            const formattedPhone = formatPhoneNumber(e.target.value, countryCode);
                            if (formattedPhone !== e.target.value) {
                                e.target.value = formattedPhone;
                            }
                        }
                    } else if (e.target.value) {
                        e.target.style.borderColor = '#dc3545';
                    }
                });
            }

            // Form submission - ensure phone is properly formatted
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

                // Ensure country field is populated
                const selectedCountry = $('#countrySelect').val();
                if (!selectedCountry) {
                    // If no country is selected, use the session country or default
                    const sessionCountry = '{{ session("user_country") ?? "" }}';
                    if (sessionCountry) {
                        $('#countrySelect').val(sessionCountry);
                    } else {
                        // Set a default country or leave empty for "All Countries"
                        $('#countrySelect').val('');
                    }
                }

                // Validate and format phone number before submission
                const phoneInput = $('input[name="phone"]');
                if (phoneInput.length && phoneInput.val()) {
                    const countryCode = phoneInput.attr('data-country-code');
                    const phoneValue = phoneInput.val();
                    
                    if (countryCode && isValidPhone(phoneValue)) {
                        const formattedPhone = formatPhoneNumber(phoneValue, countryCode);
                        phoneInput.val(formattedPhone);
                    }
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
        });
    </script>
@endpush 