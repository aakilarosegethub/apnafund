@extends($activeTheme . 'layouts.app')

@php $userBanContent = getSiteData('user_ban.content', true); @endphp

@section('content')
    <section class="account py-120">
        <div class="account__bg bg-img" data-background-image="{{ getImage('assets/images/site/user_ban/' . @$userBanContent->data_info->background_image, '1920x1080') }}"></div>
        <div class="container">
            <div class="row justify-content-md-between justify-content-center align-items-center">
                <div class="col-xl-6 col-lg-5 col-md-4">
                    <div class="account-thumb">
                        <img src="{{ getImage('assets/images/site/user_ban/' . @$userBanContent->data_info->image, '635x645') }}" alt="">
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6 col-md-7">
                    @include($activeTheme . 'partials.basicBackToHome')

                    <div class="account-form">
                        <div class="account-form__content mb-4">
                            <h3 class="account-form__title mb-2 text-danger">{{ __(@$userBanContent->data_info->form_heading) }}</h3>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="have-account text-left">
                                    <p class="have-account__text">
                                        <b>@lang('Reason'):</b> {{ $user->ban_reason }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
