@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-lg-5">
                                    <ul class="user-profile-list">
                                        <li><span><i class="ti ti-user-filled"></i> @lang('Username')</span> {{ __($user->username) }}</li>
                                        <li><span><i class="ti ti-mail-filled"></i> @lang('Email')</span> {{ $user->email }}</li>
                                        <li><span><i class="ti ti-device-mobile-filled"></i> @lang('Mobile')</span> {{ $user->mobile }}</li>
                                        <li><span><i class="ti ti-world"></i> @lang('Country')</span> {{ __($user->country_name) }}</li>
                                        <li><span><i class="ti ti-map-pin-filled"></i> @lang('Address')</span> {{ @$user->address->address }}</li>
                                    </ul>
                                </div>
                                <div class="col-lg-7">
                                    <form action="" method="POST" class="row gx-4 gy-3">
                                        @csrf
                                        <div class="col-sm-12">
                                            <label class="form--label required">@lang('Current Password')</label>
                                            <input type="password" class="form--control" name="current_password" required>
                                        </div>
                                        <div class="col-sm-12">
                                            <label class="form--label required">@lang('New Password')</label>
                                            <input type="password" class="form--control @if ($setting->strong_pass) secure-password @endif" name="password" required>
                                        </div>
                                        <div class="col-sm-12">
                                            <label class="form--label required">@lang('Confirm Password')</label>
                                            <input type="password" class="form--control" name="password_confirmation" required>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn--base w-100 mt-2">@lang('Submit')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($setting->strong_pass)
    @push('page-style-lib')
        <link rel="stylesheet" href="{{ asset('assets/universal/css/strongPassword.css') }}">
    @endpush

    @push('page-script-lib')
        <script src="{{asset('assets/universal/js/strongPassword.js')}}"></script>
    @endpush
@endif
