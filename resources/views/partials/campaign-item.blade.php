@php
    // Get campaign by ID if passed, otherwise use the campaign object
    $campaign = is_numeric($campaignId) ? \App\Models\Campaign::find($campaignId) : $campaignId;
    
    if (!$campaign) {
        return;
    }
    
    // Calculate percentage of goal achieved
    $progressPercentage = 0;
    if ($campaign->goal_amount > 0) {
        $progressPercentage = ($campaign->raised_amount / $campaign->goal_amount) * 100;
        $progressPercentage = min(100, $progressPercentage); // Don't exceed 100%
    }
    
    $daysLeft = now()->diffInDays($campaign->end_date, false);
@endphp

<div class="project-card {{ isset($featured) && $featured ? 'featured-project' : '' }}">
    <div class="project-image">
        <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image) }}" alt="{{ $campaign->name }}" class="img-fluid">
        <div class="project-overlay">
            <div class="project-category">{{ $campaign->category->name ?? 'General' }}</div>
            <div class="project-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $progressPercentage }}%"></div>
                </div>
                <span class="progress-text">{{ number_format($progressPercentage, 1) }}% funded</span>
            </div>
        </div>
    </div>
    <div class="project-content">
        <h3 class="project-title">{{ $campaign->name }}</h3>
        <p class="project-description">{{ Str::limit($campaign->short_description, 100) }}</p>
        <div class="project-stats">
            <div class="stat">
                <span class="stat-value">${{ number_format($campaign->raised_amount) }}</span>
                <span class="stat-label">raised</span>
            </div>
            <div class="stat">
                <span class="stat-value">{{ ceil(max(0, $daysLeft)) }}</span>
                <span class="stat-label">days left</span>
            </div>
        </div>
    </div>
</div> 