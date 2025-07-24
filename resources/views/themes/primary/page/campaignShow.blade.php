@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="donation-details pt-120 pb-60">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    @include($activeTheme . 'partials.basicCampaignShow')

                    <div class="related-campaign">
                        <h2 class="donation-details__title" data-aos="fade-up" data-aos-duration="1500">@lang('Related Campaigns')</h2>
                        <div class="row g-4">
                            @forelse ($relatedCampaigns as $campaign)
                                <div class="col-md-6" data-aos="fade-up" data-aos-duration="1500">
                                    <div class="campaign-card">
                                        @include($activeTheme . 'partials.basicCampaign')
                                    </div>
                                </div>
                            @empty
                                <div class="col-12" data-aos="fade-up" data-aos-duration="1500">
                                    <p class="text-center">{{ __($emptyMessage) }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="post-sidebar">
                        @include($activeTheme . 'partials.basicCampaignSidebar')

                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Make a Donation')</h3>
                            <div class="post-sidebar__card__body">
                                <form action="{{ route('user.deposit.insert', $campaignData->slug) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="currency">

                                    @auth
                                        <input type="hidden" name="country" value="{{ @$authUser->country_name }}">
                                    @endauth

                                    <div class="form-group">
                                        <div class="form--check">
                                            <input type="checkbox" class="form-check-input" name="anonymousDonation" @checked(old('anonymousDonation')) id="anonymousDonation">
                                            <label class="form-check-label" for="anonymousDonation">
                                                @lang('Donate as anonymous')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group anonymous-alert-text d-none">
                                        <div class="alert alert-info" role="alert">
                                            @lang('We require your information even if you choose to donate anonymously. However, rest assured that your details will not be displayed anywhere in our system.')
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label required">@lang('Full Name')</label>
                                        <input type="text" class="form--control" name="full_name" value="{{ old('full_name', @$authUser->fullname) }}" placeholder="@lang('John Doe')" @readonly(@$authUser)>
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label required">@lang('Email')</label>
                                        <input type="email" class="form--control" name="email" value="{{ old('email', @$authUser->email) }}" placeholder="@lang('example@example.com')" @readonly(@$authUser)>
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label required">@lang('Country')</label>
                                        <select class="form--control form-select select-2" name="country" @disabled(@$authUser)>
                                            @foreach ($countries as $key => $country)
                                                <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}" @selected(old('country', @$authUser->country_name) == @$country->country)>
                                                    {{ __(@$country->country) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label required">@lang('Phone')</label>
                                        <input type="hidden" name="mobile_code">
                                        
                                        @if ($authUser)
                                            <input type="text" class="form--control" name="phone" value="{{ old('phone', @$authUser->mobile) }}" placeholder="@lang('+0123 456 789')" @readonly(@$authUser)>
                                        @else
                                            <div class="input--group">
                                                <span class="input-group-text input-group-text-light mobile-code"></span>
                                                <input type="number" class="form--control checkUser" name="phone" value="{{ old('phone') }}" required>
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="post-sidebar__card__subtitle mt-4">@lang('Choose an amount')</h4>
                                    <div class="form-group">
                                        <div class="input--group">
                                            <span class="input-group-text">{{ $setting->cur_sym }}</span>
                                            <input type="number" step="any" min="0" class="form--control" id="donationAmount" name="amount" value="{{ old('amount') }}" placeholder="0" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach ($campaignData->preferred_amounts as $preferredAmount)
                                                <div class="form--radio">
                                                    <input type="radio" class="form-check-input" id="{{ 'donationAmount_' . $loop->iteration }}" name="donationAmount" data-amount="{{ $preferredAmount }}">
                                                    <label class="form-check-label" for="{{ 'donationAmount_' . $loop->iteration }}">
                                                        {{ $setting->cur_sym . $preferredAmount }}
                                                    </label>
                                                </div>
                                            @endforeach

                                            <div class="form--radio">
                                                <input type="radio" class="form-check-input" id="customDonationAmount" name="donationAmount">
                                                <label class="form-check-label" for="customDonationAmount">
                                                    @lang('Custom')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label required">@lang('Gateway')</label>
                                        <select class="form--control form-select select-2" name="gateway">
                                            <option selected disabled>@lang('Select Gateway')</option>

                                            @foreach ($gatewayCurrencies as $data)
                                                <option value="{{ $data->method_code }}" @selected(old('gateway') == $data->method_code) data-gateway="{{ $data }}">
                                                    {{ __(@$data->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mt-4 mb-3 preview-details d-none">
                                        <ul class="list-group">
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
                                    <button type="submit" class="btn btn--base w-100 mt-2">@lang('Donate Now')</button>
                                </form>
                            </div>
                        </div>
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Kindness in Action')</h3>
                            <div class="post-sidebar__card__body">
                                <ul class="d-flex flex-column gap-3">
                                    @forelse ($donations as $donation)
                                        <li>
                                            <div class="donor__card">
                                                <span class="donor__number"><i class="ti ti-user"></i></span>
                                                <span class="donor__txt">
                                                    <span class="donor__name">{{ __($donation->donorName) }}</span>
                                                    <span class="donor__amount">{{ $setting->cur_sym . showAmount($donation->amount) }}</span>
                                                </span>
                                            </div>
                                        </li>
                                    @empty
                                        <li>
                                            {{ __($emptyMessage) }}
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/select2.css') }}">
@endpush

@push('page-style')
    <style>
        .anonymous-alert-text .alert {
            background-color: #cff4fc !important;
        }

        .select2-results__option {
            padding-left: 16px;
        }
    </style>
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/select2.js') }}"></script>
@endpush

@push('page-script')
    <script>
        (function ($) {
            'use strict'

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('#anonymousDonation').on('change', function () {
                if ($(this).is(':checked')) {
                    $('.anonymous-alert-text').removeClass('d-none')
                } else {
                    $('.anonymous-alert-text').addClass('d-none')
                }
            })

            $('.select-2').select2({
                containerCssClass: ':all:',
            })

            $('[name=gateway]').change(function() {
                if (!$('[name=gateway]').val()) {
                    $('.preview-details').addClass('d-none')

                    return false
                }

                var resource       = $('[name=gateway] option:selected').data('gateway')
                var fixed_charge   = parseFloat(resource.fixed_charge)
                var percent_charge = parseFloat(resource.percent_charge)
                var rate           = parseFloat(resource.rate)

                $('.min').text(parseFloat(resource.min_amount).toFixed(2))
                $('.max').text(parseFloat(resource.max_amount).toFixed(2))

                var amount = parseFloat($('[name=amount]').val())

                if (!amount) amount = 0

                if (amount <= 0) {
                    $('.preview-details').addClass('d-none')

                    return false
                }

                $('.preview-details').removeClass('d-none')

                var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2)
                $('.charge').text(charge)

                var payable = parseFloat(parseFloat(amount) + parseFloat(charge)).toFixed(2)
                $('.payable').text(payable)

                var final_amount = (parseFloat(parseFloat(amount) + parseFloat(charge)) * rate).toFixed(2)
                $('.final_amount').text(final_amount)

                if (resource.currency != '{{ $setting->site_cur }}') {
                    var rateElement = `<span>@lang('Conversion Rate')</span> <span class="fw-bold">1 {{ __($setting->site_cur) }} = <span class="rate">${rate}</span> <span class="method_currency">${resource.currency}</span></span>`;

                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none')
                    $('.rate-element').addClass('d-flex')
                    $('.in-site-cur').removeClass('d-none')
                    $('.in-site-cur').addClass('d-flex')
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none')
                    $('.rate-element').removeClass('d-flex')
                    $('.in-site-cur').addClass('d-none')
                    $('.in-site-cur').removeClass('d-flex')
                }

                $('.method_currency').text(resource.currency)
                $('[name=currency]').val(resource.currency)
            })

            $('[name=amount]').on('input', function() {
                $('[name=gateway]').change()
            })

            $(document).on('change', '[name=donationAmount]', function () {
                $('[name=gateway]').change()
            })
        })(jQuery)
    </script>
@endpush
