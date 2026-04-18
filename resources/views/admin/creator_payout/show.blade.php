@extends('admin.layouts.master')

@section('master')
    @php
        $currency = getPlatformCurrencySymbol();
        $platformCurrencyCode = getPlatformCurrency();
        $campaign = $payout->campaign;
    @endphp
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <h3 class="title">
                    @lang('Creator Payout Details')
                    <span class="badge badge--base ms-1">{{ $platformCurrencyCode }}</span>
                </h3>
                <a href="{{ route('admin.creator-payouts.index') }}" class="btn btn--sm btn--base">
                    <i class="ti ti-arrow-left"></i> @lang('Back')
                </a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Campaign Summary')</h5>
                            </div>
                            <div class="card-body">
                                <p class="fw-semibold">{{ $campaign->name }} <span class="text--muted">#{{ $campaign->id }}</span></p>
                                <p>@lang('Goal'): {{ $currency }}{{ showAmount($campaign->goal_amount) }}</p>
                                <p>@lang('Total Raised'): {{ $currency }}{{ showAmount($payout->total_raised) }}</p>
                                <p>@lang('Payout Status'): {{ __(ucwords(str_replace('_', ' ', $payout->payout_status))) }}</p>
                                <p>@lang('Fulfillment Status'): {{ __(ucwords(str_replace('_', ' ', $payout->fulfillment_status))) }}</p>
                                <p>@lang('Success Marked'): {{ $payout->success_marked_at ? showDateTime($payout->success_marked_at) : __('N/A') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Creator Bank Details')</h5>
                            </div>
                            <div class="card-body">
                                <p>@lang('Bank'): {{ $campaign->payoutBank->name ?? __('N/A') }}</p>
                                <p>@lang('Account'): {{ $campaign->bank_account_number ?? __('N/A') }}</p>
                                <p>@lang('Email'): {{ $campaign->bank_account_email ?? __('N/A') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Fee & Withholding Breakdown')</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <p class="fw-semibold">@lang('Platform Fee')</p>
                                        <p>{{ $currency }}{{ showAmount($payout->platform_fee_amount) }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="fw-semibold">@lang('Marketing Fee')</p>
                                        <p>{{ $currency }}{{ showAmount($payout->marketing_fee_amount) }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="fw-semibold">@lang('Chargeback Withholding')</p>
                                        <p>{{ $currency }}{{ showAmount($payout->chargeback_withholding_amount) }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="fw-semibold">@lang('Fulfillment Withholding')</p>
                                        <p>{{ $currency }}{{ showAmount($payout->fulfillment_withholding_amount) }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="fw-semibold">@lang('Net Payable')</p>
                                        <p>{{ $currency }}{{ showAmount($payout->net_payable_amount) }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="fw-semibold">@lang('Withheld Balance')</p>
                                        <p>{{ $currency }}{{ showAmount($payout->remainingWithheldBalance()) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Payout Action Log')</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table--striped table-borderless table--responsive--xl">
                                    <thead>
                                        <tr>
                                            <th>@lang('Action')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Admin')</th>
                                            <th>@lang('Notes')</th>
                                            <th>@lang('Date')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payout->actions as $action)
                                            <tr>
                                                <td>{{ __(ucwords(str_replace('_', ' ', $action->action_type))) }}</td>
                                                <td>{{ $action->amount ? $currency . showAmount($action->amount) : __('N/A') }}</td>
                                                <td>{{ $action->admin->name ?? __('System') }}</td>
                                                <td>{{ $action->notes ?? __('-') }}</td>
                                                <td>{{ showDateTime($action->created_at) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">@lang('No actions recorded yet')</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
