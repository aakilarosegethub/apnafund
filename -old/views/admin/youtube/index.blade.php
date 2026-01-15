@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🎥 YouTube Integration Settings</h4>
                </div>
                <div class="card-body">
                    
                    @if($isConfigured)
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> YouTube Integration is Configured!</h5>
                            <p>Your YouTube integration is ready to use. Users can now upload videos that will be automatically uploaded to YouTube.</p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> YouTube Integration Not Configured</h5>
                            <p>Please follow the setup steps below to enable YouTube automatic upload functionality.</p>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <h5>📋 Setup Instructions</h5>
                            <div class="setup-steps">
                                
                                <div class="step">
                                    <h6><span class="step-number">1</span> Google Cloud Console Setup</h6>
                                    <ul>
                                        <li>Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                        <li>Create a new project or select existing one</li>
                                        <li>Enable "YouTube Data API v3"</li>
                                        <li>Create OAuth 2.0 credentials</li>
                                        <li>Download credentials JSON file</li>
                                    </ul>
                                </div>

                                <div class="step">
                                    <h6><span class="step-number">2</span> File Upload</h6>
                                    <ul>
                                        <li>Upload the downloaded JSON file to: <code>storage/app/youtube-credentials.json</code></li>
                                        <li>Set proper file permissions (644)</li>
                                    </ul>
                                </div>

                                <div class="step">
                                    <h6><span class="step-number">3</span> Environment Configuration</h6>
                                    <ul>
                                        <li>Add YouTube credentials to your <code>.env</code> file</li>
                                        <li>Set redirect URI: <code>{{ url('/youtube/callback') }}</code></li>
                                    </ul>
                                </div>

                                <div class="step">
                                    <h6><span class="step-number">4</span> Authorization</h6>
                                    <ul>
                                        <li>Click "Authorize YouTube" button below</li>
                                        <li>Sign in with Google account</li>
                                        <li>Grant permissions</li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <h5>⚙️ Configuration Status</h5>
                            <div class="config-status">
                                
                                <div class="status-item">
                                    <i class="fas {{ file_exists(storage_path('app/youtube-credentials.json')) ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    <span>Credentials File</span>
                                </div>

                                <div class="status-item">
                                    <i class="fas {{ config('services.youtube.client_id') ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    <span>Client ID</span>
                                </div>

                                <div class="status-item">
                                    <i class="fas {{ config('services.youtube.client_secret') ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    <span>Client Secret</span>
                                </div>

                                <div class="status-item">
                                    <i class="fas {{ config('services.youtube.access_token') ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                    <span>Access Token</span>
                                </div>

                            </div>

                            <div class="mt-4">
                                @if($isConfigured)
                                    <a href="{{ route('admin.youtube.test') }}" class="btn btn-info btn-block">
                                        <i class="fas fa-play"></i> Test YouTube Upload
                                    </a>
                                @else
                                    <a href="{{ route('admin.youtube.auth') }}" class="btn btn-primary btn-block">
                                        <i class="fab fa-youtube"></i> Authorize YouTube
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>📖 Documentation</h5>
                        <p>For detailed setup instructions, please refer to the <code>YOUTUBE_SETUP_GUIDE.md</code> file in your project root.</p>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Important Notes:</h6>
                            <ul class="mb-0">
                                <li>YouTube API has daily quotas (10,000 units/day by default)</li>
                                <li>Each video upload costs ~1,600 units</li>
                                <li>Videos are uploaded as "unlisted" by default</li>
                                <li>Only authenticated users can upload videos</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.setup-steps .step {
    margin-bottom: 2rem;
    padding: 1rem;
    border-left: 4px solid #007bff;
    background: #f8f9fa;
}

.step-number {
    display: inline-block;
    width: 30px;
    height: 30px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    text-align: center;
    line-height: 30px;
    margin-right: 10px;
    font-weight: bold;
}

.config-status .status-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    padding: 8px;
    background: #f8f9fa;
    border-radius: 4px;
}

.config-status .status-item i {
    margin-right: 10px;
    width: 20px;
}

.status-item span {
    font-weight: 500;
}
</style>
@endsection