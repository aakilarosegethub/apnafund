@php
    $activeTheme = 'themes.green.';
    $activeThemeTrue = 'themes.green.';
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')
<!-- HERO -->
<section class="hero mt-5">
  <div class="container">
    <span class="badge bg-light text-success mb-3">50,000+ Backers</span>
    <h1>
        <span>{{ @$heroContent->data_info->hero_heading_1 ?? 'Crowd' }}</span>{{ @$heroContent->data_info->hero_heading_1 ? '' : 'Funding' }}
        <br><span>{{ @$heroContent->data_info->hero_heading_2 ?? 'By' }}</span> {{ @$heroContent->data_info->hero_heading_2 ? '' : 'The People,' }}
        <br><span>{{ @$heroContent->data_info->hero_heading_3 ?? 'For' }}</span> {{ @$heroContent->data_info->hero_heading_3 ? '' : 'The People,' }}
    </h1>
    <p class="mt-3">
        {{ @$heroContent->data_info->hero_description ?? 'Together, we empower small businesses— From young dreamers, bold visionaries and those who want to improve their societies.' }}
    </p>
    <a href="{{ @$heroContent->data_info->button_url ?? route('campaign') }}" class="btn btn-light mt-3 px-4">
        {{ @$heroContent->data_info->button_text ?? 'Explore Campaigns' }}
    </a>
  </div>
</section>

<!-- STATS -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row text-center g-4">
      <div class="col-md-4">
        <div class="stats-box">
          <h4>${{ number_format((@$infoBannerContent->data_info->stat_1_value ?? 12) / 1000000, 1) }}M+</h4>
          <p>{{ @$infoBannerContent->data_info->stat_1_label ?? 'Total Funded' }}</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-box">
          <h4>{{ number_format(@$infoBannerContent->data_info->stat_2_value ?? 2500, 0) }}+</h4>
          <p>{{ @$infoBannerContent->data_info->stat_2_label ?? 'Successful Projects' }}</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-box">
          <h4>{{ number_format((@$infoBannerContent->data_info->stat_3_value ?? 50), 0) }}K+</h4>
          <p>{{ @$infoBannerContent->data_info->stat_3_label ?? 'Active Backers' }}</p>
        </div>
      </div>
    </div>
  </div>
</section>

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
@if($categories->count() > 0)
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
<section class="container my-5">
  <div class="row g-4">
    @forelse($featuredCampaigns as $campaign)
        <div class="col-lg-4 col-md-6">
            <div class="campaign-card h-100">
                <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" class="campaign-img" alt="{{ $campaign->name }}">
                <div class="p-4">
                    <h6 class="fw-semibold mb-1">{{ Str::limit($campaign->name, 40) }}</h6>
                    <p class="text-muted small mb-3">{{ Str::limit(strip_tags($campaign->description), 60) }}</p>
                    <div class="progress mb-3">
                        @php
                            $raised = $campaign->raised ?? 0;
                            $goal = $campaign->goal_amount ?? 1;
                            $percentage = min(100, ($raised / $goal) * 100);
                        @endphp
                        <div class="progress-bar" style="width:{{ $percentage }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small fw-semibold text-success">
                        <span>${{ number_format($raised, 0) }} RAISED</span>
                        <span>
                            @if($campaign->end_date)
                                {{ \Carbon\Carbon::parse($campaign->end_date)->diffInDays(\Carbon\Carbon::now()) }} DAYS LEFT
                            @else
                                ONGOING
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <!-- Default static cards if no campaigns -->
        <div class="col-lg-4 col-md-6">
            <div class="campaign-card h-100">
                <img src="https://images.unsplash.com/photo-1526304640581-d334cdbbf45e" class="campaign-img">
                <div class="p-4">
                    <h6 class="fw-semibold mb-1">Indie Album Production</h6>
                    <p class="text-muted small mb-3">Supporting independent musicians</p>
                    <div class="progress mb-3">
                        <div class="progress-bar" style="width:60%"></div>
                    </div>
                    <div class="d-flex justify-content-between small fw-semibold text-success">
                        <span>$8,500 RAISED</span>
                        <span>18 DAYS LEFT</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="campaign-card h-100">
                <img src="https://images.unsplash.com/photo-1526304640581-d334cdbbf45e" class="campaign-img">
                <div class="p-4">
                    <h6 class="fw-semibold mb-1">Community School</h6>
                    <p class="text-muted small mb-3">Education for everyone</p>
                    <div class="progress mb-3">
                        <div class="progress-bar" style="width:75%"></div>
                    </div>
                    <div class="d-flex justify-content-between small fw-semibold text-success">
                        <span>$14,200 RAISED</span>
                        <span>10 DAYS LEFT</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="campaign-card h-100">
                <img src="https://images.unsplash.com/photo-1526304640581-d334cdbbf45e" class="campaign-img">
                <div class="p-4">
                    <h6 class="fw-semibold mb-1">Medical Support</h6>
                    <p class="text-muted small mb-3">Helping rural hospitals</p>
                    <div class="progress mb-3">
                        <div class="progress-bar" style="width:85%"></div>
                    </div>
                    <div class="d-flex justify-content-between small fw-semibold text-success">
                        <span>$32,000 RAISED</span>
                        <span>25 DAYS LEFT</span>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
  </div>
  
  @if($featuredCampaigns->count() > 0)
  <div class="row mt-4">
    <div class="col-12 text-center">
      <a href="{{ route('campaign') }}" class="btn btn-success btn-lg">
        <i class="fas fa-eye me-2"></i>View All Campaigns
      </a>
    </div>
  </div>
  @endif
</section>

<!-- CTA -->
<section class="cta">
  <h2>Ready to Start Something Big?</h2>
  <p class="mt-2">Launch your campaign and reach thousands of backers.</p>
  @auth
      <a href="{{ route('user.campaign.new') }}" class="btn btn-light px-4 mt-3">Start a Campaign</a>
  @else
      <a href="{{ route('user.login') }}" class="btn btn-light px-4 mt-3">Start a Campaign</a>
  @endauth
</section>
@endsection
