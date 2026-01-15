<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $setting->siteName($pageTitle ?? '') }}</title>
        <link rel="shortcut icon" type="image/png" href="{{ getImage(getFilePath('logoFavicon').'/favicon.png') }}">

        <link rel="stylesheet" href="{{ asset('assets/universal/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/universal/css/tabler.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/select2.min.css') }}">

        @stack('page-style-lib')
        @stack('page-style')

        <link rel="stylesheet" href="{{ asset('assets/admin/css/main.css') }}">
    </head>

    <body>
        @yield('content')

        <script src="{{ asset('assets/universal/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/universal/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/admin/js/select2.min.js') }}"></script>

        @stack('page-script-lib')
        @stack('page-script')

        <script src="{{ asset('assets/admin/js/main.js') }}"></script>

        @include('partials.toasts')
    </body>
</html>
