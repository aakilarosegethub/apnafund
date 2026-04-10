@extends(activeTheme() . 'layouts.green-home')

@section('title', __(@$campaignData->name ?? 'Campaign') . ' - Apnacrowdfunding')

@push('styles')
<style>
    body {
        padding-top: 80px;
    }

    .main-wrapper {
        padding-top: 40px;
        padding-bottom: 60px;
    }

    .badge-category {
        background: #e9f7ef;
        color: #198754;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
    }

    .campaign-title {
        font-weight: 700;
        margin-top: 10px;
    }

    .campaign-subtitle {
        color: #6c757d;
        margin-bottom: 20px;
    }

    /* ✅ IMAGE STYLE (FIX) */
    .campaign-image {
        width: 100%;
        height: 400px;
        border-radius: 14px;
        margin-bottom: 20px;
        object-fit: cover;
    }

    /* ✅ VIDEO WRAPPER STYLE */
    .campaign-video-wrapper {
        width: 100%;
        position: relative;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
        height: 0;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .campaign-video-wrapper iframe,
    .campaign-video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 14px;
    }

    .campaign-author {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .campaign-author img {
        border-radius: 50%;
    }

    .campaign-author span {
        font-size: 14px;
        color: #6c757d;
    }

    .campaign-tabs {
        margin-top: 20px;
    }

    .story-content {
        background: white;
        padding: 25px;
        border-radius: 12px;
        margin-top: 15px;
    }

    .funding-box {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        top: 100px;
    }

    .amount {
        color: #198754;
        font-weight: 700;
    }

    .goal {
        color: #6c757d;
    }

    .progress {
        height: 8px;
        border-radius: 10px;
        background: #e9ecef;
    }

    .progress-bar {
        background: #198754;
    }

    .stats {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    .stats span {
        color: #6c757d;
        font-size: 14px;
    }

    .end-date {
        font-size: 13px;
        text-align: center;
        color: #6c757d;
        margin-top: 10px;
    }

    .tiers h5 {
        margin-top: 25px;
    }

    .tier-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 14px;
        margin-top: 12px;
    }

    .tier-card strong {
        color: #198754;
    }

    .tier-card .backers {
        float: right;
        font-size: 12px;
        color: #6c757d;
    }

    .recent-donations {
        margin-top: 20px;
    }

    .donations-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .donations-list::-webkit-scrollbar {
        width: 4px;
    }

    .donations-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .donations-list::-webkit-scrollbar-thumb {
        background: #198754;
        border-radius: 10px;
    }

    .donation-item {
        padding: 8px 0;
    }

    /* Toast Notifications */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        z-index: 99999;
        max-width: 400px;
        min-width: 300px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        font-size: 14px;
        line-height: 1.5;
    }

    .toast-notification.success {
        background-color: #198754;
        border-left: 4px solid #157347;
    }

    .toast-notification.error {
        background-color: #dc3545;
        border-left: 4px solid #c82333;
    }

    .toast-notification.info {
        background-color: #333;
        border-left: 4px solid #222;
    }
    
    .toast-notification i {
        font-size: 18px;
        margin-right: 10px;
    }

    /* Rate debug icon (shown when ?test=1) */
    .rate-debug-icon {
        cursor: pointer;
        color: #198754;
        font-size: 12px;
        margin-left: 4px;
        opacity: 0.8;
    }
    .rate-debug-icon:hover {
        opacity: 1;
    }
    
    @media (max-width: 768px) {
        .toast-notification {
            right: 10px;
            left: 10px;
            max-width: calc(100% - 20px);
            min-width: auto;
        }
    }
</style>
@endpush

@section('content')
@php
    // DB: goal_amount / goal_amount_usd in platform (USD base), original_goal_amount in creator currency
    $goalAmountUsd = (float)(@$campaignData->goal_amount ?? @$campaignData->goal_amount_usd ?? 0);
    $raisedAmountUsd = (float)(@$campaignData->raised_amount ?? 0);
    if ($raisedAmountUsd == 0) {
        $raisedAmountUsd = $campaignData->deposits()
            ->where('status', \App\Constants\ManageStatus::PAYMENT_SUCCESS)
            ->sum(\DB::raw('COALESCE(usd_amount, amount)'));
    }
    $setting = bs();
    $goalAmount = usdToLocal($goalAmountUsd);
    $raisedAmount = usdToLocal($raisedAmountUsd);
    $percentage = donationPercentage($goalAmount, $raisedAmount);
    $activeTab = request()->get('tab', 'campaign');
    $showRateDebug = request()->has('test');
    $localCur = strtoupper(getLocalCurrencyCode());
    $localPerOneUsd = 1.0;
    if ($localCur !== 'USD') {
        try {
            $cs = app(\App\Services\CurrencyService::class);
            $lc = $cs->getOrCreateByCode($localCur);
            $rUsdPerUnit = $cs->getRateToUsd($lc);
            $localPerOneUsd = ($rUsdPerUnit > 0) ? (1 / $rUsdPerUnit) : 1.0;
        } catch (\Throwable $e) {
            $localPerOneUsd = 1.0;
        }
    }
    $donateUrl = route('campaign.donate', $campaignData->slug);
    if ($showRateDebug) {
        $donateUrl .= (strpos($donateUrl, '?') !== false ? '&' : '?') . 'test=1';
    }
@endphp

@php
    $campaignImagePath = getFilePath('campaign') . '/' . (@$campaignData->image ?? '');
    $isLocalHost = in_array(request()->getHost(), ['localhost', '127.0.0.1', '0.0.0.0'], true);
    $assetBase = $isLocalHost ? rtrim(url('/'), '/') : rtrim((string) env('ASSETS_URL', url('/')), '/');
    $campaignImageUrl = $assetBase . '/' . ltrim($campaignImagePath, '/');

    $campaignVideoPath = getFilePath('campaign') . '/' . (@$campaignData->video ?? '');
    $hasCampaignVideoFile = !empty($campaignData->video)
        && (file_exists(public_path($campaignVideoPath)) || file_exists(base_path('public/' . $campaignVideoPath)));
@endphp

<!-- ================= MAIN ================= -->
<main class="container main-wrapper">
    <div class="row">
        <!-- LEFT COLUMN -->
        <div class="col-lg-8">
            <span class="badge badge-category mb-3">{{ @$campaignData->category->name ?? 'Campaign' }}</span>
            @if($campaignData->isExpired())
                <span class="badge bg-danger ms-2 px-3 py-2" style="font-size: 0.85rem;">@lang('Ended') / @lang('Expired')</span>
            @endif

            <h1 class="campaign-title">{{ __(@$campaignData->name) }}</h1>

            <p class="campaign-subtitle">
                {{ Str::limit(strip_tags(@$campaignData->description), 150) }}
            </p>

            <!-- ✅ VIDEO OR IMAGE DISPLAY WITH COVER -->
            @if(@$campaignData->youtube_url)
                @php
                    // Extract YouTube video ID from URL
                    $videoId = '';
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $campaignData->youtube_url, $matches)) {
                        $videoId = $matches[1];
                    }
                @endphp
                @if($videoId)
                    <div class="campaign-video-wrapper" style="position: relative; width: 100%; padding-bottom: 56.25%; height: 0; border-radius: 14px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                        <!-- Cover Image with Play Button (Initially Visible) -->
                        <div id="video-cover-{{ $videoId }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer; z-index: 10;">
                            <img src="{{ $campaignImageUrl }}" 
                                 alt="{{ @$campaignData->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px;">
                            
                            <!-- Green Play Button Overlay -->
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(25, 135, 84, 0.9); border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
                                 onmouseover="this.style.background='rgba(25, 135, 84, 1)'; this.style.transform='translate(-50%, -50%) scale(1.1)'"
                                 onmouseout="this.style.background='rgba(25, 135, 84, 0.9)'; this.style.transform='translate(-50%, -50%) scale(1)'">
                                <i class="fas fa-play" style="font-size: 32px; color: white; margin-left: 5px;"></i>
                            </div>
                        </div>
                        
                        <!-- YouTube Video Iframe (Initially Hidden) -->
                        <iframe 
                            id="video-iframe-{{ $videoId }}"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; display: none;"
                            src="" 
                            data-src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1&rel=0"
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    
                    <script>
                        // Play video when cover is clicked
                        document.getElementById('video-cover-{{ $videoId }}').addEventListener('click', function() {
                            var cover = document.getElementById('video-cover-{{ $videoId }}');
                            var iframe = document.getElementById('video-iframe-{{ $videoId }}');
                            
                            // Hide cover
                            cover.style.display = 'none';
                            
                            // Show and play video
                            iframe.style.display = 'block';
                            iframe.src = iframe.getAttribute('data-src');
                        });
                    </script>
                @endif
            @elseif(@$campaignData->video && $hasCampaignVideoFile)
                <div class="campaign-video-wrapper" style="position: relative; width: 100%; padding-bottom: 56.25%; height: 0; border-radius: 14px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                    <video 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                        controls
                        poster="{{ getImage(getFilePath('campaign') . '/' . @$campaignData->image, getFileSize('campaign')) }}">
                        <source src="{{ getImage(getFilePath('campaign') . '/' . $campaignData->video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @else
                <!-- IMAGE FALLBACK -->
                <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaignData->image, getFileSize('campaign')) }}"
                     class="campaign-image"
                     alt="{{ @$campaignData->name }}">
            @endif

            <!-- AUTHOR -->
            <div class="campaign-author">
                @if($campaignData->user->image)
                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $campaignData->user->image) }}" alt="{{ $campaignData->user->fullname }}">
                @else
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #198754; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                        {{ strtoupper(substr($campaignData->user->fullname, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <strong>{{ $campaignData->user->fullname }}</strong>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $campaignData->location ?? 'Location not specified' }}</span>
                </div>
                @if(auth()->id() != $campaignData->user_id)
                    @php
                        $creator = $campaignData->user;
                        $chatUrl = auth()->check()
                            ? route('user.inbox.index', ['start' => $creator->id, 'campaign_id' => $campaignData->id, 'campaign_slug' => $campaignData->slug, 'campaign_title' => $campaignData->name ?? ''])
                            : route('user.login') . '?redirect=' . urlencode(route('user.inbox.index', ['start' => $creator->id, 'campaign_id' => $campaignData->id, 'campaign_slug' => $campaignData->slug, 'campaign_title' => $campaignData->name ?? '']));
                    @endphp
                    <a href="{{ $chatUrl }}" class="btn btn-outline-success btn-sm ms-2" title="Chat"><i class="fas fa-comments"></i> Chat</a>
                    {{-- Commented: Call, Email, WhatsApp - using Chat only --}}
                @endif
            </div>

            <!-- TABS -->
            <ul class="nav nav-tabs campaign-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'campaign' ? 'active' : '' }}" href="{{ route('campaign.show', $campaignData->slug) }}?tab=campaign">Campaign Story</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'rewards' ? 'active' : '' }}" href="{{ route('campaign.show', $campaignData->slug) }}?tab=rewards">Rewards</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'updates' ? 'active' : '' }}" href="{{ route('campaign.show', $campaignData->slug) }}?tab=updates">Updates ({{ $campaignData->updates()->count() }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'comments' ? 'active' : '' }}" href="{{ route('campaign.show', $campaignData->slug) }}?tab=comments">Comments ({{ $commentCount ?? 0 }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($activeTab == 'faqs' || $activeTab == 'faq') ? 'active' : '' }}" href="{{ route('campaign.show', $campaignData->slug) }}?tab=faq">FAQs ({{ $faqs ? $faqs->count() : 0 }})</a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="story-content">
                <!-- Campaign Tab Content (Description) -->
                @if($activeTab == 'campaign' || !request()->has('tab'))
                    {!! $campaignData->description !!}
                @endif

                <!-- Rewards Section -->
                @if($activeTab == 'rewards')
                    <div class="rewards-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3>
                                <i class="fas fa-gift text-success"></i>
                                Campaign Rewards
                            </h3>
                            @auth
                                @if(auth()->id() == $campaignData->user_id)
                                    <div>
                                        <a href="{{ route('user.rewards.index', $campaignData->slug) }}" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-cog"></i>
                                            Manage Rewards
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        @php
                            $campaignRewards = $campaignData->rewards()->active()->orderBy('minimum_amount')->get();
                        @endphp
                        
                        @if($campaignRewards->count() > 0)
                            <div class="row g-4">
                                @foreach($campaignRewards as $reward)
                                    <div class="col-md-6">
                                        <div class="card h-100 border">
                                            @if($reward->image)
                                                <img src="{{ getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $reward->title }}"
                                                     style="height: 200px; object-fit: cover;">
                                            @endif
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0">{{ $reward->title }}</h5>
                                                    <span class="badge bg-success">{{ formatUsdForDisplay($reward->minimum_amount, 0) }}</span>
                                                </div>
                                                <p class="card-text text-muted">{{ $reward->description }}</p>
                                                
                                                <div class="d-flex gap-3 mb-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-{{ $reward->type == 'physical' ? 'box' : 'download' }}"></i>
                                                        {{ ucfirst($reward->type) }} Reward
                                                    </small>
                                                    @if($reward->quantity)
                                                        <small class="text-muted">
                                                            <i class="fas fa-layer-group"></i>
                                                            {{ $reward->getRemainingQuantity() }} left
                                                        </small>
                                                    @endif
                                                </div>

                                                @if($reward->terms_conditions)
                                                    <div class="mb-3">
                                                        <small class="text-muted">{{ $reward->terms_conditions }}</small>
                                                    </div>
                                                @endif
                                                
                                                <a href="{{ route('campaign.donate', $campaignData->slug) }}?reward={{ $reward->id }}" 
                                                   class="btn btn-success w-100">
                                                    Get This Reward
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-gift" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                                <h4 class="mb-2">No Rewards Available</h4>
                                <p class="text-muted">This campaign doesn't have any rewards yet.</p>
                                @auth
                                    @if(auth()->id() == $campaignData->user_id)
                                        <a href="{{ route('user.rewards.index', $campaignData->slug) }}" class="btn btn-success mt-3">
                                            <i class="fas fa-plus"></i>
                                            Add Your First Reward
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Comments Section -->
                @if($activeTab == 'comments')
                    <h3>Comments & Reviews</h3>
                    <p class="text-muted mb-4">Share your thoughts and experiences about this campaign.</p>

                    <!-- Review Form -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h4>Write a Comment</h4>
                        @if(!auth()->check())
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;">
                                <i class="fas fa-info-circle"></i> 
                                You can comment as a guest. Just fill in your name and email below.
                            </p>
                        @endif
                        <form class="review-form" id="reviewForm" method="POST" action="{{ route('campaign.comment', $campaignData->slug) }}">
                            @csrf
                            @auth
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-user-check text-success"></i>
                                    Commenting as <strong>{{ auth()->user()->fullname ?? auth()->user()->username }}</strong>
                                </p>
                            @endauth
                            <div class="mb-3">
                                <label for="reviewTitle" class="form-label">Comment Title:</label>
                                <input type="text" class="form-control" id="reviewTitle" name="title" placeholder="Give your comment a title">
                            </div>
                            <div class="mb-3">
                                <label for="reviewContent" class="form-label">Your Comment:</label>
                                <textarea class="form-control" id="reviewContent" name="comment" rows="4" placeholder="Share your thoughts about this campaign..." required></textarea>
                            </div>
                            @guest
                            <div class="mb-3">
                                <label for="reviewerName" class="form-label">Your Name:</label>
                                <input type="text" class="form-control" id="reviewerName" name="name" placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3">
                                <label for="reviewerEmail" class="form-label">Your Email:</label>
                                <input type="email" class="form-control" id="reviewerEmail" name="email" placeholder="Enter your email" required>
                            </div>
                            @endguest
                            <button type="submit" class="btn btn-success">Submit Comment</button>
                        </form>
                    </div>

                    <!-- Reviews Display -->
                    <div class="reviews-display">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4>Recent Comments</h4>
                            </div>
                        </div>

                        <div class="reviews-list">
                            @forelse ($comments as $comment)
                                <div class="mb-4 pb-4 border-bottom">
                                    <div class="d-flex align-items-start gap-3 mb-2">
                                        <div class="flex-shrink-0">
                                            @if($comment->user && $comment->user->image)
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $comment->user->image) }}" alt="{{ $comment->user->fullname }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            @else
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #198754; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                    {{ strtoupper(substr($comment->user ? $comment->user->fullname : $comment->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $comment->user ? $comment->user->fullname : $comment->name }}</h5>
                                            <small class="text-muted">{{ showDateTime($comment->created_at, 'd M, Y') }}</small>
                                        </div>
                                    </div>
                                    @if($comment->title)
                                        <h6 class="mb-2">{{ $comment->title }}</h6>
                                    @endif
                                    <p class="mb-0">{{ $comment->comment }}</p>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-star" style="font-size: 2rem; color: #ddd; margin-bottom: 15px;"></i>
                                    <h5 class="mb-2">No reviews yet</h5>
                                    <p class="text-muted">Be the first to leave a review and share your experience with this campaign!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- FAQ Section -->
                @if($activeTab == 'faqs' || $activeTab == 'faq')
                    <h3 class="mb-4">
                        <i class="fas fa-question-circle text-success"></i>
                        Frequently Asked Questions
                    </h3>
                    
                    @php
                        if (!isset($faqs) || $faqs->isEmpty()) {
                            $faqs = $campaignData->faqs()->orderBy('order')->orderBy('id')->get();
                        }
                    @endphp
                    
                    @if($faqs && $faqs->count() > 0)
                        <div class="faq-list">
                            @foreach($faqs as $index => $faq)
                                <div class="faq-item mb-3 p-3 bg-white border rounded" style="cursor: pointer;" onclick="toggleFaq({{ $index }})">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">{{ $faq->question }}</h5>
                                        <i class="fas fa-chevron-down faq-icon" id="faq-icon-{{ $index }}"></i>
                                    </div>
                                    <div class="faq-answer mt-3" id="faq-answer-{{ $index }}" style="display: none;">
                                        <p class="text-muted mb-0">{{ $faq->answer }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-question-circle" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                            <h4 class="mb-2">No FAQs Available</h4>
                            <p class="text-muted">This campaign doesn't have any frequently asked questions yet.</p>
                        </div>
                    @endif
                @endif

                <!-- Updates Section -->
                @if($activeTab == 'updates')
                    <div class="updates-section">
                        <h3 class="mb-4">
                            <i class="fas fa-bullhorn text-success"></i>
                            Campaign Updates
                        </h3>
                        
                        @php
                            $campaignUpdates = $campaignData->updates()->where('is_published', true)->latest()->get();
                        @endphp
                        
                        @if($campaignUpdates && $campaignUpdates->count() > 0)
                            <div class="row g-3">
                                @foreach($campaignUpdates as $update)
                                    <div class="col-md-6">
                                        <div class="card h-100 border hover-shadow" style="transition: all 0.3s ease;">
                                            @if($update->image)
                                                <img src="{{ getImage(getFilePath('campaign') . '/' . $update->image, getFileSize('campaign')) }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $update->title }}"
                                                     style="height: 200px; object-fit: cover;">
                                            @endif
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="{{ route('campaign.update.show', [$campaignData->slug, $update->id]) }}" 
                                                       class="text-decoration-none text-dark">
                                                        {{ $update->title }}
                                                    </a>
                                                </h5>
                                                <div class="d-flex gap-3 mb-3 text-muted small">
                                                    <span><i class="fas fa-calendar-alt text-success"></i> {{ $update->created_at->format('M d, Y') }}</span>
                                                    <span><i class="fas fa-clock text-success"></i> {{ $update->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="card-text text-muted">
                                                    {{ Str::limit(strip_tags($update->content), 120) }}
                                                </p>
                                                <a href="{{ route('campaign.update.show', [$campaignData->slug, $update->id]) }}" 
                                                   class="btn btn-success btn-sm">
                                                    Read Update <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-newspaper" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                                <h4 class="mb-2">No Updates Yet</h4>
                                <p class="text-muted">This campaign hasn't posted any updates yet. Check back later!</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4">
            <div class="funding-box sticky-top">
                <h2 class="amount d-inline-block">
                {{ showCurrency(round($raisedAmount, 0), 0) }}
                    @if($showRateDebug)
                    <i class="fas fa-calculator rate-debug-icon ms-1" role="button" tabindex="0"
                       data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="click"
                       data-bs-html="true"
                       data-bs-content="<div class='rate-debug-popover'><strong>Currency &amp; Rate</strong><br>USD: {{ '$' . number_format($raisedAmountUsd, 2) }}<br>Local currency: {{ $localCur }}<br>Rate: 1 USD = {{ number_format($localPerOneUsd, 4) }} {{ $localCur }}<br>Converted: {{ number_format($raisedAmount, 2) }} {{ $localCur }}</div>"
                       title="Rate Info"></i>
                    @endif
                </h2>
                <p class="goal">
                    pledged of {{ showCurrency(round($goalAmount, 0), 0) }} goal
                    @if($showRateDebug)
                    <i class="fas fa-calculator rate-debug-icon ms-1" role="button" tabindex="0"
                       data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="click"
                       data-bs-html="true"
                       data-bs-content="<div class='rate-debug-popover'><strong>Currency &amp; Rate</strong><br>USD: {{ '$' . number_format($goalAmountUsd, 2) }}<br>Local currency: {{ $localCur }}<br>Rate: 1 USD = {{ number_format($localPerOneUsd, 4) }} {{ $localCur }}<br>Converted: {{ number_format($goalAmount, 2) }} {{ $localCur }}</div>"
                       title="Rate Info"></i>
                    @endif
                </p>

                <div class="progress">
                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                </div>
                
                <small class="funded-text">{{ round($percentage) }}% funded</small>

                <div class="stats">
                    <div>
                        <strong>{{ $campaignData->deposits->where('status', \App\Constants\ManageStatus::PAYMENT_SUCCESS)->count() }}</strong>
                        <span>backers</span>
                    </div>
                    <div>
                        <strong>
                            @if($campaignData->end_date)
                                @php
                                    $endDate = \Carbon\Carbon::parse($campaignData->end_date);
                                    $now = \Carbon\Carbon::now();
                                    $daysLeft =(int) $now->diffInDays($campaignData->end_date, false);
                                @endphp
                                @if($daysLeft < 0)
                                    0
                                @else
                                    {{ $daysLeft }}
                                @endif
                            @else
                                N/A
                            @endif
                        </strong>
                        <span>
                            @if($campaignData->end_date && \Carbon\Carbon::parse($campaignData->end_date)->isPast())
                                days ago
                            @else
                                days left
                            @endif
                        </span>
                    </div>
                </div>

                <a href="{{ $donateUrl }}" class="btn btn-success w-100 mt-3">Back This Project</a>

                <!-- Recent Donations/Backers List -->
                @if(isset($donations) && $donations->count() > 0)
                    <div class="recent-donations mt-4 pt-4 border-top">
                        <h6 class="mb-3 fw-semibold">
                            <i class="fas fa-heart text-danger"></i>
                            Recent Backers
                        </h6>
                        <div class="donations-list" style="max-height: 300px; overflow-y: auto;">
                            @foreach($donations as $donation)
                            @php
                                $donationUsd = (float)($donation->amount ?? 0);
                                $donationAmount = usdToLocal($donationUsd);
                            @endphp
                                <div class="donation-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-heart text-danger" style="font-size: 0.9rem;"></i>
                                            <div>
                                                <div class="fw-semibold">{{ __($donation->donorName) }}</div>
                                                <small class="text-muted">
                                                    @if($loop->first)
                                                        Recent contribution
                                                    @elseif($donation->amount == $donations->max('amount'))
                                                        Top contribution
                                                    @else
                                                        {{ diffForHumans($donation->created_at) }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-success fw-bold">
                                        {{ showCurrency($donationAmount, 0) }}
                                            @if($showRateDebug)
                                            <i class="fas fa-calculator rate-debug-icon ms-1" role="button" tabindex="0"
                                               data-bs-toggle="popover" data-bs-placement="left" data-bs-trigger="click"
                                               data-bs-html="true"
                                               data-bs-content="<div class='rate-debug-popover'><strong>Donation</strong><br>USD: {{ '$' . number_format($donationUsd, 2) }}<br>Local currency: {{ $localCur }}<br>Rate: 1 USD = {{ number_format($localPerOneUsd, 4) }} {{ $localCur }}<br>Converted: {{ number_format($donationAmount, 2) }} {{ $localCur }}</div>"
                                               title="Rate Info"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="recent-donations mt-4 pt-4 border-top">
                        <h6 class="mb-3 fw-semibold">
                            <i class="fas fa-heart text-danger"></i>
                            Recent Backers
                        </h6>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0 small">No contributions yet. Be the first!</p>
                        </div>
                    </div>
                @endif

                @if($campaignData->end_date)
                    <p class="end-date">
                        Campaign ends on {{ \Carbon\Carbon::parse($campaignData->end_date)->format('F d, Y') }}
                    </p>
                @endif

                <!-- TIERS -->
                @if($campaignData->rewards && $campaignData->rewards->count() > 0)
                    <div class="tiers">
                        <h5>Support Tiers</h5>
                        @foreach($campaignData->rewards as $reward)
                            @php
                                $rewardUsd = (float)($reward->minimum_amount ?? $reward->amount ?? 0);
                                $rewardLocal = usdToLocal($rewardUsd);
                            @endphp
                            <div class="tier-card">
                                <strong>
                                    {{ formatUsdForDisplay($reward->minimum_amount ?? $reward->amount ?? 0, 0) }}
                                    @if($showRateDebug)
                                    <i class="fas fa-calculator rate-debug-icon ms-1" role="button" tabindex="0"
                                       data-bs-toggle="popover" data-bs-placement="right" data-bs-trigger="click"
                                       data-bs-html="true"
                                       data-bs-content="<div class='rate-debug-popover'><strong>Reward</strong><br>USD: {{ '$' . number_format($rewardUsd, 2) }}<br>Local currency: {{ $localCur }}<br>Rate: 1 USD = {{ number_format($localPerOneUsd, 4) }} {{ $localCur }}<br>Converted: {{ number_format($rewardLocal, 2) }} {{ $localCur }}</div>"
                                       title="Rate Info"></i>
                                    @endif
                                </strong>
                                <span class="backers">{{ $reward->backers_count ?? 0 }} backers</span>
                                <p>{{ $reward->name }}</p>
                                <small>{{ Str::limit($reward->description, 60) }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleFaq(index) {
        const answer = document.getElementById('faq-answer-' + index);
        const icon = document.getElementById('faq-icon-' + index);
        
        if (answer.style.display === 'none' || answer.style.display === '') {
            answer.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            answer.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Toast Notification Function
    function showToast(type, message) {
        // Remove any existing toasts first
        $('.toast-notification').remove();
        
        // Create toast element with icon
        var icon = '';
        if (type === 'success') {
            icon = '<i class="fas fa-check-circle"></i>';
        } else if (type === 'error') {
            icon = '<i class="fas fa-exclamation-circle"></i>';
        } else if (type === 'info') {
            icon = '<i class="fas fa-info-circle"></i>';
        }
        
        var toast = $('<div class="toast-notification ' + type + '">' + icon + '<span>' + message + '</span></div>');
        
        // Add to page
        $('body').append(toast);
        
        // Animate in
        setTimeout(function() {
            toast.css('transform', 'translateX(0)');
        }, 100);
        
        // Remove after 5 seconds
        setTimeout(function() {
            toast.css('transform', 'translateX(100%)');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 5000);
    }

    // Initialize rate debug popovers (when ?test=1)
    $(document).ready(function() {
        if (typeof bootstrap !== 'undefined') {
            document.querySelectorAll('.rate-debug-icon[data-bs-toggle="popover"]').forEach(function(el) {
                new bootstrap.Popover(el, { sanitize: false });
            });
        }
    });

    // Comment Form Submission with AJAX
    $(document).ready(function() {
        $('#reviewForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = new FormData(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            
            // Disable submit button
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
            
            // Get CSRF token
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('Comment submission success:', response);
                    
                    // Show success toast
                    var message = response.message || 'Comment submitted successfully! Please wait for admin approval.';
                    showToast('success', message);
                    
                    // Reset form
                    $('#reviewForm')[0].reset();
                    
                    // Reload page after a short delay to show new comment
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    console.error('Comment submission error:', xhr);
                    var message = 'An error occurred while submitting your comment.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = [];
                        for (var field in errors) {
                            errorMessages.push(errors[field][0]);
                        }
                        message = errorMessages.join(', ');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        message = 'Session expired. Please refresh the page and try again.';
                    } else if (xhr.status === 422) {
                        message = 'Please check your form data and try again.';
                    }
                    
                    // Show error toast
                    showToast('error', message);
                    
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush
