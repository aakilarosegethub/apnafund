@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
    $imgs_path = '/apnacroudfunding/';
@endphp
@extends($activeTheme . 'layouts.frontend')
@section('style')
<style>
    html {
    font-size: 62.5%;
}

/* CSS Custom Properties (Variables) */
/* KICKSTARTER EXACT STYLE CTA SECTION 🎨 */
.kickstarter-cta {
    padding: 0;
  
    position: relative;
    overflow: hidden;
    min-height: 100vh;
}

.cta-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 100vh;
    position: relative;
    gap: 2rem; /* spacing between sections */
}

/* Left Section */
.left-section {
    position: relative;
    width: 30%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
}

.yellow-shape {
    position: absolute;
    z-index: 1;
      height: 458px;
    margin-left: -93px;
}

.yellow-shape img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.person-image {
    position: relative;
    z-index: 2;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    overflow: hidden;
    margin-left: 50px;
}

.person-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Center Content */
.center-content {
    position: relative;
    width: 40%;
    text-align: center;
    z-index: 10;
}

.orange-star {
    position: absolute;
    top: -100px;
    right: -50px;
    width: 300px;
    height: 300px;
    z-index: 1;
}

.orange-star img {
    width: 100%;
    object-fit: contain;
    height: 76%;
}

.main-text {
    position: relative;
    z-index: 5;
}

.main-title {
    font-size: 7rem;
    font-weight: 900;
    color: #2d2d2d;
    line-height: 0.9;
    margin-bottom: 2rem;
    letter-spacing: -0.03em;
}

.funding-text {
    color: #2d2d2d;
    position: relative;
}

.main-subtitle {
    font-size: 1.8rem;
    color: #666;
    margin-bottom: 3rem;
    line-height: 1.5;
    font-weight: 400;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.get-started-btn {
    display: inline-block;
    background: #2d2d2d;
    color: white;
    font-size: 1.6rem;
    font-weight: 600;
    padding: 1.5rem 3rem;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.get-started-btn:hover {
    background: #1a1a1a;
    color: white;
}

.purple-blob {
    position: absolute;
    bottom: -150px;
    left: -100px;
    width: 400px;
    height: 400px;
    z-index: 1;
}

.purple-blob img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* Right Section */
.right-section {
    position: relative;
    width: 30%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.green-shapes {
    position: absolute;
    z-index: 1;
    width: 450px;
    height: 450px;
}

.green-shapes img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.person-working {
    position: relative;
    z-index: 2;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 50px;
}

.person-working img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Responsive Design for Kickstarter CTA */
@media (max-width: 991.98px) {
    .kickstarter-cta {
        min-height: auto;
        padding: 5rem 2rem;
    }

    .cta-wrapper {
        flex-direction: column;
        min-height: auto;
        text-align: center;
    }

    .left-section, .center-content, .right-section {
        width: 100%;
        margin-bottom: 3rem;
    }

    .main-title {
        font-size: 5rem;
    }

    .main-subtitle {
        font-size: 1.6rem;
    }

    .yellow-shape, .green-shapes {
        width: 300px;
        height: 300px;
    }

    .orange-star, .purple-blob {
        width: 200px;
        height: 200px;
    }

    .person-image, .person-working {
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }
}

@media (max-width: 767.98px) {
    .main-title {
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }

    .main-subtitle {
        font-size: 1.4rem;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }

    .get-started-btn {
        font-size: 1.4rem;
        padding: 1.2rem 2.5rem;
    }

    .yellow-shape, .green-shapes {
        width: 250px;
        height: 250px;
    }

    .orange-star, .purple-blob {
        width: 150px;
        height: 150px;
    }

    .person-image, .person-working {
        width: 180px;
        height: 180px;
    }

    .left-section, .right-section {
        margin-bottom: 2.5rem;
    }
}

@media (max-width: 575.98px) {
    .main-title {
        font-size: 3rem;
    }

    .main-subtitle {
        font-size: 1.3rem;
        padding: 0 1rem;
    }

    .yellow-shape, .green-shapes {
        width: 200px;
        height: 200px;
    }
    .green-shapes img{
        display: none;
    }
     .green-shapes{
        display: none;
    }

    .orange-star, .purple-blob {
        width: 120px;
        height: 120px;
    }

    .person-image, .person-working {
        width: 150px;
        height: 150px;
    }

    .get-started-btn {
        font-size: 1.3rem;
        padding: 1rem 2rem;
        width: 100%;
    }
}








/*/* NEW MODERN BOX DESIGN (Same as screenshot) */
/* Section styling */
.confidence-section {
    padding: 80px 0;
    background: #ffffff;
    text-align: center;
}

.confidence-title {
    font-size: 4rem;
    font-weight: 900;
    color: #1d1d1d;
    margin-bottom: 50px;
}

/* Box wrapper */
.conf-box {
    background: #ffffff;
    padding: 30px;
    border-radius: 25px;
    text-align: left;
    border: 2px solid transparent;
    background-clip: padding-box;
    position: relative;
    transition: 0.3s ease;
    min-height: 300px;
    box-shadow: 0px 8px 22px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

/* ICON */
.conf-box i {
    font-size: 40px;
    margin-bottom: 15px;
}

/* Title */
.conf-box h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 12px;
}

/* Text */
.conf-box p {
    font-size: 1.5rem;
    color: #555;
    line-height: 1.5;
}

/* COLOR THEMES */
.box1 { border-color: #00c6a7; }
.box1 i { color: #00c6a7; }

.box2 { border-color: #2b7bff; }
.box2 i { color: #2b7bff; }

.box3 { border-color: #c46bff; }
.box3 i { color: #c46bff; }

/* Hover effect */
.conf-box:hover {
    transform: translateY(-7px);
    box-shadow: 0px 12px 28px rgba(0,0,0,0.12);
}

/* Boxes responsive width using Bootstrap + flex */
.row.conf-box-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px; /* spacing between boxes */
}

.row.conf-box-row > .col-custom {
    flex: 0 0 30%;
    max-width: 30%;
}

/* Tablets */
@media (max-width: 992px) {
    .row.conf-box-row > .col-custom {
        flex: 0 0 45%;
        max-width: 45%;
    }
}

/* Mobiles */
@media (max-width: 768px) {
    .row.conf-box-row > .col-custom {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .conf-box {
        text-align: center;
        min-height: auto;
    }
    .conf-box h3 {
        font-size: 1.6rem;
    }
    .conf-box p {
        font-size: 1.3rem;
    }
    .confidence-title {
        font-size: 3rem;
    }
}

/* Extra small screens */
@media (max-width: 576px) {
    .conf-box h3 {
        font-size: 1.5rem;
    }
    .conf-box p {
        font-size: 1.2rem;
    }
    .confidence-title {
        font-size: 2.5rem;
    }
}

/* Button responsive */
.conf-btn {
    background: linear-gradient(90deg, #00c6a7, #2b7bff, #c46bff);
    padding: 12px 28px;
    border-radius: 30px;
    border: none;
    font-size: 1.6rem;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
    display: inline-block;
    margin-top: 30px;
}

.conf-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0px 10px 25px rgba(0,0,0,0.15);
}

@media (max-width: 576px) {
    .conf-btn {
        width: 100%;
        padding: 12px 0;
        font-size: 1.4rem;
    }
}











.conf-btn {
    background: linear-gradient(90deg, #00c6a7, #2b7bff, #c46bff);
    padding: 12px 28px;
    border-radius: 30px;
    border: none;
    font-size: 1.6rem;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
    display: inline-block;
    margin-top: 30px;
}

.conf-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.15);
}
.box_2{
    width:30%;
}


</style>
@endsection
@section('frontend')
    @php
        $commonSliderImage = custom_asset($activeThemeTrue . 'images/slider-img-shape-2.png');
        $commonShapeImage  = custom_asset($activeThemeTrue . 'images/mask-shape-1.png');
    @endphp
    <section class="kickstarter-cta">
        <div class="container-fluid">
            <div class="cta-wrapper">
                <!-- Left Side - Yellow Shape with Person -->
                <div class="left-section">
                    <div class="yellow-shape">
                        <img src="{{ $imgs_path }}Pic.png" alt="Yellow Shape">

                    </div>
                    <div class="person-image">
                
                    </div>
                </div>

                <!-- Center Content -->
                <div class="center-content">
                    <!-- Orange Star Shape -->
                    <div class="orange-star">
                       <img src="{{ $imgs_path }}Star.png" alt="Yellow Shape">
                    </div>
                    
                    

                    
                    <!-- Main Text -->
                    <div class="main-text">
                        <h1 class="main-title">
                          
                            <span class="funding-text">Raise Funds</span><br>
                            For Your Projects
                        </h1>
                        <p class="main-subtitle">
                          Whatever spark your business dream holds, ApnaCrowdfunding is here to fuel it with the support and funding it deserves. We help bring your vision to life-giving birth to the projects you imagine. Join a community that turns ideas into reality.
                        </p>
                        <a href="{{ url('business-resources') }}" class="get-started-btn">Get started</a>
                    </div>

                    <!-- Purple Blob Shape -->
                
                    
                </div>

                <!-- Right Side - Green Shapes with Person -->
                <div class="right-section">
                    <div class="green-shapes">
                        <img src="{{ $imgs_path }}Brush.png" alt="Yellow Shape">
                    </div>
                  
                </div>
            </div>
        </div>
    </section>

    <!-- Create With Confidence Section -->
<section class="confidence-section">
    <div class="container">

        <h1 class="confidence-title">Create with confidence</h1>

      <div class="row justify-content-center conf-box-row">
    <div class="col-custom mb-4">
        <div class="conf-box box1">
            <i class="fas fa-users"></i>
            <h3>Join a community of millions backers on ApnaCrowdfunding</h3>
            <p> 
Connect with passionate backers who share your values, believe in your ideas and are excited to help you build what matters. Your community is here — people who get your vision and stand behind it.
</p>
        </div>
    </div>

    <div class="col-custom mb-4">
        <div class="conf-box box2">
            <i class="fas fa-shield-alt"></i>
            <h3>Creative Freedom, Always</h3>
            <p> On ApnaCrowdfunding, you maintain full creative control and complete ownership of every project you launch. Build without limits — and turn your passion into reality on your terms.</p>
        </div>
    </div>

    <div class="col-custom mb-4">
        <div class="conf-box box3">
            <i class="fas fa-chart-line"></i>
            <h3>A Platform That Helps You Win, Yes</h3>
            <p>We’re more than a funding platform — we’re here to help you succeed.                   Access smart tools, expert guidance and insights that have helped over 10 million creators and innovators start their journeys. From your first concept to your final milestone, we’re with you every step of the way.</p>
        </div>
    </div>
</div>


       <a href="{{ route('start.project') }}">
  <button class="conf-btn">Start a Project</button>
</a>

    </div>
</section>

@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ custom_asset($activeThemeTrue . 'css/odometer.css') }}">
@endpush

@push('page-script-lib')
    <script src="{{ custom_asset($activeThemeTrue . 'js/odometer.min.js') }}"></script>
@endpush

@push('page-script')
    <script>
        'use strict';

        (function ($) {
            $('.subscribeBtn').on('click',function () {
                let email = $('[name=subscriber_email]').val();
                let csrf  = '{{csrf_token()}}';
                let url   = "{{ route('subscriber.store') }}";
                let data  = {email:email, _token:csrf};

                $.post(url, data,function(response) {
                    if(response.success){
                        showToasts('success', response.success);
                        $('[name=subscriber_email]').val('');
                    }else{
                        showToasts('error', response.error);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
