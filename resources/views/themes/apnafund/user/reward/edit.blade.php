@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Edit Reward') - {{ $campaign->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.rewards.update', [$campaign->id, $reward->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Reward Title') *</label>
                                    <input type="text" class="form--control" name="title" value="{{ old('title', $reward->title) }}" required>
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Description') *</label>
                                    <textarea class="form--control" name="description" rows="4" required>{{ old('description', $reward->description) }}</textarea>
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
                                                <input type="number" class="form--control" name="minimum_amount" value="{{ old('minimum_amount', $reward->minimum_amount) }}" step="0.01" min="1" required>
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
                                                <option value="physical" {{ old('type', $reward->type) == 'physical' ? 'selected' : '' }}>@lang('Physical Reward')</option>
                                                <option value="digital" {{ old('type', $reward->type) == 'digital' ? 'selected' : '' }}>@lang('Digital Reward')</option>
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
                                            <input type="number" class="form--control" name="quantity" value="{{ old('quantity', $reward->quantity) }}" min="1" placeholder="@lang('Leave empty for unlimited')">
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
                                                <option value="primary" {{ old('color_theme', $reward->color_theme) == 'primary' ? 'selected' : '' }}>@lang('Primary (Blue)')</option>
                                                <option value="success" {{ old('color_theme', $reward->color_theme) == 'success' ? 'selected' : '' }}>@lang('Success (Green)')</option>
                                                <option value="warning" {{ old('color_theme', $reward->color_theme) == 'warning' ? 'selected' : '' }}>@lang('Warning (Yellow)')</option>
                                                <option value="danger" {{ old('color_theme', $reward->color_theme) == 'danger' ? 'selected' : '' }}>@lang('Danger (Red)')</option>
                                                <option value="info" {{ old('color_theme', $reward->color_theme) == 'info' ? 'selected' : '' }}>@lang('Info (Cyan)')</option>
                                                <option value="secondary" {{ old('color_theme', $reward->color_theme) == 'secondary' ? 'selected' : '' }}>@lang('Secondary (Gray)')</option>
                                            </select>
                                            @error('color_theme')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Terms & Conditions')</label>
                                    <textarea class="form--control" name="terms_conditions" rows="3" placeholder="@lang('Any special terms or conditions for this reward')">{{ old('terms_conditions', $reward->terms_conditions) }}</textarea>
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
                                        @if($reward->image)
                                            <div class="current-image mt-2">
                                                <label class="form-label">@lang('Current Image'):</label>
                                                <img src="{{ getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward')) }}" 
                                                     alt="{{ $reward->title }}" class="img-fluid rounded" style="max-height: 150px;">
                                            </div>
                                        @endif
                                        <div class="file-upload-preview mt-2" id="imagePreview" style="display: none;">
                                            <label class="form-label">@lang('New Image Preview'):</label>
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
                                            <span class="badge" id="previewBadge">{{ $setting->cur_sym . showAmount($reward->minimum_amount) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">@lang('Reward Statistics')</label>
                                    <div class="stats-card p-3 rounded bg-light">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <div class="stat-number">{{ $reward->claimed_count }}</div>
                                                    <div class="stat-label">@lang('Claimed')</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <div class="stat-number">
                                                        @if($reward->quantity === null)
                                                            ∞
                                                        @else
                                                            {{ $reward->getRemainingQuantity() }}
                                                        @endif
                                                    </div>
                                                    <div class="stat-label">@lang('Available')</div>
                                                </div>
                                            </div>
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
                                <i class="ti ti-save me-1"></i>@lang('Update Reward')
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

        // Initialize color preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            const colorSelect = document.querySelector('select[name="color_theme"]');
            if (colorSelect.value) {
                const event = new Event('change');
                colorSelect.dispatchEvent(event);
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
        
        .stats-card {
            border: 1px solid #e9ecef;
        }
        
        .stat-item {
            padding: 10px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
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