@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="custom--card">
        <div class="card-header">
            <h3 class="title">@lang('OTP Provider Settings')</h3>
            <p class="text-muted">@lang('Choose Firebase or Twilio for sending OTP. Firebase is recommended for better delivery.')</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.firebase.otp.update') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form--label required">@lang('OTP Provider')</label>
                        <select class="form--control form-select" name="otp_provider" id="otp_provider" required>
                            <option value="twilio" @selected(($dataInfo['otp_provider'] ?? '') == 'twilio')>@lang('Twilio (SMS)')</option>
                            <option value="firebase" @selected(($dataInfo['otp_provider'] ?? '') == 'firebase')>@lang('Firebase (Phone Auth)')</option>
                            <option value="msg91" @selected(($dataInfo['otp_provider'] ?? '') == 'msg91')>@lang('MSG91')</option>
                        </select>
                        <small class="text-muted">@lang('Firebase uses Google\'s infrastructure. Configure SMS in Firebase Console for phone OTP.')</small>
                    </div>
                </div>

                <div id="firebase_settings" style="display: {{ ($dataInfo['otp_provider'] ?? '') == 'firebase' ? 'block' : 'none' }};">
                    <hr>
                    <h5 class="mb-3"><i class="ti ti-brand-firebase"></i> @lang('Firebase Service Account (from JSON)')</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form--label">@lang('Project ID')</label>
                            <input type="text" class="form--control" name="project_id" value="{{ $dataInfo['project_id'] ?? '' }}" placeholder="your-project-id">
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Private Key ID')</label>
                            <input type="text" class="form--control" name="private_key_id" value="{{ $dataInfo['private_key_id'] ?? '' }}" placeholder="abc123...">
                        </div>
                        <div class="col-12">
                            <label class="form--label">@lang('Private Key')</label>
                            <textarea class="form--control" name="private_key" rows="4" placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----">{{ $dataInfo['private_key'] ?? '' }}</textarea>
                            <small class="text-muted">@lang('Paste full key from Firebase Console > Service Accounts > Generate key')</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Client Email')</label>
                            <input type="text" class="form--control" name="client_email" value="{{ $dataInfo['client_email'] ?? '' }}" placeholder="firebase-adminsdk-xxx@project.iam.gserviceaccount.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Client ID')</label>
                            <input type="text" class="form--control" name="client_id" value="{{ $dataInfo['client_id'] ?? '' }}" placeholder="123456789">
                        </div>
                        <div class="col-12">
                            <label class="form--label">@lang('Client Cert URL')</label>
                            <input type="text" class="form--control" name="client_cert_url" value="{{ $dataInfo['client_cert_url'] ?? '' }}" placeholder="https://www.googleapis.com/robot/v1/metadata/x509/...">
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">@lang('Firebase Web Config (for Chat)')</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form--label">@lang('API Key')</label>
                            <input type="text" class="form--control" name="api_key" value="{{ $dataInfo['api_key'] ?? '' }}" placeholder="AIzaSy...">
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Auth Domain')</label>
                            <input type="text" class="form--control" name="auth_domain" value="{{ $dataInfo['auth_domain'] ?? '' }}" placeholder="project-id.firebaseapp.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Storage Bucket')</label>
                            <input type="text" class="form--control" name="storage_bucket" value="{{ $dataInfo['storage_bucket'] ?? '' }}" placeholder="project-id.appspot.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Messaging Sender ID')</label>
                            <input type="text" class="form--control" name="messaging_sender_id" value="{{ $dataInfo['messaging_sender_id'] ?? '' }}" placeholder="123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('App ID')</label>
                            <input type="text" class="form--control" name="app_id" value="{{ $dataInfo['app_id'] ?? '' }}" placeholder="1:123:web:abc...">
                        </div>
                        <div class="col-md-6">
                            <label class="form--label">@lang('Collection Prefix')</label>
                            <input type="text" class="form--control" name="collection_prefix" value="{{ $dataInfo['collection_prefix'] ?? 'apnafund' }}" placeholder="apnafund">
                        </div>
                    </div>

                    <div class="alert alert--info mt-3">
                        <strong>@lang('Setup:')</strong>
                        <ol class="mb-0 mt-2">
                            <li>@lang('Go to') <a href="https://console.firebase.google.com" target="_blank">Firebase Console</a></li>
                            <li>@lang('Project Settings > Service Accounts > Generate new private key')</li>
                            <li>@lang('Copy values from JSON to fields above')</li>
                            <li>@lang('For Web config: Project Settings > General > Your apps > Web app')</li>
                        </ol>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn--base px-4" type="submit">
                            <i class="ti ti-device-floppy"></i> @lang('Save Settings')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-script')
<script>
(function($) {
    function toggleFirebase() {
        if ($('#otp_provider').val() === 'firebase') {
            $('#firebase_settings').slideDown();
        } else {
            $('#firebase_settings').slideUp();
        }
    }
    $('#otp_provider').on('change', toggleFirebase);
    toggleFirebase();
})(jQuery);
</script>
@endpush
