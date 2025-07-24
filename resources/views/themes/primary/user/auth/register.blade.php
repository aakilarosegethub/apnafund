@extends($activeTheme . 'layouts.app')

@section('content')
    <section class="account py-120">
        <div class="account__bg bg-img" data-background-image="{{ getImage('assets/images/site/register/' . @$registerContent->data_info->background_image, '1920x1080') }}"></div>
        <div class="container">
            <div class="row justify-content-md-between justify-content-center align-items-center">
                <div class="col-xl-6 col-lg-5 col-md-4">
                    <div class="account-thumb">
                        <img src="{{ getImage('assets/images/site/register/' . @$registerContent->data_info->image, '635x645') }}" alt="">
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6 col-md-7">
                    @include($activeTheme . 'partials.basicBackToHome')

                    <div class="account-form">
                        <div class="account-form__content mb-4">
                            <h3 class="account-form__title mb-2">{{ __(@$registerContent->data_info->form_heading) }}</h3>
                        </div>
                        <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha">
                            @csrf
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('First Name')</label>
                                    <input type="text" class="form--control" name="firstname" value="{{ old('firstname') }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Last Name')</label>
                                    <input type="text" class="form--control" name="lastname" value="{{ old('lastname') }}" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Username')</label>
                                    <input type="text" class="form--control checkUser" name="username" value="{{ old('username') }}" required>
                                    <small class="text-danger usernameExist"></small>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Email Address')</label>
                                    <input type="email" class="form--control checkUser" name="email" value="{{ old('email') }}" required>
                                    <small class="text-danger emailExist"></small>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Country')</label>
                                    <select name="country" class="form--control form-select" required>
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">
                                                {{ __(@$country->country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Phone')</label>
                                    <div class="input--group">
                                        <span class="input-group-text input-group-text-light mobile-code"></span>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        <input type="number" class="form--control checkUser" name="mobile" value="{{ old('mobile') }}" required>
                                    </div>
                                    <small class="text-danger mobileExist"></small>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Password')</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form--control @if ($setting->strong_pass) secure-password @endif" name="password" id="your-password" required>
                                        <span class="password-show-hide ti ti-eye toggle-password" id="#your-password"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Confirm Password')</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form--control" name="password_confirmation" id="confirm-password" required>
                                        <span class="password-show-hide ti ti-eye toggle-password" id="#confirm-password"></span>
                                    </div>
                                </div>

                                @if ($setting->agree_policy)
                                    <div class="col-sm-12">
                                        <div class="form--check">
                                            <input type="checkbox" class="form-check-input" name="agree" id="agree" @checked(old('agree')) required>
                                            <label for="agree" class="form-check-label">@lang('I agree with') @foreach ($policyPages as $policy) <a href="{{ route('policy.pages', [slug($policy->data_info->title), $policy->id]) }}" target="_blank">{{ __($policy->data_info->title) }}</a>@if (!$loop->last), @endif @endforeach</label>
                                        </div>
                                    </div>
                                @endif

                                <x-captcha />

                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn--base w-100" id="recaptcha">
                                        {{ __(@$registerContent->data_info->submit_button_text) }}
                                    </button>
                                </div>
                                <div class="col-sm-12">
                                    <div class="have-account text-center">
                                        <p class="have-account__text">@lang('Already have an account?') <a href="{{ route('user.login') }}" class="have-account__link text--base">@lang('Sign In')</a> @lang('here.')</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if ($setting->strong_pass)
    @push('page-style-lib')
        <link rel="stylesheet" href="{{ asset('assets/universal/css/strongPassword.css') }}">
    @endpush

    @push('page-script-lib')
        <script src="{{asset('assets/universal/js/strongPassword.js')}}"></script>
    @endpush
@endif

@push('page-script')
    <script>
        (function($) {
            "use strict";

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.check.user') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }

                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }

                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }

                $.post(url, data, function(response) {
                    if (response.data != false && (response.type == 'email' || response.type == 'username' || response.type == 'mobile')) {
                        $(`.${response.type}Exist`).text(`${response.type} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
