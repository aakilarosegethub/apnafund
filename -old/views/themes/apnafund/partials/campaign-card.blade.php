<div class="modern-campaign-card">
    <!-- Campaign Image -->
    <div class="campaign-image">
        <a href="{{ route('campaign.show', $campaign->slug) }}">
            <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaign->image, getThumbSize('campaign')) }}" alt="{{ $campaign->name }}">
            <div class="campaign-overlay">
                <div class="campaign-category">
                    <span class="category-badge">{{ $campaign->category->name ?? 'General' }}</span>
                </div>
                <div class="campaign-actions">
                    <button class="btn btn-light btn-sm">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="btn btn-light btn-sm">
                        <i class="fas fa-share"></i>
                    </button>
                </div>
            </div>
        </a>
    </div>

    <!-- Campaign Content -->
    <div class="campaign-content">
        <!-- Campaign Header -->
        <div class="campaign-header">
            <h3 class="campaign-title">
                <a href="{{ route('campaign.show', $campaign->slug) }}">
                    {{ __(strLimit(@$campaign->name, 40)) }}
                </a>
            </h3>
            <p class="campaign-description">
                {{ __(strLimit(@$campaign->short_description ?? $campaign->description, 80)) }}
            </p>
        </div>

        <!-- Campaign Creator -->
        <div class="campaign-creator">
            <div class="creator-info">
                <div class="creator-avatar">
                    <img src="{{ getImage(getFilePath('userProfile') . '/' . @$campaign->user->image, getThumbSize('userProfile')) }}" alt="{{ $campaign->user->name }}">
                </div>
                <div class="creator-details">
                    <h6 class="creator-name">{{ $campaign->user->name }}</h6>
                    <small class="creator-location">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $campaign->user->country ?? 'Unknown' }}
                    </small>
                </div>
            </div>
            <div class="campaign-status">
                @if($campaign->status == 1)
                    <span class="status-badge status-active">Active</span>
                @elseif($campaign->status == 2)
                    <span class="status-badge status-pending">Pending</span>
                @else
                    <span class="status-badge status-rejected">Rejected</span>
                @endif
            </div>
        </div>

        <!-- Progress Section -->
        <div class="campaign-progress">
            @php
                $percentage = donationPercentage($campaign->goal_amount, $campaign->raised_amount);
                $daysLeft = \Carbon\Carbon::parse($campaign->end_date)->diffInDays(now());
            @endphp
            
            <div class="progress-info">
                <div class="progress-stats">
                    <div class="stat-item">
                        <span class="stat-label">Raised</span>
                        <span class="stat-value">{{ $setting->cur_sym . showAmount(@$campaign->raised_amount) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Goal</span>
                        <span class="stat-value">{{ $setting->cur_sym . showAmount(@$campaign->goal_amount) }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Days Left</span>
                        <span class="stat-value">{{ $daysLeft > 0 ? $daysLeft : 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-wrapper">
                <div class="progress custom-progress" role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                </div>
                <span class="progress-text">{{ $percentage }}%</span>
            </div>
        </div>

        <!-- Campaign Footer -->
        <div class="campaign-footer">
            <div class="campaign-meta">
                <div class="meta-item">
                    <i class="fas fa-users"></i>
                    <span>{{ $campaign->donors_count ?? 0 }} donors</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>{{ \Carbon\Carbon::parse($campaign->created_at)->format('M d, Y') }}</span>
                </div>
            </div>
            
            <div class="campaign-actions">
                <a href="{{ route('campaign.show', $campaign->slug) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-heart me-1"></i>Support Now
                </a>
            </div>
        </div>
    </div>
</div> 