@extends($activeTheme . 'layouts.app')

@section('content')
    <section class="account py-120">
        <div class="account__bg bg-img" data-background-image="{{ getImage('assets/images/site/password_reset/' . @$passwordResetContent->data_info->background_image, '1920x1080') }}"></div>
        <div class="container">
            <div class="row justify-content-md-between justify-content-center align-items-center">
                <div class="col-xl-6 col-lg-5 col-md-4">
                    <div class="account-thumb">
                        <img src="{{ getImage('assets/images/site/password_reset/' . @$passwordResetContent->data_info->image, '635x645') }}" alt="">
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6 col-md-7">
                    @include($activeTheme . 'partials.basicBackToHome')

                    <div class="account-form">
                        <div class="account-form__content mb-4">
                            <h3 class="account-form__title mb-2">{{ __(@$passwordResetContent->data_info->form_heading) }}</h3>
                        </div>
                        <form action="{{ route('user.password.reset') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="code" value="{{ $code }}">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="form--label required">@lang('Password')</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form--control @if ($setting->strong_pass) secure-password @endif" name="password" id="your-password" required>
                                        <span class="password-show-hide ti ti-eye toggle-password" id="#your-password"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="form--label required">@lang('Confirm Password')</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control form--control" name="password_confirmation" id="confirm-password" required>
                                        <span class="password-show-hide ti ti-eye toggle-password" id="#confirm-password"></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn--base w-100">
                                        {{ __(@$passwordResetContent->data_info->submit_button_text) }}
                                    </button>
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
