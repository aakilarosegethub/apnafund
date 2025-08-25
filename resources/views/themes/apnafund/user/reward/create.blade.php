@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Add New Reward') - {{ $campaign->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.rewards.store', $campaign->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Reward Title') *</label>
                                    <input type="text" class="form--control" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Description') *</label>
                                    <textarea class="form--control" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">@lang('Minimum Amount') *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $setting->cur_sym }}</span>
                                                <input type="number" class="form--control" name="minimum_amount" value="{{ old('minimum_amount') }}" step="0.01" min="1" required>
                                            </div>
                                            @error('minimum_amount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">@lang('Reward Type') *</label>
                                            <select class="form--control" name="type" required>
                                                <option value="">@lang('Select Type')</option>
                                                <option value="physical" {{ old('type') == 'physical' ? 'selected' : '' }}>@lang('Physical Reward')</option>
                                                <option value="digital" {{ old('type') == 'digital' ? 'selected' : '' }}>@lang('Digital Reward')</option>
                                            </select>
                                            @error('type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">@lang('Available Quantity')</label>
                                            <input type="number" class="form--control" name="quantity" value="{{ old('quantity') }}" min="1" placeholder="@lang('Leave empty for unlimited')">
                                            <small class="text-muted">@lang('Leave empty for unlimited quantity')</small>
                                            @error('quantity')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">@lang('Color Theme') *</label>
                                            <select class="form--control" name="color_theme" required>
                                                <option value="">@lang('Select Color')</option>
                                                <option value="primary" {{ old('color_theme') == 'primary' ? 'selected' : '' }}>@lang('Primary (Blue)')</option>
                                                <option value="success" {{ old('color_theme') == 'success' ? 'selected' : '' }}>@lang('Success (Green)')</option>
                                                <option value="warning" {{ old('color_theme') == 'warning' ? 'selected' : '' }}>@lang('Warning (Yellow)')</option>
                                                <option value="danger" {{ old('color_theme') == 'danger' ? 'selected' : '' }}>@lang('Danger (Red)')</option>
                                                <option value="info" {{ old('color_theme') == 'info' ? 'selected' : '' }}>@lang('Info (Cyan)')</option>
                                                <option value="secondary" {{ old('color_theme') == 'secondary' ? 'selected' : '' }}>@lang('Secondary (Gray)')</option>
                                            </select>
                                            @error('color_theme')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Terms & Conditions')</label>
                                    <textarea class="form--control" name="terms_conditions" rows="3" placeholder="@lang('Any special terms or conditions for this reward')">{{ old('terms_conditions') }}</textarea>
                                    @error('terms_conditions')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Reward Image')</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="form--control" name="image" accept="image/*" id="rewardImage">
                                        <div class="file-upload-preview mt-2" id="imagePreview" style="display: none;">
                                            <img id="previewImg" class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    </div>
                                    <small class="text-muted">@lang('Recommended size: 400x300px. Max size: 2MB')</small>
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Color Preview')</label>
                                    <div class="color-preview p-3 rounded" id="colorPreview">
                                        <div class="text-center">
                                            <h6 class="mb-2">@lang('Sample Reward')</h6>
                                            <span class="badge" id="previewBadge">$50</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-end">
                            <a href="{{ route('user.rewards.index', $campaign->slug) }}" class="btn btn--outline me-2">
                                @lang('Cancel')
                            </a>
                            <button type="submit" class="btn btn--base">
                                <i class="ti ti-save me-1"></i>@lang('Create Reward')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        // Image preview
        document.getElementById('rewardImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Color theme preview
        document.querySelector('select[name="color_theme"]').addEventListener('change', function(e) {
            const color = e.target.value;
            const preview = document.getElementById('colorPreview');
            const badge = document.getElementById('previewBadge');
            
            if (color) {
                preview.className = `color-preview p-3 rounded bg-${color} bg-opacity-10 border border-${color}`;
                badge.className = `badge bg-${color}`;
            }
        });
    </script>
@endpush

@push('page-style')
    <style>
        .file-upload-wrapper {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s;
        }
        
        .file-upload-wrapper:hover {
            border-color: #007bff;
        }
        
        .color-preview {
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bg-primary { background-color: rgba(0, 123, 255, 0.1) !important; }
        .bg-success { background-color: rgba(40, 167, 69, 0.1) !important; }
        .bg-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
        .bg-danger { background-color: rgba(220, 53, 69, 0.1) !important; }
        .bg-info { background-color: rgba(23, 162, 184, 0.1) !important; }
        .bg-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
        
        .border-primary { border-color: #007bff !important; }
        .border-success { border-color: #28a745 !important; }
        .border-warning { border-color: #ffc107 !important; }
        .border-danger { border-color: #dc3545 !important; }
        .border-info { border-color: #17a2b8 !important; }
        .border-secondary { border-color: #6c757d !important; }
    </style>
@endpush 