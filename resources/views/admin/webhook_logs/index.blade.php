@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="custom--card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="title">
                @if(($gateway ?? '') === 'jazzcash')
                    @lang('JazzCash API Logs')
                @else
                    @lang('API Request Logs')
                @endif
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.webhook.logs.jazzcash') }}" class="btn btn--sm btn-outline--base">
                    @lang('JazzCash Only')
                </a>
                <a href="{{ route('admin.webhook.logs.export', request()->query()) }}" class="btn btn--sm btn-outline--base">
                    <i class="ti ti-download"></i> @lang('Export')
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.webhook.logs.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form--label">@lang('Endpoint / Type')</label>
                    <input type="text" name="type" class="form--control" value="{{ $type }}" placeholder="@lang('api, jazzcash, payment')">
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('Status')</label>
                    <select name="status" class="form--control">
                        <option value="">@lang('All')</option>
                        @foreach(['received', 'success', 'failed', 'error', 'pending', 'retrying'] as $option)
                            <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('Gateway')</label>
                    <input type="text" name="gateway" class="form--control" value="{{ $gateway }}" placeholder="@lang('jazzcash')">
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('From')</label>
                    <input type="date" name="date_from" class="form--control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('To')</label>
                    <input type="date" name="date_to" class="form--control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn--base w-100">@lang('Go')</button>
                </div>
            </form>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <span class="d-block text-muted">@lang('Total Requests')</span>
                        <strong>{{ $stats['total_requests'] ?? 0 }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <span class="d-block text-muted">@lang('Success')</span>
                        <strong>{{ $stats['successful_requests'] ?? 0 }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <span class="d-block text-muted">@lang('Failed')</span>
                        <strong>{{ $stats['failed_requests'] ?? 0 }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <span class="d-block text-muted">@lang('Errors')</span>
                        <strong>{{ $stats['error_requests'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>

            <h6 class="mb-3">@lang('Raw API Requests')</h6>
            <div class="table-responsive mb-4">
                <table class="table table-borderless table--striped table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Date')</th>
                            <th>@lang('Endpoint')</th>
                            <th>@lang('Method')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('IP')</th>
                            <th>@lang('Transaction')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataLogs as $log)
                            <tr>
                                <td>{{ showDateTime($log->created_at) }}</td>
                                <td><code>{{ $log->endpoint }}</code></td>
                                <td>{{ $log->method }}</td>
                                <td><span class="badge badge--info">{{ ucfirst($log->status ?? 'received') }}</span></td>
                                <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                                <td>{{ $log->transaction_id ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.webhook.logs.show', $log->id) }}" class="btn btn--sm btn-outline--base">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">@lang('No API request logs found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($dataLogs->hasPages())
                <div class="d-flex justify-content-center mb-4">
                    {{ $dataLogs->appends(request()->query())->links() }}
                </div>
            @endif

            <h6 class="mb-3">@lang('Webhook Processing Logs')</h6>
            <div class="table-responsive">
                <table class="table table-borderless table--striped table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Date')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Direction')</th>
                            <th>@lang('URL')</th>
                            <th>@lang('Method')</th>
                            <th>@lang('HTTP')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Time')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($webhookLogs as $log)
                            <tr>
                                <td>{{ showDateTime($log->created_at) }}</td>
                                <td><code>{{ $log->webhook_type }}</code></td>
                                <td><span class="badge badge--dark">{{ $log->payload['direction'] ?? '-' }}</span></td>
                                <td><small>{{ Str::limit($log->url ?? '-', 40) }}</small></td>
                                <td>{{ $log->method }}</td>
                                <td>{!! $log->http_status_badge !!}</td>
                                <td>{!! $log->status_badge !!}</td>
                                <td>{{ $log->formatted_execution_time }}</td>
                                <td>
                                    <a href="{{ route('admin.webhook.logs.show', ['id' => $log->id, 'type' => 'webhook_log']) }}" class="btn btn--sm btn-outline--base">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">@lang('No webhook processing logs found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($webhookLogs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $webhookLogs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
