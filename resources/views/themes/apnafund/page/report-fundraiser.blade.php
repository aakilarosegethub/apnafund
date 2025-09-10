@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
    $themeColors = getThemeColors();
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('style')
<style>
    body {
        background: #ffffff;
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        line-height: 1.6;
        color: #333;
    }

    /* Header */
    .main-header {
        background: #05ce78;
        padding: 20px 0;
        margin: 0;
        border: none;
    }

    .main-header .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .main-header .logo {
        text-align: center;
        margin: 0;
    }

    .main-header .logo-img {
        height: 40px;
        width: auto;
    }

    /* Navigation */
    .nav-links {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        margin-top: 10px;
    }

    .nav-left,
    .nav-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .nav-link {
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
    }

    .nav-link:hover {
        color: #fff;
        text-decoration: underline;
    }

    .btn-start-fundraiser {
        background: #fff;
        color: #05ce78;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-start-fundraiser:hover {
        background: #f0f0f0;
        color: #05ce78;
        text-decoration: none;
    }

    /* Main Content */
    .report-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .report-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .report-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .report-subtitle {
        font-size: 1.2rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }

    .report-content {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        border: 1px solid #f0f0f0;
        margin-bottom: 40px;
    }

    .report-content h1,
    .report-content h2,
    .report-content h3,
    .report-content h4,
    .report-content h5,
    .report-content h6 {
        margin-bottom: 1rem;
        color: #333;
        font-weight: 600;
    }

    .report-content h1 {
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }

    .report-content h2 {
        font-size: 1.5rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .report-content h3 {
        font-size: 1.3rem;
        margin-top: 1.5rem;
        margin-bottom: 0.8rem;
    }

    .report-content p {
        color: #555;
        margin-bottom: 1rem;
        line-height: 1.8;
    }

    .report-content ul,
    .report-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }

    .report-content li {
        color: #555;
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }

    .report-content strong {
        color: #333;
        font-weight: 600;
    }

    .report-content a {
        color: #05ce78;
        text-decoration: none;
        font-weight: 500;
    }

    .report-content a:hover {
        text-decoration: underline;
    }

    .report-actions {
        text-align: center;
        margin-top: 40px;
    }

    .btn-report {
        background: #05ce78;
        color: #fff;
        padding: 15px 30px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        display: inline-block;
        transition: all 0.3s ease;
        margin: 0 10px;
    }

    .btn-report:hover {
        background: #04b368;
        color: #fff;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(5, 206, 120, 0.3);
    }

    .btn-secondary {
        background: #fff;
        color: #333;
        border: 2px solid #ddd;
        padding: 15px 30px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        display: inline-block;
        transition: all 0.3s ease;
        margin: 0 10px;
    }

    .btn-secondary:hover {
        background: #f8f9fa;
        color: #333;
        text-decoration: none;
        border-color: #05ce78;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .report-title {
            font-size: 2rem;
        }

        .report-subtitle {
            font-size: 1rem;
        }

        .report-content {
            padding: 25px;
        }

        .report-actions {
            margin-top: 30px;
        }

        .btn-report,
        .btn-secondary {
            display: block;
            margin: 10px auto;
            width: 100%;
            max-width: 300px;
        }
    }
</style>
@endsection

@section('frontend')
<div class="report-container">
    <!-- Header -->
    <div class="report-header">
        <h1 class="report-title">{{ $reportContent->data_info->title ?? 'Report a Fundraiser' }}</h1>
        <p class="report-subtitle">
            Help us maintain the integrity of our platform by reporting any suspicious or inappropriate fundraising campaigns.
        </p>
    </div>

    <!-- Content -->
    <div class="report-content">
        @php echo $reportContent->data_info->content @endphp
    </div>

    <!-- Actions -->
    <div class="report-actions">
        <a href="{{ route('contact') }}" class="btn-report">
            <i class="fas fa-flag"></i> Report Now
        </a>
        <a href="{{ route('home') }}" class="btn-secondary">
            <i class="fas fa-home"></i> Back to Home
        </a>
    </div>
</div>
@endsection
