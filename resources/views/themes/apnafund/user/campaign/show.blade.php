@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
    $percentage = donationPercentage($campaign->goal_amount, $campaign->raised_amount);
    
    // Format address as comma-separated string
    $userAddress = '';
    if ($campaign->user->address) {
        $addressParts = [];
        if (!empty($campaign->user->address->address)) $addressParts[] = $campaign->user->address->address;
        if (!empty($campaign->user->address->city)) $addressParts[] = $campaign->user->address->city;
        if (!empty($campaign->user->address->state)) $addressParts[] = $campaign->user->address->state;
        if (!empty($campaign->user->address->zip)) $addressParts[] = $campaign->user->address->zip;
        $userAddress = implode(', ', $addressParts);
    }
@endphp
@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
<style>
        body {
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            line-height: 1.6;
            color: #333;
        }

        /* Main Fundraiser Layout */
        .fundraiser-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .fundraiser-main {
            flex: 2;
            min-width: 0;
        }

        .fundraiser-sidebar {
            flex: 1;
            max-width: 380px;
            position: sticky;
            top: 20px;
        }

        /* Hero Banner */
        .fundraiser-hero {
            position: relative;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .fundraiser-banner {
            width: 100%;
            height: 400px;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
        }

        .banner-overlay img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 20px;
        }

        .banner-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 13px 0;
            color: #333;
            text-align: left;
        }

        /* Organizer Section */
        .organizer-section {
            margin-bottom: 30px;
        }

        .organizer-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .organizer-info {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .organizer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #05ce78;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            margin-right: 15px;
        }

        .organizer-details h4 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .organizer-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .donation-protected {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
        }

        .donation-protected i {
            color: #05ce78;
        }

        /* Description */
        .fundraiser-description {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .donation-details__title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        .donation-details__desc {
            color: #666;
            line-height: 1.6;
            font-size: 1rem;
        }

        /* Engagement */
        .engagement-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .engagement-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
        }

        .engagement-item i {
            color: #ff6b6b;
        }

        /* Actions */
        .fundraiser-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-donate {
            background: #05ce78;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-donate:hover {
            background: #04b368;
            color: #fff;
            text-decoration: none;
        }

        .btn-share {
            background: #333;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-share:hover {
            background: #444;
            color: #fff;
            text-decoration: none;
        }

        /* Support Section */
        .support-section {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .support-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .support-prompt {
            color: #666;
            margin-bottom: 20px;
        }

        .support-placeholder {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            text-align: center;
            color: #666;
        }

        /* Reviews Section */
        .reviews-section {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .reviews-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .reviews-prompt {
            color: #666;
            margin-bottom: 20px;
        }

        .review-form-container {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .review-form-container h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .rating-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .rating-container label {
            font-weight: 500;
            color: #333;
            font-size: 0.95rem;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            gap: 2px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label.star {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star-rating label.star:hover,
        .star-rating label.star:hover~label.star,
        .star-rating input[type="radio"]:checked~label.star {
            color: #ffd700;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #05ce78;
            box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
        }

        .btn-submit-review {
            background: #05ce78;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            align-self: flex-start;
        }

        .btn-submit-review:hover {
            background: #04b368;
        }

        /* Sidebar Card */
        .sidebar-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .progress-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .progress-text {
            flex: 1;
        }

        .progress-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .progress-subtitle {
            font-size: 0.95rem;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .progress-circle {
            position: relative;
            width: 80px;
            height: 80px;
            margin-left: 20px;
        }

        .progress-circle svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .progress-circle-bg {
            fill: none;
            stroke: #e8e8e8;
            stroke-width: 8;
        }

        .progress-circle-fill {
            fill: none;
            stroke: #05ce78;
            stroke-width: 8;
            stroke-linecap: round;
            stroke-dasharray: 226;
            stroke-dashoffset: 160;
            transition: stroke-dashoffset 0.5s ease;
        }

        .progress-percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .btn-share-card {
            background: #333;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-share-card:hover {
            background: #444;
            color: #fff;
            text-decoration: none;
        }

        .btn-donate-card {
            background: #05ce78;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-donate-card:hover {
            background: #04b368;
            color: #fff;
            text-decoration: none;
        }

        .donation-stats {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .donation-stats i {
            color: #05ce78;
        }

        .recent-donations {
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
        }

        .donation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .donation-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .donation-details {
            display: flex;
            flex-direction: column;
        }

        .donation-name {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }

        .donation-amount {
            color: #05ce78;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .donation-type {
            color: #666;
            font-size: 0.8rem;
        }

        .donation-actions {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .donation-action-btn {
            color: #05ce78;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .donation-action-btn:hover {
            color: #04b368;
            text-decoration: none;
        }

        .news-link-container {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #f0f0f0;
        }

        .news-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .news-link:hover {
            color: #05ce78;
            text-decoration: none;
        }

        .news-link i:first-child {
            color: #05ce78;
            margin-right: 10px;
        }

        .news-link i:last-child {
            color: #666;
        }

        /* Enhanced Organizer Section */
        .organizer-details-expanded {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .organizer-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .organizer-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #05ce78;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1.5rem;
            margin-right: 20px;
        }

        .organizer-info-expanded h3 {
            margin: 0 0 8px 0;
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
        }

        .organizer-info-expanded .organizer-role {
            color: #05ce78;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .organizer-info-expanded .organizer-location {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .organizer-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .detail-label {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: #666;
            font-size: 1rem;
            line-height: 1.4;
        }

        .detail-value a {
            color: #05ce78;
            text-decoration: none;
        }

        .detail-value a:hover {
            text-decoration: underline;
        }

        .organizer-stats {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #05ce78;
            display: block;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .organizer-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-contact-organizer {
            background: #05ce78;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-contact-organizer:hover {
            background: #04b368;
            color: #fff;
            text-decoration: none;
        }

        .btn-view-profile {
            background: #333;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view-profile:hover {
            background: #444;
            color: #fff;
            text-decoration: none;
        }

        .verification-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #e8f5e8;
            color: #05ce78;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .verification-badge i {
            font-size: 0.7rem;
        }
</style>

<!-- Main Fundraiser Section -->
<div class="fundraiser-container mt-4">
    <!-- Main Content -->
    <div class="fundraiser-main">
        <!-- Hero Banner -->
        <div class="fundraiser-hero">
            <h1 class="banner-title">{{ __(@$campaign->name) }}</h1>
            <div class="fundraiser-banner">
                <div class="banner-overlay">
                    <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaign->image, getFileSize('campaign')) }}"
                        alt="{{ __(@$campaign->name) }}">
                </div>
            </div>
        </div>

        <!-- Organizer Section -->
        <div class="organizer-section">
            <div class="organizer-info">
                <div class="organizer-avatar">{{ substr(is_string(@$campaign->user->username) ? @$campaign->user->username : 'User', 0, 2) }}</div>
                <div class="organizer-details">
                    <h4>{{ is_string(@$campaign->user->username) ? @$campaign->user->username : 'User' }}</h4>
                    <p>is organizing this fundraiser</p>
                </div>
            </div>
            <div class="donation-protected">
                <i class="fas fa-shield-alt"></i>
                Donation protected
            </div>
        </div>

        <!-- Description -->
        <div class="fundraiser-description">
            <h2 class="donation-details__title">{{ __(@$campaign->name) }}</h2>
            <div class="donation-details__desc">
                @php echo @$campaign->description @endphp
            </div>
        </div>

        <!-- Engagement -->
        <div class="engagement-section">
            <div class="engagement-item">
                <i class="fas fa-heart"></i>
                <span>{{ @$campaign->deposits->count() }}</span>
            </div>
            @if(@$campaign->gallery && count(@$campaign->gallery) > 0)
            <div class="engagement-item">
                <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaign->gallery[0], getFileSize('campaign')) }}" 
                     alt="Gallery" style="width: 30px; height: 30px; border-radius: 4px; object-fit: cover;">
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="fundraiser-actions">
            <a href="{{ route('campaign.donate', $campaign->slug) }}" class="btn-donate">Donate</a>
            <a href="#" class="btn-share" onclick="shareCampaign()">Share</a>
        </div>

        <!-- Enhanced Organizer Details -->
        <div class="organizer-details-expanded">
            <div class="organizer-header">
                <div class="organizer-avatar-large">
                    {{ substr(is_string(@$campaign->user->username) ? @$campaign->user->username : 'User', 0, 2) }}
                </div>
                <div class="organizer-info-expanded">
                    <h3>
                        {{ is_string(@$campaign->user->firstname) ? @$campaign->user->firstname . ' ' . @$campaign->user->lastname : (@$campaign->user->username ?: 'User') }}
                        @if(@$campaign->user->ec == 1)
                            <span class="verification-badge">
                                <i class="fas fa-check-circle"></i>
                                Verified
                            </span>
                        @endif
                    </h3>
                    <div class="organizer-role">Campaign Organizer</div>
                    <div class="organizer-location">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $userAddress ?: 'Location not specified' }}
                    </div>
                </div>
            </div>

            <div class="organizer-details-grid">
                <div class="detail-item">
                    <div class="detail-label">Username</div>
                    <div class="detail-value">{{ is_string(@$campaign->user->username) ? @$campaign->user->username : 'Not specified' }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">
                        <a href="mailto:{{ @$campaign->user->email }}">{{ @$campaign->user->email }}</a>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">
                        @if(@$campaign->user->mobile)
                            <a href="tel:{{ @$campaign->user->mobile }}">{{ @$campaign->user->mobile }}</a>
                        @else
                            Not specified
                        @endif
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Country</div>
                    <div class="detail-value">{{ @$campaign->user->country_name ?: 'Not specified' }}</div>
                </div>
                
                @if(@$campaign->user->business_name)
                <div class="detail-item">
                    <div class="detail-label">Business Name</div>
                    <div class="detail-value">{{ @$campaign->user->business_name }}</div>
                </div>
                @endif
                
                @if(@$campaign->user->business_type)
                <div class="detail-item">
                    <div class="detail-label">Business Type</div>
                    <div class="detail-value">{{ @$campaign->user->business_type }}</div>
                </div>
                @endif
                
                @if(@$campaign->user->industry)
                <div class="detail-item">
                    <div class="detail-label">Industry</div>
                    <div class="detail-value">{{ @$campaign->user->industry }}</div>
                </div>
                @endif
            </div>

            <div class="organizer-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ @$campaign->user->campaigns->count() }}</span>
                    <span class="stat-label">Campaigns</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ @$campaign->user->campaigns->where('status', 1)->count() }}</span>
                    <span class="stat-label">Active</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ @$campaign->user->deposits->count() }}</span>
                    <span class="stat-label">Donations</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ @$campaign->user->comments->count() }}</span>
                    <span class="stat-label">Reviews</span>
                </div>
            </div>

            <div class="organizer-actions">
                <a href="mailto:{{ @$campaign->user->email }}" class="btn-contact-organizer">
                    <i class="fas fa-envelope"></i>
                    Contact Organizer
                </a>
                @if(@$campaign->user->mobile)
                <a href="tel:{{ @$campaign->user->mobile }}" class="btn-contact-organizer">
                    <i class="fas fa-phone"></i>
                    Call Organizer
                </a>
                @endif
                <a href="#" class="btn-view-profile">
                    <i class="fas fa-user"></i>
                    View Profile
                </a>
            </div>
        </div>

        <!-- Words of Support -->
        <div class="support-section">
            <h3>Words of support</h3>
            <p class="support-prompt">Please donate to share words of support.</p>
            @if(@$campaign->comments && count(@$campaign->comments) > 0)
                @foreach(@$campaign->comments->take(3) as $comment)
                <div class="support-placeholder">
                    <p>{{ $comment->comment }}</p>
                </div>
                @endforeach
            @else
                <div class="support-placeholder">
                    <p>No words of support yet</p>
                </div>
            @endif
        </div>

        <!-- Reviews and Comments Section -->
        <div class="reviews-section">
            <h3>Reviews & Comments</h3>
            <p class="reviews-prompt">Share your thoughts and experiences about this fundraiser.</p>

            <!-- Review Form -->
            <div class="review-form-container">
                <h4>Write a Review</h4>
                <form class="review-form" id="reviewForm">
                    <div class="rating-container">
                        <label>Your Rating:</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" value="5" id="star5">
                            <label for="star5" class="star">★</label>
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4" class="star">★</label>
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3" class="star">★</label>
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2" class="star">★</label>
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1" class="star">★</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reviewTitle">Review Title:</label>
                        <input type="text" id="reviewTitle" name="title" placeholder="Give your review a title" required>
                    </div>

                    <div class="form-group">
                        <label for="reviewComment">Your Review:</label>
                        <textarea id="reviewComment" name="comment" rows="4" placeholder="Share your experience with this fundraiser" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit-review">Submit Review</button>
                </form>
            </div>

            <!-- Reviews Display -->
            <div class="reviews-display">
                <div class="reviews-header">
                    <h4>Recent Reviews</h4>
                    <div class="reviews-filter">
                        <select>
                            <option>Most Recent</option>
                            <option>Highest Rated</option>
                            <option>Lowest Rated</option>
                        </select>
                    </div>
                </div>

                <div class="reviews-list">
                    @if(@$campaign->comments && count(@$campaign->comments) > 0)
                        @foreach(@$campaign->comments->take(5) as $comment)
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <span class="reviewer-name">{{ is_string($comment->user->username) ? $comment->user->username : 'Anonymous' }}</span>
                                    <div class="review-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= ($comment->rating ?? 5) ? 'filled' : '' }}">★</span>
                                        @endfor
                                    </div>
                                </div>
                                <span class="review-date">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="review-content">
                                <p>{{ $comment->comment }}</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="review-item">
                            <p>No reviews yet. Be the first to review this fundraiser!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="fundraiser-sidebar">
        <!-- Progress Card -->
        <div class="sidebar-card mt-5">
            <div class="progress-section">
                <div class="progress-text">
                    <h3 class="progress-title">{{ $setting->cur_sym . showAmount(@$campaign->raised_amount) }} raised</h3>
                    <div class="progress-subtitle align-items-center">
                        <span>{{ $percentage . '%' }} of goal</span>
                        <span>{{ showAmount(@$campaign->goal_amount).$setting->cur_sym }} goal</span>
                    </div>
                </div>
                <div class="progress-circle">
                    <svg viewBox="0 0 100 100">
                        <circle class="progress-circle-bg" cx="50" cy="50" r="45"></circle>
                        <circle class="progress-circle-fill" cx="50" cy="50" r="45" 
                                style="stroke-dashoffset: {{ 226 - (226 * $percentage / 100) }}"></circle>
                    </svg>
                    <span class="progress-percentage">{{ $percentage . '%' }}</span>
                </div>
            </div>

            <div class="action-buttons">
                <a href="#" class="btn-share-card" onclick="shareCampaign()">Share</a>
                <a href="{{ route('campaign.donate', $campaign->slug) }}" class="btn-donate-card">Donate now</a>
            </div>

            <div class="donation-stats">
                <i class="fas fa-chart-line"></i>
                <span>{{ @$campaign->deposits->count() }} people just donated</span>
            </div>

            <div class="recent-donations">
                @if(@$campaign->deposits && count(@$campaign->deposits) > 0)
                    @foreach(@$campaign->deposits->take(2) as $deposit)
                    <div class="donation-item">
                        <div class="donation-info">
                            <i class="fas fa-heart" style="color: #666; font-size: 0.9rem;"></i>
                            <div class="donation-details">
                                <span class="donation-name">{{ $deposit->full_name ?? 'Anonymous' }}</span>
                                <span class="donation-amount">{{ $setting->cur_sym }}{{ showAmount($deposit->amount) }}</span>
                            </div>
                        </div>
                        <span class="donation-type">{{ $loop->first ? 'Recent donation' : 'Top donation' }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="donation-item">
                        <div class="donation-info">
                            <i class="fas fa-heart" style="color: #666; font-size: 0.9rem;"></i>
                            <div class="donation-details">
                                <span class="donation-name">No donations yet</span>
                                <span class="donation-amount">$0</span>
                            </div>
                        </div>
                        <span class="donation-type">Be the first!</span>
                    </div>
                @endif
                
                <div class="donation-actions">
                    <a href="#" class="donation-action-btn">See all</a>
                    <a href="#" class="donation-action-btn">
                        <i class="fas fa-star"></i>
                        See top
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Latest News Link -->
        <div class="news-link-container mt-4">
            <a href="#" class="news-link" id="newsLink">
                <i class="fas fa-newspaper"></i>
                <span>Latest News & Updates</span>
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
function shareCampaign() {
    if (navigator.share) {
        navigator.share({
            title: '{{ @$campaign->name }}',
            text: 'Check out this fundraiser!',
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const url = window.location.href;
        const text = 'Check out this fundraiser: ' + url;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link copied to clipboard!');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Link copied to clipboard!');
        }
    }
}

// Initialize progress circle animation
document.addEventListener('DOMContentLoaded', function() {
    const progressCircle = document.querySelector('.progress-circle-fill');
    if (progressCircle) {
        const percentage = {{ $percentage }};
        const circumference = 2 * Math.PI * 45; // r = 45
        const offset = circumference - (percentage / 100) * circumference;
        progressCircle.style.strokeDashoffset = offset;
    }
});
</script>
@endsection
