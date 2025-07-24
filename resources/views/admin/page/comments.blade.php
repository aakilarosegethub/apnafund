@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Author')</th>
                    <th>@lang('Campaign')</th>
                    <th>@lang('Commenter') | @lang('Email')</th>
                    <th>@lang('User Type')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Comment Date')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comments as $comment)
                    <tr>
                        <td>
                            <div class="table-card-with-image">
                                <div class="table-card-with-image__img">
                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . @$comment->campaign->user->image, getFileSize('userProfile'), true) }}" alt="Image">
                                </div>
                                <div class="table-card-with-image__content">
                                    <p class="fw-semibold">{{ $comment->campaign->user->fullname }}</p>
                                    <p class="fw-semibold">
                                        <a href="{{ route('admin.user.details', $comment->campaign->user->id) }}"> <small>@</small>{{ $comment->campaign->user->username }}</a>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span title="{{ __($comment->campaign->name) }}">
                                <a href="{{ route('admin.campaigns.details', $comment->campaign->id) }}" target="_blank">
                                    {{ __(strLimit($comment->campaign->name, 30)) }}
                                </a>
                            </span>
                        </td>
                        <td>
                            <div>
                                @if($comment->user)
                                    <p>{{ $comment->user->fullname }}</p>
                                    <p>
                                        <a href="{{ route('admin.user.details', $comment->user->id) }}">
                                            {{ $comment->user->email }}
                                        </a>
                                    </p>
                                @else
                                    <p>{{ $comment->name }}</p>
                                    <p>{{ $comment->email }}</p>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php echo $comment->userTypeBadge @endphp
                        </td>
                        <td>
                            @if($comment->status == ManageStatus::CAMPAIGN_COMMENT_PENDING)
                                <span class="badge badge--warning">@lang('Pending')</span>
                            @else
                                <span class="badge badge--success">@lang('Approved')</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <p>{{ showDateTime($comment->created_at) }}</p>
                                <p class="text--base">{{ diffForHumans($comment->created_at) }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="#commentDetails" data-bs-toggle="offcanvas" class="btn btn--sm btn-outline--base commentViewBtn" data-comment="{{ $comment->comment }}">
                                    <i class="ti ti-eye"></i> @lang('View')
                                </a>
                                <div class="custom--dropdown">
                                    <button type="button" class="btn btn--icon btn--sm btn--base" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($comment->status == ManageStatus::CAMPAIGN_COMMENT_PENDING)
                                            <li>
                                                <button type="button" class="dropdown-item decisionBtn" data-question="@lang('Do you want to approve this comment?')" data-action="{{ route('admin.comments.approve', $comment->id) }}">
                                                    <span class="dropdown-icon"><i class="ti ti-circle-check text--success"></i></span> @lang('Approve')
                                                </button>
                                            </li>
                                        @endif

                                        <li>
                                            <button type="button" class="dropdown-item decisionBtn" data-question="@lang('Do you want to delete this comment?')" data-action="{{ route('admin.comments.delete', $comment->id) }}">
                                                <span class="dropdown-icon"><i class="ti ti-trash text--danger"></i></span> @lang('Delete')
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    @include('admin.partials.noData')
                @endforelse
            </tbody>
        </table>

        @if ($comments->hasPages())
            {{ paginateLinks($comments) }}
        @endif
    </div>

    <div class="col-12">
        <div class="custom--offcanvas offcanvas offcanvas-end" tabindex="-1" id="commentDetails" aria-labelledby="commentDetailsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="commentDetailsLabel">@lang('Comment')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="custom--card h-auto">
                    <div class="card-body">
                        <span class="user-comment"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-decisionModal />
@endsection

@push('breadcrumb')
    <x-searchForm placeholder="Name" />
@endpush

@push('page-script')
    <script>
        (function ($) {
            "use strict"

            $('.commentViewBtn').on('click', function() {
                let comment = $(this).data('comment')

                $('.user-comment').html(comment)
            })
        })(jQuery)
    </script>
@endpush
