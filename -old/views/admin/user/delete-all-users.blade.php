@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title text-danger">
                    <i class="ti ti-alert-triangle"></i> Delete All Users
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h5><i class="ti ti-alert-circle"></i> WARNING: DESTRUCTIVE OPERATION</h5>
                    <p class="mb-2">This action will <strong>PERMANENTLY DELETE ALL USERS</strong> from the system.</p>
                    <ul class="mb-0">
                        <li>All user accounts will be removed</li>
                        <li>All user data will be lost</li>
                        <li>All user campaigns will be deleted</li>
                        <li>All user transactions will be removed</li>
                        <li><strong>This action cannot be undone!</strong></li>
                    </ul>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h4 class="text-warning">{{ $userCount }}</h4>
                                <p class="mb-0">Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h4 class="text-info">{{ now()->format('Y-m-d H:i:s') }}</h4>
                                <p class="mb-0">Current Time</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($userCount > 0)
                    <form action="{{ route('admin.user.delete.all.users.post') }}" method="POST" id="deleteAllUsersForm">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6><i class="ti ti-info-circle"></i> Before proceeding:</h6>
                                    <ul class="mb-0">
                                        <li>Make sure you have a database backup</li>
                                        <li>Consider exporting user data if needed</li>
                                        <li>Inform your team about this action</li>
                                    </ul>
                                </div>
                            </div>
                            
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="understandRisk" checked>
                                    <label class="form-check-label text-danger" for="understandRisk">
                                        <strong>I understand that this action will permanently delete all users and cannot be undone</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn--danger" id="deleteAllBtn">
                                        <i class="ti ti-trash"></i> Delete All {{ $userCount }} Users
                                    </button>
                                    <a href="{{ route('admin.user.index') }}" class="btn btn--secondary">
                                        <i class="ti ti-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="text-center">
                        <h5 class="text-success">No users found in the system</h5>
                        <p class="text-muted">There are no users to delete.</p>
                        <a href="{{ route('admin.user.index') }}" class="btn btn--base">
                            <i class="ti ti-arrow-left"></i> Back to Users
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('deleteAllUsersForm');
    const deleteBtn = document.getElementById('deleteAllBtn');
    
    // Button is always enabled now
    deleteBtn.disabled = false;
    
    // Form submission with additional confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const userCount = {{ $userCount }};
        
        if (!confirm(`FINAL WARNING: This will permanently delete ALL ${userCount} users from the system. This action cannot be undone. Are you absolutely sure?`)) {
            return;
        }
        
        if (!confirm(`Last chance! Click OK to permanently delete all ${userCount} users. This action cannot be undone!`)) {
            return;
        }
        
        // Show loading state
        deleteBtn.innerHTML = '<i class="ti ti-loader"></i> Deleting Users...';
        deleteBtn.disabled = true;
        
        // Submit the form
        form.submit();
    });
});
</script>
@endpush

@push('breadcrumb')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.user.index') }}" class="btn btn--sm btn--base">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
        <span class="text-muted">|</span>
        <span class="text-danger">Delete All Users</span>
    </div>
@endpush
