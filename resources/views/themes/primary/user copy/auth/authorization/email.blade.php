@extends($activeTheme . 'layouts.app')

@php $emailConfirmContent = getSiteData('email_confirm.content', true); @endphp

@section('content')
    <section class="account py-120">
        <div class="account__bg bg-img" data-background-image="{{ getImage('assets/images/site/email_confirm/' . @$emailConfirmContent->data_info->background_image, '1920x1080') }}"></div>
        <div class="container">
            <div class="row justify-content-md-between justify-content-center align-items-center">
                <div class="col-xl-6 col-lg-5 col-md-4">
                    <div class="account-thumb">
                        <img src="{{ getImage('assets/images/site/email_confirm/' . @$emailConfirmContent->data_info->image, '635x645') }}" alt="">
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6 col-md-7">
                    @include($activeTheme . 'partials.basicBackToHome')

                    <div class="account-form">
                        <div class="account-form__content mb-4">
                            <h3 class="account-form__title mb-2">{{ __(@$emailConfirmContent->data_info->form_heading) }}</h3>
                        </div>
                        <form action="{{ route('user.verify.email') }}" method="POST" class="verification-code-form">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <div class="have-account text-left">
                                        <p class="have-account__text">
                                            @lang('A six-digit verification code has been sent to') <b>{{ showEmailAddress($user->email) }}</b>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    @include('partials.verificationCode')
                                </div>
                                <div class="col-sm-12 form-group">
                                    <button type="submit" class="btn btn--base w-100">
                                        {{ __(@$emailConfirmContent->data_info->submit_button_text) }}
                                    </button>
                                </div>
                                <div class="col-sm-12">
                                    <div class="have-account text-left">
                                        <p class="have-account__text">@lang('Please check including your') <b>@lang('spam')</b> @lang('folder. If not found, then you can use') <a href="{{ route('user.send.verify.code', 'email') }}" class="have-account__link text--base">@lang('Resend')</a></p>
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
