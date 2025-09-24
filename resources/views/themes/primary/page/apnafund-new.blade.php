<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apna Fund</title>
    <link rel="stylesheet" href="{{ custom_asset('apnafund/assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ custom_asset('apnafund/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ custom_asset('apnafund/assets/images/fav-icon.png') }}" type="image/png">
</head>

<body>
    <!-- Header -->
    <header class="header py-0">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="logo text-center">
                        <a href="/">
                            <img src="{{ custom_asset('apnafund/assets/images/White Logo.png') }}" alt="Apna Fund Logo" class="logo-img">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="hero-section">
        <div class="container d-flex align-items-center justify-content-left">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-dark">
                                <h1 class="display-4 hero-heading fw-bold"><span class="theme-color-text">Crowd </span>Funding</h1>
                                <h1 class="display-4 hero-heading text-white fw-bold"><span class="theme-color-text font-italic">By</span> The People,</h1>
                                <h1 class="display-4 hero-heading text-white fw-bold"><span class="theme-color-text font-italic">For</span> The People,</h1>
                            </div>
                            <div class="mt-2">
                                <p class="text-white font-bold mb-0 me-4"><span class="quotes">Together, we empower
                                        small businessesâ€”
                                        From young dreamers, bold visionaries and those who want to improve their
                                        societies.
                                    </span>
                                </p>
                            </div>
                            <div class="button-container d-block me-0 pe-0">
                                <a href="{{ route('user.register') }}" class="m-0 button-theme">
                                    <i class="fas fa-rocket me-2"></i>Get Started Now!
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Resource Section -->
    @if(isset($featuredCampaignContent) && $featuredCampaignContent)
    <section class="resource-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="content-left">
                        <h3 class="small-heading">{{ $featuredCampaignContent->data_values->title ?? 'Creator Resource Hub' }}</h3>
                        <h2 class="large-heading">{{ $featuredCampaignContent->data_values->subtitle ?? "It's your turn.<br>Learn how to get started." }}</h2>
                        <p class="description">{{ $featuredCampaignContent->data_values->description ?? 'Everything you need to get your project off the ground, reach the right people, build a marketing campaign, and see it through.' }}</p>
                        <a href="{{ route('user.register') }}" class="cta-button">{{ $featuredCampaignContent->data_values->button_text ?? "Let's go" }}</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="graphics-right">
                        <div class="arrow-container">
                            <div class="arrow arrow-orange"></div>
                            <div class="arrow arrow-green"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Steps Section -->
    @if(isset($counterElements) && $counterElements->count() > 0)
    <section class="steps-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="steps-title">{{ $counterElements->first()->data_values->title ?? 'Create with confidence' }}</h2>
            </div>

            <div class="steps-container">
                <div class="row">
                    @foreach($counterElements->take(3) as $index => $element)
                    <div class="col-lg-4">
                        <div class="step-card step-{{ $index + 1 }}">
                            <div class="step-content">
                                <h3 class="step-heading">{{ $element->data_values->title ?? 'Step ' . ($index + 1) }}</h3>
                                <p class="step-description">{{ $element->data_values->description ?? 'Description for step ' . ($index + 1) }}</p>
                            </div>
                            <div class="step-arrow step-arrow-{{ $index == 0 ? 'right' : ($index == 1 ? 'down' : 'left') }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Button -->
                <div class="text-center mt-5">
                    <a href="{{ route('user.register') }}" class="create-button">{{ $counterElements->first()->data_values->button_text ?? "Let's create" }}</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Success Stories Slider -->
    @if(isset($successContent) && isset($successElements) && $successElements->count() > 0)
    <section class="success-stories-section">
        <div class="container">
            <div class="section-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="stories-title">{{ $successContent->data_values->title ?? 'Creators are the stars of Apna Fund' }}</h2>
                        <p class="stories-subtitle">{{ $successContent->data_values->description ?? "We're the world's leading funding and launch platform because of the stellar ideas that come to life here." }}</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <div class="slider-nav">
                            <button class="nav-btn prev-btn theme-color" onclick="changeSlide(-1)">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button class="nav-btn next-btn theme-color" onclick="changeSlide(1)">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="slider-container">
                <div class="slider-track" id="sliderTrack">
                    @foreach($successElements as $index => $element)
                    <div class="slide {{ $index == 0 ? 'active' : '' }}">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="slide-image">
                                    <div class="image-frame">
                                        <div class="placeholder-image">
                                            <div class="person-avatar">
                                                @if(isset($element->data_values->image) && $element->data_values->image)
                                                    <img src="{{ custom_asset($element->data_values->image) }}" alt="{{ $element->data_values->name ?? 'Success Story' }}" class="avatar-image">
                                                @else
                                                    <div class="avatar-circle">
                                                        <span class="avatar-text">{{ substr($element->data_values->name ?? 'ST', 0, 2) }}</span>
                                                    </div>
                                                @endif
                                                <div class="microphone-icon">🎤</div>
                                            </div>
                                        </div>
                                        <div class="frame-decoration"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="slide-content">
                                    <h3 class="slide-title">{{ $element->data_values->name ?? 'Success Story ' . ($index + 1) }}</h3>
                                    <p class="slide-description">{{ $element->data_values->description ?? 'Description for success story ' . ($index + 1) }}</p>
                                    <button class="case-study-btn">{{ $element->data_values->button_text ?? 'See case study' }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- FAQ Section -->
    @if(isset($faqContent) && isset($faqElements) && $faqElements->count() > 0)
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="faq-title">{{ $faqContent->data_values->title ?? 'Frequently Asked Questions' }}</h2>
                        <p class="faq-subtitle">{{ $faqContent->data_values->description ?? 'Everything you need to know about Apna Fund and crowdfunding' }}</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <button class="help-center-btn">{{ $faqContent->data_values->button_text ?? 'Visit Help Center' }}</button>
                    </div>
                </div>
            </div>

            <div class="faq-container">
                <div class="accordion" id="faqAccordion">
                    @foreach($faqElements as $index => $element)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq{{ $index + 1 }}">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $index + 1 }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index + 1 }}">
                                {{ $element->data_values->question ?? 'FAQ Question ' . ($index + 1) }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index + 1 }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="faq{{ $index + 1 }}"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ $element->data_values->answer ?? 'FAQ Answer ' . ($index + 1) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Join Creator Community Section -->
    @if(isset($subscribeContent))
    <section class="join-community-section">
        <div class="container">
            <div class="community-card">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="community-title">{{ $subscribeContent->data_values->title ?? 'Join the Creator Community' }}</h2>
                        <p class="community-description">{{ $subscribeContent->data_values->description ?? 'Be the first to know about product updates and enjoy monthly inspiration, guides & best practices, webinars and opportunities to connect.' }}</p>

                        <div class="signup-form">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <form action="{{ route('subscriber.store') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="email" name="email" class="form-control email-input"
                                                placeholder="{{ $subscribeContent->data_values->placeholder ?? 'Enter email address' }}" aria-label="Email address" required>
                                            <button class="btn signup-btn" type="submit">{{ $subscribeContent->data_values->button_text ?? 'Sign me up' }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-logo">
                        <img src="{{ custom_asset('apnafund/assets/images/White Logo.png') }}" alt="Apna Fund Logo" class="footer-logo-img">
                        <p class="footer-tagline">Empowering Dreams, Building Futures</p>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="footer-info">
                        <p class="copyright">© {{ date('Y') }} Apna Fund. All rights reserved.</p>
                        <p class="email">contact@apnacrowdfunding.com</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ custom_asset('apnafund/assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;

        function changeSlide(direction) {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
            slides[currentSlide].classList.add('active');
        }

        // Auto slide every 5 seconds
        setInterval(() => {
            changeSlide(1);
        }, 5000);
    </script>
</body>

</html>