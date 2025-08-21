<div class="simple-campaign-card">
    <!-- Campaign Image -->
    <div class="simple-campaign-image">
        <a href="{{ route('campaign.show', $campaign->slug) }}">
            <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaign->image, getThumbSize('campaign')) }}" alt="{{ $campaign->name }}">
            <div class="simple-overlay">
                <span class="simple-category">{{ $campaign->category->name ?? 'General' }}</span>
            </div>
        </a>
    </div>

    <!-- Campaign Content -->
    <div class="simple-campaign-content">
        <h4 class="simple-campaign-title">
            <a href="{{ route('campaign.show', $campaign->slug) }}">
                {{ __(strLimit(@$campaign->name, 30)) }}
            </a>
        </h4>
        
        <p class="simple-campaign-desc">
            {{ __(strLimit(@$campaign->short_description ?? $campaign->description, 60)) }}
        </p>

        <!-- Progress Bar -->
        @php
            $percentage = donationPercentage($campaign->goal_amount, $campaign->raised_amount);
        @endphp
        <div class="simple-progress">
            <div class="simple-progress-bar">
                <div class="simple-progress-fill" style="width: {{ $percentage }}%"></div>
            </div>
            <span class="simple-progress-text">{{ $percentage }}%</span>
        </div>

        <!-- Campaign Stats -->
        <div class="simple-stats">
            <div class="simple-stat">
                <span class="simple-stat-label">Goal</span>
                <span class="simple-stat-value">{{ $setting->cur_sym . showAmount(@$campaign->goal_amount) }}</span>
            </div>
            <div class="simple-stat">
                <span class="simple-stat-label">Raised</span>
                <span class="simple-stat-value">{{ $setting->cur_sym . showAmount(@$campaign->raised_amount) }}</span>
            </div>
        </div>

        <!-- Action Button -->
        <a href="{{ route('campaign.show', $campaign->slug) }}" class="simple-support-btn">
            Support Now
        </a>
    </div>
</div>

<style>
    .simple-campaign-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 3px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        height: 100%;
    }

    .simple-campaign-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .simple-campaign-image {
        position: relative;
        height: 180px;
        overflow: hidden;
    }

    .simple-campaign-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .simple-campaign-card:hover .simple-campaign-image img {
        transform: scale(1.05);
    }

    .simple-overlay {
        position: absolute;
        top: 10px;
        left: 10px;
    }

    .simple-category {
        background: rgba(5, 206, 120, 0.9);
        color: white;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
    }

    .simple-campaign-content {
        padding: 15px;
    }

    .simple-campaign-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .simple-campaign-title a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .simple-campaign-title a:hover {
        color: #05ce78;
    }

    .simple-campaign-desc {
        color: #6c757d;
        font-size: 13px;
        line-height: 1.4;
        margin-bottom: 15px;
    }

    .simple-progress {
        position: relative;
        margin-bottom: 15px;
    }

    .simple-progress-bar {
        height: 6px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .simple-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #05ce78 0%, #04b367 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .simple-progress-text {
        position: absolute;
        right: 0;
        top: -18px;
        font-size: 11px;
        font-weight: 600;
        color: #05ce78;
    }

    .simple-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .simple-stat {
        text-align: center;
    }

    .simple-stat-label {
        display: block;
        font-size: 10px;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .simple-stat-value {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #333;
    }

    .simple-support-btn {
        display: block;
        width: 100%;
        background: #05ce78;
        color: white;
        text-align: center;
        padding: 10px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .simple-support-btn:hover {
        background: #04b367;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(5, 206, 120, 0.3);
    }
</style> 