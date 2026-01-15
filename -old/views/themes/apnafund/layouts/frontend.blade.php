@extends($activeTheme . 'layouts.app')
@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
    $themeColors = getThemeColors();
    $dashboardStats = getDashboardStats();
    $recentActivities = getRecentActivities();
    $gigCategories = getGigCategories();
    $rewardTypes = getRewardTypes();
    $rewardColorThemes = getRewardColorThemes();
    $fileUploadLimits = getFileUploadLimits();
    $dashboardNavigation = getDashboardNavigation();
    $notificationTypes = getNotificationTypes();
    $userMenuItems = getUserMenuItems();
@endphp


@if ($setting->language)
    @php $languages = App\Models\Language::active()->get() @endphp
@endif

@section('content')
    @php
    
        $isHomePage = request()->routeIs('home') || request()->path() === '/';
       
    @endphp

    @if($isHomePage)
        @include($activeTheme.'partials.header')
    @else

        @include($activeTheme.'partials.header')
    @endif



    


    @yield('frontend')

    @php
        $footerContent = getSiteData('footer.content', true);
        $footerElements = getSiteData('footer.element', false, null, true);
        $footerContactElements = getSiteData('contact_us.element', false, null, true);
    @endphp

    @if(!request()->routeIs('campaign.donate'))
        @include($activeTheme.'partials.footer')
    @endif


@endsection

@push('page-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Import Inter font from Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            color: #1e1e1e;
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        /* Custom Theme Colors */
        .theme-color-text {
            color: #05ce78 !important;
        }

        .font-italic {
            font-style: italic;
        }

        .quotes {
            font-style: italic;
            font-weight: bold;
            text-transform: capitalize;
        }

        /* Custom Buttons */
        .button-theme {
            display: inline-block;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            width: 226px;
            text-align: center;
            border: none;
            border-radius: 8px;
            background-color: #05ce78;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin: 0 10px;
            color: white;
        }

        .button-theme:hover {
            background-color: #ffffff;
            opacity: 1;
            color: #000000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5, 206, 120, 0.3);
        }

        /* Hero Section Styles */
        .hero-heading {
            font-size: 8rem;
            line-height: .9;
            font-weight: 800;
        }

        /* Hero Section Mobile Responsive */
        @media (max-width: 1200px) {
            .hero-heading {
                font-size: 5rem;
            }
        }

        @media (max-width: 992px) {
            .hero-heading {
                font-size: 4rem;
            }
            
            .hero-section {
                height: 90vh;
                min-height: 500px;
            }
            
            .hero-section p {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 768px) {
            .hero-heading {
                font-size: 2.5rem !important;
                line-height: 1.1;
                text-align: center;
            }
            
            .hero-section {
                height: 100vh;
                min-height: 450px;
                background-position: center 25%;
                text-align: center;
                padding-top: 80px;
            }
            
            .hero-section .container {
                padding: 20px 15px;
                height: auto;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            
            .hero-section .row {
                justify-content: center;
                text-align: center;
            }
            
            .hero-section .col-lg-12 {
                text-align: center;
            }
            
            .hero-section p {
                font-size: 0.9rem;
                text-align: center;
                margin: 1rem auto;
                max-width: 90%;
                line-height: 1.4;
            }
            
            .button-container {
                text-align: center;
                margin-top: 2rem;
                width: 100%;
            }
            
            .button-theme {
                width: 100%;
                max-width: 250px;
                padding: 12px 20px;
                font-size: 14px;
                margin: 0 auto;
                display: block;
            }
        }

        /* Dropdown Styles */
        .dropdown-menu {
            position: absolute !important;
            z-index: 1000 !important;
            display: none;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0;
            font-size: 1rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        }

        .dropdown-menu.show {
            display: block !important;
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            color: #1e2125;
            background-color: #e9ecef;
        }

        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid rgba(0, 0, 0, 0.175);
        }

        .search-input {
            border: 1px solid #e0e0e0;
            border-radius: 50px !important;
            padding: 12px 20px;
            font-size: 16px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            width: 100%;
            max-width: 500px;
        }

        .search-input:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 4px 12px rgba(5, 206, 120, 0.2);
        }

        .search-input::placeholder {
            color: #999;
            font-weight: 400;
        }

        /* Header Styles */
        .header {
            background-color: #05ce78;
            min-height: 80px;
            display: flex;
            align-items: center;
        }

        /* Hero Section Background */
        .hero-section {
            position: relative;
            width: 100%;
            height: 85vh;
            background-image: url('{{ custom_asset($activeThemeTrue . 'images/banner-12.jpg') }}');
            background-size: cover;
            background-position: center 70%;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .hero-section .container {
            position: relative;
            z-index: 3;
        }

        .hero-section .text-dark {
            position: relative;
            z-index: 3;
        }

        .hero-section .button-container {
            position: relative;
            z-index: 3;
        }

        /* Hero section slide-in animation */
        .hero-section .col-lg-12 {
            opacity: 0;
            transform: translateX(-100px);
            animation: slideInFromLeft 1.2s ease-out forwards;
        }

        @keyframes slideInFromLeft {
            0% {
                opacity: 0;
                transform: translateX(-100px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Stagger the animation for different elements */
        .hero-section .text-dark {
            animation-delay: 0.2s;
        }

        .hero-section .mt-2 {
            animation-delay: 0.4s;
        }

        .hero-section .button-container {
            animation-delay: 0.6s;
        }

        .logo-img {
            height: 40px;
            width: auto;
            display: block;
        }

        /* Header container alignment */
        .header .container {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .header .row {
            width: 100%;
            align-items: center;
        }

        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .search-btn {
            position: absolute;
            right: 5px;
            background: #05ce78;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: #04b368;
            transform: scale(1.05);
        }

        /* Info Banner Section Styles */
        .info-banner-section {
            background-color: #f8f9fa;
            padding: 30px 0;
            border-top: 3px solid #05ce78;
            border-bottom: 1px solid #e9ecef;
        }

        .info-banner {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
            min-width: 200px;
            justify-content: center;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background-color: #05ce78;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(5, 206, 120, 0.3);
        }

        .info-text {
            font-weight: 600;
            font-size: 1rem;
            color: #2d3436;
        }

        .info-divider {
            width: 2px;
            height: 40px;
            background-color: #dee2e6;
        }

        @media (max-width: 768px) {
            .info-banner {
                flex-direction: column;
                gap: 25px;
            }

            .info-divider {
                display: none;
            }

            .info-item {
                min-width: auto;
                width: 100%;
            }
        }

        /* Projects Section Styles */
        .projects-section {
            padding: 80px 0;
            background-color: #ffffff;
        }

        .section-title-sm {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 50px;
        }

        .project-card-link {
            text-decoration: none;
            color: inherit;
        }

        .project-card {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            margin-bottom: 30px;
        }

        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .project-card.featured-project {
            background: linear-gradient(135deg, #05ce78 0%, #04a85f 100%);
            color: white;
        }

        .project-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .project-card:hover .project-image img {
            transform: scale(1.05);
        }

        .project-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
        }

        .project-category {
            background-color: #05ce78;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            align-self: flex-start;
        }

        .project-progress {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: #05ce78;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .progress-text {
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .project-content {
            padding: 25px;
        }

        .project-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .featured-project .project-title {
            color: white;
        }

        .project-description {
            font-size: 1rem;
            color: #636e72;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .featured-project .project-description {
            color: rgba(255, 255, 255, 0.9);
        }

        .project-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2d3436;
        }

        .featured-project .stat-value {
            color: white;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #636e72;
            font-weight: 500;
        }

        .featured-project .stat-label {
            color: rgba(255, 255, 255, 0.8);
        }

        @media (max-width: 768px) {
            .projects-section {
                padding: 60px 0;
            }

            .section-title-sm {
                font-size: 2rem;
                text-align: center;
            }

            .project-image {
                height: 200px;
            }

            .project-content {
                padding: 20px;
            }

            .project-title {
                font-size: 1.2rem;
            }
        }

        /* Footer Styles */
        .footer {
            background-color: #1e1e1e;
            color: white;
            padding: 40px 0 20px;
        }

        .footer-logo-img {
            height: 40px;
            width: auto;
            margin-bottom: 10px;
        }

        .footer-tagline {
            font-size: 14px;
            color: #ccc;
            margin: 0;
        }

        .footer-info {
            text-align: right;
        }

        .copyright {
            font-size: 14px;
            color: #ccc;
            margin-bottom: 5px;
        }

        .email {
            font-size: 14px;
            color: #05ce78;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .email:hover {
            color: #04b368;
        }

        /* Responsive header adjustments */
        @media (max-width: 768px) {
            .header {
                min-height: 70px;
                padding: 10px 0;
            }

            .header .row {
                gap: 15px;
            }

            .logo-img {
                height: 35px;
            }

            .search-input {
                max-width: 100%;
                font-size: 14px;
                padding: 10px 16px;
            }

            .search-btn {
                padding: 8px 12px;
                font-size: 12px;
            }

            .footer {
                padding: 30px 0 15px;
            }

            .footer-info {
                text-align: center;
                margin-top: 20px;
            }

            .footer-logo-img {
                height: 35px;
            }
        }
    </style>
@endpush

@section('page-script')
    <script>
        alert('assets/universal/js/bootstrap.js');
        (function($) {
            "use strict";

            // Initialize Bootstrap dropdowns
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize all dropdowns
                var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl);
                });

                // Add click event for debugging
                document.querySelectorAll('.dropdown-toggle').forEach(function(element) {
                    element.addEventListener('click', function(e) {
                        console.log('Dropdown clicked');
                    });
                });
            });

            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide');
            }, 2000);

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');

                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
                        colum.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });
        })(jQuery);
    </script>
@endsection
