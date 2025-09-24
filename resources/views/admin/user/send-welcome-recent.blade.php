@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Send Welcome Emails to Recent Users')</h3>
                <p class="text-muted">@lang('Send welcome emails to users who registered in the last 7 days')</p>
            </div>
            <div class="card-body">
                @if($totalUsers > 0)
                    <div class="alert alert--info">
                        <i class="ti ti-info-circle"></i>
                        @lang('Found :count recent users who registered in the last 7 days', ['count' => $totalUsers])
                    </div>

                    <form action="{{ route('admin.user.send.welcome.recent.post') }}" method="POST" id="welcomeEmailForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        <strong>@lang('Select All Users')</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table--responsive--lg">
                                <thead>
                                    <tr>
                                        <th>@lang('Select')</th>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Email')</th>
                                        <th>@lang('Username')</th>
                                        <th>@lang('Registration Date')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input user-checkbox" type="checkbox" 
                                                           name="user_ids[]" value="{{ $user->id }}" 
                                                           id="user_{{ $user->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <label for="user_{{ $user->id }}" class="mb-0">
                                                    {{ $user->firstname }} {{ $user->lastname }}
                                                </label>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ showDateTime($user->created_at, 'd M Y, h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn--primary" id="sendEmailsBtn" disabled>
                                    <i class="ti ti-mail"></i>
                                    @lang('Send Welcome Emails')
                                    <span id="selectedCount" class="badge bg-light text-dark ms-2">0</span>
                                </button>
                                <a href="{{ route('admin.user.index') }}" class="btn btn--secondary">
                                    <i class="ti ti-arrow-left"></i>
                                    @lang('Back to Users')
                                </a>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert--warning">
                        <i class="ti ti-alert-triangle"></i>
                        @lang('No recent users found in the last 7 days')
                    </div>
                    <a href="{{ route('admin.user.index') }}" class="btn btn--primary">
                        <i class="ti ti-arrow-left"></i>
                        @lang('Back to Users')
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Select All functionality
        $('#selectAll').change(function() {
            $('.user-checkbox').prop('checked', this.checked);
            updateSendButton();
        });

        // Individual checkbox change
        $('.user-checkbox').change(function() {
            updateSendButton();
            
            // Update select all checkbox
            var totalCheckboxes = $('.user-checkbox').length;
            var checkedCheckboxes = $('.user-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        });

        function updateSendButton() {
            var checkedCount = $('.user-checkbox:checked').length;
            $('#selectedCount').text(checkedCount);
            $('#sendEmailsBtn').prop('disabled', checkedCount === 0);
        }

        // Form submission confirmation
        $('#welcomeEmailForm').on('submit', function(e) {
            var checkedCount = $('.user-checkbox:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('@lang("Please select at least one user to send welcome emails.")');
                return false;
            }
            
            if (!confirm('@lang("Are you sure you want to send welcome emails to") ' + checkedCount + ' @lang("users?")')) {
                e.preventDefault();
                return false;
            }
            
            // Disable button and show loading
            $('#sendEmailsBtn').prop('disabled', true).html('<i class="ti ti-loader"></i> @lang("Sending...")');
        });
    });
</script>
@endpush
