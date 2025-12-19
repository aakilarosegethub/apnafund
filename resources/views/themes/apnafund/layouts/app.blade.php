
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $setting->siteName(__($pageTitle)) }}</title>

        @include('partials.seo')
    <link rel="stylesheet" href="{{ asset('apnafund/assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('apnafund/assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/universal/css/iziToast.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/fav-icon.png') }}" type="image/png">
    
    <!-- Custom Header Code -->
    {!! getCustomCode('header') !!}
    <style>
        /* Dashboard Specific Styles */
        .dashboard-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .dashboard-header {
            background: #05ce78;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard-nav {
            background: #fff;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .nav-tabs {
            border: none;
            gap: 10px;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            color: #6c757d;
            background: transparent;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #05ce78;
            background: rgba(5, 206, 120, 0.1);
        }

        .nav-tabs .nav-link.active {
            background: #05ce78;
            color: #fff;
            box-shadow: 0 4px 15px rgba(5, 206, 120, 0.3);
        }

        .dashboard-content {
            padding: 40px 0;
        }

        .content-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #05ce78;
            box-shadow: 0 0 0 0.2rem rgba(5, 206, 120, 0.25);
        }

        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
        }

        .btn-primary {
            background: #05ce78;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #04b367;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5, 206, 120, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .preview-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .preview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.15);
        }

        .preview-image {
            height: 200px;
            background: linear-gradient(135deg, #05ce78, #04b367);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 48px;
        }

        .preview-content {
            padding: 25px;
        }

        .preview-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .preview-description {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .preview-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .preview-category {
            background: rgba(5, 206, 120, 0.1);
            color: #05ce78;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .preview-amount {
            font-weight: 700;
            color: #05ce78;
            font-size: 1.1rem;
        }

        .preview-progress {
            margin-bottom: 15px;
        }

        .progress {
            height: 8px;
            border-radius: 10px;
            background: #e9ecef;
        }

        .progress-bar {
            background: #05ce78;
            border-radius: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: #05ce78;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }

        .file-upload {
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload:hover {
            border-color: #05ce78;
            background: rgba(5, 206, 120, 0.05);
        }

        .file-upload i {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .file-upload-text {
            color: #6c757d;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }

        .alert-success {
            background: rgba(5, 206, 120, 0.1);
            color: #05ce78;
        }

        .alert-info {
            background: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        /* Toast Notification Customization for ApnaCrowdfunding Theme */
        .iziToast {
            border-radius: 10px !important;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .iziToast.iziToast-success {
            background: #05ce78 !important;
            border-color: #05ce78 !important;
        }

        .iziToast.iziToast-error {
            background: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        .iziToast.iziToast-warning {
            background: #ffc107 !important;
            border-color: #ffc107 !important;
        }

        .iziToast.iziToast-info {
            background: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
        }

        .iziToast-body {
            font-weight: 500 !important;
        }

        @media (max-width: 768px) {
            .nav-tabs {
                flex-wrap: wrap;
            }
            
            .nav-tabs .nav-link {
                font-size: 14px;
                padding: 10px 20px;
            }
            
            .content-card {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        /* Footer */
        .footer {
            background: #333;
            color: #fff;
            padding: 40px 0 20px;
            margin-top: 50px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-section h4 {
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
        }

        .footer-section ul li a:hover {
            color: #05ce78;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #555;
            color: #ccc;
        }
    </style>
    @yield('style')
</head>

<body>
    @yield('content')

    <script src="{{ asset('apnafund/assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/universal/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('assets/universal/js/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('assets/universal/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/themes/'.bs('active_theme').'/js/slick.min.js') }}"></script>
        <script src="{{ asset('assets/themes/'.bs('active_theme').'/js/viewport.jquery.js') }}"></script>
        <script src="{{ asset('assets/themes/'.bs('active_theme').'/js/lightbox.min.js') }}"></script>
        <script src="{{ asset('assets/themes/'.bs('active_theme').'/js/aos.js') }}"></script>
        <script src="{{ asset('assets/themes/'.bs('active_theme').'/js/main.js') }}"></script>

        @include('partials.plugins')
        @include('partials.toasts')

        @stack('page-script-lib')
        @stack('page-script')
        @yield('script')
        
        <!-- Custom Footer Code -->
        {!! getCustomCode('footer') !!}

</body>

</html>