@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">Send Bulk Email to Users</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.user.send.bulk.email.post') }}" method="POST" id="bulkEmailForm">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form--label required">Select Users</label>
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn--sm btn--base" id="selectAll">Select All</button>
                                <button type="button" class="btn btn--sm btn--secondary" id="deselectAll">Deselect All</button>
                                <span class="text-muted align-self-center">Selected: <span id="selectedCount">0</span> users</span>
                            </div>
                            
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-borderless table--striped">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAllCheckbox">
                                            </th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox">
                                                </td>
                                                <td>{{ $user->firstname }} {{ $user->lastname }}</td>
                                                <td>{{ $user->username }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @if($user->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form--label required">Email Subject</label>
                            <input type="text" class="form--control" name="subject" value="{{ old('subject') }}" placeholder="Enter email subject" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form--label required">Email Message</label>
                            <textarea class="form--control" name="message" rows="10" placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                            <small class="text-muted">You can use HTML tags for formatting. Available placeholders: {name}, {username}, {email}</small>
                        </div>
                        
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle"></i>
                                <strong>Warning:</strong> This will send emails to all selected users. Please double-check your selection and message before sending.
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn--base" id="sendBulkEmail">
                                    <i class="ti ti-send"></i> Send Bulk Email
                                </button>
                                <a href="{{ route('admin.user.index') }}" class="btn btn--secondary">
                                    <i class="ti ti-arrow-left"></i> Back to Users
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const sendBulkEmailBtn = document.getElementById('sendBulkEmail');
    
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
        selectedCountSpan.textContent = selectedCount;
        selectAllCheckbox.checked = selectedCount === userCheckboxes.length;
        sendBulkEmailBtn.disabled = selectedCount === 0;
    }
    
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });
    
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    selectAllBtn.addEventListener('click', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    });
    
    deselectAllBtn.addEventListener('click', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });
    
    // Form submission confirmation
    document.getElementById('bulkEmailForm').addEventListener('submit', function(e) {
        const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
        if (selectedCount === 0) {
            e.preventDefault();
            alert('Please select at least one user to send email to.');
            return;
        }
        
        if (!confirm(`Are you sure you want to send email to ${selectedCount} users?`)) {
            e.preventDefault();
        }
    });
    
    // Initial count update
    updateSelectedCount();
});
</script>
@endpush

@push('breadcrumb')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.user.index') }}" class="btn btn--sm btn--base">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
        <span class="text-muted">|</span>
        <span>Send Bulk Email</span>
    </div>
@endpush
