<div class="project-card">
    <div class="project-image">
        <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaign->image, getThumbSize('campaign')) }}" alt="{{ $campaign->name }}" class="img-fluid">
        <div class="project-overlay">
            <div class="project-category">{{ $campaign->category->name ?? 'General' }}</div>
            <div class="project-progress">
                @php
                    $percentage = donationPercentage($campaign->goal_amount, $campaign->raised_amount);
                @endphp
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $percentage }}%"></div>
                </div>
                <span class="progress-text">{{ $percentage }}% funded</span>
            </div>
        </div>
    </div>
    <div class="project-content">
        <h3 class="project-title">
            <a href="{{ route('campaign.show', $campaign->slug) }}">
                {{ __(strLimit(@$campaign->name, 30)) }}
            </a>
        </h3>
        <p class="project-description">
            {{ __(strLimit(@$campaign->short_description ?? $campaign->description, 80)) }}
        </p>
        <div class="project-stats">
            @php
                $daysLeft = \Carbon\Carbon::parse($campaign->end_date)->diffInDays(now());
            @endphp
            <div class="stat">
                <span class="stat-value">{{ $setting->cur_sym . showAmount(@$campaign->raised_amount) }}</span>
                <span class="stat-label">raised</span>
            </div>
            <div class="stat">
                <span class="stat-value">{{ $daysLeft > 0 ? $daysLeft : 0 }}</span>
                <span class="stat-label">days left</span>
            </div>
        </div>
        <div class="project-actions">
            <a href="{{ route('campaign.show', $campaign->slug) }}" class="project-btn">
                Support Project
            </a>
        </div>
    </div>
</div>

<style>
    .project-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        height: 100%;
        position: relative;
    }

    .project-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .project-image {
        position: relative;
        height: 220px;
        overflow: hidden;
    }

    .project-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .project-card:hover .project-image img {
        transform: scale(1.1);
    }

    .project-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.6) 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .project-card:hover .project-overlay {
        opacity: 1;
    }

    .project-category {
        background: rgba(5, 206, 120, 0.95);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        align-self: flex-start;
    }

    .project-progress {
        background: rgba(255, 255, 255, 0.95);
        padding: 15px;
        border-radius: 15px;
        backdrop-filter: blur(10px);
    }

    .progress-bar {
        height: 8px;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #05ce78 0%, #04b367 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
        position: relative;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .progress-text {
        color: #333;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
        display: block;
    }

    .project-content {
        padding: 25px;
    }

    .project-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 12px;
        line-height: 1.3;
        color: #333;
    }

    .project-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .project-title a:hover {
        color: #05ce78;
    }

    .project-description {
        color: #6c757d;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
        min-height: 44px;
    }

    .project-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .stat {
        text-align: center;
        flex: 1;
    }

    .stat:first-child {
        border-right: 1px solid #e9ecef;
        padding-right: 15px;
    }

    .stat:last-child {
        padding-left: 15px;
    }

    .stat-value {
        display: block;
        font-size: 1.1rem;
        font-weight: 700;
        color: #05ce78;
        margin-bottom: 4px;
    }

    .stat-label {
        display: block;
        font-size: 11px;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .project-actions {
        text-align: center;
    }

    .project-btn {
        display: inline-block;
        background: linear-gradient(135deg, #05ce78 0%, #04b367 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(5, 206, 120, 0.3);
        position: relative;
        overflow: hidden;
    }

    .project-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .project-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(5, 206, 120, 0.4);
        color: white;
    }

    .project-btn:hover::before {
        left: 100%;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .project-image {
            height: 180px;
        }
        
        .project-content {
            padding: 20px;
        }
        
        .project-title {
            font-size: 1.1rem;
        }
        
        .project-stats {
            flex-direction: column;
            gap: 10px;
        }
        
        .stat:first-child {
            border-right: none;
            border-bottom: 1px solid #e9ecef;
            padding-right: 0;
            padding-bottom: 10px;
        }
        
        .stat:last-child {
            padding-left: 0;
            padding-top: 10px;
        }
    }

    /* Grid Layout Support */
    .project-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }

    .project-grid.list-view {
        grid-template-columns: 1fr;
    }

    .project-grid.list-view .project-card {
        display: flex;
        height: auto;
    }

    .project-grid.list-view .project-image {
        width: 300px;
        flex-shrink: 0;
    }

    .project-grid.list-view .project-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .project-grid.list-view .project-card {
            flex-direction: column;
        }
        
        .project-grid.list-view .project-image {
            width: 100%;
        }
    }
</style> 