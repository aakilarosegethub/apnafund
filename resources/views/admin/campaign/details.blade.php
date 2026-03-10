@extends('admin.layouts.master')

@section('master')
    <div class="col-xl-8 col-lg-7">
        <div class="custom--card h-auto mb-4">
            <div class="card-body">
                <div class="campaign-details">
                    <div class="campaign-details__img">
                        <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" alt="Image">
                    </div>
                    <div class="campaign-details__txt">
                        <h3 class="campaign-details__title">{{ __($campaign->name) }}</h3>
                        <div class="campaign-details__desc">
                            <h6>@lang('Description'):</h6>
                            <div class="description scroll">
                                @php echo $campaign->description @endphp
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="custom--card h-auto">
            <div class="card-header">
                <h3 class="title">@lang('Comments')</h3>
            </div>
            <div class="card-body">
                @if(count($comments))
                    <div class="comment accordion custom--accordion" id="accordionExample">
                        @foreach($comments as $comment)
                            <div class="accordion-item" data-aos="fade-up" data-aos-duration="1500">
                                <h2 class="accordion-header">
                                    <button type="button" @class(['accordion-button', 'collapsed' => !$loop->first]) data-bs-toggle="collapse" data-bs-target="{{ '#comment-' . $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ 'comment-' . $loop->iteration }}">
                                        <span>
                                            <span class="comment__name">{{ $comment->user ? $comment->user->fullname : $comment->name }}</span>
                                            <span class="comment__date">{{ showDateTime($comment->created_at) }}</span>
                                        </span>
                                    </button>
                                    <button type="button" class="comment__delete decisionBtn" data-question="@lang('Do you want to delete this comment?')" data-action="{{ route('admin.comments.delete', $comment->id) }}"></button>
                                </h2>
                                <div id="{{ 'comment-' . $loop->iteration }}" @class(['accordion-collapse collapse', 'show' => $loop->first]) data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p>{{ __($comment->comment) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($comments->hasPages())
                        {{ paginateLinks($comments) }}
                    @endif
                @else
                    @include('admin.partials.noData')
                @endif
            </div>
        </div>
        
        <!-- Reward Claims Section -->
        <div class="custom--card h-auto mt-4">
            <div class="card-header">
                <h3 class="title">@lang('Reward Claims')</h3>
            </div>
            <div class="card-body">
                @if(count($rewardClaims))
                    <div class="table-responsive">
                        <table class="table table-flush">
                            <thead>
                                <tr>
                                    <th>@lang('Funder Name')</th>
                                    <th>@lang('Reward')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rewardClaims as $claim)
                                    <tr>
                                        <td>
                                            @if($claim->user_id)
                                                <a href="{{ route('admin.user.details', $claim->user_id) }}">
                                                    {{ $claim->user ? $claim->user->fullname : $claim->full_name }}
                                                </a>
                                            @else
                                                {{ $claim->full_name }}
                                            @endif
                                            @if($claim->donor_type == ManageStatus::ANONYMOUS_DONOR)
                                                <span class="badge badge--warning">@lang('Anonymous')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($claim->reward)
                                                <strong>{{ __($claim->reward->title) }}</strong>
                                                <br>
                                                <small class="text-muted">{{ __($claim->reward->description) }}</small>
                                            @else
                                                <span class="text-muted">@lang('Reward Deleted')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text--success">{{ $setting->cur_sym . showAmount($claim->amount) }}</strong>
                                        </td>
                                        <td>
                                            {{ showDateTime($claim->created_at) }}
                                        </td>
                                        <td>
                                            @if($claim->status == ManageStatus::PAYMENT_SUCCESS)
                                                <span class="badge badge--success">@lang('Paid')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Pending')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($rewardClaims->hasPages())
                        {{ paginateLinks($rewardClaims) }}
                    @endif
                @else
                    @include('admin.partials.noData')
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="custom--card h-auto mb-4">
            <div class="card-header">
                <h3 class="title">@lang('Basic Information')</h3>
            </div>
            <div class="card-body">
                <table class="table table-flush">
                    <tbody>
                        <tr>
                            <td class="fw-semibold">@lang('Category'):</td>
                            <td>{{ __($campaign->category->name) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Author'):</td>
                            <td>
                                <a href="{{ route('admin.user.details', $campaign->user->id) }}">
                                    <small>@</small>{{ $campaign->user->username }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Start Date'):</td>
                            <td>{{ showDateTime($campaign->start_date) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('End Date'):</td>
                            <td>
                                <span class="text--warning">{{ showDateTime($campaign->end_date) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Approval Status'):</td>
                            <td>
                                @php echo $campaign->approvalStatusBadge @endphp
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Campaign Status'):</td>
                            <td>
                                @php echo $campaign->campaignStatusBadge @endphp
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Featured Status'):</td>
                            <td>
                                @php echo $campaign->featuredStatusBadge @endphp
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Goal Amount'):</td>
                            <td>{{ $setting->cur_sym . showAmount($campaign->goal_amount) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Raised Amount'):</td>
                            <td>
                                <span class="text--success">{{ $setting->cur_sym . showAmount($campaign->raised_amount) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Total Donor'):</td>
                            <td>{{ $totalDonor }}</td>
                        </tr>
                        <tr>
                            @php $percentage = donationPercentage($campaign->goal_amount, $campaign->raised_amount) @endphp

                            <td class="fw-semibold">@lang('Contribution Progress'):</td>
                            <td>
                                <div class="progress custom--progress" role="progressbar" aria-label="Basic example" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($campaign->payout_bank_id || $campaign->bank_account_number)
        <div class="custom--card h-auto mb-4">
            <div class="card-header">
                <h3 class="title">@lang('Payout Bank Details')</h3>
            </div>
            <div class="card-body">
                <table class="table table-flush">
                    <tbody>
                        @if($campaign->payoutBank)
                        <tr>
                            <td class="fw-semibold">@lang('Bank Name'):</td>
                            <td>{{ __($campaign->payoutBank->name) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">@lang('Bank Code'):</td>
                            <td>{{ $campaign->payoutBank->code }}</td>
                        </tr>
                        @endif
                        @if($campaign->bank_account_number)
                        <tr>
                            <td class="fw-semibold">@lang('Account Number/Email'):</td>
                            <td>
                                <span class="text--base">{{ $campaign->bank_account_number }}</span>
                            </td>
                        </tr>
                        @endif
                        @if(!$campaign->payout_bank_id && !$campaign->bank_account_number)
                        <tr>
                            <td colspan="2" class="text-center text-muted">@lang('No payout bank details provided')</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        <div class="custom--card h-auto mb-4">
            <div class="card-header">
                <h3 class="title">@lang('Relevant Images')</h3>
            </div>
            <div class="card-body">
                @if($campaign->gallery && count($campaign->gallery) > 0)
                    <div id="charityImageSlide" class="custom--carousel carousel slide">
                        <div class="carousel-inner">
                            @foreach($campaign->gallery as $image)
                                <div @class(['carousel-item', 'active' => $loop->first])>
                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $image, getFileSize('campaign')) }}" class="d-block w-100" alt="Image">
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="carousel-control-prev" data-bs-target="#charityImageSlide" data-bs-slide="prev">
                            <i class="ti ti-chevron-left"></i>
                        </button>
                        <button type="button" class="carousel-control-next" data-bs-target="#charityImageSlide" data-bs-slide="next">
                            <i class="ti ti-chevron-right"></i>
                        </button>
                    </div>
                @else
                    @include('admin.partials.noData')
                @endif
            </div>
        </div>

        @if($campaign->document)
            <div class="custom--card h-auto">
                <div class="card-header">
                    <h3 class="title">@lang('Relevant Document')</h3>
                </div>
                <div class="card-body">
                    <object class="campaign-details-doc" data="{{ asset(getFilePath('document') . '/' . $campaign->document) }}" type="application/pdf"></object>
                </div>
            </div>
        @endif
    </div>

    <x-decisionModal />
@endsection

@push('breadcrumb')
    <a href="{{ $backRoute }}" class="btn btn--sm btn--base">
        <i class="ti ti-circle-arrow-left"></i> @lang('Back')
    </a>

    @if(!$campaign->isExpired())
        <div class="custom--dropdown">
            <button type="button" class="btn btn--sm btn--icon btn--base" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-dots-vertical"></i>
            </button>

            @if($campaign->status == ManageStatus::CAMPAIGN_PENDING && admin_can('campaigns.approve'))
                <ul class="dropdown-menu">
                    <li>
                        <button type="button" class="dropdown-item text--success decisionBtn" data-question="@lang('Do you want to approve this campaign?')" data-action="{{ route('admin.campaigns.status.update', [$campaign->id, 'approve']) }}">
                            <span class="dropdown-icon"><i class="ti ti-circle-check"></i></span> @lang('Approve')
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item text--danger decisionBtn" data-question="@lang('Do you want to reject this campaign?')" data-action="{{ route('admin.campaigns.status.update', [$campaign->id, 'reject']) }}">
                            <span class="dropdown-icon"><i class="ti ti-circle-x"></i></span> @lang('Reject')
                        </button>
                    </li>
                </ul>
            @endif

            @if($campaign->status == ManageStatus::CAMPAIGN_APPROVED)
                <ul class="dropdown-menu">
                    @if(admin_can('campaigns.approve'))
                    <li>
                        <button type="button" class="dropdown-item text--warning decisionBtn" data-question="@lang('Do you want to unapprove this campaign? It will move to pending status.')" data-action="{{ route('admin.campaigns.status.update', [$campaign->id, 'unapprove']) }}">
                            <span class="dropdown-icon"><i class="ti ti-arrow-back"></i></span> @lang('Unapprove')
                        </button>
                    </li>
                    @endif
                    @if($campaign->featured)
                        <li>
                            <button type="button" class="dropdown-item text--warning decisionBtn" data-question="@lang('Do you want to unfeatured this campaign?')" data-action="{{ route('admin.campaigns.featured.update', $campaign->id) }}">
                                <span class="dropdown-icon"><i class="ti ti-ban"></i></span> @lang('Unfeatured')
                            </button>
                        </li>
                    @else
                        <li>
                            <button type="button" class="dropdown-item text--success decisionBtn" data-question="@lang('Do you want to featured this campaign?')" data-action="{{ route('admin.campaigns.featured.update', $campaign->id) }}">
                                <span class="dropdown-icon"><i class="ti ti-circle-check"></i></span> @lang('Featured')
                            </button>
                        </li>
                    @endif
                </ul>
            @endif
        </div>
    @endif
@endpush

@push('page-script-lib')
    <script src="{{ asset('assets/admin/js/page/pdfobject.js') }}"></script>
@endpush

@push('page-script')
    <script>
        (function ($) {
            "use strict"

            let pdfFIle = $('.campaign-details-doc').attr('data')
            PDFObject.embed(pdfFIle, '.campaign-details-doc')
        })(jQuery)
    </script>
@endpush
