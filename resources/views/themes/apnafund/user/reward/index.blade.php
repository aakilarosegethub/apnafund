@php
    $activeTheme = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')

@section('frontend')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">@lang('Manage Rewards') - {{ $campaign->name }}</h5>
                        <a href="{{ route('user.rewards.create', $campaign->slug) }}" class="btn btn--base btn--sm">
                            <i class="ti ti-plus me-1"></i>@lang('Add New Reward')
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Image')</th>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Quantity')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rewards as $reward)
                                    <tr>
                                        <td>
                                            @if($reward->image)
                                                <img src="{{ getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) }}" 
                                                     alt="{{ $reward->title }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-gift text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $reward->title }}</td>
                                        <td>{{ strLimit($reward->description, 50) }}</td>
                                        <td>{{ $setting->cur_sym . showAmount($reward->minimum_amount) }}</td>
                                        <td>
                                            <span class="badge badge--{{ $reward->type == 'physical' ? 'primary' : 'info' }}">
                                                {{ ucfirst($reward->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($reward->quantity)
                                                {{ $reward->getRemainingQuantity() }} / {{ $reward->quantity }}
                                            @else
                                                <span class="text-muted">Unlimited</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reward->is_active)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.rewards.edit', [$campaign->slug, $reward->id]) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="@lang('Edit Reward')">
                                                    <i class="fas fa-edit me-1"></i>@lang('Edit')
                                                </a>
                                                <form action="{{ route('user.rewards.toggle.status', [$campaign->slug, $reward->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-{{ $reward->is_active ? 'warning' : 'success' }}" 
                                                            title="{{ $reward->is_active ? __('Deactivate') : __('Activate') }}">
                                                        <i class="fas fa-{{ $reward->is_active ? 'pause' : 'play' }} me-1"></i>
                                                        {{ $reward->is_active ? __('Deactivate') : __('Activate') }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('user.rewards.destroy', [$campaign->slug, $reward->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('@lang('Are you sure you want to delete this reward?')')" 
                                                            title="@lang('Delete Reward')">
                                                        <i class="fas fa-trash me-1"></i>@lang('Delete')
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-gift fa-2x mb-2"></i>
                                                <p>@lang('No rewards found')</p>
                                                <a href="{{ route('user.rewards.create', $campaign->slug) }}" class="btn btn--base btn--sm">
                                                    <i class="ti ti-plus me-1"></i>@lang('Add Your First Reward')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 