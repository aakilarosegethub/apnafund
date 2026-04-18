@extends('admin.layouts.master')

@section('master')
    @php
        $currency = getPlatformCurrencySymbol();
        $platformCurrencyCode = getPlatformCurrency();
    @endphp
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <h3 class="title mb-0">
                        @lang('Creator Payout Management')
                        <span class="badge badge--base ms-1">{{ $platformCurrencyCode }}</span>
                    </h3>
                    <form method="GET" action="{{ route('admin.creator-payouts.index') }}" class="d-flex align-items-center gap-2">
                        <select name="scope" class="form--control form-select form-select-sm" onchange="this.form.submit()">
                            <option value="successful" @selected(($scope ?? 'successful') === 'successful')>@lang('Successful Only')</option>
                            <option value="all" @selected(($scope ?? 'successful') === 'all')>@lang('All Campaigns')</option>
                        </select>
                    </form>
                </div>
                <a href="{{ route('admin.creator-payout.settings.edit') }}" class="btn btn--sm btn--base">
                    <i class="ti ti-settings"></i> @lang('Fee Settings')
                </a>
            </div>
            <div class="card-body">
                <table class="table table--striped table-borderless table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Campaign')</th>
                            <th>@lang('Goal / Raised')</th>
                            <th>@lang('Deductions')</th>
                            <th>@lang('Net Payable')</th>
                            <th>@lang('Withheld')</th>
                            <th>@lang('Creator Bank')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($campaigns as $campaign)
                            @php $payout = $campaign->creatorPayout; @endphp
                            <tr>
                                <td>
                                    <div>
                                        <a href="{{ route('admin.campaigns.details', $campaign->id) }}" class="fw-semibold text--base">
                                            {{ __($campaign->name) }}
                                        </a>
                                        <p class="text--muted">#{{ $campaign->id }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p class="fw-semibold">{{ $currency }}{{ showAmount($campaign->goal_amount) }}</p>
                                        <p>{{ $currency }}{{ showAmount($payout?->total_raised ?? 0) }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p>@lang('Platform'): {{ $currency }}{{ showAmount($payout?->platform_fee_amount ?? 0) }}</p>
                                        <p>@lang('Marketing'): {{ $currency }}{{ showAmount($payout?->marketing_fee_amount ?? 0) }}</p>
                                        <p>@lang('Chargeback'): {{ $currency }}{{ showAmount($payout?->chargeback_withholding_amount ?? 0) }}</p>
                                        <p>@lang('Fulfillment'): {{ $currency }}{{ showAmount($payout?->fulfillment_withholding_amount ?? 0) }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p class="fw-semibold">{{ $currency }}{{ showAmount($payout?->net_payable_amount ?? 0) }}</p>
                                        <p class="text--muted">
                                            @lang('Available'): {{ $currency }}{{ showAmount($payout?->availableForPayout() ?? 0) }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p>@lang('Held'): {{ $currency }}{{ showAmount($payout?->withheld_total_amount ?? 0) }}</p>
                                        <p>@lang('Released'): {{ $currency }}{{ showAmount($payout?->released_withheld_amount ?? 0) }}</p>
                                        <p>@lang('Remaining'): {{ $currency }}{{ showAmount($payout?->remainingWithheldBalance() ?? 0) }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <p class="fw-semibold mb-1">{{ $campaign->payoutBank->name ?? __('N/A') }}</p>
                                            <p class="text--muted small mb-0">{{ $campaign->bank_account_number ?? __('N/A') }}</p>
                                        </div>
                                        @if($campaign->payoutBank || $campaign->bank_account_number)
                                        <button type="button" class="btn btn--sm btn--outline-base p-1" title="@lang('View bank details')"
                                                data-bs-toggle="modal" data-bs-target="#bankDetailsModal{{ $campaign->id }}">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        @endif
                                    </div>
                                    {{-- Bank details modal --}}
                                    @if($campaign->payoutBank || $campaign->bank_account_number)
                                    <div class="modal fade" id="bankDetailsModal{{ $campaign->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">@lang('Creator Bank Details')</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="list-group list-group-flush">
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span class="text--muted">@lang('Bank')</span>
                                                            <span class="fw-semibold">{{ $campaign->payoutBank->name ?? __('N/A') }}</span>
                                                        </div>
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span class="text--muted">@lang('Sort / Code')</span>
                                                            <span class="d-flex align-items-center gap-1">
                                                                <span id="bankCode{{ $campaign->id }}">{{ $campaign->payoutBank->code ?? __('N/A') }}</span>
                                                                <button type="button" class="btn btn--sm btn--base copy-btn" data-copy-target="bankCode{{ $campaign->id }}" title="@lang('Copy')">
                                                                    <i class="ti ti-copy"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span class="text--muted">@lang('Account Number')</span>
                                                            <span class="d-flex align-items-center gap-1">
                                                                <span id="bankAccount{{ $campaign->id }}">{{ $campaign->bank_account_number ?? __('N/A') }}</span>
                                                                <button type="button" class="btn btn--sm btn--base copy-btn" data-copy-target="bankAccount{{ $campaign->id }}" title="@lang('Copy')">
                                                                    <i class="ti ti-copy"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        @if($campaign->bank_account_email)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span class="text--muted">@lang('Email')</span>
                                                            <span class="d-flex align-items-center gap-1">
                                                                <span id="bankEmail{{ $campaign->id }}">{{ $campaign->bank_account_email }}</span>
                                                                <button type="button" class="btn btn--sm btn--base copy-btn" data-copy-target="bankEmail{{ $campaign->id }}" title="@lang('Copy')">
                                                                    <i class="ti ti-copy"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        @if($payout)
                                            <span class="badge badge--info">{{ __(ucwords(str_replace('_', ' ', $payout->payout_status))) }}</span>
                                            <span class="badge badge--warning">{{ __(ucwords(str_replace('_', ' ', $payout->fulfillment_status))) }}</span>
                                        @else
                                            <span class="badge badge--secondary">@lang('Not Successful')</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($payout)
                                        <div class="d-flex flex-column gap-2">
                                            <form action="{{ route('admin.creator-payouts.partial', $payout->id) }}" method="POST">
                                                @csrf
                                                <div class="input-group input-group-sm">
                                                    <input type="text" name="amount" class="form-control" placeholder="@lang('Partial amount')" required>
                                                    <button type="submit" class="btn btn--sm btn--base">@lang('Partial')</button>
                                                </div>
                                                <input type="text" name="notes" class="form--control mt-2" placeholder="@lang('Notes (optional)')">
                                            </form>
                                            <form action="{{ route('admin.creator-payouts.full', $payout->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="notes" value="">
                                                <button type="submit" class="btn btn--sm btn--success w-100">@lang('Full Payout')</button>
                                            </form>
                                            <form action="{{ route('admin.creator-payouts.fulfillment.complete', $payout->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn--sm btn--warning w-100" {{ $payout->fulfillment_status === 'completed' ? 'disabled' : '' }}>
                                                    @lang('Fulfillment Completed')
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.creator-payouts.show', $payout->id) }}" class="btn btn--sm btn--outline-base w-100">
                                                @lang('View Log')
                                            </a>
                                        </div>
                                    @else
                                        <span class="text--muted">@lang('Awaiting success')</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">@lang('No completed campaigns found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($campaigns->hasPages())
                    <div class="pagination-wrapper">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.copy-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.getAttribute('data-copy-target');
                var el = document.getElementById(id);
                if (!el) return;
                var text = (el.textContent || el.innerText || '').trim();
                if (text === '' || text === '{{ __("N/A") }}') return;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function() {
                        var icon = btn.querySelector('i');
                        if (icon) { icon.className = 'ti ti-check'; setTimeout(function() { icon.className = 'ti ti-copy'; }, 1500); }
                    });
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed'; ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); } catch (e) {}
                    document.body.removeChild(ta);
                    var icon = btn.querySelector('i');
                    if (icon) { icon.className = 'ti ti-check'; setTimeout(function() { icon.className = 'ti ti-copy'; }, 1500); }
                }
            });
        });
    </script>
@endsection
