@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
<div class="dashboard py-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="fas fa-gift me-2"></i>@lang('Create New Reward')
                        </h4>
                        <a href="{{ route('user.rewards.index', $campaign->slug) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>@lang('Back to Rewards')
                        </a>
                </div>
                    <p class="text-muted mb-4">@lang('Add a reward for your campaign') - <strong>{{ $campaign->name }}</strong></p>
                        <form action="{{ route('user.rewards.store', $campaign->slug) }}" method="POST" enctype="multipart/form-data" id="rewardForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group required-field">
                                    <label for="rewardTitle" class="form-label">@lang('Reward Title')</label>
                                    <input type="text" class="form-control" id="rewardTitle" name="title" 
                                                   value="{{ old('title') }}" placeholder="@lang('Enter an attractive title for your reward')" required>
                                    <div class="form-text">
                                        <span id="titleCounter">0 characters</span>
                                    </div>
                                    @error('title')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                        </div>
                                    </div>
                            <div class="col-md-4">
                                <div class="form-group required-field">
                                    <label for="rewardType" class="form-label">@lang('Reward Type')</label>
                                    <select class="form-control form-select" name="type" id="rewardType" required>
                                        <option value="">@lang('Select reward type')</option>
                                        <option value="physical" {{ old('type') == 'physical' ? 'selected' : '' }}>
                                            @lang('Physical Reward')
                                        </option>
                                        <option value="digital" {{ old('type') == 'digital' ? 'selected' : '' }}>
                                            @lang('Digital Reward')
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                </div>
                            </div>

                        <div class="form-group required-field">
                            <label for="rewardDescription" class="form-label">@lang('Description')</label>
                            <textarea class="form-control" name="description" id="rewardDescription" rows="4" 
                                      placeholder="@lang('Describe what supporters will receive with this reward')" required>{{ old('description') }}</textarea>
                            <div class="form-text d-flex justify-content-between">
                                <span>@lang('Be specific about what supporters will get. This helps build trust and excitement.')</span>
                                <span class="text-muted" id="descriptionCounter">0 characters</span>
                            </div>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                                <div class="row">
                                    <div class="col-md-6">
                                <div class="form-group required-field">
                                    <label for="minimumAmount" class="form-label">@lang('Minimum Donation Amount')</label>
                                    <div class="input-group">
                                                <span class="input-group-text">{{ $setting->cur_sym }}</span>
                                        <input type="number" class="form-control" name="minimum_amount" id="minimumAmount"
                                                       value="{{ old('minimum_amount') }}" step="0.01" min="1" 
                                                       placeholder="0.00" required>
                                            </div>
                                            <div class="form-text">@lang('Minimum amount supporters need to donate to get this reward')</div>
                                            @error('minimum_amount')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rewardQuantity" class="form-label">@lang('Available Quantity')</label>
                                    <input type="number" class="form-control" name="quantity" id="rewardQuantity"
                                                   value="{{ old('quantity') }}" min="1" 
                                                   placeholder="@lang('Leave empty for unlimited')">
                                            <div class="form-text">@lang('Leave empty for unlimited quantity')</div>
                                            @error('quantity')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                        </div>

                        <div class="row">
                                    <div class="col-md-6">
                                <div class="form-group required-field">
                                    <label for="colorTheme" class="form-label">@lang('Color Theme')</label>
                                    <select class="form-control form-select" name="color_theme" id="colorTheme" required>
                                        <option value="">@lang('Select Color')</option>
                                        <option value="primary" {{ old('color_theme', 'primary') == 'primary' ? 'selected' : '' }}>@lang('Primary (Blue)')</option>
                                        <option value="success" {{ old('color_theme') == 'success' ? 'selected' : '' }}>@lang('Success (Green)')</option>
                                        <option value="warning" {{ old('color_theme') == 'warning' ? 'selected' : '' }}>@lang('Warning (Yellow)')</option>
                                        <option value="danger" {{ old('color_theme') == 'danger' ? 'selected' : '' }}>@lang('Danger (Red)')</option>
                                        <option value="info" {{ old('color_theme') == 'info' ? 'selected' : '' }}>@lang('Info (Cyan)')</option>
                                        <option value="secondary" {{ old('color_theme') == 'secondary' ? 'selected' : '' }}>@lang('Secondary (Gray)')</option>
                                    </select>
                                            @error('color_theme')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required-field">
                                    <label for="rewardImage" class="form-label">@lang('Reward Image')</label>
                                    <input type="file" class="form-control" id="rewardImage" name="image" accept="image/*">
                                    <div class="form-text">@lang('Recommended size: 400x300px. Max size: 2MB')</div>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                            </div>

                        <div class="form-group">
                            <label for="termsConditions" class="form-label">@lang('Terms & Conditions')</label>
                            <textarea class="form-control" name="terms_conditions" id="termsConditions" rows="3" 
                                              placeholder="@lang('Any special terms or conditions for this reward (optional)')">{{ old('terms_conditions') }}</textarea>
                                    <div class="form-text">@lang('Specify any special conditions, delivery timelines, or restrictions')</div>
                                    @error('terms_conditions')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                            </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>@lang('Create Reward')
                            </button>
                            <a href="{{ route('user.rewards.index', $campaign->slug) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>@lang('Cancel')
                            </a>
                            </div>
                        </form>
                                        </div>
                                    </div>

            <div class="col-lg-4">
                <div class="content-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>@lang('Live Preview')
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshPreview()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div id="rewardPreview">
                        <div class="preview-card">
                            <div class="preview-image" id="previewImageArea">
                                <i class="fas fa-gift" id="previewImageIcon"></i>
                                <img id="previewImage" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 10px 10px 0 0;">
                            </div>
                            <div class="preview-content">
                                <div class="preview-title" id="previewTitle">@lang('Your Reward Title')</div>
                                <div class="preview-description" id="previewDescription">@lang('Your reward description will appear here...')</div>
                                <div class="preview-meta">
                                    <span class="preview-category" id="previewType">@lang('Physical Reward')</span>
                                    <span class="preview-amount" id="previewAmount">{{ $setting->cur_sym }}0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image upload functionality
            const imageInput = document.getElementById('rewardImage');
            const previewImg = document.getElementById('previewImage');
            const previewImageIcon = document.getElementById('previewImageIcon');
            
            // File input change
            if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                            if (previewImg && previewImageIcon) {
                    previewImg.src = e.target.result;
                                previewImg.style.display = 'block';
                                previewImageIcon.style.display = 'none';
                                console.log('Image preview updated');
                            }
                    }
                    reader.readAsDataURL(file);
                }
            });
            }

            // Live preview updates
            const titleInput = document.getElementById('rewardTitle');
            const descriptionInput = document.getElementById('rewardDescription');
            const amountInput = document.getElementById('minimumAmount');
            const typeSelect = document.getElementById('rewardType');
            const colorSelect = document.getElementById('colorTheme');
            const termsInput = document.getElementById('termsConditions');

            // Debug: Check if elements are found
            console.log('Elements found:');
            console.log('titleInput:', titleInput);
            console.log('descriptionInput:', descriptionInput);
            console.log('amountInput:', amountInput);
            console.log('typeSelect:', typeSelect);
            console.log('colorSelect:', colorSelect);
            console.log('termsInput:', termsInput);

            // Real-time preview updates with debouncing for better performance
            let previewTimeout;
            
            function debouncedUpdatePreview() {
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(updatePreview, 100);
            }

            // Update preview when inputs change
            if (titleInput) {
                titleInput.addEventListener('input', debouncedUpdatePreview);
                titleInput.addEventListener('keyup', updatePreview);
            }
            
            if (descriptionInput) {
                descriptionInput.addEventListener('input', debouncedUpdatePreview);
                descriptionInput.addEventListener('keyup', updatePreview);
            }
            
            if (amountInput) {
                amountInput.addEventListener('input', debouncedUpdatePreview);
                amountInput.addEventListener('keyup', updatePreview);
            }
            
            if (typeSelect) {
            typeSelect.addEventListener('change', updatePreview);
            }
            
            if (colorSelect) {
                colorSelect.addEventListener('change', updatePreview);
            }
            
            if (termsInput) {
                termsInput.addEventListener('input', debouncedUpdatePreview);
                termsInput.addEventListener('keyup', updatePreview);
            }

            // Character counter for description
            if (descriptionInput) {
                descriptionInput.addEventListener('input', function() {
                    const counter = document.getElementById('descriptionCounter');
                    if (counter) {
                        const length = this.value.length;
                        counter.textContent = length + ' characters';
                        
                        // Change color based on length
                        if (length > 500) {
                            counter.style.color = '#dc3545';
                        } else if (length > 300) {
                            counter.style.color = '#ffc107';
                        } else {
                            counter.style.color = '#6c757d';
                        }
                    }
                });
            }

            // Title validation and counter
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    const counter = document.getElementById('titleCounter');
                    if (counter) {
                        const length = this.value.length;
                        counter.textContent = length + ' characters';
                        
                        // Change color based on length
                        if (length >= 10 && length <= 100) {
                            counter.style.color = '#28a745';
                        } else if (length > 0) {
                            counter.style.color = '#ffc107';
                        } else {
                            counter.style.color = '#6c757d';
                        }
                    }
                });
            }

            function updatePreview() {
                console.log('updatePreview called');
                
                // Update title
                const titleElement = document.getElementById('previewTitle');
                if (titleElement && titleInput) {
                const title = titleInput.value || 'Your Reward Title';
                    titleElement.textContent = title;
                    console.log('Title updated:', title);
                }

                // Update description
                const descriptionElement = document.getElementById('previewDescription');
                if (descriptionElement && descriptionInput) {
                    const description = descriptionInput.value || 'Your reward description will appear here...';
                    descriptionElement.textContent = description;
                    console.log('Description updated:', description);
                }

                // Update amount
                const amountElement = document.getElementById('previewAmount');
                if (amountElement && amountInput) {
                const amount = amountInput.value || '0.00';
                    const formattedAmount = parseFloat(amount).toFixed(2);
                    amountElement.textContent = '{{ $setting->cur_sym }}' + formattedAmount;
                    console.log('Amount updated:', formattedAmount);
                }

                // Update type
                const typeElement = document.getElementById('previewType');
                if (typeElement && typeSelect) {
                const type = typeSelect.value;
                const typeText = type === 'digital' ? 'Digital Reward' : 'Physical Reward';
                    typeElement.textContent = typeText;
                    console.log('Type updated:', typeText);
                }
            }

            // Global refresh function
            window.refreshPreview = function() {
                console.log('refreshPreview called');
                updatePreview();
            };

            function updatePreviewImage(imageSrc) {
                const previewImageArea = document.getElementById('previewImageArea');
                if (imageSrc) {
                    previewImageArea.innerHTML = `<img src="${imageSrc}" class="img-fluid rounded" style="max-height: 150px; width: 100%; object-fit: cover;">`;
                } else {
                    previewImageArea.innerHTML = `
                        <div class="preview-placeholder">
                            <i class="fas fa-gift fa-2x"></i>
                            <p>No image selected</p>
                        </div>
                    `;
                }
            }

            // Initialize preview
            updatePreview();
        });
    </script>
@endpush

@push('page-style')
    <style>
        /* Required field styling - only for fields with required-field class */
        .required-field .form-label {
            color: #dc3545 !important;
            font-weight: 600;
        }
        
        .required-field .form-label:after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
        
        /* Required input styling */
        .form-control[required] {
            border-left: 3px solid #dc3545;
        }
        
        .form-control[required]:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        /* Required select styling */
        .form-select[required] {
            border-left: 3px solid #dc3545;
        }
        
        .form-select[required]:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        /* Error field styling */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .is-invalid:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        /* Preview Styles */
        .preview-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .preview-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        
        .preview-image {
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .preview-content {
            padding: 1.5rem;
        }
        
        .preview-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .preview-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            min-height: 2.5rem;
        }

        .preview-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .preview-category {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .preview-amount {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f8e6f;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .preview-image {
                height: 150px;
            }
            
            .preview-content {
                padding: 1rem;
            }
            
            .preview-title {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush 