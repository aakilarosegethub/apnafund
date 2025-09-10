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

    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-7 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Contribution via') {{ __(@$gateway->name) }}</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.deposit.manual.update') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                                @csrf
                                <div class="text-center">
                                    <p class="fw-bold payment-preview-text">
                                        @lang('You have requested a donation of') <span class="text--base">{{ showAmount(@$deposit['amount']) . ' ' . __(@$setting->site_cur) }}</span>, @lang('Please pay') <span class="text--base">{{ showAmount(@$deposit['final_amount']) . ' ' . @$deposit['method_currency'] }}</span> @lang('for the successful payment.')
                                    </p>
                                    <h5 class="payment-preview-text mt-4 mb-1">@lang('Please follow the instruction below')</h5>
                                </div>

                                @php echo @$gateway->guideline @endphp

                                <x-phinix-form identifier="id" identifierValue="{{ @$gateway->form_id }}" />

                                <div class="col-12">
                                    <button type="submit" class="btn btn--base w-100 mt-2 payment-button">@lang('Pay Now')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .payment-preview-text {
            color: hsl(var(--black) / 0.6);
        }
    </style>
@endpush
