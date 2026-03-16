@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')
@php
    $showRateDebug = request()->has('test');
    $setting = bs();
    $siteCur = strtoupper($setting->site_cur ?? 'USD');
@endphp
<style>
    .campaign-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .campaign-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
    a.text-decoration-none:hover .campaign-card {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
    .campaign-image {
        display: block;
        width: 100%;
    }
    a.text-decoration-none {
        display: block;
        width: 100%;
    }
    .featured-modern {
    align-items: center;
}

/* FEATURED WRAPPER */
.featured-modern {
    gap: 30px;
}

/* IMAGE */
.featured-image-modern {
    height: 420px;
    border-radius: 26px;
    background-size: cover;
    background-position: center;
    position: relative;
    box-shadow: 0 25px 50px rgba(0,0,0,.15);
}

/* DEMO IMAGE */
.project-image-1 {
    background-image: url('https://apnacrowdfunding.com/assets/universal/images/campaign/69006a68a3b4e1761634920.jpeg'); /* <-- image path */
}

/* TAG */
.featured-tag {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #ff6a00;
    color: #fff;
    padding: 8px 18px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
}

/* CONTENT BOX */
.featured-box-modern {
    background: #ffffff;
    padding: 38px;
    border-radius: 26px;
    box-shadow: 0 20px 45px rgba(0,0,0,0.08);
}

/* TITLE */
.featured-box-modern h3 {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 15px;
}

/* TEXT */
.featured-box-modern p {
    color: #555;
    margin-bottom: 30px;
    line-height: 1.6;
}

/* STATS */
.featured-stats-modern {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.featured-stats-modern strong {
    font-size: 20px;
}

.featured-stats-modern span {
    font-size: 13px;
    color: #777;
}

/* PROGRESS */
.progress-modern {
    height: 8px;
    background: #eee;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 30px;
}

.progress-modern-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #5dd879);
}

/* BUTTON */
.featured-btn {
    display: inline-block;
    background: #28a745;
    color: #fff;
    padding: 14px 34px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: .3s;
}

.featured-btn:hover {
    background: #1f8a3c;
    transform: translateY(-2px);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .featured-image-modern {
        height: 260px;
    }

    .featured-stats-modern {
        flex-direction: column;
        gap: 12px;
    }
}
.projects-section h2{
    text-align:center;
    color: #198754;
}
    
    /* LINK RESET */
/* LINK RESET */
.project-card-link{
    text-decoration: none;
    color: inherit;
    display: block;
}

/* CARD */
.project-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 18px 40px rgba(0,0,0,.08);
    transition:.3s;
}

.project-card:hover{
    transform:translateY(-6px);
}
.project-image{
    position: relative;
    width: 100%;
       height: 505px !important;
    overflow: hidden;

    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;   
    background-color: #f3f3f3;
}

.project-image-2{
    background-image:url('https://images.unsplash.com/photo-1549880338-65ddcdfd017b');
}
.project-image-3{
    background-image:url('https://images.unsplash.com/photo-1506806732259-39c2d0268443');
}
.project-image-4{
    background-image:url('https://images.unsplash.com/photo-1511379938547-c1f69419868d');
}

.project-content{
    padding:16px 18px;
}

.project-title{
    font-size:18px;
    font-weight:700;
}

.project-description{
    font-size:14px;
    color:#666;
}

/* PAGINATION */
.pagination-wrap{
    text-align:center;
    margin-top:30px;
}

.page{
    display:inline-block;
    padding:6px 12px;
    margin:0 5px;
    cursor:pointer;
    font-weight:600;
    color:#28a745;
}

.page.active{
    text-decoration:underline;
}

/* Rate debug icon (?test=1) */
.rate-debug-icon { cursor: pointer; color: #198754; font-size: 11px; margin-left: 4px; opacity: 0.85; }
.rate-debug-icon:hover { opacity: 1; }

</style>
<!-- HERO -->
@php
    $heroBgImage = null;
    $heroHeading1 = 'Crowd';
    $heroHeading2 = 'By';
    $heroHeading3 = 'For';
    $heroDescription = 'Together, we empower small businesses— From young dreamers, bold visionaries and those who want to improve their societies.';
    $heroButtonText = 'Explore Campaigns';
    $heroButtonUrl = route('campaign');
    
    if ($heroContent && $heroContent->data_info) {
        $heroData = is_array($heroContent->data_info) ? $heroContent->data_info : (array)$heroContent->data_info;
        $heroBgImage = $heroData['hero_background_image'] ?? null;
        $heroHeading1 = $heroData['hero_heading_1'] ?? 'Crowd';
        $heroHeading2 = $heroData['hero_heading_2'] ?? 'By';
        $heroHeading3 = $heroData['hero_heading_3'] ?? 'For';
        $heroDescription = $heroData['hero_description'] ?? $heroDescription;
        $heroButtonText = $heroData['button_text'] ?? $heroButtonText;
        $heroButtonUrl = $heroData['button_url'] ?? $heroButtonUrl;
    }
@endphp
<section class="hero mt-5" @if($heroBgImage) style="background-image: url('{{ custom_asset('assets/images/site/home/' . $heroBgImage) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;" @endif>
  <div class="container">
    <span class="badge bg-light text-success mb-3">50,000+ Backers</span>
    <h1>
        @php
            // Helper function to split on space, return [first, rest]
            if (!function_exists('splitFirstWord')) {
            function splitFirstWord($text) {
                $parts = explode(' ', $text, 2);
                return [
                  $parts[0] ?? '',
                  $parts[1] ?? ''
                ];
                }
            }
            [$h1_first, $h1_rest] = splitFirstWord($heroHeading1);
            [$h2_first, $h2_rest] = splitFirstWord($heroHeading2);
            [$h3_first, $h3_rest] = splitFirstWord($heroHeading3);
        @endphp
        <span>{{ $h1_first }}</span>{{ $h1_rest ? ' ' . $h1_rest : '' }}{{ $heroHeading1 == 'Crowd' ? 'Funding' : '' }}
        <br><span>{{ $h2_first }}</span>{{ $h2_rest ? ' ' . $h2_rest : '' }}{{ $heroHeading2 == 'By' ? ' The People,' : '' }}
        <br><span>{{ $h3_first }}</span>{{ $h3_rest ? ' ' . $h3_rest : '' }}{{ $heroHeading3 == 'For' ? ' The People,' : '' }}
    </h1>
    <p class="mt-3">
        {{ $heroDescription }}
    </p>
    <a href="{{ $heroButtonUrl }}" class="btn btn-light mt-3 px-4">
        {{ $heroButtonText }}
    </a>
  </div>
</section>



<!-- TRENDING CAMPAIGN SECTION -->
@php
    $trendingCampaignContent = \App\Models\SiteData::where('data_key', 'home.trending_campaign')->first();
    $showTrending = 0;
    $trendingCampaignId = null;
    $trendingCampaign = null;
    
    if ($trendingCampaignContent && $trendingCampaignContent->data_info) {
        $dataInfo = is_array($trendingCampaignContent->data_info) 
            ? $trendingCampaignContent->data_info 
            : (array)$trendingCampaignContent->data_info;
        $showTrending = $dataInfo['show_trending'] ?? 0;
        $trendingCampaignId = $dataInfo['trending_campaign_id'] ?? null;
    }
    
    if ($showTrending == 1 && $trendingCampaignId) {
        $trendingCampaign = \App\Models\Campaign::where('id', $trendingCampaignId)->approve()->running()->first();
  }
@endphp


<!-- CATEGORIES -->
@php
    // Get only categories that have at least one approved campaign
    $categories = \App\Models\Category::active()
        ->whereHas('campaigns', function($query) {
            $query->where('status', 1); // Approved campaigns
        })
        ->limit(6)
        ->get();
@endphp


    @if($showTrending == 1 && $trendingCampaign)
    <section class="projects-section">
    <div class="container">
            @php
                // Get raised amount - use raised_amount field
                $raised = $trendingCampaign->raised_amount ?? 0;
                $goal = $trendingCampaign->goal_amount ?? 1;
                $percentage = min(100, ($raised / $goal) * 100);
                
                // Calculate days left dynamically
                $daysLeft = 0;
                $daysText = 'Days Left';
                if ($trendingCampaign->end_date) {
                    try {
                        $endDate = \Carbon\Carbon::parse($trendingCampaign->end_date);
                        $now = \Carbon\Carbon::now();
                        
                        // Check if campaign has ended
                        if ($endDate->isPast() || $endDate->isToday()) {
                            $daysLeft = 0;
                            $daysText = 'Ended';
                        } else {
                            // Calculate integer number of days remaining
                            $daysLeft = max(0, (int)floor($now->diffInDays($endDate, false)));
                            $daysText = $daysLeft == 1 ? 'Day Left' : 'Days Left';
                        }
                    } catch (\Exception $e) {
                        $daysLeft = 0;
                        $daysText = 'Ongoing';
                    }
                } else {
                    $daysText = 'Ongoing';
                }
            @endphp

            <h2 class="section-title-sm mb-4 mt-5" style="text-align:center;">
                Featured Project
            </h2>

            <div class="row align-items-stretch">
                <!-- LEFT CONTENT -->
                <div class="col-lg-5 d-flex">
                    <div class="featured-box-modern w-100">
                        <h3>{{ $trendingCampaign->name }}</h3>

                        <p>
                            {{ Str::limit(strip_tags($trendingCampaign->description), 100) }}
                        </p>

                        <div class="featured-stats-modern">
                            <div>
                                <strong>{{ $setting->cur_sym ?? '$' }}{{ number_format($raised, 0) }}</strong>
                                @if($showRateDebug)
                                @php $tc = $trendingCampaign; $sc = strtoupper($setting->site_cur ?? 'USD'); $cc = strtoupper($tc->original_currency ?? $sc); $er = (float)($tc->exchange_rate_used ?? 1); @endphp
                                <span onclick="event.preventDefault();event.stopPropagation();"><i class="fas fa-calculator rate-debug-icon" role="button" tabindex="0" data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="click" data-bs-html="true" data-bs-content="<div class='rate-debug-popover'><strong>Currency &amp; Rate</strong><br>Site: {{ $sc }}<br>Campaign: {{ $cc }}<br>Rate: 1 {{ $sc }} = {{ number_format($er, 4) }} {{ $cc }}</div>" title="Rate Info"></i></span>
                                @endif
                                <span>Raised</span>
                            </div>
                            <div>
                                <strong>{{ number_format($percentage, 0) }}%</strong>
                                <span>Funded</span>
                            </div>
                            <div>
                                <strong>{{ $daysLeft }}</strong>
                                <span>{{ $daysText }}</span>
                            </div>
                        </div>

                        <div class="progress-modern">
                            <div class="progress-modern-fill" style="width:{{ $percentage }}%"></div>
                        </div>

                        <a href="{{ route('campaign.show', $trendingCampaign->slug) }}{{ $showRateDebug ? '?test=1' : '' }}" class="featured-btn">View Campaign →</a>
                    </div>
                </div>

                <!-- RIGHT IMAGE -->
                <div class="col-lg-7 d-flex">
                    <div class="featured-image-modern w-100" style="background-image: url('{{ getImage(getFilePath('campaign') . '/' . $trendingCampaign->image, getFileSize('campaign')) }}');">
                    </div>
                </div>
            </div>
    </div>
</section>
    @endif

<style>
.trending-badge {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: #fff;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.5);
    }
}

.projects-section .section-title-sm {
    align-items: center;
    gap: 15px;
}

@media (max-width: 768px) {
    .trending-badge {
        font-size: 12px;
        padding: 6px 12px;
    }
}
</style>

 <div class="container new_2">

        <div class="row mb-4">
            <div class="col-12">
               
            </div>
        </div>

        <!-- MAIN FEATURED -->
      

        <!-- BELOW FEATURED - FEATURED CAMPAIGNS SLIDER -->
@if(isset($featuredCampaigns) && $featuredCampaigns->count() > 0)
<div class="container featured-campaigns-slider-wrapper">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="section-title-sm text-center mb-4">Other Popular Campaigns</h2>
        </div>
    </div>

    <!-- SLIDER CONTAINER -->
    <div class="position-relative">
        <!-- SLIDER WRAPPER -->
        <div class="featured-slider-container">
            <div class="featured-slider-track" id="featuredSliderTrack">
                <div class="row g-4" id="featuredSliderContent">
                    @foreach($featuredCampaigns as $campaign)
                        <div class="col-md-6 featured-slide">
                            <a href="{{ route('campaign.show', $campaign->slug) }}{{ $showRateDebug ? '?test=1' : '' }}" class="project-card-link">
                                <div class="project-card">
                                    <div class="project-image" style="background-image: url('{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}');"></div>
                                    <div class="project-content">
                                        <h3 class="project-title">{{ Str::limit($campaign->name, 50) }}</h3>
                                        <p class="project-description">{{ Str::limit(strip_tags($campaign->short_description ?? $campaign->description), 80) }}</p>
                                        <div class="mt-3">
                                            @php
                                                // Use raised_amount field for accurate raised amount
                                                $raised = $campaign->raised_amount ?? 0;
                                                $goal = $campaign->goal_amount ?? 1;
                                                $percentage = min(100, ($raised / $goal) * 100);
                                                
                                                // Calculate days left dynamically
                                                $daysLeft = 0;
                                                $daysText = 'Days Left';
                                                if ($campaign->end_date) {
                                                    try {
                                                        $endDate = \Carbon\Carbon::parse($campaign->end_date);
                                                        $now = \Carbon\Carbon::now();
                                                        
                                                        if ($endDate->isPast() || $endDate->isToday()) {
                                                            $daysLeft = 0;
                                                            $daysText = 'Ended';
                                                        } else {
                                                            $daysLeft = max(0, (int)floor($now->diffInDays($endDate, false)));
                                                            $daysText = $daysLeft == 1 ? 'Day Left' : 'Days Left';
                                                        }
                                                    } catch (\Exception $e) {
                                                        $daysLeft = 0;
                                                        $daysText = 'Ongoing';
                                                    }
                                                } else {
                                                    $daysText = 'Ongoing';
                                                }
                                            @endphp
                                            <div class="progress" style="height: 6px; background: #eee; border-radius: 10px;">
                                                <div class="progress-bar bg-success" style="width:{{ $percentage }}%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2 small">
                                                <span><strong>{{ $setting->cur_sym ?? '$' }}{{ number_format($raised, 0) }}</strong>
                                                @if($showRateDebug)
                                                @php $sc = strtoupper($setting->site_cur ?? 'USD'); $cc = strtoupper($campaign->original_currency ?? $sc); $er = (float)($campaign->exchange_rate_used ?? 1); @endphp
                                                <span onclick="event.preventDefault();event.stopPropagation();"><i class="fas fa-calculator rate-debug-icon" role="button" tabindex="0" data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="click" data-bs-html="true" data-bs-content="<div class='rate-debug-popover'><strong>Currency &amp; Rate</strong><br>Site: {{ $sc }}<br>Campaign: {{ $cc }}<br>Rate: 1 {{ $sc }} = {{ number_format($er, 4) }} {{ $cc }}</div>" title="Rate Info"></i></span>
                                                @endif
                                                raised</span>
                                                <span>{{ number_format($percentage, 0) }}%</span>
                                            </div>
                                            <div class="mt-2 small text-muted">
                                                <span>{{ $daysLeft }} {{ $daysText }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- PAGINATION WITH ARROWS -->
    <div class="slider-pagination-wrapper mt-4 d-flex align-items-center justify-content-center gap-3">
        <!-- PREVIOUS BUTTON -->
        <button class="slider-btn-pagination slider-btn-prev" id="prevBtn" aria-label="Previous">
            <i class="fas fa-chevron-left"></i>
        </button>

        <!-- PAGINATION DOTS -->
        <div class="slider-pagination" id="sliderPagination">
            <!-- Dots will be generated by JavaScript -->
        </div>

        <!-- NEXT BUTTON -->
        <button class="slider-btn-pagination slider-btn-next" id="nextBtn" aria-label="Next">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>
@endif

    </div>

    <script>
// Featured Campaigns Slider - 2 Columns Layout
(function() {
    const sliderTrack = document.getElementById('featuredSliderTrack');
    const sliderContent = document.getElementById('featuredSliderContent');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pagination = document.getElementById('sliderPagination');
    
    if (!sliderTrack || !sliderContent) return;
    
    const slides = sliderContent.querySelectorAll('.featured-slide');
    const totalSlides = slides.length;
    const slidesPerView = 2; // Always show 2 campaigns
    const totalPages = Math.ceil(totalSlides / slidesPerView);
    let currentPage = 0;
    
    // Initialize pagination dots with numbers
    function initPagination() {
        if (!pagination) return;
        pagination.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('span');
            dot.className = 'pagination-dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('data-page', i);
            dot.textContent = i + 1; // Show page number (1, 2, 3, etc.)
            dot.onclick = () => goToPage(i);
            pagination.appendChild(dot);
        }
    }
    
    // Update pagination active state
    function updatePagination() {
        if (!pagination) return;
        const dots = pagination.querySelectorAll('.pagination-dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentPage);
        });
    }
    
    // Go to specific page
    function goToPage(page) {
        currentPage = Math.max(0, Math.min(page, totalPages - 1));
        // Calculate offset: each page moves by 100% (2 slides)
        const offset = -currentPage * 100;
        sliderContent.style.transform = `translateX(${offset}%)`;
        updatePagination();
        updateButtons();
    }
    
    // Update button states
    function updateButtons() {
        if (prevBtn) {
            prevBtn.style.opacity = currentPage === 0 ? '0.5' : '1';
            prevBtn.style.cursor = currentPage === 0 ? 'not-allowed' : 'pointer';
        }
        if (nextBtn) {
            nextBtn.style.opacity = currentPage === totalPages - 1 ? '0.5' : '1';
            nextBtn.style.cursor = currentPage === totalPages - 1 ? 'not-allowed' : 'pointer';
        }
    }
    
    // Previous button
    if (prevBtn) {
        prevBtn.onclick = () => {
            if (currentPage > 0) goToPage(currentPage - 1);
        };
    }
    
    // Next button
    if (nextBtn) {
        nextBtn.onclick = () => {
            if (currentPage < totalPages - 1) goToPage(currentPage + 1);
        };
    }
    
    // Initialize
    initPagination();
    updateButtons();
    goToPage(0);
})();

// Rate debug popovers (?test=1)
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap !== 'undefined') {
        document.querySelectorAll('.rate-debug-icon[data-bs-toggle="popover"]').forEach(function(el) {
            new bootstrap.Popover(el, { sanitize: false });
        });
    }
});
</script>

<style>
.featured-campaigns-slider-wrapper {
    position: relative;
    padding: 20px 0;
}

.featured-slider-container {
    overflow: hidden;
    position: relative;
}

.featured-slider-track {
    width: 100%;
}

#featuredSliderContent {
    display: flex;
    transition: transform 0.5s ease;
    width: auto;
    flex-wrap: nowrap;
}

.featured-slide {
    flex: 0 0 50%;
    padding: 0 15px;
    min-width: 50%;
}

@media (max-width: 767px) {
    .featured-slide {
        flex: 0 0 100%;
        min-width: 100%;
    }
}

.slider-pagination-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.slider-btn-pagination {
    background: #198754;
    color: #fff;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    flex-shrink: 0;
}

.slider-btn-pagination:hover {
    background: #157347;
    transform: scale(1.1);
}

.slider-btn-pagination:disabled,
.slider-btn-pagination[style*="opacity: 0.5"] {
    cursor: not-allowed;
    opacity: 0.5;
}

.slider-btn-pagination[style*="opacity: 0.5"]:hover {
    transform: scale(1);
    background: #198754;
}

.slider-pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
}

.pagination-dot {
    min-width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #ddd;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    padding: 0 8px;
}

.pagination-dot.active {
    background: #198754;
    color: #fff;
    min-width: 40px;
    height: 40px;
}

.pagination-dot:hover {
    background: #198754;
    color: #fff;
    opacity: 0.9;
    transform: scale(1.1);
}
</style>



@if($categories->count() > 0 && false)
<section class="container my-4">
  <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
    <a href="{{ route('campaign') }}" class="btn btn-success category-btn">All</a>
    @foreach($categories as $category)
        <a href="{{ route('campaign', ['category' => $category->slug]) }}" class="btn btn-outline-success category-btn">{{ $category->name }}</a>
    @endforeach
  </div>
</section>
@endif

<!-- CAMPAIGNS -->
@if($featuredProjectsContent && $featuredProjectsContent->data_info && false)
<section class="container my-5">
  @if(@$featuredProjectsContent->data_info->section_title)
  <div class="row mb-4">
    <div class="col-12">
      <h2 class="text-center mb-4">{{ @$featuredProjectsContent->data_info->section_title }}</h2>
    </div>
  </div>
  @endif
  <div class="row g-4">
    @forelse($featuredCampaigns as $campaign)
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('campaign.show', $campaign->slug) }}" class="text-decoration-none text-dark d-block">
                <div class="campaign-card h-100 rounded overflow-hidden shadow-sm" style="border-radius: 12px; cursor: pointer;">
                    <div class="campaign-image" style="background-image: url('{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 250px; width: 100%; display: block; border-top-left-radius: 12px; border-top-right-radius: 12px;"></div>
                    <div class="p-4">
                        <h6 class="fw-semibold mb-2">{{ Str::limit($campaign->name, 40) }}</h6>
                        <p class="text-muted small mb-3">{{ Str::limit(strip_tags($campaign->short_description ?? $campaign->description), 80) }}</p>
                        <div class="progress mb-3" style="height: 6px;">
                            @php
                                // Use raised_amount field for accurate raised amount
                                $raised = $campaign->raised_amount ?? 0;
                                $goal = $campaign->goal_amount ?? 1;
                                $percentage = min(100, ($raised / $goal) * 100);
                                
                                // Calculate days left dynamically
                                $daysLeft = 0;
                                $daysText = 'Days Left';
                                if ($campaign->end_date) {
                                    try {
                                        $endDate = \Carbon\Carbon::parse($campaign->end_date);
                                        $now = \Carbon\Carbon::now();
                                        
                                        // Check if campaign has ended
                                        if ($endDate->isPast() || $endDate->isToday()) {
                                            $daysLeft = 0;
                                            $daysText = 'ENDED';
                                        } else {
                                            // Calculate integer number of days remaining
                                            $daysLeft = max(0, (int)floor($now->diffInDays($endDate, false)));
                                            $daysText = $daysLeft . ' DAYS LEFT';
                                        }
                                    } catch (\Exception $e) {
                                        $daysLeft = 0;
                                        $daysText = 'ONGOING';
                                    }
                                } else {
                                    $daysText = 'ONGOING';
                                }
                            @endphp
                            <div class="progress-bar bg-success" style="width:{{ $percentage }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small fw-semibold text-dark">
                            <span>{{ $setting->cur_sym ?? '$' }}{{ number_format($raised, 0) }} RAISED</span>
                            <span>{{ $daysText }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @empty
    
    @endforelse
  </div>
  
  @if($featuredCampaigns->count() > 0)
  <div class="row mt-4">
    <div class="col-12 text-center">
      <a href="{{ @$featuredProjectsContent->data_info->view_all_button_url ?? route('campaign') }}" class="btn btn-success btn-lg">
        <i class="fas fa-eye me-2"></i>{{ @$featuredProjectsContent->data_info->view_all_button_text ?? 'View All Campaigns' }}
      </a>
    </div>
  </div>
  @endif
</section>
@endif

<!-- CTA -->

@endsection


