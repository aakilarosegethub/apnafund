@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body">
                    <p class="text-muted small mb-4">@lang('Your contributions and manual payment proof status.')</p>
                    <table class="table table-striped table-borderless table--responsive--xl align-middle">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Trx')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Campaign')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Gateway')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Proof uploaded')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deposits as $deposit)
                                <tr>
                                    <td>{{ $deposits->firstItem() + $loop->index }}</td>
                                    <td><span class="badge badge--primary">{{ $deposit->trx }}</span></td>
                                    <td>
                                        <span class="d-block">{{ showDateTime($deposit->created_at) }}</span>
                                        <span class="d-block text-muted small">{{ diffForHumans($deposit->created_at) }}</span>
                                    </td>
                                    <td>
                                        @if($deposit->campaign)
                                            <a href="{{ route('campaign.show', $deposit->campaign->slug) }}">{{ strLimit($deposit->campaign->name, 40) }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ formatPlatformForDisplay($deposit->amount, 2) }}</td>
                                    <td>
                                        @if($deposit->isManualGateway())
                                            <span class="badge badge--warning">@lang('Manual')</span>
                                        @else
                                            <span class="badge badge--success">@lang('Automated')</span>
                                        @endif
                                        <div class="small text-muted mt-1">{{ $deposit->gateway->name ?? '—' }}</div>
                                    </td>
                                    <td>
                                        @if ($deposit->status == ManageStatus::PAYMENT_PENDING || $deposit->status == ManageStatus::PAYMENT_INITIATE)
                                            <span class="badge badge--warning">@lang('Pending')</span>
                                        @elseif ($deposit->status == ManageStatus::PAYMENT_SUCCESS)
                                            <span class="badge badge--success">@lang('Succeeded')</span>
                                        @elseif ($deposit->status == ManageStatus::PAYMENT_CANCEL)
                                            <span class="badge badge--danger">@lang('Cancelled')</span>
                                        @else
                                            <span class="badge badge--secondary">@lang('Initiated')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deposit->proofSubmittedFlag() === 1)
                                            <span class="badge badge--success">@lang('Yes')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('No')</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deposit->needsProofUpload())
                                            <button type="button" class="btn btn--sm btn--base openManualProofModalBtn" data-trx="{{ $deposit->trx }}">
                                                <i class="ti ti-upload"></i> @lang('Submit proof')
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="9">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($deposits->hasPages())
                        {{ $deposits->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.user-manual-proof-modal')
@endsection

@push('page-script')
    <script>
        (function ($) {
            'use strict';
            $(document).on('click', '.openManualProofModalBtn', function () {
                var trx = $(this).data('trx') || '';
                $('#manualProofModalTrx').val(trx);
                $('#manualProofModalTrxLabel').text(trx || '—');
                $('#manualProofModalFile').val('');
                $('#manualProofModalNote').val('');
                var el = document.getElementById('manualProofUploadModal');
                if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(el).show();
                } else if (el && $.fn.modal) {
                    $(el).modal('show');
                }
            });
        })(jQuery);
    </script>
@endpush
