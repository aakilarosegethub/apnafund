@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="donation pt-120 pb-60">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-header text-center mb-5">
                        <h2 class="section-title">@lang('Campaign Rewards')</h2>
                        <p class="section-subtitle">{{ $campaign->name }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <!-- Campaign Info Card -->
                    <div class="card custom--card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getThumbSize('campaign')) }}" 
                                         alt="{{ $campaign->name }}" class="img-fluid rounded">
                                </div>
                                <div class="col-md-9">
                                    <h4>{{ $campaign->name }}</h4>
                                    <p class="text-muted">{{ strLimit($campaign->description, 150) }}</p>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">@lang('Goal'):</small>
                                            <div class="fw-bold">{{ $setting->cur_sym . showAmount($campaign->goal_amount) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">@lang('Raised'):</small>
                                            <div class="fw-bold">{{ $setting->cur_sym . showAmount($campaign->raised_amount) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rewards List -->
                    @if($rewards->count() > 0)
                        <div class="row g-4">
                            @foreach($rewards as $reward)
                                <div class="col-md-6">
                                    <div class="card custom--card h-100 reward-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0">{{ $reward->title }}</h5>
                                                <span class="badge badge--{{ $reward->color_theme }}">
                                                    {{ $setting->cur_sym . showAmount($reward->minimum_amount) }}
                                                </span>
                                            </div>
                                            
                                            @if($reward->image)
                                                <div class="reward-image mb-3">
                                                    <img src="{{ getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) }}" 
                                                         alt="{{ $reward->title }}" class="img-fluid rounded">
                                                </div>
                                            @endif
                                            
                                            <p class="card-text">{{ $reward->description }}</p>
                                            
                                            <div class="reward-details mb-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted">@lang('Type'):</small>
                                                        <div class="fw-bold text-capitalize">{{ $reward->type }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">@lang('Available'):</small>
                                                        <div class="fw-bold">
                                                            @if($reward->quantity === null)
                                                                @lang('Unlimited')
                                                            @else
                                                                {{ $reward->getRemainingQuantity() }} @lang('left')
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($reward->terms_conditions)
                                                <div class="reward-terms mb-3">
                                                    <small class="text-muted">@lang('Terms & Conditions'):</small>
                                                    <div class="small">{{ $reward->terms_conditions }}</div>
                                                </div>
                                            @endif
                                            
                                            <div class="text-center">
                                                <a href="{{ route('campaign.donate', $campaign->slug) }}?reward={{ $reward->id }}" 
                                                   class="btn btn--base btn--sm">
                                                    @lang('Get This Reward')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="ti ti-gift display-1 text-muted mb-3"></i>
                                <h4>@lang('No Rewards Available')</h4>
                                <p class="text-muted">@lang('This campaign doesn\'t have any rewards yet.')</p>
                                <a href="{{ route('campaign.show', $campaign->slug) }}" class="btn btn--base">
                                    @lang('Back to Campaign')
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .reward-card {
            transition: transform 0.2s ease-in-out;
            border: 1px solid #e9ecef;
        }
        
        .reward-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .reward-image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .empty-state {
            padding: 2rem;
        }
        
        .badge--primary { background-color: #007bff; }
        .badge--success { background-color: #28a745; }
        .badge--warning { background-color: #ffc107; }
        .badge--danger { background-color: #dc3545; }
        .badge--info { background-color: #17a2b8; }
        .badge--secondary { background-color: #6c757d; }
    </style>
@endpush 