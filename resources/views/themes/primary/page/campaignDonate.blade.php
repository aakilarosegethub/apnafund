@extends($activeTheme . 'layouts.frontend')

@section('frontend')
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
                                    <h4 class="campaign-title">{{ $campaignData->name }}</h4>
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
            // Anonymous donation toggle
            $('#anonymousDonation').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.anonymous-alert-text').removeClass('d-none');
                } else {
                    $('.anonymous-alert-text').addClass('d-none');
                }
            });

            // Amount selection
            $('input[name="donationAmount"]').on('change', function() {
                const amount = $(this).data('amount');
                const isCustom = $(this).attr('id') === 'customDonationAmount';
                
                if (isCustom) {
                    $('#donationAmount').prop('readonly', false).focus();
                } else {
                    $('#donationAmount').prop('readonly', true).val(amount);
                }
                
                updatePaymentPreview();
            });

            // Custom amount input
            $('#donationAmount').on('input', function() {
                updatePaymentPreview();
            });

            // Gateway selection
            $('input[name="gateway"]').on('change', function() {
                updatePaymentPreview();
            });

            function updatePaymentPreview() {
                const amount = parseFloat($('#donationAmount').val()) || 0;
                const gateway = $('input[name="gateway"]:checked');
                
                if (amount > 0 && gateway.length > 0) {
                    $('.payment-preview').removeClass('d-none');
                    $('.amount').text(amount.toFixed(2));
                    
                    // Here you would typically make an AJAX call to get gateway details
                    // For now, we'll show placeholder values
                    $('.min').text('1.00');
                    $('.max').text('10000.00');
                    $('.charge').text('0.00');
                    $('.payable').text(amount.toFixed(2));
                    
                    $('#donateBtn').prop('disabled', false);
                } else {
                    $('.payment-preview').addClass('d-none');
                    $('#donateBtn').prop('disabled', true);
                }
            }

            // Form validation
            $('#donationForm').on('submit', function(e) {
                const amount = parseFloat($('#donationAmount').val()) || 0;
                const gateway = $('input[name="gateway"]:checked');
                
                if (amount <= 0) {
                    e.preventDefault();
                    alert('Please select a valid amount');
                    return false;
                }
                
                if (gateway.length === 0) {
                    e.preventDefault();
                    alert('Please select a payment method');
                    return false;
                }
            });
        });
    </script>
@endpush 