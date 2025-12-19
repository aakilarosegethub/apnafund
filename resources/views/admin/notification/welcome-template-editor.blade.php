@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Welcome Email Template Editor')</h3>
                <p class="text-muted">@lang('Customize your welcome email template with emojis and rich content')</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.notification.welcome.template.update') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label class="form--label required">@lang('Email Subject')</label>
                            <input type="text" class="form--control" name="subject" 
                                   value="🎉 Welcome to ApnaCrowdfunding - Your Journey Begins!" required>
                            <div class="form-text">@lang('Use emojis to make your subject line more engaging')</div>
                        </div>
                        
                        <div class="col-lg-6">
                            <label class="form--label">@lang('Available Variables')</label>
                            <div class="alert alert--info">
                                <small>
                                    <strong>User Variables:</strong><br>
                                    {{firstname}}, {{lastname}}, {{username}}, {{email}}, {{mobile}}<br>
                                    <strong>Business Variables:</strong><br>
                                    {{business_name}}, {{business_type}}, {{industry}}
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-4 mt-3">
                        <div class="col-12">
                            <label class="form--label required">@lang('Email Content')</label>
                            <textarea class="form--control" name="content" rows="20" required>
🌟 Welcome to ApnaCrowdfunding! We're thrilled to have you join our community of entrepreneurs and innovators.

Your account has been created successfully and you're ready to start your fundraising journey!

📋 **Your Account Details:**
👤 Username: {{username}}
📧 Email: {{email}}
📱 Mobile: {{mobile}}

@if(business_name)
🏢 **Business Information:**
Business Name: {{business_name}}
Business Type: {{business_type}}
Industry: {{industry}}
@endif

🚀 **What's Next?**
• Create your first fundraising campaign
• Set up your business profile
• Connect with potential investors
• Start raising funds for your dreams!

💡 **Pro Tips:**
• Complete your profile to build trust
• Use high-quality images for your campaigns
• Share your story authentically
• Engage with your supporters regularly

[🚀 Start Your First Campaign]({{login_url}})

Need help? Our support team is here for you!
📞 Contact us anytime - we're here to help you succeed!

Best regards,
The ApnaCrowdfunding Team 💙
                            </textarea>
                            <div class="form-text">@lang('Use Markdown formatting and emojis to create engaging content')</div>
                        </div>
                    </div>
                    
                    <div class="row g-4 mt-3">
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn--primary">
                                    <i class="ti ti-device-floppy"></i>
                                    @lang('Update Template')
                                </button>
                                <a href="{{ route('admin.user.test.email.last.user') }}" class="btn btn--info">
                                    <i class="ti ti-mail-check"></i>
                                    @lang('Test Email')
                                </a>
                                <a href="{{ route('admin.user.send.welcome.recent') }}" class="btn btn--success">
                                    <i class="ti ti-mail-heart"></i>
                                    @lang('Send to Recent Users')
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Add emoji picker functionality
        $('textarea[name="content"]').on('input', function() {
            // Auto-save to localStorage
            localStorage.setItem('welcome_email_template', $(this).val());
        });
        
        // Load from localStorage
        var savedTemplate = localStorage.getItem('welcome_email_template');
        if (savedTemplate) {
            $('textarea[name="content"]').val(savedTemplate);
        }
    });
</script>
@endpush
