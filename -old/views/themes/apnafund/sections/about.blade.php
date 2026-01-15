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
                    @php
                        $aboutImage = @$aboutUsContent->data_info['image'] ?? '';
                        $imageUrl = '';
                        if ($aboutImage) {
                            // Check if it's a URL
                            if (filter_var($aboutImage, FILTER_VALIDATE_URL)) {
                                $imageUrl = $aboutImage;
                            } else {
                                $imageUrl = getImage('assets/images/site/about/' . $aboutImage, '655x690');
                            }
                        } else {
                            $imageUrl = getImage('assets/images/site/about/' . $aboutImage, '655x690');
                        }
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ @$aboutUsContent->data_info['image_alt'] ?? 'About Us' }}">
                    <span class="about__img__vector" data-mask-image="{{ asset($activeThemeTrue . 'images/slider-img-shape.png') }}"></span>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6 col-md-10">
                <div class="about__content" data-aos="fade-up" data-aos-duration="1500">
                    <div class="section-heading">
                        <h2 class="section-heading__title">{{ __(@$aboutUsContent->data_info['heading'] ?? '') }}</h2>
                    </div>
                    <div class="about__desc">
                        {!! @$aboutUsContent->data_info['description'] ?? '' !!}
                    </div>
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
                    @if(@$aboutUsContent->data_info['url'] ?? '')
                        <a href="{{ @$aboutUsContent->data_info['url'] }}" class="btn btn--base" target="_blank">
                            @lang('Learn More')
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(@$aboutUsContent->data_info['more_details'] ?? '')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="about__more-details">
                    {!! @$aboutUsContent->data_info['more_details'] !!}
                </div>
            </div>
        </div>
    </div>
@endif




