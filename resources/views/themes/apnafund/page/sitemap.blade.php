@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('style')
<style>
    .sitemap-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .sitemap-title {
        color: #D4AF37;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 3rem;
        text-align: center;
        position: relative;
    }
    
    .sitemap-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: #D4AF37;
    }
    
    .sitemap-card {
        background: #f1f1f1;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60px;
    }
    
    .sitemap-card:hover {
        background: #05ce78;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(5, 206, 120, 0.3);
    }
    
    .sitemap-card a {
        color: #666;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.5px;
        transition: color 0.3s ease;
        text-transform: uppercase;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .sitemap-card:hover a {
        color: white;
    }
    
    .sitemap-category {
        margin-bottom: 50px;
    }
    
    .sitemap-category h3 {
        color: #D4AF37;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
    }
    
    .sitemap-category h3::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: #D4AF37;
    }
    
    @media (max-width: 768px) {
        .sitemap-title {
            font-size: 2rem;
        }
        
        .sitemap-category h3 {
            font-size: 1.5rem;
        }
        
        .sitemap-card {
            margin-bottom: 15px;
            padding: 15px;
            min-height: 50px;
        }
        
        .sitemap-card a {
            font-size: 12px;
        }
    }
</style>
@endsection

@section('frontend')
    <div class="sitemap-section">
        <div class="container">
            <!-- All Pages Section -->
            <div class="sitemap-category">
                <div class="row">
                    <div class="col-12">
                        <h3>All Pages</h3>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('home') }}">HOME</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('about.us') }}">ABOUT US</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('contact') }}">CONTACT US</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('campaign') }}">CAMPAIGNS</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('faq') }}">FAQS</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ url('policy/privacy-policy/11') }}">PRIVACY POLICY</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ url('policy/terms-of-service/12') }}">TERMS OF SERVICE</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('help') }}">HELP</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('volunteers') }}">VOLUNTEERS</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('stories') }}">SUCCESS STORIES</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('upcoming') }}">UPCOMING CAMPAIGNS</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="sitemap-card">
                            <a href="{{ route('business.resources') }}">BUSINESS RESOURCES</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Categories -->
            <div class="sitemap-category">
                <div class="row">
                    <div class="col-12">
                        <h3>Campaign Categories</h3>
                    </div>
                </div>
                <div class="row g-3">
                    @php
                        $categories = App\Models\Category::active()->get();
                    @endphp
                    @foreach($categories as $category)
                        <div class="col-md-3 mb-3">
                            <div class="sitemap-card">
                                <a href="{{ route('campaign') }}?category={{ $category->slug }}">{{ strtoupper($category->name) }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection 