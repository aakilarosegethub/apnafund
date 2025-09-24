@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">Send Email to User</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>User Information</h5>
                        <p><strong>Name:</strong> {{ $user->firstname }} {{ $user->lastname }}</p>
                        <p><strong>Username:</strong> {{ $user->username }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Account Status</h5>
                        <p><strong>Status:</strong> 
                            @if($user->status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                        <p><strong>Email Verified:</strong> 
                            @if($user->ec == 1)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Unverified</span>
                            @endif
                        </p>
                        <p><strong>Mobile Verified:</strong> 
                            @if($user->sc == 1)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-warning">Unverified</span>
                            @endif
                        </p>
                    </div>
                </div>

                <form action="{{ route('admin.user.send.email.post', $user->id) }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form--label required">Email Subject</label>
                            <input type="text" class="form--control" name="subject" value="{{ old('subject') }}" placeholder="Enter email subject" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form--label required">Email Message</label>
                            <textarea class="form--control" name="message" rows="10" placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                            <small class="text-muted">You can use HTML tags for formatting.</small>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn--base">
                                    <i class="ti ti-send"></i> Send Email
                                </button>
                                <a href="{{ route('admin.user.details', $user->id) }}" class="btn btn--secondary">
                                    <i class="ti ti-arrow-left"></i> Back to User Details
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.user.index') }}" class="btn btn--sm btn--base">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
        <span class="text-muted">|</span>
        <span>Send Email to {{ $user->username }}</span>
    </div>
@endpush
