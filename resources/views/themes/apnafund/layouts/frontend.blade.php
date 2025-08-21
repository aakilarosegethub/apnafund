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
        @include($activeTheme.'partials.home-header')
    @else

        @include($activeTheme.'partials.simple-header')
    @endif



    


    @yield('frontend')

    @php
        $footerContent = getSiteData('footer.content', true);
        $footerElements = getSiteData('footer.element', false, null, true);
        $footerContactElements = getSiteData('contact_us.element', false, null, true);
    @endphp

    @include($activeTheme.'partials.footer')

    @php
        $cookie = App\Models\SiteData::where('data_key', 'cookie.data')->first();
    @endphp

    @if ($cookie->data_info->status == ManageStatus::ACTIVE && !\Cookie::get('gdpr_cookie'))
        <!-- cookies dark version start -->
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon">
                <img src="{{ getImage('assets/images/cookie.png') }}" alt="cookies">
            </div>

            <p class="mt-4 cookies-card__content">{{ $cookie->data_info->short_details }}</p>

            <div class="cookies-card__btn mt-4">
                <button type="button" class="btn btn--base px-5 policy">@lang('Allow')</button>
                <a href="{{ route('cookie.policy') }}" target="_blank" type="button" class="text--base px-5 pt-3">@lang('Learn More')</a>
            </div>
        </div>
        <!-- cookies dark version end -->
    @endif

@endsection

@push('page-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Import Inter font from Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

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

@push('page-script')
    <script>
        (function($) {
            "use strict";

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
@endpush
