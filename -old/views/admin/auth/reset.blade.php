@extends('admin.layouts.app')
@section('content')
    <section class="account bg-img" data-background-image="{{ asset('assets/admin/images/account-bg.png') }}">
        <div class="account__form">
            <div class="account__form__container">
                <div class="account__top d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <a href="{{ route('home') }}" target="_blank"><img src="{{ getImage(getFilePath('logoFavicon').'/logo_dark.png') }}" alt="Logo"></a>
                    </div>
                </div>
                <div class="account__form__content">
                    <div class="account__form__thumb">
                        <img src="{{ asset('assets/admin/images/lock.gif') }}" alt="Hi">
                    </div>
                    <h3 class="account__form__title">{{ __($setting->site_name) }}</h3>
                    <p>@lang('Please reset your password')</p>
                </div>
                <form action="{{ route('admin.password.reset') }}" method="POST">
                    @csrf

                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="code" value="{{ $verCode }}">
                    
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <label for="your-password-new" class="form--label">@lang('New Password')</label>
                            <div class="position-relative">
                                <input id="your-password-new" type="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" class="form-control form--control @if (bs('strong_pass')) secure-password @endif" required>
                                <span class="password-show-hide ti ti-eye toggle-password" id="#your-password-new"></span>
                            </div>
                            @if (bs('strong_pass'))
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
                        <div class="col-sm-12 form-group">
                            <label for="your-password" class="form--label">@lang('Confirm Password')</label>
                            <div class="position-relative">
                                <input id="your-password" type="password" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" class="form-control form--control" required>
                                <span class="password-show-hide ti ti-eye toggle-password" id="#your-password"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <button type="submit" class="btn btn--base w-100">@lang('Reset')</button>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('admin.login') }}"><i class="ti ti-chevrons-left"></i> @lang('Back to login')</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@if (bs('strong_pass'))
    @push('page-style-lib')
        <link rel="stylesheet" href="{{ asset('assets/universal/css/strongPassword.css') }}">
    @endpush

    @push('page-script-lib')
        <script src="{{asset('assets/universal/js/strongPassword.js')}}"></script>
    @endpush
@endif
