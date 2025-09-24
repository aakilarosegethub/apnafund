@extends('admin.layouts.master')

@section('master')
    <div class="col-12 order-3">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">Welcome Email Template</h3>
            </div>
            <div class="card-body">
                <form class="row g-lg-4 g-3" action="{{ route('admin.notification.welcome.update') }}" method="POST">
                    @csrf

                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label required">Email Subject</label>
                        <input type="text" class="form--control" name="email_subject" value="Welcome to ApnaFund - Account Created Successfully!" required>
                    </div>
                    
                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-xl-6 col-lg-12 col-md-6">
                                <label class="form--label required">Email Body</label>
                                <textarea class="form--control email-body-html" name="email_body" required>
                                    <h1>Welcome to ApnaFund!</h1>
                                    <p>Dear {{firstname}} {{lastname}},</p>
                                    <p>Welcome to ApnaFund! Your account has been created successfully.</p>
                                    <p>Here are your account details:</p>
                                    <ul>
                                        <li><strong>Username:</strong> {{username}}</li>
                                        <li><strong>Email:</strong> {{email}}</li>
                                        <li><strong>Mobile:</strong> {{mobile}}</li>
                                    </ul>
                                    <p>Thank you for joining our crowdfunding platform!</p>
                                    <p>If you have any questions, feel free to contact our support team.</p>
                                    <p>Best regards,<br>ApnaFund Team</p>
                                </textarea>
                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-6">
                                <label class="form--label">Email Preview</label>
                                <div class="custom--card email-body-output"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn--base">Update Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        function updatePreview() {
            var content = $('.email-body-html').val();
            $('.email-body-output').html(content);
        }
        
        $('.email-body-html').on('input', updatePreview);
        updatePreview();
    });
</script>
@endpush
