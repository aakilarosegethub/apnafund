@extends('admin.layouts.master')

@php
    $pageTitle = 'Activity Logs';
@endphp

@section('master')
<div class="col-12">
    <div class="custom--card">
        <div class="card-header">
            <h5 class="title">@lang('Admin Activity Logs')</h5>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3 mb-4">
                <div class="col-md-2">
                    <label class="form--label">@lang('Action')</label>
                    <select name="action_type" class="form--control">
                        <option value="">@lang('All')</option>
                        @foreach($actionTypes as $type)
                            <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('Module')</label>
                    <select name="module_name" class="form--control">
                        <option value="">@lang('All')</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ request('module_name') == $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('From Date')</label>
                    <input type="date" name="from_date" class="form--control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('To Date')</label>
                    <input type="date" name="to_date" class="form--control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn--base">@lang('Filter')</button>
                </div>
            </form>

            <!-- Logs Table -->
            <div class="table-responsive">
                <table class="table table-borderless table--striped table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Date')</th>
                            <th>@lang('Admin')</th>
                            <th>@lang('Action')</th>
                            <th>@lang('Module')</th>
                            <th>@lang('Record')</th>
                            <th>@lang('IP')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ showDateTime($log->created_at) }}</td>
                                <td>{{ $log->admin?->name ?? __('System') }}</td>
                                <td><span class="badge bg--{{ $log->action_type === 'login' || $log->action_type === 'created' ? 'success' : ($log->action_type === 'unauthorized' || $log->action_type === 'failed_login' ? 'danger' : 'info') }}">{{ ucfirst(str_replace('_', ' ', $log->action_type)) }}</span></td>
                                <td>{{ $log->module_name ?? '-' }}</td>
                                <td>{{ $log->record_id ?? '-' }}</td>
                                <td><code>{{ $log->ip ?? '-' }}</code></td>
                                <td>
                                    <button type="button" class="btn btn--sm btn-outline--base view-log-btn" data-id="{{ $log->id }}">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">@lang('No activity logs found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Log Detail Modal -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Log Details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.querySelectorAll('.view-log-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const modal = new bootstrap.Modal(document.getElementById('logDetailModal'));
        modal.show();
        document.getElementById('logDetailContent').innerHTML = '<div class="text-center py-4"><div class="spinner-border"></div></div>';
        fetch('{{ route("admin.activity-logs.show", ":id") }}'.replace(':id', id))
            .then(r => r.json())
            .then(data => {
                const log = data.log;
                let html = `
                    <table class="table table-sm">
                        <tr><th width="140">@lang('Admin')</th><td>${data.admin_name}</td></tr>
                        <tr><th>@lang('Action')</th><td>${log.action_type}</td></tr>
                        <tr><th>@lang('Module')</th><td>${log.module_name || '-'}</td></tr>
                        <tr><th>@lang('Record ID')</th><td>${log.record_id || '-'}</td></tr>
                        <tr><th>@lang('IP')</th><td><code>${log.ip || '-'}</code></td></tr>
                        <tr><th>@lang('URL')</th><td><small>${log.url || '-'}</small></td></tr>
                        <tr><th>@lang('Time')</th><td>${log.created_at}</td></tr>
                    </table>
                `;
                if (log.description) {
                    html += `<p><strong>@lang('Description'):</strong> ${log.description}</p>`;
                }
                if (log.old_data && Object.keys(log.old_data).length) {
                    html += `<h6 class="mt-3">@lang('Old Data')</h6><pre class="bg-light p-2 rounded small overflow-auto" style="max-height:200px">${JSON.stringify(log.old_data, null, 2)}</pre>`;
                }
                if (log.new_data && Object.keys(log.new_data).length) {
                    html += `<h6 class="mt-3">@lang('New Data')</h6><pre class="bg-light p-2 rounded small overflow-auto" style="max-height:200px">${JSON.stringify(log.new_data, null, 2)}</pre>`;
                }
                document.getElementById('logDetailContent').innerHTML = html || '<p class="text-muted">@lang('No additional details')</p>';
            })
            .catch(() => {
                document.getElementById('logDetailContent').innerHTML = '<div class="alert alert-danger">@lang('Failed to load log details')</div>';
            });
    });
});
</script>
@endpush
