@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    @php
        $commonSliderImage = asset($activeThemeTrue . 'images/slider-img-shape-2.png');
        $commonShapeImage  = asset($activeThemeTrue . 'images/mask-shape-1.png');
    @endphp


    <section class="hero-section" @if(@$heroContent->data_info->hero_background_image) style="background-image: url('{{ asset('assets/images/site/home/' . @$heroContent->data_info->hero_background_image) }}');" @endif>
        <div class="container d-flex align-items-center justify-content-left">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-dark">
                                <h1 class="display-4 hero-heading fw-bold"><span class="theme-color-text">{{ @$heroContent->data_info->hero_heading_1 ?? 'Crowd' }}</span>{{ @$heroContent->data_info->hero_heading_1 ? '' : 'Funding' }}</h1>
                                <h1 class="display-4 hero-heading text-white fw-bold"><span
                                        class="theme-color-text font-italic">{{ @$heroContent->data_info->hero_heading_2 ?? 'By' }}</span> {{ @$heroContent->data_info->hero_heading_2 ? '' : 'The People,' }}</h1>
                                <h1 class="display-4 hero-heading text-white fw-bold"><span
                                        class="theme-color-text font-italic">{{ @$heroContent->data_info->hero_heading_3 ?? 'For' }}</span> {{ @$heroContent->data_info->hero_heading_3 ? '' : 'The People,' }}</h1>
                            </div>
                            <div class="mt-2">
                                <p class="text-white font-bold mb-0 me-4"><span class="quotes">{{ @$heroContent->data_info->hero_description ?? 'Together, we empower small businesses— From young dreamers, bold visionaries and those who want to improve their societies.' }}</span></p>
                            </div>
                            <div class="button-container d-block me-0 pe-0">
                                <a href="{{ @$heroContent->data_info->button_url ?? route('business.resources') }}" class="m-0 button-theme">
                                    <i class="fas fa-rocket me-2"></i>{{ @$heroContent->data_info->button_text ?? 'Get Started Now!' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Banner Section -->
    <section class="info-banner-section">
        <div class="container p-0">
            <div class="row">
                <div class="col-12">
                    <div class="info-banner">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="{{ @$infoBannerContent->data_info->info_item_1_icon ?? 'fas fa-bolt' }}"></i>
                            </div>
                            <div class="info-text">
                                <span>{{ @$infoBannerContent->data_info->info_item_1_text ?? 'No fee to start fundraising' }}</span>
                            </div>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="{{ @$infoBannerContent->data_info->info_item_2_icon ?? 'fas fa-heart-circle' }}"></i>
                            </div>
                            <div class="info-text">
                                <span>{{ @$infoBannerContent->data_info->info_item_2_text ?? '1 Fund made every second' }}</span>
                            </div>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="{{ @$infoBannerContent->data_info->info_item_3_icon ?? 'fas fa-layer-group' }}"></i>
                            </div>
                            <div class="info-text">
                                <span>{{ @$infoBannerContent->data_info->info_item_3_text ?? '8K+ fundraisers started daily' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section class="projects-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title-sm text-left mb-4">{{ @$featuredProjectsContent->data_info->section_title ?? 'Featured Projects' }}</h2>
                </div>
            </div>

            <!-- Featured Campaigns Display -->
            @forelse($featuredCampaigns->chunk(2) as $chunk)
                <div class="row mb-4">
                    @foreach($chunk as $index => $campaign)
                        @php
                            $colClass = $loop->first && $loop->parent->first ? 'col-12 col-lg-6' : 'col-12 col-md-4';
                        @endphp
                        <div class="{{ $colClass }}">
                            <a href="{{ route('campaign.show', $campaign->slug) }}" class="project-card-link">
                                @include('partials.campaign-item', ['campaignId' => $campaign->id, 'featured' => true])
                            </a>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="text-muted">No featured campaigns available at the moment.</p>
                    </div>
                </div>
            @endforelse
            
            <!-- View All Button -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="{{ @$featuredProjectsContent->data_info->view_all_button_url ?? route('campaign') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-eye me-2"></i>{{ @$featuredProjectsContent->data_info->view_all_button_text ?? 'View All Campaigns' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
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
