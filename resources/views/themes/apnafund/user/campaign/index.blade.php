@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
@section('frontend')
<div class="dashboard py-60">
    <div class="container">
        <div class="card custom--card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">@lang('Manage Your Gigs')</h4>
                    <a href="{{ route('user.campaign.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>@lang('Create New Gig')
                    </a>
                </div>

                <!-- Campaign Cards -->
                <div class="row">
                    @forelse ($campaigns as $campaign)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="preview-card">
                                <div class="preview-image">
                                    @if($campaign->image)
                                        <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image) }}" alt="{{ __($campaign->name) }}">
                                    @else
                                        <i class="fas fa-seedling"></i>
                                    @endif
                                </div>
                                <div class="preview-content">
                                    <div class="preview-title">{{ __($campaign->name) }}</div>
                                    <div class="preview-description">{{ Str::limit(strip_tags($campaign->description), 100) }}</div>
                                    <div class="preview-meta">
                                        <span class="preview-category">{{ __($campaign->category->name) }}</span>
                                        <span class="preview-amount">{{ bs('cur_sym') . showAmount($campaign->raised_amount) }}</span>
                                    </div>
                                    <div class="preview-progress">
                                        @php
                                            $percentage = $campaign->goal_amount > 0 ? ($campaign->raised_amount / $campaign->goal_amount) * 100 : 0;
                                            $percentage = min($percentage, 100);
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}% of {{ bs('cur_sym') . showAmount($campaign->goal_amount) }} goal</small>
                                    </div>
                                    
                                    <!-- Status Badges -->
                                    <div class="preview-status mb-3">
                                        @if ($campaign->status == \App\Constants\ManageStatus::CAMPAIGN_PENDING)
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif ($campaign->status == \App\Constants\ManageStatus::CAMPAIGN_REJECTED)
                                            <span class="badge badge--danger">@lang('Rejected')</span>
                                        @else
                                            <span class="badge badge--success">@lang('Approved')</span>
                                        @endif
                                        
                                        @if ($campaign->status == \App\Constants\ManageStatus::CAMPAIGN_APPROVED)
                                            @if ($campaign->isRunning())
                                                <span class="badge badge--success">@lang('Running')</span>
                                            @elseif ($campaign->isExpired())
                                                <span class="badge badge--secondary">@lang('Expired')</span>
                                            @else
                                                <span class="badge badge--info">@lang('Upcoming')</span>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="{{ route('user.campaign.edit', $campaign->slug) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit me-1"></i>@lang('Edit')
                                        </a>
                                        <a href="{{ route('user.campaign.show', $campaign->slug) }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye me-1"></i>@lang('View')
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCampaign('{{ $campaign->id }}')">
                                            <i class="fas fa-trash me-1"></i>@lang('Delete')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">{{ __($emptyMessage ?? 'No campaigns found') }}</h5>
                                <a href="{{ route('user.campaign.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-2"></i>@lang('Create Your First Campaign')
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if ($campaigns->hasPages())
                    <div class="mt-4">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">@lang('Confirm Delete')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @lang('Are you sure you want to delete this campaign? This action cannot be undone.')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">@lang('Delete')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-style')
<style>
/* Header Styles */
.card-body h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
}

.btn-primary {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: #059669;
    border-color: #059669;
    transform: translateY(-1px);
}

/* Card Grid */
.row {
    margin: 0 -12px;
}

.col-lg-4, .col-md-6 {
    padding: 0 12px;
}

/* Card Styles */
.preview-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.preview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.preview-image {
    height: 180px;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-image i {
    font-size: 2.5rem;
    color: #ffffff;
    opacity: 0.9;
}

.preview-content {
    padding: 24px;
}

.preview-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
    line-height: 1.4;
}

.preview-description {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 16px;
    min-height: 42px;
}

.preview-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.preview-category {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.preview-amount {
    font-weight: 600;
    color: #059669;
    font-size: 1rem;
}

.preview-progress {
    margin-bottom: 16px;
}

.preview-progress .progress {
    height: 6px;
    border-radius: 3px;
    background: #f3f4f6;
    margin-bottom: 6px;
}

.preview-progress .progress-bar {
    background: linear-gradient(90deg, #10b981, #059669);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.preview-progress small {
    font-size: 0.75rem;
    color: #9ca3af;
}

.preview-status {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}

.preview-status .badge {
    font-size: 0.7rem;
    padding: 3px 8px;
    border-radius: 12px;
}

.badge--warning {
    background: #f59e0b;
    color: #ffffff;
}

.badge--danger {
    background: #ef4444;
    color: #ffffff;
}

.badge--success {
    background: #10b981;
    color: #ffffff;
}

.badge--secondary {
    background: #6b7280;
    color: #ffffff;
}

.badge--info {
    background: #3b82f6;
    color: #ffffff;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.75rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-sm:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}

.btn-primary:hover {
    background: #059669;
    border-color: #059669;
}

.btn-secondary {
    background: #6b7280;
    border-color: #6b7280;
    color: #ffffff;
}

.btn-secondary:hover {
    background: #4b5563;
    border-color: #4b5563;
}

.btn-outline-danger {
    color: #ef4444;
    border-color: #ef4444;
    background: transparent;
}

.btn-outline-danger:hover {
    background: #ef4444;
    color: #ffffff;
}

/* Empty State */
.text-center.py-5 {
    padding: 3rem 0;
}

.text-center.py-5 i {
    color: #9ca3af;
    margin-bottom: 1rem;
}

.text-center.py-5 h5 {
    color: #6b7280;
    font-weight: 500;
}

@media (max-width: 768px) {
    .preview-card {
        margin-bottom: 20px;
    }
    
    .preview-image {
        height: 150px;
    }
    
    .preview-content {
        padding: 20px;
    }
    
    .preview-title {
        font-size: 1rem;
    }
    
    .card-body h4 {
        font-size: 1.25rem;
    }
}
</style>
@endpush

@push('page-script')
<script>
function deleteCampaign(campaignId) {
    if (confirm('@lang("Are you sure you want to delete this campaign? This action cannot be undone.")')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("user.campaign.destroy", "") }}/' + campaignId;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
