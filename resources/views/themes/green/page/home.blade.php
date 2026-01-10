@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')
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
@if($featuredProjectsContent && $featuredProjectsContent->data_info)
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
            <div class="campaign-card h-100 rounded overflow-hidden shadow-sm" style="border-radius: 12px;">
                <div style="background-image: url('{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 250px;"></div>
                <div class="p-4">
                    <h6 class="fw-semibold mb-2">{{ Str::limit($campaign->name, 40) }}</h6>
                    <p class="text-muted small mb-3">{{ Str::limit(strip_tags($campaign->description), 60) }}</p>
                    <div class="progress mb-3" style="height: 6px;">
                        @php
                            $raised = $campaign->raised ?? 0;
                            $goal = $campaign->goal_amount ?? 1;
                            $percentage = min(100, ($raised / $goal) * 100);
                        @endphp
                        <div class="progress-bar bg-success" style="width:{{ $percentage }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small fw-semibold text-dark">
                        <span>${{ number_format($raised, 0) }} RAISED</span>
                        <span>
                            @if($campaign->end_date)
                                @php
                                    try {
                                        $endDate = \Carbon\Carbon::parse($campaign->end_date);
                                        $now = \Carbon\Carbon::now();
                                        
                                        // Check if campaign has ended
                                        if ($endDate->isPast()) {
                                            $daysText = '0';
                                        }
                                        if ($endDate->isPast() || $endDate->isToday()) {
                                            $daysText = 'ENDED';
                                        } else {
                                            // Calculate integer number of days remaining
                                            $daysLeft = $now->diffInDays($endDate, false);
                                            $daysLeft = max(0, (int)$daysLeft);
                                            $daysText = $daysLeft . ' DAYS LEFT';
                                        }
                                    } catch (\Exception $e) {
                                        $daysText = 'ONGOING';
                                    }
                                @endphp
                                {{ $daysText }}
                            @else
                                ONGOING
                            @endif
                        </span>
                    </div>
                </div>
            </div>
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


