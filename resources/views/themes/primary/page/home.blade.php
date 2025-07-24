@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    @php
        $commonSliderImage = asset($activeThemeTrue . 'images/slider-img-shape-2.png');
        $commonShapeImage  = asset($activeThemeTrue . 'images/mask-shape-1.png');
    @endphp

    <section class="banner-section">
        <div class="banner-slider">
            @foreach ($bannerElements as $banner)
                <div class="banner-slider__slide bg-img" data-background-image="{{ getImage('assets/images/site/banner/' . @$banner->data_info->background_image, '1920x1080') }}">
                    <div class="container">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-lg-6 col-md-7">
                                <div class="banner-content">
                                    <h4 class="banner-content__subtitle">{{ __(@$banner->data_info->title) }}</h4>
                                    <h1 class="banner-content__title">{{ __(@$banner->data_info->heading) }}</h1>
                                    <p class="banner-content__desc">{{ __(@$banner->data_info->short_description) }}</p>
                                    <div class="banner-content__button d-flex gap-3 flex-wrap">
                                        <a href="{{ @$banner->data_info->first_button_url }}" class="btn btn--base" target="_blank">
                                            {{ __(@$banner->data_info->first_button_text) }}
                                        </a>
                                        <a href="{{ @$banner->data_info->second_button_url }}" class="btn btn--base" target="_blank">
                                            {{ __(@$banner->data_info->second_button_text) }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-10">
                                <div class="banner-img">
                                    <img class="bg-img" data-background-image="{{ $commonSliderImage }}" data-mask-image="{{ $commonSliderImage }}" src="{{ getImage('assets/images/site/banner/' . @$banner->data_info->background_image, '1920x1080') }}" alt="image">
                                    <span class="banner-img__mask" data-mask-image="{{ $commonSliderImage }}"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @include($activeTheme . 'sections.about')

    <div class="featured-campaign py-60">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$featuredCampaignContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$featuredCampaignContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse ($featuredCampaigns as $campaign)
                    <div class="col-xl-4 col-lg-5 col-sm-6">
                        <div class="campaign-card" data-aos="fade-up" data-aos-duration="1500">
                            @include($activeTheme . 'partials.basicCampaign')
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center" data-aos="fade-up" data-aos-duration="1500">{{ __($emptyMessage) }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="cause-category py-60">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$campaignCategoryContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$campaignCategoryContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="cause-category__slider" data-aos="fade-up" data-aos-duration="1500">
                @foreach ($campaignCategories as $category)
                    <div class="cause-category__slide">
                        <div class="cause-category__img">
                            <a href="{{ route('campaign', ['category' => $category->slug]) }}">
                                <img class="bg-img" data-background-image="{{ $commonShapeImage }}" data-mask-image="{{ $commonShapeImage }}" src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}" alt="{{ $category->name }}">
                            </a>
                        </div>
                        <h3 class="cause-category__title">
                            <a href="{{ route('campaign', ['category' => $category->slug]) }}">{{ __($category->name) }}</a>
                        </h3>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="new-campaign py-60">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$recentCampaignContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$recentCampaignContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="new-campaign__slider" data-aos="fade-up" data-aos-duration="1500">
                @forelse ($recentCampaigns as $campaign)
                    <div class="new-campaign__slide">
                        <div class="campaign-card">
                            @include($activeTheme . 'partials.basicCampaign')
                        </div>
                    </div>
                @empty
                    <p class="text-center">{{ __($emptyMessage) }}</p>
                @endforelse
            </div>
        </div>
    </div>

    @include($activeTheme . 'sections.volunteer')
    @include($activeTheme . 'sections.donor')

    <div class="counter-section py-60">
        <div class="container">
            <div class="row counter-section__row g-4">
                @foreach ($counterElements as $counter)
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-duration="1500">
                        <div class="counter-section__card" data-mask-image="{{ $commonSliderImage }}">
                            <h3 class="counter-section__number odometer" data-count="{{ @$counter->data_info->counter_digit }}">0</h3>
                            <p class="counter-section__name">{{ __(@$counter->data_info->title) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="upcoming-event py-120">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$upcomingContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$upcomingContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="upcoming-event__row">
                @forelse ($upcomingCampaigns as $upcomingCampaign)
                    <div class="upcoming-event__card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="upcoming-event__schedule">
                            <span class="upcoming-event__date">{{ @$upcomingCampaign->start_date->format('d') }}</span>
                            <span class="upcoming-event__month">{{ __(@$upcomingCampaign->start_date->format('F')) }}</span>
                        </div>
                        <div class="upcoming-event__txt">
                            <a href="{{ route('upcoming.show', $upcomingCampaign->slug) }}" class="upcoming-event__title text--base">{{ __(strLimit(@$upcomingCampaign->name, 30)) }}</a>
                            <p>{{ __(strLimit(strip_tags(@$upcomingCampaign->description), 100)) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center" data-aos="fade-up" data-aos-duration="1500">{{ __($emptyMessage) }}</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="subscribe">
        <div class="container">
            <div class="subscribe__bg" data-aos="fade-up" data-aos-duration="1500">
                <div class="subscribe__vector bg-img" data-background-image="{{ getImage('assets/images/site/subscribe/' . @$subscribeContent->data_info->background_image, '1920x1280') }}"></div>
                <div class="section-heading section-heading-light text-center">
                    <h2 class="section-heading__title text-light">{{ __(@$subscribeContent->data_info->section_heading) }}</h2>
                    <p class="section-heading__desc">{{ __(@$subscribeContent->data_info->description) }}</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="card border-0">
                            <form class="card-body p-2 row g-3 justify-content-center">
                                <div class="col-md-8 col-sm-7">
                                    <input type="email" name="subscriber_email" placeholder="@lang('Enter your email address')" class="form--control">
                                </div>
                                <div class="col-md-4 col-sm-5 col-xsm-6">
                                    <button class="btn btn--base w-100 subscribeBtn px-3" type="button">
                                        {{ __(@$subscribeContent->data_info->submit_button_text) }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="success-showcase py-120">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$successContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$successContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse ($successElements->take(3) as $successElement)
                    <div class="col-xl-4 col-lg-5 col-md-6" data-aos="fade-up" data-aos-duration="1500">
                        @include($activeTheme . 'partials.basicSuccessStory')
                    </div>
                @empty
                    <p class="text-center" data-aos="fade-up" data-aos-duration="1500">{{ __($emptyMessage) }}</p>
                @endforelse
            </div>

            <div class="d-flex justify-content-center pt-5" data-aos="fade-up" data-aos-duration="1500">
                <a href="{{ route('stories') }}" class="btn btn--base">@lang('View All Story')</a>
            </div>
        </div>
    </div>

    @include($activeTheme . 'sections.partner')
@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/odometer.css') }}">
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/odometer.min.js') }}"></script>
@endpush

@push('page-script')
    <script>
        'use strict';

        (function ($) {
            $('.subscribeBtn').on('click',function () {
                let email = $('[name=subscriber_email]').val();
                let csrf  = '{{csrf_token()}}';
                let url   = "{{ route('subscriber.store') }}";
                let data  = {email:email, _token:csrf};

                $.post(url, data,function(response) {
                    if(response.success){
                        showToasts('success', response.success);
                        $('[name=subscriber_email]').val('');
                    }else{
                        showToasts('error', response.error);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
