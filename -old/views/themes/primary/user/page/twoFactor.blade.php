@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="custom--card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="two-fa-setting">
                                <h2 class="two-fa-setting__title">@lang('Two-factor Authentication')</h2>
                                <p>@lang('Two factor authentication provides extra protection for your account by requiring a special code.')</p>

                                @if(!auth()->user()->ts)
                                    <p><strong>@lang('Note'):</strong> @lang('You are only enabling two-factor authentication for an extra layer of security when you login.')</p>
                                @else
                                    <p><strong>@lang('Note'):</strong> @lang('You are disabling two-factor authentication and it will have no effect when you login.')</p>
                                @endif

                                <p>@lang('Have a smart phone? Use Google Authenticator')</p>

                                <div class="download-app">
                                    <a href=""https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank">
                                        <img src="{{ asset($activeThemeTrue . 'images/google-play.png') }}" alt="Google Play">
                                    </a>
                                    <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank">
                                        <img src="{{ asset($activeThemeTrue . 'images/app-store.png') }}" alt="Google Play">
                                    </a>
                                </div>

                                <p class="fw-semibold text--secondary"><em><small>@lang('Google Authenticator is a software-based authenticator by Google that implements two-step verification services using the Time-based One-time Password, for authenticating users of mobile applications by generating a six to eight-digit one-time password. It works with multiple accounts on a single device, allowing users to enable two-factor authentication (2FA) for various online accounts.')</small></em></p>

                                @if(!auth()->user()->ts)
                                    <p>@lang('To enable two factor authentication, scan the QR code on the right using Google Authenticator. When you have successfully scanned the QR code, enter the token from Google Authenticator into the "Google Authenticator OTP" field. We make sure you can generate tokens correctly before enabling two factor auth.')</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            @if(!auth()->user()->ts)
                                <div class="alert alert--base">
                                    <span class="alert__content w-100 ps-0"><small><strong>@lang('Use the QR code or setup key on your Google Authenticator app to add your account.')</strong></small></span>
                                </div>
                                <div class="qr-code-img">
                                    <img src="{{ $qrCodeUrl }}" alt="QR code">
                                </div>
                                <div class="account-setup-key">
                                    <div class="form-group mb-4">
                                        <label class="form--label">@lang('Setup Key')</label>
                                        <div class="input--group referral-link">
                                            <input type="text" class="form--control" id="accountSetupKey" name="key" value="{{ $secret }}" readonly>
                                            <span class="badge badge--success account-setup-key__badge">@lang('Copied')</span>
                                            <button class="btn btn--base account-setup-key__copy"><i class="ti ti-copy"></i></button>
                                        </div>
                                    </div>
                                    <form class="verification-code-form" action="{{ route('user.twofactor.enable') }}" method="POST">
                                        @csrf
                                        <label class="form--label required" for="authenticatorOtp">@lang('Google Authenticator OTP')</label>
                                        <input type="hidden" name="key" value="{{ $secret }}">

                                        @include('partials.verificationCode')

                                        <button class="btn btn--base w-100 mt-3" type="submit">@lang('Submit')</button>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert--base">
                                    <span class="alert__content w-100 ps-0"><small><strong>@lang('To disable two factor authentication, enter the token from Google Authenticator into the "Google Authenticator OTP" field.')</strong></small></span>
                                </div>
                                <div class="account-setup-key mt-4">
                                    <form class="verification-code-form" action="{{ route('user.twofactor.disable') }}" method="POST">
                                        @csrf
                                        <label class="form--label required" for="authenticatorOtp">@lang('Google Authenticator OTP')</label>
                                        <input type="hidden" name="key" value="{{ $secret }}">

                                        @include('partials.verificationCode')

                                        <button class="btn btn--base w-100 mt-3" type="submit">@lang('Submit')</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
