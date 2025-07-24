@extends($activeTheme . 'layouts.app')

@section('content')
    <section class="account py-120">
        <div class="account__bg bg-img" data-background-image="{{ getImage('assets/images/site/login/' . @$loginContent->data_info->background_image, '1920x1080') }}"></div>
        <div class="container">
            <div class="row justify-content-md-between justify-content-center align-items-center">
                <div class="col-xl-6 col-lg-5 col-md-4">
                    <div class="account-thumb">
                        <img src="{{ getImage('assets/images/site/login/' . @$loginContent->data_info->image, '635x645') }}" alt="">
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6 col-md-7">
                    @include($activeTheme . 'partials.basicBackToHome')

                    <div class="account-form">
                        <div class="account-form__content mb-4">
                            <h3 class="account-form__title mb-2">{{ __(@$loginContent->data_info->form_heading) }}</h3>
                        </div>
                        <form action="" method="POST" class="verify-gcaptcha">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="form--label">@lang('Username or Email Address')</label>
                                    <input type="text" class="form--control" name="username" value="{{ old('username') }}" required>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="form--label">@lang('Password')</label>
                                    <div class="position-relative">
                                        <input id="your-password" type="password" class="form-control form--control" name="password" required>
                                        <span class="password-show-hide ti ti-eye toggle-password" id="#your-password"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <div class="d-flex flex-wrap justify-content-between">
                                        <div class="form--check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" @checked(old('remember'))>
                                            <label class="form-check-label" for="remember">@lang('Remember me')</label>
                                        </div>
                                        <a href="{{ route('user.password.request.form') }}" class="forgot-password text--base">
                                            @lang('Forgot Your Password?')
                                        </a>
                                    </div>
                                </div>

                                <x-captcha />

                                <div class="col-sm-12 form-group">
                                    <button type="submit" class="btn btn--base w-100" id="recaptcha">
                                        {{ __(@$loginContent->data_info->submit_button_text) }}
                                    </button>
                                </div>
                                <div class="col-sm-12">
                                    <div class="have-account text-center">
                                        <p class="have-account__text">@lang('Don\'t have any account?') <a href="{{ route('user.register') }}" class="have-account__link text--base">@lang('Create Account')</a></p>
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
