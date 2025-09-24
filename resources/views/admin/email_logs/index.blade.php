@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">@lang('Email Logs')</h5>
        </div>
        <div class="card-body">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total_emails'] ?? 0 }}</h4>
                                    <p class="mb-0">@lang('Total Emails')</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-envelope fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['sent_emails'] ?? 0 }}</h4>
                                    <p class="mb-0">@lang('Sent')</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['failed_emails'] ?? 0 }}</h4>
                                    <p class="mb-0">@lang('Failed')</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['welcome_emails'] ?? 0 }}</h4>
                                    <p class="mb-0">@lang('Welcome Emails')</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-handshake fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" action="{{ route('admin.email-logs.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <select name="type" class="form-control">
                                <option value="">@lang('All Types')</option>
                                @foreach($emailTypes as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">@lang('All Status')</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="@lang('From Date')">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="@lang('To Date')">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">@lang('Filter')</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" action="{{ route('admin.email-logs.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="@lang('Search by email, subject, or sender')">
                        <button type="submit" class="btn btn-outline-primary">@lang('Search')</button>
                    </form>
                </div>
            </div>

            <!-- Email Logs Table -->
            <div class="table-responsive">
                <table class="table table-striped table-borderless table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('S.N.')</th>
                            <th>@lang('To Email')</th>
                            <th>@lang('To Name')</th>
                            <th>@lang('Subject')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Provider')</th>
                            <th>@lang('Sent At')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($emailLogs as $log)
                            <tr>
                                <td>{{ $emailLogs->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope me-2 text-muted"></i>
                                        {{ $log->to_email }}
                                    </div>
                                </td>
                                <td>{{ $log->to_name ?? '-' }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $log->subject }}">
                                        {{ $log->subject }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge--{{ $log->type_badge }}">
                                        {{ ucfirst($log->email_type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge--{{ $log->status_badge }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>{{ $log->provider ?? '-' }}</td>
                                <td>
                                    @if($log->sent_at)
                                        {{ showDateTime($log->sent_at) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Preview Button -->
                                        <button type="button" class="btn btn-sm btn-outline-primary preview-btn" 
                                                data-log-id="{{ $log->id }}" 
                                                title="@lang('Preview Email')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <!-- Resend Button (only for failed emails) -->
                                        @if($log->status === 'failed')
                                            <form action="{{ route('admin.email-logs.resend', $log->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                        title="@lang('Resend Email')"
                                                        onclick="return confirm('@lang('Are you sure you want to resend this email?')')">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.email-logs.destroy', $log->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="@lang('Delete Log')"
                                                    onclick="return confirm('@lang('Are you sure you want to delete this email log?')')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>@lang('No email logs found')</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($emailLogs->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $emailLogs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Email Preview Modal -->
<div class="modal fade" id="emailPreviewModal" tabindex="-1" aria-labelledby="emailPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailPreviewModalLabel">@lang('Email Preview')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="emailPreviewContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Email logs page loaded');
    
    // Email preview functionality
    document.querySelectorAll('.preview-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            console.log('Preview button clicked');
            const logId = this.getAttribute('data-log-id');
            console.log('Log ID:', logId);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('emailPreviewModal'));
            modal.show();
            
            // Load email content
            fetch('{{ route("admin.email-logs.preview", ":id") }}'.replace(':id', logId))
                .then(response => response.json())
                .then(data => {
                    console.log('Preview data:', data);
                    if (data.success) {
                        const emailData = data.data;
                        let content = `
                            <div class="email-preview">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>To:</strong> ${emailData.to_email} (${emailData.to_name || 'N/A'})
                                    </div>
                                    <div class="col-md-6">
                                        <strong>From:</strong> ${emailData.from_email || 'N/A'} (${emailData.from_name || 'N/A'})
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Subject:</strong> ${emailData.subject}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Type:</strong> <span class="badge badge--${emailData.email_type === 'welcome' ? 'success' : 'info'}">${emailData.email_type}</span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Status:</strong> <span class="badge badge--${emailData.status === 'sent' ? 'success' : 'danger'}">${emailData.status}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Provider:</strong> ${emailData.provider || 'N/A'}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Sent At:</strong> ${emailData.sent_at || 'N/A'}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Created At:</strong> ${emailData.created_at}
                                    </div>
                                </div>
                                ${emailData.error_message ? `<div class="alert alert-danger"><strong>Error:</strong> ${emailData.error_message}</div>` : ''}
                                <hr>
                                <div class="email-body">
                                    <h6>Email Body:</h6>
                                    <div class="border p-3" style="max-height: 500px; overflow-y: auto;">
                                        ${emailData.body}
                                    </div>
                                </div>
                            </div>
                        `;
                        document.getElementById('emailPreviewContent').innerHTML = content;
                    } else {
                        document.getElementById('emailPreviewContent').innerHTML = '<div class="alert alert-danger">Failed to load email preview</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading preview:', error);
                    document.getElementById('emailPreviewContent').innerHTML = '<div class="alert alert-danger">Error loading email preview</div>';
                });
        });
    });
});
</script>
@endpush
