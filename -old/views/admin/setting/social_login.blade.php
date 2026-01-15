@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="custom--card">
        <div class="card-header">
            <h3 class="title">@lang('Social Login Settings')</h3>
            <p class="text-muted">@lang('Configure Facebook and Google social login for your users')</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.social.login.update') }}" method="POST" id="socialLoginForm">
                @csrf
                
                <!-- Facebook Settings -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="social-provider-card">
                            <div class="provider-header">
                                <div class="provider-icon">
                                    <i class="fab fa-facebook-f"></i>
                                </div>
                                <div class="provider-info">
                                    <h4>Facebook Login</h4>
                                    <p>Allow users to login with their Facebook account</p>
                                </div>
                                <div class="provider-toggle">
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="facebook_status" 
                                               id="facebook_status" value="1" 
                                               @if(@$socialLoginSettings->data_info['facebook']['status']) checked @endif>
                                        <label class="form-check-label" for="facebook_status">
                                            @lang('Enable Facebook Login')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="provider-settings" id="facebook_settings" 
                                 style="display: {{ @$socialLoginSettings->data_info['facebook']['status'] ? 'block' : 'none' }};">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form--label">@lang('Facebook App ID')</label>
                                        <input type="text" class="form--control" name="facebook_client_id" 
                                               value="{{ @$socialLoginSettings->data_info['facebook']['client_id'] }}" 
                                               placeholder="Enter Facebook App ID">
                                        <small class="text-muted">Get this from Facebook Developer Console</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form--label">@lang('Facebook App Secret')</label>
                                        <input type="password" class="form--control" name="facebook_client_secret" 
                                               value="{{ @$socialLoginSettings->data_info['facebook']['client_secret'] }}" 
                                               placeholder="Enter Facebook App Secret">
                                        <small class="text-muted">Get this from Facebook Developer Console</small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form--label">@lang('Redirect URI')</label>
                                        <input type="text" class="form--control" readonly 
                                               value="{{ @$socialLoginSettings->data_info['facebook']['redirect_uri'] }}">
                                        <small class="text-muted">Add this URL to your Facebook App settings</small>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn--sm btn--info test-config-btn" 
                                                data-provider="facebook">
                                            <i class="fas fa-check"></i> @lang('Test Configuration')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Settings -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="social-provider-card">
                            <div class="provider-header">
                                <div class="provider-icon">
                                    <i class="fab fa-google"></i>
                                </div>
                                <div class="provider-info">
                                    <h4>Google Login</h4>
                                    <p>Allow users to login with their Google account</p>
                                </div>
                                <div class="provider-toggle">
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="google_status" 
                                               id="google_status" value="1" 
                                               @if(@$socialLoginSettings->data_info['google']['status']) checked @endif>
                                        <label class="form-check-label" for="google_status">
                                            @lang('Enable Google Login')
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="provider-settings" id="google_settings" 
                                 style="display: {{ @$socialLoginSettings->data_info['google']['status'] ? 'block' : 'none' }};">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form--label">@lang('Google Client ID')</label>
                                        <input type="text" class="form--control" name="google_client_id" 
                                               value="{{ @$socialLoginSettings->data_info['google']['client_id'] }}" 
                                               placeholder="Enter Google Client ID">
                                        <small class="text-muted">Get this from Google Cloud Console</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form--label">@lang('Google Client Secret')</label>
                                        <input type="password" class="form--control" name="google_client_secret" 
                                               value="{{ @$socialLoginSettings->data_info['google']['client_secret'] }}" 
                                               placeholder="Enter Google Client Secret">
                                        <small class="text-muted">Get this from Google Cloud Console</small>
                                    </div>
                                    <div class="col-12">
                                        <label class="form--label">@lang('Redirect URI')</label>
                                        <input type="text" class="form--control" readonly 
                                               value="{{ @$socialLoginSettings->data_info['google']['redirect_uri'] }}">
                                        <small class="text-muted">Add this URL to your Google OAuth settings</small>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn--sm btn--info test-config-btn" 
                                                data-provider="google">
                                            <i class="fas fa-check"></i> @lang('Test Configuration')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Setup Instructions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert--info">
                            <h5><i class="fas fa-info-circle"></i> @lang('Setup Instructions')</h5>
                            <div class="setup-instructions">
                                <div class="instruction-section">
                                    <h6>@lang('Facebook Setup:')</h6>
                                    <ol>
                                        <li>@lang('Go to') <a href="https://developers.facebook.com/" target="_blank">Facebook Developers</a></li>
                                        <li>@lang('Create a new app or select existing one')</li>
                                        <li>@lang('Add "Facebook Login" product to your app')</li>
                                        <li>@lang('Set Valid OAuth Redirect URIs to:') <code>{{ env('APP_URL') }}/auth/facebook/callback</code></li>
                                        <li>@lang('Copy App ID and App Secret to the fields above')</li>
                                    </ol>
                                </div>
                                
                                <div class="instruction-section">
                                    <h6>@lang('Google Setup:')</h6>
                                    <ol>
                                        <li>@lang('Go to') <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                        <li>@lang('Create a new project or select existing one')</li>
                                        <li>@lang('Enable Google+ API')</li>
                                        <li>@lang('Create OAuth 2.0 credentials')</li>
                                        <li>@lang('Add authorized redirect URI:') <code>{{ env('APP_URL') }}/auth/google/callback</code></li>
                                        <li>@lang('Copy Client ID and Client Secret to the fields above')</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn--base px-4">
                                <i class="fas fa-save"></i> @lang('Save Settings')
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.social-provider-card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.provider-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.provider-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 24px;
    color: white;
}

.provider-icon .fa-facebook-f {
    background: #1877f2;
    padding: 12px;
    border-radius: 50%;
}

.provider-icon .fa-google {
    background: #db4437;
    padding: 12px;
    border-radius: 50%;
}

.provider-info {
    flex: 1;
}

.provider-info h4 {
    margin: 0 0 5px 0;
    font-size: 18px;
    font-weight: 600;
}

.provider-info p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.provider-toggle {
    margin-left: auto;
}

.provider-settings {
    border-top: 1px solid #e0e0e0;
    padding-top: 20px;
    margin-top: 15px;
}

.setup-instructions {
    margin-top: 15px;
}

.instruction-section {
    margin-bottom: 20px;
}

.instruction-section h6 {
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
}

.instruction-section ol {
    margin: 0;
    padding-left: 20px;
}

.instruction-section li {
    margin-bottom: 5px;
    color: #555;
}

.test-config-btn {
    margin-top: 10px;
}

.alert--info {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #0d47a1;
}
</style>
@endsection

@push('page-script')
<script>
$(document).ready(function() {
    // Toggle provider settings
    $('#facebook_status').change(function() {
        if ($(this).is(':checked')) {
            $('#facebook_settings').slideDown();
        } else {
            $('#facebook_settings').slideUp();
        }
    });

    $('#google_status').change(function() {
        if ($(this).is(':checked')) {
            $('#google_settings').slideDown();
        } else {
            $('#google_settings').slideUp();
        }
    });

    // Test configuration
    $('.test-config-btn').click(function() {
        const provider = $(this).data('provider');
        const button = $(this);
        const originalText = button.html();
        
        button.html('<i class="fas fa-spinner fa-spin"></i> Testing...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.social.login.test") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                provider: provider
            },
            success: function(response) {
                if (response.success) {
                    button.html('<i class="fas fa-check"></i> ' + response.message);
                    button.removeClass('btn--info').addClass('btn--success');
                } else {
                    button.html('<i class="fas fa-times"></i> ' + response.message);
                    button.removeClass('btn--info').addClass('btn--danger');
                }
            },
            error: function() {
                button.html('<i class="fas fa-times"></i> Test failed');
                button.removeClass('btn--info').addClass('btn--danger');
            },
            complete: function() {
                setTimeout(function() {
                    button.html(originalText);
                    button.prop('disabled', false);
                    button.removeClass('btn--success btn--danger').addClass('btn--info');
                }, 3000);
            }
        });
    });
});
</script>
@endpush
