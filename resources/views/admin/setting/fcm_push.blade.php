@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="custom--card">
        <div class="card-header">
            <h3 class="title">@lang('Push notifications (FCM)')</h3>
            <p class="text-muted mb-0">@lang('Server-side Firebase Cloud Messaging uses the same type of service account as the Admin SDK. Mobile apps need API key, Sender ID, and App ID in their Firebase config (google-services.json / GoogleService-Info.plist).')</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.fcm.push.update') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-12">
                        <input type="hidden" name="enabled" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="enabled" id="fcm_enabled" value="1"
                                @checked(old('enabled', !empty($dataInfo['enabled'])))>
                            <label class="form-check-label" for="fcm_enabled">@lang('Use credentials below for push (overrides .env for FCM)')</label>
                        </div>
                        <small class="text-muted d-block mt-1">@lang('Until you save settings here for the first time, the server keeps using FIREBASE_* from .env (if set). After you save with this switch off, push is disabled until you turn it on again.')</small>
                    </div>
                </div>

                <div class="alert alert--warning">
                    <strong>@lang('Note:')</strong> @lang('Saving with the switch off (after you have saved once before) disables push completely, even if .env has keys. First time: saving with the switch off does nothing so .env keeps working.')
                </div>

                <hr>
                <h5 class="mb-3">@lang('Paste service account JSON (optional)')</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form--label">@lang('Full JSON file')</label>
                        <textarea class="form--control" name="service_account_json" rows="5" placeholder='{"type":"service_account",...}'></textarea>
                        <small class="text-muted">@lang('Firebase Console → Project settings → Service accounts → Generate new private key. Saves on submit; other fields fill from JSON.')</small>
                    </div>
                </div>

                <h5 class="mb-3"><i class="ti ti-brand-firebase"></i> @lang('Service account (server / FCM v1)')</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form--label">@lang('Project ID')</label>
                        <input type="text" class="form--control" name="project_id" value="{{ old('project_id', $dataInfo['project_id'] ?? '') }}" placeholder="your-project-id">
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('Private Key ID')</label>
                        <input type="text" class="form--control" name="private_key_id" value="{{ old('private_key_id', $dataInfo['private_key_id'] ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Private Key')</label>
                        <textarea class="form--control" name="private_key" rows="4" placeholder="@lang('Leave blank to keep the saved key')">{{ old('private_key', $dataInfo['private_key'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('Client Email')</label>
                        <input type="text" class="form--control" name="client_email" value="{{ old('client_email', $dataInfo['client_email'] ?? '') }}" placeholder="firebase-adminsdk-xxx@....iam.gserviceaccount.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('Client ID')</label>
                        <input type="text" class="form--control" name="client_id" value="{{ old('client_id', $dataInfo['client_id'] ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Client X509 Cert URL')</label>
                        <input type="text" class="form--control" name="client_cert_url" value="{{ old('client_cert_url', $dataInfo['client_cert_url'] ?? '') }}">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">@lang('Mobile / Web client (reference for app team)')</h5>
                <p class="text-muted small">@lang('These are not used by the PHP FCM sender; store them here for your Android/iOS/Web builds.')</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form--label">@lang('API Key')</label>
                        <input type="text" class="form--control" name="api_key" value="{{ old('api_key', $dataInfo['api_key'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('Auth Domain')</label>
                        <input type="text" class="form--control" name="auth_domain" value="{{ old('auth_domain', $dataInfo['auth_domain'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('Storage Bucket')</label>
                        <input type="text" class="form--control" name="storage_bucket" value="{{ old('storage_bucket', $dataInfo['storage_bucket'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('Messaging Sender ID')</label>
                        <input type="text" class="form--control" name="messaging_sender_id" value="{{ old('messaging_sender_id', $dataInfo['messaging_sender_id'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('App ID')</label>
                        <input type="text" class="form--control" name="app_id" value="{{ old('app_id', $dataInfo['app_id'] ?? '') }}">
                    </div>
                </div>

                <hr class="my-4">
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" name="sync_to_env" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sync_to_env" id="sync_to_env" value="1" @checked(old('sync_to_env'))>
                            <label class="form-check-label" for="sync_to_env">@lang('Also write these values to .env (FIREBASE_*) and clear config cache')</label>
                        </div>
                        <small class="text-muted">@lang('Optional. Useful if other features (OTP, chat) should read the same keys from environment.')</small>
                    </div>
                </div>

                <div class="alert alert--info mt-4">
                    <strong>@lang('Console checklist')</strong>
                    <ol class="mb-0 mt-2">
                        <li>@lang('Enable Cloud Messaging API (Google Cloud) for the project.')</li>
                        <li>@lang('Service account JSON → fields above (or paste JSON).')</li>
                        <li>@lang('Add Android/iOS apps in Firebase and copy API Key, Sender ID, App ID into the reference section.')</li>
                    </ol>
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
