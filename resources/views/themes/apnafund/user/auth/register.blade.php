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
                        <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <div id="registerFormErrors" class="alert alert-danger py-2 px-3" style="display:none;" role="alert"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('First Name')</label>
                                    <input type="text" class="form--control register-field" name="firstname" id="firstname" value="{{ old('firstname') }}" required maxlength="{{ registrationNamePartMaxLength() }}" autocomplete="given-name">
                                    <small class="text-danger d-block mt-1 field-error" data-for="firstname"></small>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Last Name')</label>
                                    <input type="text" class="form--control register-field" name="lastname" id="lastname" value="{{ old('lastname') }}" required maxlength="{{ registrationNamePartMaxLength() }}" autocomplete="family-name">
                                    <small class="text-danger d-block mt-1 field-error" data-for="lastname"></small>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Username')</label>
                                    <input type="text" class="form--control checkUser" name="username" value="{{ old('username') }}" required>
                                    <small class="text-danger usernameExist"></small>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Email Address')</label>
                                    <input type="email" class="form--control checkUser register-field" name="email" id="email" value="{{ old('email') }}" required maxlength="191" autocomplete="email">
                                    <small class="text-danger emailExist"></small>
                                    <small class="text-danger d-block mt-1 field-error" data-for="email"></small>
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
                                        <input type="password" class="form-control form--control register-field @if ($setting->strong_pass) secure-password @endif" name="password" id="your-password" required minlength="{{ registrationPasswordMinLength() }}" maxlength="{{ registrationPasswordMaxLength() }}" autocomplete="new-password">
                                        <span class="password-show-hide ti ti-eye toggle-password" id="#your-password"></span>
                                    </div>
                                    <small class="text-danger d-block mt-1 field-error" data-for="password"></small>
                                    @if ($setting->strong_pass)
                                        <div class="password-requirements mt-2">
                                            <div class="password-requirement">
                                                <span class="requirement-icon capital">✗</span>
                                                <span class="requirement-text">@lang('At least one uppercase letter')</span>
                                            </div>
                                            <div class="password-requirement">
                                                <span class="requirement-icon lower">✗</span>
                                                <span class="requirement-text">@lang('At least one lowercase letter')</span>
                                            </div>
                                            <div class="password-requirement">
                                                <span class="requirement-icon number">✗</span>
                                                <span class="requirement-text">@lang('At least one number')</span>
                                            </div>
                                            <div class="password-requirement">
                                                <span class="requirement-icon special">✗</span>
                                                <span class="requirement-text">@lang('At least one special character')</span>
                                            </div>
                                            <div class="password-requirement">
                                                <span class="requirement-icon minimum">✗</span>
                                                <span class="requirement-text">@lang('At least 6 characters long')</span>
                                            </div>
                                        </div>
                                        <div class="password-strength">
                                            <div class="password-strength-bar"></div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Confirm Password')</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form--control" name="password_confirmation" id="confirm-password" required maxlength="{{ registrationPasswordMaxLength() }}">
                                        <span class="password-show-hide ti ti-eye toggle-password" id="#confirm-password"></span>
                                    </div>
                                </div>

                                @if ($setting->agree_policy)
                                    <div class="col-sm-12">
                                        <div class="form--check">
                                            <input type="checkbox" class="form-check-input" name="agree" id="agree" @checked(old('agree')) required>
                                            <label for="agree" class="form-check-label">@lang('I agree with') @if($policyPages && is_array($policyPages) && count($policyPages) > 0) @foreach ($policyPages as $policy) <a href="{{ route('policy.pages', [slug($policy->data_info->title), $policy->id]) }}" target="_blank">{{ __($policy->data_info->title) }}</a>@if (!$loop->last), @endif @endforeach @else @lang('terms and conditions') @endif</label>
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

    <script>
        (function($) {
            "use strict";

            const NAME_PART_MAX = {{ registrationNamePartMaxLength() }};
            const PASSWORD_MAX = {{ registrationPasswordMaxLength() }};
            const PASSWORD_MIN = {{ registrationPasswordMinLength() }};
            const WEAK_PASSWORDS = @json(\App\Constants\WeakPasswords::listForFrontend());
            const formErrors = document.getElementById('registerFormErrors');

            function showFieldError(field, message) {
                const el = document.querySelector('.field-error[data-for="' + field + '"]');
                if (el) {
                    el.textContent = message || '';
                }
            }

            function showFormErrors(messages) {
                if (!formErrors) {
                    return;
                }
                if (!messages.length) {
                    formErrors.style.display = 'none';
                    formErrors.innerHTML = '';
                    return;
                }
                formErrors.style.display = 'block';
                formErrors.innerHTML = '<ul class="mb-0 ps-3">' + messages.map(function(m) {
                    return '<li>' + m + '</li>';
                }).join('') + '</ul>';
            }

            function validatePassword(value, email, fullName) {
                const errors = [];
                if (!value) {
                    errors.push('Password is required.');
                    return errors;
                }
                if (value.length > PASSWORD_MAX) {
                    errors.push('Password must not exceed ' + PASSWORD_MAX + ' characters.');
                }
                if (value.length < PASSWORD_MIN) {
                    errors.push('Password must be at least ' + PASSWORD_MIN + ' characters.');
                }
                if (!/[A-Z]/.test(value)) errors.push('Password must include an uppercase letter.');
                if (!/[a-z]/.test(value)) errors.push('Password must include a lowercase letter.');
                if (!/[0-9]/.test(value)) errors.push('Password must include a number.');
                if (!/[^A-Za-z0-9]/.test(value)) errors.push('Password must include a special character.');
                const lower = value.toLowerCase();
                if (WEAK_PASSWORDS.indexOf(lower) !== -1) {
                    errors.push('Password is too common. Choose a stronger password.');
                }
                const emailLocal = (email || '').split('@')[0].toLowerCase();
                const nameParts = (fullName || '').toLowerCase().split(/\s+/).filter(Boolean);
                if (emailLocal && emailLocal.length >= 3 && lower.indexOf(emailLocal) !== -1) {
                    errors.push('Password must not contain your email.');
                }
                nameParts.forEach(function(part) {
                    if (part.length >= 3 && lower.indexOf(part) !== -1) {
                        errors.push('Password must not contain your name.');
                    }
                });
                return errors;
            }

            function runClientValidation() {
                const firstname = ($('#firstname').val() || '').trim();
                const lastname = ($('#lastname').val() || '').trim();
                const email = ($('#email').val() || '').trim();
                const password = ($('[name=password]').val() || '');
                const messages = [];

                showFieldError('firstname', '');
                showFieldError('lastname', '');
                showFieldError('email', '');
                showFieldError('password', '');

                if (firstname.length > NAME_PART_MAX) {
                    showFieldError('firstname', 'First name must not exceed ' + NAME_PART_MAX + ' characters.');
                    messages.push('First name is too long.');
                }
                if (lastname.length > NAME_PART_MAX) {
                    showFieldError('lastname', 'Last name must not exceed ' + NAME_PART_MAX + ' characters.');
                    messages.push('Last name is too long.');
                }
                if (email.length > 191) {
                    showFieldError('email', 'Email must not exceed 191 characters.');
                    messages.push('Email is too long.');
                }
                const passErrors = validatePassword(password, email, firstname + ' ' + lastname);
                if (passErrors.length) {
                    showFieldError('password', passErrors[0]);
                    messages = messages.concat(passErrors);
                }
                showFormErrors(messages);
                return messages.length === 0;
            }

            $('.register-field').on('input blur', function() {
                runClientValidation();
            });

            $('form.verify-gcaptcha').on('submit', function(e) {
                if (!runClientValidation()) {
                    e.preventDefault();
                    return false;
                }
            });

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
@endsection
