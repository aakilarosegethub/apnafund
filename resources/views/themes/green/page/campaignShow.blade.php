<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __(@$campaignData->name) }} - FundGreen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>
        body {
            font-family: Inter, system-ui, sans-serif;
            background: #f8f9fa;
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

        .footer {
            background: #f1f3f5;
            padding: 50px 0 20px;
        }

        .footer h6 {
            font-weight: 700;
        }

        .footer ul {
            list-style: none;
            padding: 0;
        }

        .footer li {
            margin-bottom: 6px;
            color: #6c757d;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #6c757d;
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
        
        @media (max-width: 768px) {
            .toast-notification {
                right: 10px;
                left: 10px;
                max-width: calc(100% - 20px);
                min-width: auto;
            }
        }
    </style>
</head>
<body>
@php
            $goalAmount = @$campaignData->goal_amount ?? 0;
            $raisedAmount = @$campaignData->raised_amount ?? 0;
            
            if ($raisedAmount == 0) {
                $raisedAmount = $campaignData->deposits()
                    ->where('status', \App\Constants\ManageStatus::PAYMENT_SUCCESS)
                    ->sum('amount');
            }
            
            $percentage = donationPercentage($goalAmount, $raisedAmount);
        $activeTab = request()->get('tab', 'campaign');
        $setting = bs();
                            @endphp
                            
    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-success" href="{{ route('home') }}">
                <i class="fa-solid fa-heart"></i> FundGreen
            </a>

            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('campaign') }}">Explore</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('campaign') }}">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('about') }}">How it Works</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('about') }}">About</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-magnifying-glass"></i>
                <i class="fa-regular fa-user"></i>
                @auth
                    <a href="{{ route('start.project') }}" class="btn btn-success px-4 rounded-pill">Start a Campaign</a>
                @else
                    <a href="{{ route('user.login') }}" class="btn btn-success px-4 rounded-pill">Start a Campaign</a>
                @endauth
                                    </div>
                                </div>
    </nav>

    <!-- ================= MAIN ================= -->
    <main class="container main-wrapper">
        <div class="row">
            <!-- LEFT COLUMN -->
            <div class="col-lg-8">
                <span class="badge badge-category mb-3">{{ @$campaignData->category->name ?? 'Campaign' }}</span>

                <h1 class="campaign-title">{{ __(@$campaignData->name) }}</h1>

                <p class="campaign-subtitle">
                    {{ Str::limit(strip_tags(@$campaignData->description), 150) }}
                </p>

                <!-- ✅ IMAGE FIXED HERE -->
                        <img src="{{ getImage(getFilePath('campaign') . '/' . @$campaignData->image, getFileSize('campaign')) }}"
                     class="campaign-image"
                                alt="{{ @$campaignData->name }}">

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
                        <a class="nav-link {{ $activeTab == 'updates' ? 'active' : '' }}" href="{{ route('campaign.show', $campaignData->slug) }}?tab=updates">Updates (0)</a>
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
                                                        <span class="badge bg-success">{{ $setting->site_currency_sym }}{{ number_format($reward->minimum_amount, 0) }}</span>
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
                                <div class="mb-3">
                                    <label for="reviewTitle" class="form-label">Comment Title:</label>
                                    <input type="text" class="form-control" id="reviewTitle" name="title" placeholder="Give your comment a title">
                        </div>
                                <div class="mb-3">
                                    <label for="reviewContent" class="form-label">Your Comment:</label>
                                    <textarea class="form-control" id="reviewContent" name="comment" rows="4" placeholder="Share your thoughts about this campaign..." required></textarea>
                        </div>
                                <div class="mb-3">
                                    <label for="reviewerName" class="form-label">Your Name:</label>
                                    <input type="text" class="form-control" id="reviewerName" name="name" placeholder="Enter your name" required>
                        </div>
                                <div class="mb-3">
                                    <label for="reviewerEmail" class="form-label">Your Email:</label>
                                    <input type="email" class="form-control" id="reviewerEmail" name="email" placeholder="Enter your email" required>
                        </div>
                                <button type="submit" class="btn btn-success">Submit Comment</button>
                    </form>
                </div>

                <!-- Reviews Display -->
                <div class="reviews-display">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4>Recent Comments</h4>
                            @php
                                $avgRating = $comments->whereNotNull('rating')->avg('rating');
                                $totalReviews = $comments->whereNotNull('rating')->count();
                            @endphp
                            @if($avgRating)
                                        <p class="text-muted mb-0">
                                    <span style="color: #ffd700;">★★★★★</span> 
                                    {{ number_format($avgRating, 1) }} average rating 
                                    ({{ $totalReviews }} {{ $totalReviews == 1 ? 'review' : 'reviews' }})
                                </p>
                            @endif
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
                                                @if($comment->rating)
                                                    <div class="mb-2">
                                                        <span style="color: #ffd700;">
                                                        @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $comment->rating)★@else☆@endif
                                                        @endfor
                                                    </span>
                                                        <span class="ms-2">{{ $comment->rating }}.0</span>
                                                    </div>
                                                @endif
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

                    <!-- Updates Tab -->
                    @if($activeTab == 'updates')
                        <p class="text-muted">No updates available at this time.</p>
                        @endif

                                </div>
                                    </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">
                <div class="funding-box sticky-top">
                    <h2 class="amount">{{ $setting->site_currency_sym }}{{ number_format($raisedAmount, 0) }}</h2>
                    <p class="goal">pledged of {{ $setting->site_currency_sym }}{{ number_format($goalAmount, 0) }} goal</p>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        
                    <small class="funded-text">{{ round($percentage) }}% funded</small>

                    <div class="stats">
                        <div>
                            <strong>{{ $campaignData->deposits ? $campaignData->deposits->count() : ($donations ? $donations->count() : 0) }}</strong>
                            <span>backers</span>
                                        </div>
                        <div>
                            <strong>
                                @if($campaignData->end_date)
                                    @php
                                        $endDate = \Carbon\Carbon::parse($campaignData->end_date);
                                        $now = \Carbon\Carbon::now();
                                        $daysLeft = $endDate->diffInDays($now, false);
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
                                    days to go
                                @endif
                            </span>
                        </div>
        </div>

                    <a href="{{ route('campaign.donate', $campaignData->slug) }}" class="btn btn-success w-100 mt-3">Back This Project</a>

                    <!-- Recent Donations/Backers List -->
                    @if(isset($donations) && $donations->count() > 0)
                    <div class="recent-donations mt-4 pt-4 border-top">
                        <h6 class="mb-3 fw-semibold">
                            <i class="fas fa-heart text-danger"></i>
                            Recent Backers
                        </h6>
                        <div class="donations-list" style="max-height: 300px; overflow-y: auto;">
                            @foreach($donations as $donation)
                                <div class="donation-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-heart text-danger" style="font-size: 0.9rem;"></i>
                                            <div>
                                                <div class="fw-semibold">{{ __($donation->donorName) }}</div>
                                                <small class="text-muted">
                                @if($loop->first)
                                    Recent donation
                                @elseif($donation->amount == $donations->max('amount'))
                                    Top donation
                                @else
                                    {{ diffForHumans($donation->created_at) }}
                                @endif
                                                </small>
                        </div>
                                </div>
                                        <div class="text-success fw-bold">
                                            {{ $setting->site_currency_sym }}{{ number_format($donation->amount, 0) }}
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
                            <p class="text-muted mb-0 small">No donations yet. Be the first!</p>
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
                            <div class="tier-card">
                                <strong>{{ $setting->site_currency_sym }}{{ number_format($reward->amount, 0) }}</strong>
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

    <!-- ================= FOOTER ================= -->
    @php
        $footerContent = getSiteData('footer.content', true);
    @endphp
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h6>FundGreen</h6>
                    <p>{{ @$footerContent->data_info->footer_text ?? 'Empowering dreams through community-driven crowdfunding.' }}</p>
                    </div>
                    
                <div class="col-md-3">
                    <h6>Explore</h6>
                    <ul>
                        <li><a href="{{ route('campaign') }}" style="color: #6c757d; text-decoration: none;">Browse Campaigns</a></li>
                        <li><a href="{{ route('campaign') }}" style="color: #6c757d; text-decoration: none;">Categories</a></li>
                        <li><a href="{{ route('campaign') }}" style="color: #6c757d; text-decoration: none;">Success Stories</a></li>
                        <li><a href="{{ route('campaign') }}" style="color: #6c757d; text-decoration: none;">Featured</a></li>
                    </ul>
                    </div>
                    
                <div class="col-md-3">
                    <h6>Support</h6>
                    <ul>
                        <li><a href="{{ url('about') }}" style="color: #6c757d; text-decoration: none;">How it Works</a></li>
                        <li><a href="{{ url('faq') }}" style="color: #6c757d; text-decoration: none;">FAQ</a></li>
                        <li><a href="{{ url('contact') }}" style="color: #6c757d; text-decoration: none;">Contact Us</a></li>
                        <li style="color: #6c757d;">Trust & Safety</li>
                    </ul>
                    </div>
                    
                <div class="col-md-3">
                    <h6>Company</h6>
                    <ul>
                        <li><a href="{{ url('about') }}" style="color: #6c757d; text-decoration: none;">About Us</a></li>
                        <li style="color: #6c757d;">Careers</li>
                        <li style="color: #6c757d;">Press</li>
                        <li><a href="http://apnacrowdfunding.com/blog" style="color: #6c757d; text-decoration: none;">Blog</a></li>
                    </ul>
        </div>
    </div>

            <div class="footer-bottom">
                © {{ date('Y') }} FundGreen. {{ @$footerContent->data_info->copyright_text ?? 'All rights reserved.' }}
            </div>
                    </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
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
</body>
</html>
