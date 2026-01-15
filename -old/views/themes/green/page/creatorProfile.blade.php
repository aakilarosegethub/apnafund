@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')

<!-- Creator Profile Section -->
<section class="creator-profile py-5">
    <div class="container">
        <!-- Creator Header -->
        <div class="creator-header mb-5">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <div class="creator-avatar-wrapper">
                        @if($user->image)
                            <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="{{ $user->fullname }}" class="creator-avatar">
                        @else
                            <div class="creator-avatar-initials">
                                {{ strtoupper(substr($user->fullname, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-9">
                    <h1 class="creator-name">{{ $user->fullname }}</h1>
                    @if($user->business_name)
                        <p class="creator-business">{{ $user->business_name }}</p>
                    @endif
                    @if($user->business_description)
                        <p class="creator-description">{{ $user->business_description }}</p>
                    @endif
                    <div class="creator-meta">
                        @if($user->country_name)
                            <span class="meta-item">
                                <i class="fas fa-map-marker-alt"></i> {{ $user->country_name }}
                            </span>
                        @endif
                        @if($user->industry)
                            <span class="meta-item">
                                <i class="fas fa-briefcase"></i> {{ $user->industry }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Creator Stats -->
        <div class="creator-stats mb-5">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">{{ $totalCampaigns }}</h3>
                            <p class="stat-label">Campaigns</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">{{ showAmount($totalRaised) }}</h3>
                            <p class="stat-label">Total Raised</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value">{{ $campaigns->total() }}</h3>
                            <p class="stat-label">Active Campaigns</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Creator Campaigns -->
        <div class="creator-campaigns">
            <div class="section-header mb-4">
                <h2 class="section-title">Campaigns by {{ $user->fullname }}</h2>
                <p class="section-subtitle">Explore all campaigns created by this creator</p>
            </div>

            @if($campaigns->count() > 0)
                <div class="row">
                    @foreach($campaigns as $campaign)
                        <div class="col-12 col-md-4 mb-4">
                            <a href="{{ route('campaign.show', $campaign->slug) }}" class="project-card-link">
                                @include('partials.campaign-item', ['campaignId' => $campaign->id, 'featured' => true])
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($campaigns->count() > 0)
                    <div class="pagination-wrapper mt-4">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            @else
                <div class="no-campaigns text-center py-5">
                    <div class="no-campaigns-icon mb-3">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h4 class="text-muted">No Campaigns Yet</h4>
                    <p class="text-muted">This creator hasn't created any campaigns yet.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<style>
.creator-profile {
    background: #f8f9fa;
    min-height: 60vh;
}

.creator-header {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.creator-avatar-wrapper {
    position: relative;
}

.creator-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid #05ce78;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.creator-avatar-initials {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: linear-gradient(135deg, #05ce78 0%, #04b869 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 700;
    border: 5px solid #05ce78;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.creator-name {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.creator-business {
    font-size: 1.2rem;
    color: #05ce78;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.creator-description {
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.creator-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.95rem;
}

.meta-item i {
    color: #05ce78;
}

.creator-stats {
    margin-top: 2rem;
}

.stat-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #05ce78 0%, #04b869 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.5rem;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
}

.creator-campaigns {
    margin-top: 3rem;
}

.section-header {
    text-align: center;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    font-size: 1rem;
    color: #666;
}

.no-campaigns {
    background: #fff;
    padding: 3rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .creator-name {
        font-size: 2rem;
    }
    
    .creator-avatar,
    .creator-avatar-initials {
        width: 120px;
        height: 120px;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>

@endsection

