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


    <section class="hero-section">
        <div class="container d-flex align-items-center justify-content-left">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-dark">
                                <h1 class="display-4 hero-heading fw-bold"><span class="theme-color-text">Crowd
                                    </span>Funding</h1>
                                <h1 class="display-4 hero-heading text-white fw-bold"><span
                                        class="theme-color-text font-italic">By</span> The People,</h1>
                                <h1 class="display-4 hero-heading text-white fw-bold"><span
                                        class="theme-color-text font-italic">For</span> The People,</h1>
                            </div>
                            <div class="mt-2">
                                <p class="text-white font-bold mb-0 me-4"><span class="quotes">Together, we empower
                                        small businesses—
                                        From young dreamers, bold visionaries and those who want to improve their
                                        societies.
                                    </span>
                                </p>
                            </div>
                            <div class="button-container d-block me-0 pe-0">
                                <a href="{{ route('business.resources') }}" class="m-0 button-theme">
                                    <i class="fas fa-rocket me-2"></i>Get Started Now!
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
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="info-text">
                                <span>No fee to start fundraising</span>
                            </div>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-heart-circle"></i>
                            </div>
                            <div class="info-text">
                                <span>1 Fund made every second</span>
                            </div>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="info-text">
                                <span>8K+ fundraisers started daily</span>
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
                    <h2 class="section-title-sm text-left mb-4">Featured Projects</h2>
                </div>
            </div>

            <!-- First Row: One item in 6 columns -->
            <div class="row mb-4">
                <div class="col-12 col-lg-6">
                    <a href="gig-showcase.html" class="project-card-link">
                        <div class="project-card featured-project">
                            <div class="project-image">
                                <img src="{{ asset('apnafund/assets/images/banner-1.jpg') }}" alt="Featured Project" class="img-fluid">
                                <div class="project-overlay">
                                    <div class="project-category">Technology</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 75%"></div>
                                        </div>
                                        <span class="progress-text">75% funded</span>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">Smart Home Automation System</h3>
                                <p class="project-description">Revolutionary IoT system that transforms your home into a
                                    smart, energy-efficient living space.</p>
                                <div class="project-stats">
                                    <div class="stat">
                                        <span class="stat-value">$45,000</span>
                                        <span class="stat-label">raised</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">15</span>
                                        <span class="stat-label">days left</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-3">
                    <a href="gig-showcase.html" class="project-card-link">
                        <div class="project-card">
                            <div class="project-image">
                                <img src="{{ asset('apnafund/assets/images/banner-2.jpg') }}" alt="Project 1" class="img-fluid">
                                <div class="project-overlay">
                                    <div class="project-category">Art</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 60%"></div>
                                        </div>
                                        <span class="progress-text">60% funded</span>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">Digital Art Collection</h3>
                                <p class="project-description">Exclusive NFT collection featuring contemporary digital
                                    artists.</p>
                                <div class="project-stats">
                                    <div class="stat">
                                        <span class="stat-value">$12,500</span>
                                        <span class="stat-label">raised</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">8</span>
                                        <span class="stat-label">days left</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-3">
                    <a href="gig-showcase.html" class="project-card-link">
                        <div class="project-card">
                            <div class="project-image">
                                <img src="{{ asset('apnafund/assets/images/banner-3.jpeg') }}" alt="Project 2" class="img-fluid">
                                <div class="project-overlay">
                                    <div class="project-category">Food</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 85%"></div>
                                        </div>
                                        <span class="progress-text">85% funded</span>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">Organic Farm Expansion</h3>
                                <p class="project-description">Sustainable farming initiative to provide fresh organic
                                    produce to local communities.</p>
                                <div class="project-stats">
                                    <div class="stat">
                                        <span class="stat-value">$28,000</span>
                                        <span class="stat-label">raised</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">22</span>
                                        <span class="stat-label">days left</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- Third Row: Three items in 3 columns each -->
            <div class="row">
                <div class="col-12 col-md-4">
                    <a href="gig-showcase.html" class="project-card-link">
                        <div class="project-card">
                            <div class="project-image">
                                <img src="{{ asset('apnafund/assets/images/banner-4.jpg') }}" alt="Project 3" class="img-fluid">
                                <div class="project-overlay">
                                    <div class="project-category">Music</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 45%"></div>
                                        </div>
                                        <span class="progress-text">45% funded</span>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">Indie Album Production</h3>
                                <p class="project-description">Supporting independent musicians to create their debut album.
                                </p>
                                <div class="project-stats">
                                    <div class="stat">
                                        <span class="stat-value">$8,500</span>
                                        <span class="stat-label">raised</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">18</span>
                                        <span class="stat-label">days left</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <a href="gig-showcase.html" class="project-card-link">
                        <div class="project-card">
                            <div class="project-image">
                                <img src="{{ asset('apnafund/assets/images/banner-5.jpg') }}" alt="Project 4" class="img-fluid">
                                <div class="project-overlay">
                                    <div class="project-category">Education</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 70%"></div>
                                        </div>
                                        <span class="progress-text">70% funded</span>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">Coding Bootcamp</h3>
                                <p class="project-description">Free programming education for underprivileged youth.</p>
                                <div class="project-stats">
                                    <div class="stat">
                                        <span class="stat-value">$15,200</span>
                                        <span class="stat-label">raised</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">12</span>
                                        <span class="stat-label">days left</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4">
                    <a href="gig-showcase.html" class="project-card-link">
                        <div class="project-card">
                            <div class="project-image">
                                <img src="{{ asset('apnafund/assets/images/banner-6.jpg') }}" alt="Project 5" class="img-fluid">
                                <div class="project-overlay">
                                    <div class="project-category">Health</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 55%"></div>
                                        </div>
                                        <span class="progress-text">55% funded</span>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <h3 class="project-title">Medical Equipment</h3>
                                <p class="project-description">Providing essential medical devices to rural healthcare
                                    centers.</p>
                                <div class="project-stats">
                                    <div class="stat">
                                        <span class="stat-value">$32,000</span>
                                        <span class="stat-label">raised</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-value">25</span>
                                        <span class="stat-label">days left</span>
                                    </div>
                                </div>
                            </div>
                        </div>
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
