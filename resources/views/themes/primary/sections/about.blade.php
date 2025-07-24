@php
    $aboutUsContent     = getSiteData('about.content', true);
    $basicCampaignQuery = App\Models\Campaign::campaignCheck()->approve();
    $totalFundRaised    = (clone $basicCampaignQuery)->sum('raised_amount');
    $totalCampaignCount = (clone $basicCampaignQuery)->count(); 
@endphp

<div class="about py-120 bg-img" data-background-image="{{ getImage('assets/images/site/about/' . @$aboutUsContent->data_info->background_image, '1920x1080') }}">
    <div class="about__vector bg-img" data-background-image="{{ asset($activeThemeTrue . 'images/animation-vector-1.png') }}"></div>
    <div class="container">
        <div class="row justify-content-lg-between justify-content-center align-items-center">
            <div class="col-lg-6 col-md-10">
                <div class="about__img" data-aos="fade-up" data-aos-duration="1500">
                    <img src="{{ getImage('assets/images/site/about/' . @$aboutUsContent->data_info->image, '655x690') }}" alt="About Us">
                    <span class="about__img__vector" data-mask-image="{{ asset($activeThemeTrue . 'images/slider-img-shape.png') }}"></span>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6 col-md-10">
                <div class="about__content" data-aos="fade-up" data-aos-duration="1500">
                    <div class="section-heading">
                        <h2 class="section-heading__title">{{ __(@$aboutUsContent->data_info->heading) }}</h2>
                    </div>
                    <p class="about__desc">{{ __(@$aboutUsContent->data_info->description) }}</p>
                    <div class="row about__card-row g-4">
                        <div class="col-sm-6 col-xsm-6">
                            <div class="about__card">
                                <div class="counter">{{ $setting->cur_sym }}<span class="odometer" data-count="{{ $totalFundRaised }}">0</span></div>
                                <span class="name">@lang('Total Fund Raised')</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xsm-6">
                            <div class="about__card">
                                <div class="counter"><span class="odometer" data-count="{{ $totalCampaignCount }}">0</span>+</div>
                                <span class="name">@lang('Total Campaigns')</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ @$aboutUsContent->data_info->button_url }}" class="btn btn--base" target="_blank">
                        {{ __(@$aboutUsContent->data_info->button_text) }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>