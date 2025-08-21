@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <!-- Resource Hub Main Content -->
    <div class="resource-hub-container">
        <div class="resource-hub-sections">
            <div class="resource-hub-header">
                <h1 class="resource-hub-title">@lang('Learn Crowd Funding')</h1>
                <p class="resource-hub-subtitle">@lang('This is a comprehensive guidelines for your crowdfunding campaign success')</p>
            </div>
        </div>



        <div class="row mb-4">
            <div class="col-12">
                <h2>Build a winning campaign</h2>
            </div>
        </div>

        <div class="row mb-5">
            @forelse ($successElements as $successElement)
                <div class="col-md-3 mb-4">
                    @include($activeTheme . 'partials.success-story-card', ['successElement' => $successElement])
                </div>
            @empty
                <!-- Fallback Campaign Cards if no success elements -->
                <div class="col-md-3 mb-4">
                    <div class="campaign-card">
                        <div class="campaign-card-img"
                            style="background-image: url('assets/images/banner-1.jpg'); background-size: cover; background-position: center;">
                        </div>
                        <div class="campaign-card-body">
                            <h3 class="campaign-card-title">Campaign Success</h3>
                            <p class="campaign-card-text">Learn from successful campaigns and their strategies</p>
                            <a href="10-ways-to-level-up-crowdfunding.html" class="campaign-card-link">Learn more <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="campaign-card">
                        <div class="campaign-card-img"
                            style="background-image: url('assets/images/banner-2.jpg'); background-size: cover; background-position: center;">
                        </div>
                        <div class="campaign-card-body">
                            <h3 class="campaign-card-title">Telling Your Story</h3>
                            <p class="campaign-card-text">Craft a compelling narrative that resonates with potential backers
                            </p>
                            <a href="10-ways-to-level-up-crowdfunding.html" class="campaign-card-link">Learn more <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="campaign-card">
                        <div class="campaign-card-img"
                            style="background-image: url('assets/images/banner-3.jpeg'); background-size: cover; background-position: center;">
                        </div>
                        <div class="campaign-card-body">
                            <h3 class="campaign-card-title">10 Ways To Level Up</h3>
                            <p class="campaign-card-text">Expert advice to maximize your campaign's potential and reach</p>
                            <a href="10-ways-to-level-up-crowdfunding.html" class="campaign-card-link">Learn more <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="campaign-card">
                        <div class="campaign-card-img"
                            style="background-image: url('assets/images/banner-4.jpg'); background-size: cover; background-position: center;">
                        </div>
                        <div class="campaign-card-body">
                            <h3 class="campaign-card-title">Funding Strategies</h3>
                            <p class="campaign-card-text">Effective approaches to reach your funding goals and beyond</p>
                            <a href="10-ways-to-level-up-crowdfunding.html" class="campaign-card-link">Learn more <i
                                    class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>


        <!-- Ready to Get Started Section -->
        <div class="row mb-5">
            <div class="col-md-8">
                <h2>@lang('Ready to get started?')</h2>
                <p>@lang('Take the first step toward launching your campaign and bringing your idea to life')</p>
                 <a href="{{ route('user.register.business') }}" class="btn-theme">@lang('Start a project')</a> 
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ asset('assets/images/light.avif') }}" alt="Light bulb illustration" style="max-width: 25%;">
            </div>
        </div>

        <!-- Category Section -->
        <div class="category-section">
            <h2 class="category-title">@lang('Creator tips by category')</h2>
            <div class="row">
                <!-- Category 1 -->
                <div class="col-md-3 mb-4">
                    <div class="category-card">
                        <div class="category-icon category-icon-basics">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3 class="category-name">@lang('Basics')</h3>
                        <p class="category-description">@lang('Essential knowledge for all creators')</p>
                    </div>
                </div>

                <!-- Category 2 -->
                <div class="col-md-3 mb-4">
                    <div class="category-card">
                        <div class="category-icon category-icon-guide">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3 class="category-name">@lang('Guides & Resources')</h3>
                        <p class="category-description">@lang('Detailed instructions and helpful tools')</p>
                    </div>
                </div>

                <!-- Category 3 -->
                <div class="col-md-3 mb-4">
                    <div class="category-card">
                        <div class="category-icon category-icon-education">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3 class="category-name">@lang('Education & Demo')</h3>
                        <p class="category-description">@lang('Learn through examples and tutorials')</p>
                    </div>
                </div>

                <!-- Category 4 -->
                <div class="col-md-3 mb-4">
                    <div class="category-card">
                        <div class="category-icon category-icon-faq">
                            <i class="fas fa-question"></i>
                        </div>
                        <h3 class="category-name">@lang('FAQ')</h3>
                        <p class="category-description">@lang('Answers to common questions')</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Community Section -->
        <div class="community-section">
            <div class="container">
                <h2 class="community-title">@lang('Join the Creator Community')</h2>
                <p class="community-description">@lang('Connect with fellow entrepreneurs, share insights, and learn from each other\'s experiences')</p>
                <a href="{{ route('user.register') }}" class="btn-theme">@lang('Join Now')</a>
            </div>
        </div>

        <!-- Featured Section -->
        <div class="featured-section">
            <h2 class="featured-title">@lang('Get inspired')</h2>
            <div class="row">
                <!-- Featured Item 1 -->
                <div class="col-md-6 mb-4">
                    <div class="featured-card">
                        <img src="{{ asset('assets/images/banner-1.jpg') }}" alt="Success Story">
                        <div class="featured-card-overlay">
                            <h3 class="featured-card-title">@lang('Success Stories')</h3>
                            <p class="featured-card-text text-white">@lang('Learn from campaigns that exceeded their goals')</p>
                        </div>
                    </div>
                </div>

                <!-- Featured Item 2 -->
                <div class="col-md-6 mb-4">
                    <div class="featured-card">
                        <img src="{{ asset('assets/images/banner-2.jpg') }}" alt="Creative Independence">
                        <div class="featured-card-overlay">
                            <h3 class="featured-card-title">@lang('The Creative Independence')</h3>
                            <p class="featured-card-text text-white">@lang('Insights on building sustainable creative businesses')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Journey Section -->
        <div class="row mt-5 mb-5">
            <div class="col-md-8 offset-md-2 text-center">
                <h2>@lang('A new platform for every stage of your crowdfunding journey')</h2>
                <p>@lang('From concept to fulfillment, we provide the tools and resources you need to succeed')</p>
                <a href="{{ route('stories') }}" class="btn-theme mt-3">@lang('Discover all resources')</a>
            </div>
        </div>

        <!-- What's Happening Section -->
        <div class="featured-section">
            <h2 class="featured-title">@lang('What\'s happening around Apnafund')</h2>
            <div class="row">
                <!-- News Item 1 -->
                <div class="col-md-4 mb-4">
                    <div class="campaign-card">
                        <img src="{{ asset('assets/images/banner-3.jpeg') }}" alt="Creator Interview" class="campaign-card-img">
                        <div class="campaign-card-body">
                            <h3 class="campaign-card-title">@lang('Creator Interviews')</h3>
                            <p class="campaign-card-text">@lang('Insights from successful entrepreneurs')</p>
                            <a href="{{ route('stories.show', 5) }}" class="campaign-card-link">@lang('Read more') <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- News Item 2 -->
                <div class="col-md-4 mb-4">
                    <div class="campaign-card">
                        <img src="{{ asset('assets/images/banner-4.jpg') }}" alt="Funding News" class="campaign-card-img">
                        <div class="campaign-card-body">
                            <h3 class="campaign-card-title">@lang('Funding News')</h3>
                            <p class="campaign-card-text">@lang('Latest updates on campaign successes')</p>
                            <a href="{{ route('stories.show', 6) }}" class="campaign-card-link">@lang('Read more') <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- News Item 3 -->
                <div class="col-md-4 mb-4">
                    <div class="campaign-card">
                        <img src="{{ asset('assets/images/banner-5.jpg') }}" alt="Campaign of the Month" class="campaign-card-img">
                        <div class="campaign-card-body">
                            <h3 class="campaign-card-title">@lang('Campaign of the Month')</h3>
                            <p class="campaign-card-text">@lang('Highlighting exceptional projects')</p>
                            <a href="{{ route('stories.show', 7) }}" class="campaign-card-link">@lang('Read more') <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Section -->
        <div class="community-section mt-5">
            <div class="container">
                <h2 class="community-title">@lang('Got questions?')</h2>
                <p class="community-description">@lang('Our team is here to help you navigate every step of your crowdfunding journey')</p>
                <a href="{{ route('contact') }}" class="btn-theme">@lang('Contact Support')</a>
            </div>
        </div>
    </div>

    <style>
        /* Resource Hub Styles */
        .resource-hub-container {
            max-width: 1200px;
            margin: 0px auto;
            padding: 20px;
        }

        .resource-hub-sections {
            margin: 20px auto;
            padding: 20px;
        }

        .resource-hub-header {
            text-align: center;
        }

        .resource-hub-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .resource-hub-subtitle {
            font-size: 1.2rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Campaign Card Styles */
        .campaign-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .campaign-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .campaign-card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background-color: #f5f5f5;
        }

        .campaign-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .campaign-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .campaign-card-text {
            color: #666;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .campaign-card-link {
            color: #05ce78;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .campaign-card-link:hover {
            text-decoration: underline;
        }

        .campaign-card-link i {
            margin-left: 5px;
        }

        /* Category Section */
        .category-section {
            margin-top: 60px;
        }

        .category-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .category-card {
            text-align: center;
            padding: 25px 15px;
            border-radius: 12px;
            background: #f9f9f9;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .category-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 2rem;
        }

        .category-icon-basics {
            background-color: #ffebda;
            color: #ff8a3d;
        }

        .category-icon-guide {
            background-color: #e0e8ff;
            color: #4a6cf7;
        }

        .category-icon-education {
            background-color: #d1f5ea;
            color: #0cbc87;
        }

        .category-icon-faq {
            background-color: #ffe0e0;
            color: #d6293e;
        }

        .category-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .category-description {
            color: #666;
            font-size: 0.95rem;
        }

        /* Community Section */
        .community-section {
            background-color: #f0faf5;
            padding: 50px 0;
            margin-top: 60px;
            text-align: center;
            border-radius: 12px;
        }

        .community-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .community-description {
            max-width: 700px;
            margin: 0 auto 25px;
            color: #666;
        }

        /* Get Started Section */
        .get-started-section {
            margin-top: 60px;
            text-align: center;
        }

        .get-started-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .get-started-description {
            max-width: 700px;
            margin: 0 auto 25px;
            color: #666;
        }

        /* Featured Section */
        .featured-section {
            margin-top: 60px;
        }

        .featured-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .featured-card {
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            height: 250px;
        }

        .featured-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .featured-card-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: #fff;
        }

        .featured-card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .featured-card-text {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Button Styles */
        .btn-theme {
            background: linear-gradient(135deg, #05ce78 0%, #04a85f 100%);
            color: #fff;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(5, 206, 120, 0.3);
        }

        .btn-theme:hover {
            background: linear-gradient(135deg, #04a85f 0%, #038c4f 100%);
            color: #fff;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 206, 120, 0.4);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .resource-hub-title {
                font-size: 2rem;
            }

            .campaign-card-img {
                height: 150px;
            }

            .featured-card {
                height: 200px;
                margin-bottom: 20px;
            }
        }
    </style>
@endsection 