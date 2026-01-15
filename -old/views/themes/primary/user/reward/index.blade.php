@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">@lang('Campaign Rewards') - {{ $campaign->name }}</h5>
                        <a href="{{ route('user.rewards.create', $campaign->id) }}" class="btn btn--base btn--sm">
                            <i class="ti ti-plus me-1"></i>@lang('Add New Reward')
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($rewards->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>@lang('Reward')</th>
                                        <th>@lang('Minimum Amount')</th>
                                        <th>@lang('Type')</th>
                                        <th>@lang('Quantity')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rewards as $reward)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($reward->image)
                                                        <img src="{{ getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) }}" 
                                                             alt="{{ $reward->title }}" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $reward->title }}</h6>
                                                        <small class="text-muted">{{ strLimit($reward->description, 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge--{{ $reward->color_theme }}">
                                                    {{ $setting->cur_sym . showAmount($reward->minimum_amount) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge--{{ $reward->type == 'digital' ? 'info' : 'warning' }}">
                                                    {{ ucfirst($reward->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($reward->quantity === null)
                                                    <span class="text-success">@lang('Unlimited')</span>
                                                @else
                                                    <span class="text-{{ $reward->isAvailable() ? 'success' : 'danger' }}">
                                                        {{ $reward->claimed_count }}/{{ $reward->quantity }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge--{{ $reward->is_active ? 'success' : 'danger' }}">
                                                    {{ $reward->is_active ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('user.rewards.edit', [$campaign->id, $reward->id]) }}" 
                                                       class="btn btn--sm btn--outline-primary">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('user.rewards.toggle.status', [$campaign->id, $reward->id]) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn--sm btn--outline-{{ $reward->is_active ? 'warning' : 'success' }}"
                                                                onclick="return confirm('{{ $reward->is_active ? __('Are you sure you want to deactivate this reward?') : __('Are you sure you want to activate this reward?') }}')">
                                                            <i class="ti ti-{{ $reward->is_active ? 'eye-off' : 'eye' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('user.rewards.destroy', [$campaign->id, $reward->id]) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn--sm btn--outline-danger"
                                                                onclick="return confirm('@lang('Are you sure you want to delete this reward?')')">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="ti ti-gift display-1 text-muted mb-3"></i>
                                <h4>@lang('No Rewards Yet')</h4>
                                <p class="text-muted">@lang('You haven\'t created any rewards for this campaign yet.')</p>
                                <a href="{{ route('user.rewards.create', $campaign->id) }}" class="btn btn--base">
                                    <i class="ti ti-plus me-1"></i>@lang('Create Your First Reward')
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('user.campaign.show', $campaign->slug) }}" class="btn btn--outline">
                    <i class="ti ti-arrow-left me-1"></i>@lang('Back to Campaign')
                </a>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .empty-state {
            padding: 2rem;
        }
        
        .badge--primary { background-color: #007bff; }
        .badge--success { background-color: #28a745; }
        .badge--warning { background-color: #ffc107; }
        .badge--danger { background-color: #dc3545; }
        .badge--info { background-color: #17a2b8; }
        .badge--secondary { background-color: #6c757d; }
        
        .btn--outline-primary { border-color: #007bff; color: #007bff; }
        .btn--outline-success { border-color: #28a745; color: #28a745; }
        .btn--outline-warning { border-color: #ffc107; color: #ffc107; }
        .btn--outline-danger { border-color: #dc3545; color: #dc3545; }
    </style>
@endpush 