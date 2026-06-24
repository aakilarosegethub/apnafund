@extends('admin.layouts.master')

@section('master')
@php
    $curlCommand = null;
    if ($type === 'webhook_log' && is_array($log->payload ?? null)) {
        $curlCommand = $log->payload['curl_command'] ?? null;
    } elseif (is_array($log->request_data ?? null)) {
        $curlCommand = $log->request_data['curl_command'] ?? null;
    }
@endphp
<div class="col-12">
    <div class="custom--card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="title">@lang('API Request Log Details')</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.webhook.logs.index', ['gateway' => 'jazzcash', 'type' => 'jazzcash']) }}" class="btn btn--sm btn-outline--base">
                    @lang('JazzCash Logs')
                </a>
                <a href="{{ route('admin.webhook.logs.index') }}" class="btn btn--sm btn-outline--base">
                    <i class="ti ti-arrow-left"></i> @lang('Back')
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($type === 'webhook_log')
                <div class="table-responsive mb-4">
                    <table class="table table-borderless table--striped">
                        <tr><th width="180">@lang('Type')</th><td><code>{{ $log->webhook_type }}</code></td></tr>
                        <tr><th>@lang('Gateway')</th><td><code>{{ $log->payload['gateway'] ?? '-' }}</code></td></tr>
                        <tr><th>@lang('Flow')</th><td><code>{{ $log->payload['flow'] ?? '-' }}</code></td></tr>
                        <tr><th>@lang('Direction')</th><td><span class="badge badge--info">{{ $log->payload['direction'] ?? '-' }}</span></td></tr>
                        <tr><th>@lang('URL')</th><td><small>{{ $log->url ?? '-' }}</small></td></tr>
                        <tr><th>@lang('Method')</th><td>{{ $log->method }}</td></tr>
                        <tr><th>@lang('Transaction')</th><td><code>{{ $log->payload['transaction_id'] ?? '-' }}</code></td></tr>
                        <tr><th>@lang('Deposit ID')</th><td>{{ $log->payload['deposit_id'] ?? '-' }}</td></tr>
                        <tr><th>@lang('Status')</th><td>{!! $log->status_badge !!}</td></tr>
                        <tr><th>@lang('HTTP Status')</th><td>{!! $log->http_status_badge !!}</td></tr>
                        <tr><th>@lang('Execution Time')</th><td>{{ $log->formatted_execution_time }}</td></tr>
                        <tr><th>@lang('User')</th><td>{{ $log->user?->username ?? '-' }}</td></tr>
                        <tr><th>@lang('Campaign')</th><td>{{ $log->campaign?->name ?? '-' }}</td></tr>
                        <tr><th>@lang('Error')</th><td>{{ $log->error_message ?? '-' }}</td></tr>
                        <tr><th>@lang('Created')</th><td>{{ showDateTime($log->created_at) }}</td></tr>
                    </table>
                </div>

                @if($curlCommand)
                    <h6>@lang('cURL Command')</h6>
                    <pre class="bg-dark text-light p-3 rounded overflow-auto mb-0" id="curlCommand">{{ $curlCommand }}</pre>
                    <button type="button" class="btn btn--sm btn--base mt-2" onclick="navigator.clipboard.writeText(document.getElementById('curlCommand').innerText)">
                        @lang('Copy cURL')
                    </button>
                @endif

                <h6 class="mt-4">@lang('Headers')</h6>
                <pre class="bg-light p-3 rounded overflow-auto">{{ json_encode($log->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                <h6 class="mt-4">@lang('Request Payload')</h6>
                <pre class="bg-light p-3 rounded overflow-auto">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                @if(!empty($log->payload['raw_input']))
                    <h6 class="mt-4">@lang('Raw Input')</h6>
                    <pre class="bg-light p-3 rounded overflow-auto">{{ $log->payload['raw_input'] }}</pre>
                @endif

                <h6 class="mt-4">@lang('Response')</h6>
                <pre class="bg-light p-3 rounded overflow-auto">{{ is_array($log->response_body) ? json_encode($log->response_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ($log->response_body ?? '-') }}</pre>
            @else
                <div class="table-responsive mb-4">
                    <table class="table table-borderless table--striped">
                        <tr><th width="180">@lang('Endpoint')</th><td><code>{{ $log->endpoint }}</code></td></tr>
                        <tr><th>@lang('Method')</th><td>{{ $log->method }}</td></tr>
                        <tr><th>@lang('Status')</th><td><span class="badge badge--info">{{ ucfirst($log->status ?? 'received') }}</span></td></tr>
                        <tr><th>@lang('IP')</th><td><code>{{ $log->ip_address ?? '-' }}</code></td></tr>
                        <tr><th>@lang('User Agent')</th><td><small>{{ $log->user_agent ?? '-' }}</small></td></tr>
                        <tr><th>@lang('Transaction')</th><td>{{ $log->transaction_id ?? '-' }}</td></tr>
                        <tr><th>@lang('Created')</th><td>{{ showDateTime($log->created_at) }}</td></tr>
                    </table>
                </div>

                @if($curlCommand)
                    <h6>@lang('cURL Command')</h6>
                    <pre class="bg-dark text-light p-3 rounded overflow-auto">{{ $curlCommand }}</pre>
                @endif

                <h6 class="mt-4">@lang('Headers')</h6>
                <pre class="bg-light p-3 rounded overflow-auto">{{ json_encode($log->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                <h6 class="mt-4">@lang('Request Data')</h6>
                <pre class="bg-light p-3 rounded overflow-auto">{{ json_encode($log->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                <h6 class="mt-4">@lang('Raw Input')</h6>
                <pre class="bg-light p-3 rounded overflow-auto">{{ $log->raw_input ?: '-' }}</pre>

                <h6 class="mt-4">@lang('Response')</h6>
                <pre class="bg-light p-3 rounded overflow-auto">{{ $log->response ?: '-' }}</pre>
            @endif
        </div>
    </div>
</div>
@endsection
