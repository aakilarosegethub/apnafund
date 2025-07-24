<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{ $setting->siteName(__($pageTitle)) }}</title>

        @include('partials.seo')
        <link rel="stylesheet" href="{{ asset('assets/universal/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/slick.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/universal/css/tabler.css') }}">
        <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/lightbox.min.css') }}">
        <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/aos.css') }}">
        <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/main.css') }}">
        <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/color.php?color1=' . $setting->first_color . '&color2=' . $setting->second_color) }}">

        @stack('page-style-lib')
        @stack('page-style')
    </head>

    <body>
        <div class="preloader">
            <div class="loader-p"></div>
        </div>

        <div class="body-overlay"></div>
        <div class="sidebar-overlay"></div>

        <a class="scroll-top">
            <i class="ti ti-arrow-big-up-lines"></i>
        </a>

        @yield('content')

        <script src="{{ asset('assets/universal/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/universal/js/bootstrap.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/slick.min.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/viewport.jquery.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/lightbox.min.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/aos.js') }}"></script>
        <script src="{{ asset($activeThemeTrue . 'js/main.js') }}"></script>

        @include('partials.plugins')
        @include('partials.toasts')

        @stack('page-script-lib')
        @stack('page-script')
    </body>
</html>
