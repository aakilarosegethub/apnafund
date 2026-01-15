@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    @include($activeTheme . 'sections.about')
    
    <section class="testimonials pt-120 pb-60">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$clientContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$clientContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="row g-4 justify-content-center align-items-center">
                <div class="col-xxl-6 col-xl-7 col-lg-8 col-md-8" data-aos="fade-up" data-aos-duration="1500">
                    <div class="testimonial-txt-slider">
                        @foreach ($clientElements as $client)
                            <div class="testimonails-card">
                                <div class="testimonial-item">
                                    <p class="testimonial-item__desc">{{ __(@$client->data_info->client_review) }}</p>
                                    <div class="testimonial-item__content">
                                        <div class="testimonial-item__info">
                                            <div class="testimonial-item__details">
                                                <h5 class="testimonial-item__name">{{ __(@$client->data_info->client_name) }}</h5>
                                                <span class="testimonial-item__designation">{{ __(@$client->data_info->client_designation) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-md-4" data-aos="fade-up" data-aos-duration="1500">
                    <div class="testimonial-img-slider">
                        @foreach ($clientElements as $client)
                            <div class="testimonial-img">
                                <img src="{{ getImage('assets/images/site/client_review/' . @$client->data_info->client_image, '500x500') }}" alt="image">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include($activeTheme . 'sections.partner')
@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/odometer.css') }}">
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/odometer.min.js') }}"></script>
@endpush
