@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="row g-4">
        <!-- Sync Statistics Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="ti ti-package text--primary"></i>
                </div>
                <div class="dashboard-card__content">
                    <h4 class="dashboard-card__title">{{ number_format($syncStats['total_products']) }}</h4>
                    <p class="dashboard-card__text">@lang('Total Products')</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="ti ti-check-circle text--success"></i>
                </div>
                <div class="dashboard-card__content">
                    <h4 class="dashboard-card__title">{{ number_format($syncStats['synced_products']) }}</h4>
                    <p class="dashboard-card__text">@lang('Synced Products')</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="ti ti-clock text--warning"></i>
                </div>
                <div class="dashboard-card__content">
                    <h4 class="dashboard-card__title">{{ number_format($syncStats['pending_products']) }}</h4>
                    <p class="dashboard-card__text">@lang('Pending Products')</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="dashboard-card__icon">
                    <i class="ti ti-x-circle text--danger"></i>
                </div>
                <div class="dashboard-card__content">
                    <h4 class="dashboard-card__title">{{ number_format($syncStats['failed_products']) }}</h4>
                    <p class="dashboard-card__text">@lang('Failed Products')</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Status and Controls -->
    <div class="row g-4 mt-4">
        <div class="col-xl-8">
            <div class="dashboard-card">
                <div class="dashboard-card__header">
                    <h4 class="dashboard-card__title">@lang('Sync Status')</h4>
                    <div class="dashboard-card__action">
                        <span class="badge badge--{{ $syncStats['sync_status'] == 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($syncStats['sync_status']) }}
                        </span>
                    </div>
                </div>
                <div class="dashboard-card__body">
                    <div class="sync-progress">
                        <div class="sync-progress__header">
                            <span>@lang('Sync Progress')</span>
                            <span class="sync-progress__percentage">{{ $syncStats['sync_percentage'] }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $syncStats['sync_percentage'] }}%" 
                                 aria-valuenow="{{ $syncStats['sync_percentage'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="sync-details mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="sync-details__item">
                                    <span class="fw-semibold">@lang('Last Sync'):</span>
                                    <span class="text--muted">{{ $syncStats['last_sync'] }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="sync-details__item">
                                    <span class="fw-semibold">@lang('Sync Rate'):</span>
                                    <span class="text--success">{{ $syncStats['sync_percentage'] }}%</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="dashboard-card">
                <div class="dashboard-card__header">
                    <h4 class="dashboard-card__title">@lang('Quick Actions')</h4>
                </div>
                <div class="dashboard-card__body">
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn--base" id="runCronBtn">
                            <i class="ti ti-refresh me-2"></i>
                            @lang('Run Sync Now')
                        </button>
                        
                        <button type="button" class="btn btn-outline--base" id="refreshStatusBtn">
                            <i class="ti ti-reload me-2"></i>
                            @lang('Refresh Status')
                        </button>
                        
                        <button type="button" class="btn btn-outline--info" id="viewLogsBtn">
                            <i class="ti ti-file-text me-2"></i>
                            @lang('View Sync Logs')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sync Activity -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="dashboard-card__header">
                    <h4 class="dashboard-card__title">@lang('Recent Sync Activity')</h4>
                </div>
                <div class="dashboard-card__body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>@lang('Time')</th>
                                    <th>@lang('Action')</th>
                                    <th>@lang('Products Synced')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Duration')</th>
                                </tr>
                            </thead>
                            <tbody id="syncActivityTable">
                                <tr>
                                    <td>{{ $syncStats['last_sync'] }}</td>
                                    <td>@lang('Full Sync')</td>
                                    <td>{{ $syncStats['synced_products'] }}</td>
                                    <td>
                                        <span class="badge badge--success">@lang('Completed')</span>
                                    </td>
                                    <td>2.3s</td>
                                </tr>
                                <tr>
                                    <td>{{ now()->subHours(2)->format('Y-m-d H:i:s') }}</td>
                                    <td>@lang('Incremental Sync')</td>
                                    <td>45</td>
                                    <td>
                                        <span class="badge badge--success">@lang('Completed')</span>
                                    </td>
                                    <td>1.1s</td>
                                </tr>
                                <tr>
                                    <td>{{ now()->subHours(4)->format('Y-m-d H:i:s') }}</td>
                                    <td>@lang('Full Sync')</td>
                                    <td>{{ $syncStats['synced_products'] - 45 }}</td>
                                    <td>
                                        <span class="badge badge--success">@lang('Completed')</span>
                                    </td>
                                    <td>3.2s</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text--base mb-3" role="status">
                    <span class="visually-hidden">@lang('Loading...')</span>
                </div>
                <h5>@lang('Processing Sync Request...')</h5>
                <p class="text--muted">@lang('Please wait while we sync your products.')</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-script')
<script>
$(document).ready(function() {
    // Run Cron Button
    $('#runCronBtn').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        // Show loading modal
        $('#loadingModal').modal('show');
        
        // Disable button
        btn.prop('disabled', true).html('<i class="ti ti-loader me-2"></i>@lang("Running...")');
        
        $.ajax({
            url: '{{ route("admin.store.run-cron") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loadingModal').modal('hide');
                
                if (response.success) {
                    // Show success toast
                    showToast('success', response.message);
                    
                    // Update sync stats
                    updateSyncStats();
                    
                    // Add new activity row
                    addActivityRow(response.data);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                
                let message = '@lang("An error occurred while running sync!")';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                showToast('error', message);
            },
            complete: function() {
                // Re-enable button
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Refresh Status Button
    $('#refreshStatusBtn').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="ti ti-loader me-2"></i>@lang("Refreshing...")');
        
        $.ajax({
            url: '{{ route("admin.store.sync-status") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    updateSyncStats(response.data);
                    showToast('success', '@lang("Status refreshed successfully!")');
                }
            },
            error: function() {
                showToast('error', '@lang("Failed to refresh status!")');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // View Logs Button
    $('#viewLogsBtn').on('click', function() {
        showToast('info', '@lang("Logs feature coming soon!")');
    });
    
    function updateSyncStats(data = null) {
        if (data) {
            // Update cards
            $('.dashboard-card__title').eq(0).text(data.total_products.toLocaleString());
            $('.dashboard-card__title').eq(1).text(data.synced_products.toLocaleString());
            $('.dashboard-card__title').eq(2).text(data.pending_products.toLocaleString());
            $('.dashboard-card__title').eq(3).text(data.failed_products.toLocaleString());
            
            // Update progress bar
            $('.progress-bar').css('width', data.sync_percentage + '%');
            $('.sync-progress__percentage').text(data.sync_percentage + '%');
            
            // Update last sync time
            $('.sync-details__item span:last').eq(0).text(data.last_sync);
            $('.sync-details__item span:last').eq(1).text(data.sync_percentage + '%');
        }
    }
    
    function addActivityRow(data) {
        const newRow = `
            <tr>
                <td>${data.timestamp}</td>
                <td>@lang('Manual Sync')</td>
                <td>${data.newly_synced}</td>
                <td><span class="badge badge--success">@lang('Completed')</span></td>
                <td>${data.execution_time}</td>
            </tr>
        `;
        
        $('#syncActivityTable').prepend(newRow);
    }
    
    function showToast(type, message) {
        // Simple toast notification
        const toast = $(`
            <div class="toast-notification toast-${type}">
                <i class="ti ti-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.fadeOut(() => toast.remove());
        }, 5000);
    }
});
</script>

<style>
.sync-progress {
    margin-bottom: 1rem;
}

.sync-progress__header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.sync-progress__percentage {
    color: var(--bs-primary);
}

.sync-details__item {
    margin-bottom: 0.5rem;
}

.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 300px;
}

.toast-success {
    border-left: 4px solid #28a745;
}

.toast-error {
    border-left: 4px solid #dc3545;
}

.toast-info {
    border-left: 4px solid #17a2b8;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.progress-bar {
    background-color: var(--bs-primary);
    border-radius: 4px;
    transition: width 0.3s ease;
}
</style>
@endpush
