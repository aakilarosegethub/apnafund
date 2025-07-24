@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Author')</th>
                    <th>@lang('Name')</th>
                    <th>@lang('Category')</th>
                    <th>@lang('Goal') | @lang('Raised')</th>
                    <th>@lang('Start Date') | @lang('End Date')</th>
                    <th>@lang('Approval Status')</th>
                    <th>@lang('Campaign Status')</th>
                    <th>@lang('Featured')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($campaigns as $campaign)
                    <tr>
                        <td>
                            <div class="table-card-with-image">
                                <div class="table-card-with-image__img">
                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . @$campaign->user->image, getFileSize('userProfile'), true) }}" alt="Image">
                                </div>
                                <div class="table-card-with-image__content">
                                    <p class="fw-semibold">{{ $campaign->user->fullname }}</p>
                                    <p class="fw-semibold">
                                        <a href="{{ appendQuery('search', @$campaign->user->username) }}"> <small>@</small>{{ $campaign->user->username }}</a>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span title="{{ __($campaign->name) }}">{{ __(strLimit($campaign->name, 20)) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ __($campaign->category->name) }}</span>
                        </td>
                        <td>
                            <div>
                                <p>{{ showAmount($campaign->goal_amount) . ' ' . __($setting->site_cur) }}</p>
                                <p class="text--success">{{ showAmount($campaign->raised_amount) . ' ' . __($setting->site_cur) }}</p>
                            </div>
                        </td>
                        <td>
                            <div>
                                <p>{{ showDateTime($campaign->start_date) }}</p>
                                <p class="text--warning">{{ showDateTime($campaign->end_date) }}</p>
                            </div>
                        </td>
                        <td>
                            @php echo $campaign->approvalStatusBadge @endphp
                        </td>
                        <td>
                            @php echo $campaign->campaignStatusBadge @endphp
                        </td>
                        <td>
                            @php echo $campaign->featuredStatusBadge @endphp
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.campaigns.details', $campaign->id) }}" class="btn btn--sm btn-outline--base">
                                    <i class="ti ti-info-square-rounded"></i> @lang('Details')
                                </a>

                                @if($campaign->status != ManageStatus::CAMPAIGN_REJECTED && !$campaign->isExpired())
                                    <div class="custom--dropdown">
                                        <button type="button" class="btn btn--icon btn--sm btn--base" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>

                                        @if($campaign->status == ManageStatus::CAMPAIGN_PENDING)
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button type="button" class="dropdown-item decisionBtn" data-question="@lang('Do you want to approve this campaign?')" data-action="{{ route('admin.campaigns.status.update', [$campaign->id, 'approve']) }}">
                                                        <span class="dropdown-icon"><i class="ti ti-circle-check text--success"></i></span> @lang('Approve')
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item decisionBtn" data-question="@lang('Do you want to reject this campaign?')" data-action="{{ route('admin.campaigns.status.update', [$campaign->id, 'reject']) }}">
                                                        <span class="dropdown-icon"><i class="ti ti-circle-x text--danger"></i></span> @lang('Reject')
                                                    </button>
                                                </li>
                                            </ul>
                                        @elseif($campaign->status == ManageStatus::CAMPAIGN_APPROVED)
                                            <ul class="dropdown-menu">
                                                <li>
                                                    @if($campaign->featured)
                                                        <button type="button" class="dropdown-item decisionBtn" data-question="@lang('Do you want to unfeatured this campaign?')" data-action="{{ route('admin.campaigns.featured.update', $campaign->id) }}">
                                                            <span class="dropdown-icon"><i class="ti ti-circle-x text--warning"></i></span> @lang('Unfeatured')
                                                        </button>
                                                    @else
                                                        <button type="button" class="dropdown-item decisionBtn" data-question="@lang('Do you want to featured this campaign?')" data-action="{{ route('admin.campaigns.featured.update', $campaign->id) }}">
                                                            <span class="dropdown-icon"><i class="ti ti-circle-check text--success"></i></span> @lang('Featured')
                                                        </button>
                                                    @endif
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    @include('admin.partials.noData')
                @endforelse
            </tbody>
        </table>

        @if ($campaigns->hasPages())
            {{ paginateLinks($campaigns) }}
        @endif
    </div>

    <x-decisionModal />
@endsection

@push('breadcrumb')
    <x-searchForm placeholder="Search..." />
@endpush
