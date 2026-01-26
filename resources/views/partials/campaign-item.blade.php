@php
    // Get campaign by ID if passed, otherwise use the campaign object
    $campaign = is_numeric($campaignId) ? \App\Models\Campaign::find($campaignId) : $campaignId;
    
    if (!$campaign) {
        return;
    }
    
    // Get raised amount (using accessor which calculates from deposits if needed)
    $raisedAmount = $campaign->raised_amount ?? 0;
    
    // Calculate percentage of goal achieved
    $progressPercentage = 0;
    if ($campaign->goal_amount > 0) {
        $progressPercentage = ($raisedAmount / $campaign->goal_amount) * 100;
        $progressPercentage = min(100, $progressPercentage); // Don't exceed 100%
    }
    
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

<div class="project-card {{ isset($featured) && $featured ? 'featured-project' : '' }}">
    <div class="project-image">
        <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" alt="{{ $campaign->name }}" class="img-fluid">
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
                <span class="stat-value">{{ bs('cur_sym') }}{{ number_format($raisedAmount, 0) }}</span>
                <span class="stat-label">raised</span>
            </div>
            <div class="stat">
                <span class="stat-value">{{ $daysLeft }}</span>
                <span class="stat-label">{{ $daysText }}</span>
            </div>
        </div>
    </div>
</div> 