<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Apna Crowdfunding</title>
    <link rel="stylesheet" href="{{ asset('apnafund/assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('apnafund/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('apnafund/assets/images/fav-icon.png') }}" type="image/png">
</head>

<body>
    <!-- Header -->
    <header class="header py-0">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="logo text-center">
                        <a href="/">
                            <img src="{{ asset('apnafund/assets/images/White Logo.png') }}" alt="Apna Crowdfunding Logo" class="logo-img">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="hero-section">
        <div class="container d-flex align-items-center justify-content-center">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="text-dark">
                        <h1 class="display-4 hero-heading theme-color-text fw-bold">{{ $setting->home_hero_title_1 ?? 'Funding By The People' }}</h1>
                        <h1 class="display-4 hero-heading text-white fw-bold">{{ $setting->home_hero_title_2 ?? 'Funding For The People' }}</h1>
                    </div>
                    <div class="button-container">
                        <a href="{{ route('user.register') }}" class="button-theme">
                            <i class="fas fa-briefcase me-2"></i>{{ $setting->home_business_button_text ?? 'For Business' }}
                        </a>
                        <a href="{{ route('user.register') }}" class="button-theme">
                            <i class="fas fa-user me-2"></i>{{ $setting->home_personal_button_text ?? 'For Myself' }}
                        </a>
                        <p class="text-white font-bold mt-4"><span class="quotes">{{ $setting->home_hero_subtitle ?? 'Together, we empower small businesses— From young dreamers, bold visionaries and those who want to improve their societies.' }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Resource Section -->
    <section class="resource-section d-none">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="content-left">
                        <h3 class="small-heading">{{ $setting->home_resource_title ?? 'Creator Resource Hub' }}</h3>
                        <h2 class="large-heading">{{ $setting->home_resource_subtitle ?? 'It\'s your turn. Learn how to get started.' }}</h2>
                        <p class="description">{{ $setting->home_resource_description ?? 'Everything you need to get your project off the ground, reach the right people, build a marketing campaign, and see it through.' }}</p>
                        <a href="#" class="cta-button">{{ $setting->home_resource_button_text ?? 'Let\'s go' }}</a>
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

    <!-- Steps Section -->
    <section class="steps-section d-none">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="steps-title">{{ $setting->home_steps_title ?? 'Create with confidence' }}</h2>
            </div>

            <div class="steps-container">
                <div class="row">
                    <!-- Step 1 -->
                    <div class="col-lg-4">
                        <div class="step-card step-1">
                            <div class="step-content">
                                <h3 class="step-heading">{{ $setting->home_step_1_title ?? '23 million+ backers on Kickstarter' }}</h3>
                                <p class="step-description">{{ $setting->home_step_1_description ?? 'Connect with passionate, engaged backers who share your values and interests. You\'ll find your community here — people who get your vision and are excited to support it.' }}</p>
                            </div>
                            <div class="step-arrow step-arrow-right"></div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="col-lg-4">
                        <div class="step-card step-2">
                            <div class="step-content">
                                <h3 class="step-heading">{{ $setting->home_step_2_title ?? 'Creative independence' }}</h3>
                                <p class="step-description">{{ $setting->home_step_2_description ?? 'Create on your own terms. You\'ll always have complete creative control and ownership of every project you launch on Kickstarter.' }}</p>
                            </div>
                            <div class="step-arrow step-arrow-down"></div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="col-lg-4">
                        <div class="step-card step-3">
                            <div class="step-content">
                                <h3 class="step-heading">{{ $setting->home_step_3_title ?? 'Empowering platform' }}</h3>
                                <p class="step-description">{{ $setting->home_step_3_description ?? 'We\'re here to help you succeed. Optimize your results with tools, tips, and support throughout the life of your project and beyond.' }}</p>
                            </div>
                            <div class="step-arrow step-arrow-left"></div>
                        </div>
                    </div>
                </div>

                <!-- Button -->
                <div class="text-center mt-5">
                    <button class="create-button">Let's create</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Slider -->
    <section class="success-stories-section d-none">
        <div class="container">
            <div class="section-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="stories-title">{{ $setting->home_stories_title ?? 'Creators are the stars of Apna Crowdfunding' }}</h2>
                        <p class="stories-subtitle">{{ $setting->home_stories_subtitle ?? 'We\'re the world\'s leading funding and launch platform because of the stellar ideas that come to life here.' }}</p>
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
                    <!-- Slide 1 -->
                    <div class="slide active">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="slide-image">
                                    <div class="image-frame">
                                        <div class="placeholder-image">
                                            <div class="person-avatar">
                                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face"
                                                    alt="John Doe" class="avatar-image">
                                                <div class="microphone-icon">🎤</div>
                                            </div>
                                        </div>
                                        <div class="frame-decoration"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="slide-content">
                                    <h3 class="slide-title">John Doe - Senior Developer</h3>
                                    <p class="slide-description">"Apna Crowdfunding helped us raise $2.5M for our tech startup.
                                        The platform's ease of use and supportive community made all the difference in
                                        our journey from idea to successful launch."</p>
                                    <button class="case-study-btn">See case study</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="slide">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="slide-image">
                                    <div class="image-frame">
                                        <div class="placeholder-image">
                                            <div class="person-avatar">
                                                <div class="avatar-circle">
                                                    <span class="avatar-text">SM</span>
                                                </div>
                                                <div class="microphone-icon">🎤</div>
                                            </div>
                                        </div>
                                        <div class="frame-decoration"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="slide-content">
                                    <h3 class="slide-title">Sarah Miller - Creative Director</h3>
                                    <p class="slide-description">"Our creative project found its perfect audience
                                        through Apna Crowdfunding. We exceeded our funding goal by 300% and built a community of
                                        passionate supporters who believe in our vision."</p>
                                    <button class="case-study-btn">See case study</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="slide">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="slide-image">
                                    <div class="image-frame">
                                        <div class="placeholder-image">
                                            <div class="person-avatar">
                                                <div class="avatar-circle">
                                                    <span class="avatar-text">RK</span>
                                                </div>
                                                <div class="microphone-icon">🎤</div>
                                            </div>
                                        </div>
                                        <div class="frame-decoration"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="slide-content">
                                    <h3 class="slide-title">Rajesh Kumar - Startup Founder</h3>
                                    <p class="slide-description">"Apna Crowdfunding's platform transformed our startup journey.
                                        We raised $1.8M and connected with investors who shared our vision for
                                        sustainable technology solutions."</p>
                                    <button class="case-study-btn">See case study</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section d-none">
        <div class="container">
            <div class="section-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="faq-title">{{ $setting->home_faq_title ?? 'Frequently Asked Questions' }}</h2>
                        <p class="faq-subtitle">{{ $setting->home_faq_subtitle ?? 'Everything you need to know about Apna Crowdfunding and crowdfunding' }}</p>
                    </div>
                    <div class="col-lg-4 text-end">
                        <button class="help-center-btn">Visit Help Center</button>
                    </div>
                </div>
            </div>

            <div class="faq-container">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                What can I use Apna Crowdfunding to finance?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Apna Crowdfunding supports a wide range of creative and innovative projects including technology
                                startups, creative arts, social causes, business ventures, and community initiatives.
                                Whether you're launching a new product, creating art, or starting a business, our
                                platform provides the tools and community to bring your vision to life.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                Who can contribute to my project?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Anyone with an internet connection can contribute to your project! Our global community
                                includes passionate supporters, potential customers, investors, and fellow creators. You
                                can reach out to friends, family, social media followers, and our built-in community of
                                backers who are always looking for exciting new projects to support.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                How much work is involved in running a project?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Running a successful project requires dedication and planning, but we make it as easy as
                                possible! You'll need to create compelling content, set realistic goals, engage with
                                your community, and fulfill your promises. Our platform provides tools, resources, and
                                support to help you succeed. Most creators spend 2-4 hours daily during their campaign.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                How do I get help with questions?
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We're here to help! You can reach our support team through multiple channels: email us
                                at support@apnacrowdfunding.com, use our live chat feature, or schedule a consultation call. We
                                also offer comprehensive guides, video tutorials, and a community forum where
                                experienced creators share their insights and advice.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                What makes a successful crowdfunding campaign?
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Successful campaigns typically have a clear, compelling story, realistic funding goals,
                                engaging rewards, and active community engagement. High-quality visuals, regular
                                updates, and authentic communication with backers are key. Our platform provides
                                analytics and insights to help you optimize your campaign and reach your goals.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Join Creator Community Section -->
    <section class="join-community-section d-none">
        <div class="container">
            <div class="community-card">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="community-title">{{ $setting->home_community_title ?? 'Join the Creator Community' }}</h2>
                        <p class="community-description">{{ $setting->home_community_description ?? 'Be the first to know about product updates and enjoy monthly inspiration, guides & best practices, webinars and opportunities to connect.' }}</p>

                        <div class="signup-form">
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="email" class="form-control email-input"
                                            placeholder="Enter email address" aria-label="Email address">
                                        <button class="btn signup-btn" type="button">{{ $setting->home_community_button_text ?? 'Sign me up' }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer d-none">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-logo">
                        <img src="assets/images/White Logo.png" alt="Apna Crowdfunding Logo" class="footer-logo-img">
                        <p class="footer-tagline">Empowering Dreams, Building Futures</p>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="footer-info">
                        <p class="copyright">© 2024 Apna Crowdfunding. All rights reserved.</p>
                        <p class="email">contact@apnacrowdfunding.com</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
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