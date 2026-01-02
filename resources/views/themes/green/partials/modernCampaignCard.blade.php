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

@push('page-style')
<style>
    .modern-campaign-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .modern-campaign-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    /* Campaign Image */
    .campaign-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .campaign-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .modern-campaign-card:hover .campaign-image img {
        transform: scale(1.05);
    }

    .campaign-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 100%);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 15px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modern-campaign-card:hover .campaign-overlay {
        opacity: 1;
    }

    .category-badge {
        background: rgba(5, 206, 120, 0.9);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .campaign-actions {
        display: flex;
        gap: 8px;
    }

    .campaign-actions .btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    /* Campaign Content */
    .campaign-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .campaign-header {
        margin-bottom: 15px;
    }

    .campaign-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .campaign-title a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .campaign-title a:hover {
        color: #05ce78;
    }

    .campaign-description {
        color: #6c757d;
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
    }

    /* Campaign Creator */
    .campaign-creator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .creator-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .creator-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #05ce78;
    }

    .creator-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .creator-name {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        color: #333;
    }

    .creator-location {
        color: #6c757d;
        font-size: 12px;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background: rgba(5, 206, 120, 0.1);
        color: #05ce78;
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .status-rejected {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Progress Section */
    .campaign-progress {
        margin-bottom: 15px;
    }

    .progress-info {
        margin-bottom: 10px;
    }

    .progress-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-label {
        display: block;
        font-size: 11px;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .stat-value {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #333;
    }

    .progress-wrapper {
        position: relative;
    }

    .custom-progress {
        height: 8px;
        border-radius: 10px;
        background: #e9ecef;
        overflow: hidden;
    }

    .custom-progress .progress-bar {
        background: linear-gradient(90deg, #05ce78 0%, #04b367 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .progress-text {
        position: absolute;
        right: 0;
        top: -20px;
        font-size: 12px;
        font-weight: 600;
        color: #05ce78;
    }

    /* Campaign Footer */
    .campaign-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
    }

    .campaign-meta {
        display: flex;
        gap: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #6c757d;
    }

    .meta-item i {
        color: #05ce78;
        font-size: 14px;
    }

    .campaign-actions .btn {
        border-radius: 25px;
        padding: 8px 20px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .campaign-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(5, 206, 120, 0.3);
    }

    /* List View Styles */
    .campaign-grid.list-view .modern-campaign-card {
        flex-direction: row;
        height: auto;
    }

    .campaign-grid.list-view .campaign-image {
        width: 300px;
        height: 200px;
        flex-shrink: 0;
    }

    .campaign-grid.list-view .campaign-content {
        flex: 1;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .campaign-grid.list-view .modern-campaign-card {
            flex-direction: column;
        }

        .campaign-grid.list-view .campaign-image {
            width: 100%;
        }

        .progress-stats {
            grid-template-columns: 1fr;
            gap: 5px;
        }

        .campaign-footer {
            flex-direction: column;
            gap: 10px;
            align-items: stretch;
        }

        .campaign-meta {
            justify-content: center;
        }
    }
</style>
@endpush 