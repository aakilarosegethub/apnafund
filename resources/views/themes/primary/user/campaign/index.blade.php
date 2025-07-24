@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <form action="" class="input--group">
                            <input type="text" class="form--control" name="search" value="{{ request('search') }}" placeholder="@lang('Campaign/Category...')">
                            <button type="submit" class="btn btn--sm btn--base">
                                <i class="ti ti-search"></i>
                            </button>
                        </form>
                    </div>
                    <table class="table table-striped table-borderless table--responsive--xl">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Goal Amount')</th>
                                <th>@lang('Fund Raised')</th>
                                <th>@lang('Deadline')</th>
                                <th>@lang('Approval Status')</th>
                                <th>@lang('Campaign Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($campaigns as $campaign)
                                <tr>
                                    <td>
                                        {{ @$campaigns->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        <span class="text-overflow-1 text--base">{{ __(@$campaign->name) }}</span>
                                    </td>
                                    <td>{{ __(@$campaign->category->name) }}</td>
                                    <td>{{ $setting->cur_sym . showAmount(@$campaign->goal_amount) }}</td>
                                    <td>{{ $setting->cur_sym . showAmount(@$campaign->raised_amount) }}</td>
                                    <td>
                                        <span>
                                            <span class="d-block">{{ showDateTime(@$campaign->end_date, 'd-M-Y') }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        @if (@$campaign->status == ManageStatus::CAMPAIGN_PENDING)
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif (@$campaign->status == ManageStatus::CAMPAIGN_REJECTED)
                                            <span class="badge badge--danger">@lang('Rejected')</span>
                                        @else
                                            <span class="badge badge--success">@lang('Approved')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (@$campaign->status == ManageStatus::CAMPAIGN_PENDING || @$campaign->status == ManageStatus::CAMPAIGN_REJECTED)
                                            <span>@lang('N/A')</span>
                                        @elseif (@$campaign->isRunning())
                                            <span class="badge badge--success">@lang('Running')</span>
                                        @elseif (@$campaign->isExpired())
                                            <span class="badge badge--secondary">@lang('Expired')</span>
                                        @else
                                            <span class="badge badge--info">@lang('Upcoming')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="custom--dropdown dropdown-sm">
                                            <button class="btn btn--sm btn-outline--base dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                @lang('Action')
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('user.campaign.show', $campaign->slug) }}" class="dropdown-item">
                                                        <i class="ti ti-eye text--info"></i> @lang('Details')
                                                    </a>
                                                </li>

                                                @if (@$campaign->status == ManageStatus::CAMPAIGN_APPROVED && !@$campaign->isExpired())
                                                    <li>
                                                        <a href="{{ route('user.campaign.edit', $campaign->slug) }}" class="dropdown-item">
                                                            <i class="ti ti-edit text--success"></i> @lang('Edit')
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($campaigns->hasPages())
                        {{ $campaigns->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
