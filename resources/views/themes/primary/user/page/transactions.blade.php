@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row gy-3 align-items-end mb-4">
                            <div class="col-xl-6 col-lg-5 col-sm-6 col-xsm-6">
                                <label class="form--label">@lang('Transaction Number')</label>
                                <input type="text" class="form--control" name="search" value="{{ request('search') }}">
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-6 col-xsm-6">
                                <label class="form--label">@lang('Remark')</label>
                                <select class="form--control form-select" name="remark">
                                    <option value="">@lang('Any')</option>

                                    @foreach ($remarks as $remark)
                                        <option value="{{ $remark->remark }}" @selected(request('remark') == $remark->remark)>
                                            {{ transactionRemarkDisplay($remark->remark) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-3">
                                <button type="submit" class="btn btn--base w-100">@lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped table-borderless table--responsive--xl align-middle">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Trx')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Details')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>
                                        {{ @$transactions->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        <span class="badge badge--primary">{{ @$transaction->trx }}</span>
                                    </td>
                                    <td>
                                        <span>
                                            <span class="d-block">{{ showDateTime(@$transaction->created_at) }}</span>
                                            <span class="d-block">{{ diffForHumans(@$transaction->created_at) }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="@if ($transaction->trx_type == '+') text--success @else text--danger @endif">
                                            {{ formatPlatformForDisplay(@$transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>{{ formatPlatformForDisplay(@$transaction->post_balance, 2) }}</td>
                                    <td>
                                        <div class="text-wrap" style="max-width: 320px;">
                                            {{ __(contributionLabelDisplay((string) (@$transaction->details ?? ''))) }}
                                        </div>
                                        @if(@$transaction->remark)
                                            <small class="text-muted d-block mt-1">{{ transactionRemarkDisplay((string) $transaction->remark) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(@$transaction->deposit && $transaction->deposit->needsProofUpload())
                                            <button type="button" class="btn btn--sm btn--base openManualProofModalBtn" data-trx="{{ $transaction->trx }}">
                                                <i class="ti ti-upload"></i> @lang('Submit proof')
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        {{ __($emptyMessage) }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($transactions->hasPages())
                        {{ $transactions->links() }}
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
