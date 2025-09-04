@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@section('style')
    <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.css"
/>
@endsection
@extends($activeTheme . 'layouts.dashboard')
@section('frontend')
    <!-- Edit Gig Tab -->
    <div class="tab-pane fade active show" id="edit" role="tabpanel">
        <div class="row">
            <div class="col-lg-8">
                <div class="content-card">
                    <h4 class="mb-4">Edit Donation Gig</h4>
                    
                    <form action="{{ route('user.campaign.update', $campaign->id) }}" method="POST" id="editGigForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Previous Gallery Images - COMMENTED OUT -->
                        {{-- 
                        <div class="form-group mb-4">
                            
                            <label class="form-label">Previous Gallery Images</label>
                            <div class="row">
                                @foreach ($campaign->gallery as $image)
                                    <div class="col-3 gallery-image mb-3">
                                        <div class="image-container">
                                            <div style="position: relative; display: inline-block;">
                                                <button type="button" class="remove-button" data-image="{{ json_encode($image) }}" data-action="{{ route('user.campaign.image.remove', $campaign->id) }}" title="Delete Image" style="position: absolute !important; top: -5px !important; right: -5px !important; width: 35px !important; height: 35px !important; background: #dc3545 !important; border: 3px solid #ffffff !important; border-radius: 50% !important; color: white !important; font-size: 16px !important; font-weight: bold !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; z-index: 99999999999999999 !important; box-shadow: 0 4px 12px rgba(220,53,69,0.4) !important; opacity: 1 !important; visibility: visible !important; pointer-events: auto !important; transition: all 0.3s ease !important;">
                                                    <i class="fas fa-times" style="font-size: 14px !important;"></i>
                                                </button>
                                                <img src="{{ getImage(getFilePath('campaign') . '/' . $image, getFileSize('campaign')) }}" alt="Gallery Image" class="img-fluid rounded">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Add New Gallery Images -->
                        <div class="form-group mb-4">
                            <label class="form-label">Add New Gallery Images</label>
                            
                            <!-- Simple File Input (Primary) -->
                            <div id="simpleFileInput">
                                <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                            </div>
                            
                            <!-- Dropzone (Fallback) -->
                            <div class="dropzone" id="gigImagesDropzone" style="display: none;">
                                <div class="dz-message">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h4>Drop images here or click to upload</h4>
                                    <p>Upload multiple images for your campaign gallery</p>
                                    <small>Supported: JPG, JPEG, PNG (Max: 5MB each)</small>
                                </div>
                            </div>
                            
                            <small class="text-muted">Supported files: JPG, JPEG, PNG. Image size: {{ getFileSize('campaign') }}px</small>
                        </div>
                        --}}

                        <!-- Campaign Details -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="gigTitle" class="form-label">Gig Title *</label>
                                    <input type="text" class="form-control" id="gigTitle" name="name" value="{{ $campaign->name ?? '' }}" placeholder="Enter a compelling title for your gig" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gigCategory" class="form-label">Category *</label>
                                    <select class="form-control form-select" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories ?? [] as $category)
                                            <option value="{{ $category->id }}" @selected(($campaign->category_id ?? '') == $category->id)>
                                                {{ __($category->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group mb-4">
                            <label for="campaignDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="gigDescription" name="description" rows="8" placeholder="Describe your campaign, its purpose, and how donations will be used" required>{{ $campaign->description ?? '' }}</textarea>
                        </div>

                        <!-- Main Campaign Image -->
                        <div class="form-group">
                            <label for="mainImage" class="form-label">Main Campaign Image</label>
                            <input type="file" class="form-control" id="mainImage" name="image" accept="image/*">
                            <small class="text-muted">Leave empty to keep the current image</small>
                            @if($campaign->image)
                                <div class="mt-2">
                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            @endif
                        </div>

                        <!-- Document Upload -->
                        <div class="form-group">
                            <label for="document" class="form-label">Document</label>
                            <input type="file" class="form-control" id="document" name="document" accept=".pdf">
                            <small class="text-muted">Supported file: PDF</small>
                            @if($campaign->document)
                                <div class="mt-2">
                                    <a href="{{ asset(getFilePath('document') . '/' . $campaign->document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf me-1"></i>View Current Document
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Video Upload Options -->
                        <div class="form-group mb-4">
                            <label class="form-label">Campaign Video</label>
                            
                            <!-- Video Upload Toggle -->
                            <div class="mb-3">
                                <div class="btn-group" role="group" aria-label="Video upload options">
                                    <input type="radio" class="btn-check" name="video_type" id="video_file" value="file" autocomplete="off" {{ !$campaign->youtube_url ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="video_file">Upload Video File</label>
                                    
                                    <input type="radio" class="btn-check" name="video_type" id="video_youtube" value="youtube" autocomplete="off" {{ $campaign->youtube_url ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="video_youtube">YouTube URL</label>
                                </div>
                            </div>
                            
                            <!-- File Upload Section -->
                            <div id="file_upload_section" style="{{ $campaign->youtube_url ? 'display: none;' : '' }}">
                                <input type="file" class="form-control mb-2" name="video" accept="video/*">
                                <small class="text-muted">Upload a video file (MP4, AVI, MOV, etc.)</small>
                                @if($campaign->video && !$campaign->youtube_url)
                                    <div class="mt-2">
                                        <video width="300" height="200" controls>
                                            <source src="{{ asset(getFilePath('campaign') . '/' . $campaign->video) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- YouTube URL Section -->
                            <div id="youtube_url_section" style="{{ !$campaign->youtube_url ? 'display: none;' : '' }}">
                                <input type="url" class="form-control mb-2" name="youtube_url" id="youtube_url" value="{{ $campaign->youtube_url ?? '' }}" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                                <small class="text-muted">Enter YouTube video URL for better streaming performance</small>
                                @if($campaign->youtube_url)
                                    <div class="mt-2">
                                        <div class="youtube-preview">
                                            <iframe width="300" height="200" src="{{ str_replace('watch?v=', 'embed/', $campaign->youtube_url) }}" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="targetAmount" class="form-label">Target Amount ({{ $setting->cur_sym }}) *</label>
                                    <input type="number" name="goal_amount" class="form-control" id="targetAmount" value="{{ number_format($campaign->goal_amount ?? 0, $setting->fraction_digit ?? 2, '.', '') }}" placeholder="5000" min="1" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="startDate" class="form-label">Start Date *</label>
                                    <input type="date" name="start_date" class="form-control" id="startDate" value="{{ $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="endDate" class="form-label">End Date *</label>
                                    <input type="date" name="end_date" class="form-control" id="endDate" value="{{ $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Update Campaign
                            </button>
                            <a href="{{ route('user.campaign.show', $campaign->slug) }}" class="btn btn-secondary">
                                <i class="fas fa-eye me-2"></i>View Campaign
                            </a>
                            <a href="{{ route('user.campaign.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="content-card">
                    <h5 class="mb-3">Campaign Preview</h5>
                    <div id="gigPreview">
                        <div class="preview-card">
                            <div class="preview-image" id="previewMainImage">
                                @if($campaign->image)
                                    <img id="previewImage" src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" alt="{{ $campaign->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px 10px 0 0;">
                                @else
                                    <i class="fas fa-image" id="previewImageIcon"></i>
                                    <img id="previewImage" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 10px 10px 0 0;">
                                @endif
                            </div>
                            <div class="preview-content">
                                <div class="preview-title">{{ $campaign->name ?? 'Campaign Title' }}</div>
                                <div class="preview-description">{{ Str::limit(strip_tags($campaign->description ?? ''), 100) }}</div>
                                <div class="preview-meta">
                                    <span class="preview-category">{{ $campaign->category->name ?? 'Category' }}</span>
                                    <span class="preview-amount">{{ bs('cur_sym') . showAmount($campaign->raised_amount) }}</span>
                                </div>
                                <div class="preview-progress">
                                    @php
                                        $percentage = $campaign->goal_amount > 0 ? ($campaign->raised_amount / $campaign->goal_amount) * 100 : 0;
                                        $percentage = min($percentage, 100);
                                    @endphp
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
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

@include('themes.apnafund.user.campaign.commonStyleScript')

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset('assets/universal/css/datepicker.css') }}">
    <!-- Load Dropzone CSS from CDN for reliability -->
    <link rel="stylesheet" href="https://unpkg.com/dropzone@6.0.0-beta.2/dist/dropzone.css">
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/sweetalert2.min.css') }}">
@endpush

@push('page-script-lib')
    <!-- CKEditor 4 CDN - Latest LTS Version -->
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

    <script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
    <script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
    <!-- Load Dropzone from CDN for reliability -->
    <script src="https://unpkg.com/dropzone@6.0.0-beta.2/dist/dropzone-min.js"></script>
    <script src="{{ asset($activeThemeTrue . 'js/sweetalert2.min.js') }}"></script>
@endpush

@push('page-style')
<style>
/* Content Card Styling */
.content-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.preview-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}

.preview-image {
    height: 150px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 2rem;
}

.preview-content {
    padding: 15px;
}

.preview-title {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 8px;
    color: #212529;
}

.preview-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 12px;
    line-height: 1.4;
}

.preview-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.preview-category {
    background: #e9ecef;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    color: #495057;
}

.preview-amount {
    font-weight: 600;
    color: #28a745;
}

.preview-progress {
    margin-top: 10px;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background: #e9ecef;
    margin-bottom: 5px;
}

.progress-bar {
    background: #28a745;
    border-radius: 4px;
}

/* Form Styling */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-control {
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 10px 12px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Image Container Styling - COMMENTED OUT */
/*
.image-container {
    position: relative !important;
    border-radius: 8px;
    overflow: visible !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
    display: block !important;
}

/* Wrapper for image and button */
.image-container > div {
    position: relative !important;
    display: inline-block !important;
}

.image-container:hover {
    transform: scale(1.02);
}

.remove-button {
    position: absolute !important;
    top: 5px !important;
    right: 5px !important;
    width: 40px !important;
    height: 40px !important;
    background: #ff0000 !important;
    border: 4px solid #ffffff !important;
    border-radius: 50% !important;
    color: white !important;
    font-size: 18px !important;
    font-weight: bold !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    z-index: 99999999999999999 !important;
    box-shadow: 0 6px 12px rgba(0,0,0,0.5) !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}

/* Additional specific rules to ensure visibility */
.gallery-image .image-container .remove-button,
.col-3 .image-container .remove-button,
.image-container button.remove-button {
    position: absolute !important;
    top: 5px !important;
    right: 5px !important;
    width: 40px !important;
    height: 40px !important;
    background: #dc3545 !important;
    border: 4px solid #ffffff !important;
    border-radius: 50% !important;
    color: white !important;
    font-size: 18px !important;
    font-weight: bold !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 99999999999999999 !important;
    box-shadow: 0 6px 12px rgba(220,53,69,0.5) !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transition: all 0.3s ease !important;
}

/* Hover effects for delete button */
.remove-button:hover {
    background: #c82333 !important;
    transform: scale(1.1) !important;
    box-shadow: 0 8px 16px rgba(220,53,69,0.6) !important;
}

/* Ensure button is always on top */
.image-container {
    position: relative !important;
    overflow: visible !important;
}

.image-container > div {
    position: relative !important;
    display: inline-block !important;
}

.remove-button:hover {
    background: #cc0000 !important;
    transform: scale(1.1) !important;
    box-shadow: 0 8px 16px rgba(0,0,0,0.6) !important;
}

.remove-button i {
    font-size: 10px;
}
*/

/* Dropzone Styling */
.dropzone {
    border: 2px dashed #dee2e6 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    min-height: 150px !important;
    padding: 30px 20px !important;
    text-align: center !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    margin-bottom: 15px !important;
}

.dropzone:hover {
    border-color: #007bff !important;
    background: #f8f9fa !important;
}

.dropzone.dz-drag-hover {
    border-color: #007bff !important;
    background: #e3f2fd !important;
}

/* Button Styling */
.btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #545b62;
    border-color: #545b62;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

/* CKEditor Styling */
.cke_chrome {
    border-radius: 6px !important;
    border: 1px solid #ced4da !important;
}

.cke_top {
    border-radius: 6px 6px 0 0 !important;
    background: #f8f9fa !important;
    border-bottom: 1px solid #ced4da !important;
}

.cke_bottom {
    border-radius: 0 0 6px 6px !important;
    background: #f8f9fa !important;
    border-top: 1px solid #ced4da !important;
}

.cke_editable {
    padding: 15px !important;
    min-height: 200px !important;
}

/* Responsive */
@media (max-width: 768px) {
    .content-card {
        padding: 20px;
    }
    
    .d-flex.gap-3 {
        flex-direction: column;
    }
    
    .d-flex.gap-3 .btn {
        margin-bottom: 10px;
    }
}
</style>
@endpush

@section('page-script')
<script type="text/javascript">
    (function($) {
        "use strict"

        console.log('Script starting...');
        console.log('jQuery available:', typeof $ !== 'undefined');
        console.log('CKEditor available:', typeof CKEDITOR !== 'undefined');
        
        // CKEditor License Configuration
        if (typeof CKEDITOR !== 'undefined') {
            // Set license key from environment variable (recommended for production)
            // Add this to your .env file: CKEDITOR_LICENSE_KEY=your_license_key_here
            CKEDITOR.licenseKey = '{{ config("app.ckeditor_license_key", "") }}';
            
            // If you want to hardcode the license key (not recommended for production):
            // CKEDITOR.licenseKey = 'YOUR_LICENSE_KEY_HERE';
        }
        
        // Wait for document to be ready
        $(document).ready(function() {
            
            // CKEditor Configuration - Initialize inside document ready
            function initializeCKEditor() {
                if (typeof CKEDITOR !== 'undefined') {
                    try {
                        // Check if element exists
                        if ($('#campaignDescription').length > 0) {
                            CKEDITOR.replace('campaignDescription', {
                                height: 300,
                                toolbar: [
                                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                                    { name: 'links', items: ['Link', 'Unlink'] },
                                    { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
                                    { name: 'styles', items: ['Format'] },
                                    { name: 'tools', items: ['Maximize'] }
                                ],
                                removeButtons: '',
                                removePlugins: 'elementspath,resize',
                                removeDialogTabs: 'image:advanced;link:advanced',
                                contentsCss: ['body { font-family: Arial, sans-serif; font-size: 14px; }'],
                                on: {
                                    instanceReady: function(evt) {
                                        console.log('CKEditor instance ready!');
                                        setupRealTimeDescriptionPreview();
                                    }
                                }
                            });
                            console.log('CKEditor initialized successfully');
                        } else {
                            console.error('campaignDescription textarea not found!');
                        }
                    } catch (error) {
                        console.error('Error initializing CKEditor:', error);
                        // Fallback to regular textarea
                        $('#campaignDescription').show();
                    }
                } else {
                    console.error('CKEditor is not loaded! Using fallback textarea');
                    // Show the original textarea if CKEditor fails
                    $('#campaignDescription').show();
                }
            }
            
            // Initialize CKEditor
            initializeCKEditor();
            
            // Fallback: If CKEditor doesn't load within 3 seconds, show regular textarea
            setTimeout(function() {
                if (typeof CKEDITOR === 'undefined' || !CKEDITOR.instances.campaignDescription) {
                    console.log('CKEditor failed to load, showing fallback textarea');
                    $('#campaignDescription').show().css({
                        'display': 'block',
                        'min-height': '300px',
                        'width': '100%',
                        'padding': '10px',
                        'border': '1px solid #ced4da',
                        'border-radius': '6px',
                        'font-family': 'Arial, sans-serif',
                        'font-size': '14px'
                    });
                    setupDescriptionPreview(); // Setup preview for regular textarea
                }
            }, 3000);

            // Initialize datepicker with error handling
            if (typeof $.fn.datepicker !== 'undefined') {
                $('.date-picker').datepicker({
                    dateFormat: 'dd-mm-yyyy',
                    minDate: new Date(),
                });

                $('.date-picker').on('input keyup keydown keypress', function() {
                    return false;
                });
            } else {
                console.error('Datepicker plugin not loaded!');
                // Fallback to HTML5 date inputs
                $('.date-picker').attr('type', 'date');
            }
            
            // Real-time preview updates
            $('#gigTitle').on('input', function() {
                $('.preview-title').text($(this).val() || '{{ $campaign->name ?? "Campaign Title" }}');
            });
            
            $('#targetAmount').on('input', function() {
                var amount = $(this).val() || '{{ number_format($campaign->goal_amount ?? 0, $setting->fraction_digit ?? 2, ".", "") }}';
                var currencySymbol = '{{ $setting->cur_sym }}';
                
                // Format amount for display
                var formattedAmount = parseFloat(amount).toFixed({{ $setting->fraction_digit ?? 2 }});
                $('.preview-amount').text(currencySymbol + formattedAmount);
                
                // Calculate progress based on raised amount vs goal amount
                var raisedAmount = {{ $campaign->raised_amount ?? 0 }};
                var percentage = amount > 0 ? (raisedAmount / amount) * 100 : 0;
                percentage = Math.min(percentage, 100);
                
                $('.progress-bar').css('width', percentage + '%');
                // $('.text-muted').text(percentage.toFixed(1) + '% of ' + currencySymbol + formattedAmount + ' goal');
            });
            
            $('select[name="category_id"]').on('change', function() {
                var categoryText = $(this).find('option:selected').text();
                // $('.preview-category').text(categoryText || '{{ $campaign->category->name ?? "Category" }}');
            });
            
            // Description preview update (for CKEditor and fallback textarea)
            function setupDescriptionPreview() {
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.campaignDescription) {
                    const editor = CKEDITOR.instances.campaignDescription;
                    if (editor && typeof editor.on === 'function') {
                        // CKEditor is available
                        editor.on('change', function() {
                            updateDescriptionPreview();
                        });
                        
                        editor.on('keyup', function() {
                            updateDescriptionPreview();
                        });
                        
                        editor.on('keydown', function() {
                            updateDescriptionPreview();
                        });
                        
                        editor.on('input', function() {
                            updateDescriptionPreview();
                        });
                        
                        // Also listen for paste events
                        editor.on('paste', function() {
                            setTimeout(function() {
                                updateDescriptionPreview();
                            }, 100);
                        });
                    }
                } else {
                    // Fallback to regular textarea
                    $('#campaignDescription').on('input keyup keydown change paste', function() {
                        updateDescriptionPreview();
                    });
                }
            }
            
            // Setup description preview
            setupDescriptionPreview();
            
            // Description preview update function
            function updateDescriptionPreview() {
                let description = '';
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.campaignDescription) {
                    const editor = CKEDITOR.instances.campaignDescription;
                    if (editor && typeof editor.getData === 'function') {
                        description = editor.getData();
                    } else {
                        description = $('#campaignDescription').val();
                    }
                } else {
                    description = $('#campaignDescription').val();
                }
                
                // Remove HTML tags and get plain text
                let plainText = description.replace(/<[^>]*>/g, '').trim();
                
                // Limit to 150 characters for preview
                if (plainText.length > 150) {
                    plainText = plainText.substring(0, 150) + '...';
                }
                
                $('.preview-description').text(plainText || '{{ Str::limit(strip_tags($campaign->description ?? ""), 100) }}');
            }
            
            // Add real-time typing preview with debouncing
            let typingTimer;
            function setupRealTimeDescriptionPreview() {
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.campaignDescription) {
                    const editor = CKEDITOR.instances.campaignDescription;
                    
                    if (editor && typeof editor.on === 'function') {
                        // Listen to all possible input events
                        editor.on('keyup', function() {
                            clearTimeout(typingTimer);
                            typingTimer = setTimeout(function() {
                                updateDescriptionPreview();
                            }, 50); // Update after 50ms of no typing
                        });
                        
                        editor.on('keydown', function() {
                            clearTimeout(typingTimer);
                            updateDescriptionPreview(); // Immediate update on keydown
                        });
                        
                        editor.on('input', function() {
                            clearTimeout(typingTimer);
                            typingTimer = setTimeout(function() {
                                updateDescriptionPreview();
                            }, 30); // Very fast update
                        });
                        
                        editor.on('paste', function() {
                            clearTimeout(typingTimer);
                            typingTimer = setTimeout(function() {
                                updateDescriptionPreview();
                            }, 100);
                        });
                    }
                }
            }
            
            // Initialize real-time preview after CKEditor is ready
            if (typeof CKEDITOR !== 'undefined') {
                CKEDITOR.on('instanceReady', function(evt) {
                    if (evt.editor && evt.editor.name === 'campaignDescription') {
                        setupRealTimeDescriptionPreview();
                    }
                });
            }
            
            // Initialize preview with existing campaign data
            function initializePreview() {
                // Get existing campaign data from form fields
                var title = $('#gigTitle').val() || '{{ $campaign->name ?? "Campaign Title" }}';
                var category = $('#gigCategory option:selected').text() || '{{ $campaign->category->name ?? "Category" }}';
                var amount = $('#targetAmount').val() || '{{ number_format($campaign->goal_amount ?? 0, $setting->fraction_digit ?? 2, ".", "") }}';
                var currencySymbol = '{{ $setting->cur_sym }}';
                
                // Format amount for display
                var formattedAmount = parseFloat(amount).toFixed({{ $setting->fraction_digit ?? 2 }});
                
                // Update preview with existing data
                $('.preview-title').text(title);
                $('.preview-category').text(category);
                $('.preview-amount').text(currencySymbol + formattedAmount);
                
                // Update progress bar with existing data
                var raisedAmount = {{ $campaign->raised_amount ?? 0 }};
                var goalAmount = {{ $campaign->goal_amount ?? 0 }};
                var percentage = goalAmount > 0 ? (raisedAmount / goalAmount) * 100 : 0;
                percentage = Math.min(percentage, 100);
                
                $('.progress-bar').css('width', percentage + '%');
                // $('.text-muted').text(percentage.toFixed(1) + '% of ' + currencySymbol + formattedAmount + ' goal');
                
                // Update description preview
                updateDescriptionPreview();
            }
            
            // Initialize preview when page loads
            initializePreview();
            
            // Main Image Preview Functionality
            $('#mainImage').on('change', function() {
                const file = this.files[0];
                const previewImage = $('#previewImage');
                const previewImageIcon = $('#previewImageIcon');
                
                if (file) {
                    // Check if file is an image
                    if (!file.type.startsWith('image/')) {
                        alert('Please select an image file');
                        this.value = '';
                        return;
                    }
                    
                    // Check file size (5MB limit)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Image size must be less than 5MB');
                        this.value = '';
                        return;
                    }
                    
                    // Create a FileReader to preview the image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.attr('src', e.target.result);
                        previewImage.show();
                        previewImageIcon.hide();
                    };
                    reader.readAsDataURL(file);
                } else {
                    // No file selected, show default icon
                    previewImage.hide();
                    previewImageIcon.show();
                    previewImage.attr('src', '');
                }
            });

        // Dropzone Configuration - COMMENTED OUT
        /*
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;
            
            const dropzone = new Dropzone(".dropzone", {
                url: "{{ route('user.campaign.gallery.upload') }}",
                paramName: "file",
                maxFilesize: 5, // MB
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                maxFiles: 10,
                parallelUploads: 3,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function() {
                    this.on("success", function(file, response) {
                        file.serverFileName = response.image;
                    });
                    this.on("error", function(file, errorMessage) {
                        console.error('Upload error:', errorMessage);
                    });
                }
            });
        }
        */

        // Remove Gallery Image - COMMENTED OUT
        /*
        $(document).on('click', '.remove-button', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            let image = $(this).data('image');
            let url = $(this).data('action');
            let data = {
                image: image,
                _token: "{{ csrf_token() }}",
            };

            let _this = $(this);

            console.log('Delete button clicked');
            console.log('Image data:', image);
            console.log('URL:', url);
            console.log('Button element:', _this);

            // Check if SweetAlert2 is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "Delete Image?",
                    text: "This will permanently delete this gallery image!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel",
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d"
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteImage(_this, url, data);
                    }
                });
            } else {
                // Fallback to regular confirm
                if (confirm("Are you sure you want to delete this image?")) {
                    deleteImage(_this, url, data);
                }
            }
        });

        // Test button for debugging (remove this after testing)
        $(document).on('click', '.test-delete', function() {
            console.log('Test delete button clicked');
            alert('Test delete button works!');
        });

        // Function to handle image deletion
        function deleteImage(_this, url, data) {
            console.log('Deleting image with data:', data);
            
            // Show loading state
            _this.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Response:', response);
                    
                    if (response.status === 'success') {
                        // Remove the image container with animation
                        _this.closest('.gallery-image').fadeOut(300, function() {
                            $(this).remove();
                            
                            // If only one image left, remove delete button
                            if ($('.gallery-image').length == 1) {
                                $('.gallery-image').find('.remove-button').remove();
                            }
                            
                            // Show success message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                alert('Image deleted successfully!');
                            }
                        });
                    } else {
                        // Reset button state
                        _this.prop('disabled', false).html('<i class="fas fa-times"></i>');
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error!', response.message, 'error');
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    
                    // Reset button state
                    _this.prop('disabled', false).html('<i class="fas fa-times"></i>');
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error!', 'Failed to delete image. Please try again.', 'error');
                    } else {
                        alert('Failed to delete image. Please try again.');
                    }
                }
            });
        }
        */

        // Preview function
        window.previewGig = function() {
            var title = $('#gigTitle').val() || '{{ $campaign->name ?? "Campaign Title" }}';
            var category = $('#gigCategory option:selected').text() || '{{ $campaign->category->name ?? "Category" }}';
            var amount = $('#targetAmount').val() || '{{ number_format($campaign->goal_amount ?? 0, $setting->fraction_digit ?? 2, ".", "") }}';
            var currencySymbol = '{{ $setting->cur_sym }}';
            
            // Format amount for display
            var formattedAmount = parseFloat(amount).toFixed({{ $setting->fraction_digit ?? 2 }});
            
            $('.preview-title').text(title);
            $('.preview-category').text(category);
            $('.preview-amount').text(currencySymbol + formattedAmount);
            
            // Update description preview
            updateDescriptionPreview();
            
            // Update progress bar with actual raised amount
            var raisedAmount = {{ $campaign->raised_amount ?? 0 }};
            var percentage = amount > 0 ? (raisedAmount / amount) * 100 : 0;
            percentage = Math.min(percentage, 100);
            
            $('.progress-bar').css('width', percentage + '%');
            // $('.text-muted').text(percentage.toFixed(1) + '% of ' + currencySymbol + formattedAmount + ' goal');
            
            // Check if main image is selected and show preview
            const mainImageFile = $('#mainImage')[0].files[0];
            if (mainImageFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result).show();
                    $('#previewImageIcon').hide();
                };
                reader.readAsDataURL(mainImageFile);
            }
        };

        // Form Submission
        $('#editGigForm').on('submit', function(e) {
            e.preventDefault();
            
            // Disable HTML5 validation
            this.setAttribute('novalidate', true);
            
            // Validate form
            const validation = validateForm();
            
            if (!validation.isValid) {
                // Show first error in toast
                if (validation.errors.length > 0) {
                    showToast('error', validation.errors[0]);
                }
                return false;
            }
            
            // Show loading state
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
            
            // Get form data
            var formData = new FormData(this);
            
            // Add CKEditor content to form data
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.campaignDescription) {
                const editor = CKEDITOR.instances.campaignDescription;
                if (editor && typeof editor.getData === 'function') {
                    formData.set('description', editor.getData());
                }
            }
            
            // Submit form via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Success toast
                    showToast('success', '✅ Campaign updated successfully!');
                    
                    // Redirect to campaigns list or show success message
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '{{ route("user.campaign.index") }}';
                    }
                },
                error: function(xhr, status, error) {
                    // Error toast
                    var errorMessage = '❌ Error occurred while updating campaign!';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = '❌ ' + xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Handle validation errors from server
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0];
                        if (Array.isArray(firstError)) {
                            errorMessage = '❌ ' + firstError[0];
                        } else {
                            errorMessage = '❌ ' + firstError;
                        }
                    } else if (xhr.responseText) {
                        errorMessage = '❌ ' + xhr.responseText;
                    }
                    
                    showToast('error', errorMessage);
                    
                    // Reset button state
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Campaign');
                }
            });
        });

        // Form Validation Function
        function validateForm() {
            let isValid = true;
            let errors = [];
            
            // Clear previous validation states
            $('.form-control').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            
            // Validate Gig Title
            const gigTitle = $('#gigTitle').val().trim();
            if (!gigTitle) {
                $('#gigTitle').addClass('is-invalid');
                $('#gigTitle').after('<div class="invalid-feedback">Gig title is required</div>');
                errors.push('Gig title is required');
                isValid = false;
            } else if (gigTitle.length < 10) {
                $('#gigTitle').addClass('is-invalid');
                $('#gigTitle').after('<div class="invalid-feedback">Gig title must be at least 10 characters long</div>');
                errors.push('Gig title must be at least 10 characters long');
                isValid = false;
            } else {
                $('#gigTitle').addClass('is-valid');
            }
            
            // Validate Category
            const category = $('select[name="category_id"]').val();
            if (!category) {
                $('select[name="category_id"]').addClass('is-invalid');
                $('select[name="category_id"]').after('<div class="invalid-feedback">Please select a category</div>');
                errors.push('Please select a category');
                isValid = false;
            } else {
                $('select[name="category_id"]').addClass('is-valid');
            }
            
            // Validate Description
            let description = '';
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.campaignDescription) {
                const editor = CKEDITOR.instances.campaignDescription;
                if (editor && typeof editor.getData === 'function') {
                    description = editor.getData().replace(/<[^>]*>/g, '').trim();
                } else {
                    description = $('#campaignDescription').val().trim();
                }
            } else {
                description = $('#campaignDescription').val().trim();
            }
            
            if (!description) {
                $('#campaignDescription').addClass('is-invalid');
                $('#campaignDescription').after('<div class="invalid-feedback">Description is required</div>');
                errors.push('Description is required');
                isValid = false;
            } else if (description.length < 50) {
                $('#campaignDescription').addClass('is-invalid');
                $('#campaignDescription').after('<div class="invalid-feedback">Description must be at least 50 characters long</div>');
                errors.push('Description must be at least 50 characters long');
                isValid = false;
            } else {
                $('#campaignDescription').addClass('is-valid');
            }
            
            // Validate Target Amount
            const targetAmount = $('#targetAmount').val();
            if (!targetAmount) {
                $('#targetAmount').addClass('is-invalid');
                $('#targetAmount').after('<div class="invalid-feedback">Target amount is required</div>');
                errors.push('Target amount is required');
                isValid = false;
            } else if (targetAmount < 1) {
                $('#targetAmount').addClass('is-invalid');
                $('#targetAmount').after('<div class="invalid-feedback">Target amount must be at least $1</div>');
                errors.push('Target amount must be at least $1');
                isValid = false;
            } else {
                $('#targetAmount').addClass('is-valid');
            }
            
            // Validate Start Date
            const startDate = $('#startDate').val();
            if (!startDate) {
                $('#startDate').addClass('is-invalid');
                $('#startDate').after('<div class="invalid-feedback">Start date is required</div>');
                errors.push('Start date is required');
                isValid = false;
            } else {
                $('#startDate').addClass('is-valid');
            }
            
            // Validate End Date
            const endDate = $('#endDate').val();
            if (!endDate) {
                $('#endDate').addClass('is-invalid');
                $('#endDate').after('<div class="invalid-feedback">End date is required</div>');
                errors.push('End date is required');
                isValid = false;
            } else {
                const startDateObj = new Date(startDate);
                const endDateObj = new Date(endDate);
                if (endDateObj <= startDateObj) {
                    $('#endDate').addClass('is-invalid');
                    $('#endDate').after('<div class="invalid-feedback">End date must be after start date</div>');
                    errors.push('End date must be after start date');
                    isValid = false;
                } else {
                    $('#endDate').addClass('is-valid');
                }
            }
            
            return { isValid, errors };
        }

        // Show Toast Function
        function showToast(type, message) {
            if (typeof iziToast !== 'undefined') {
                iziToast[type]({
                    message: message,
                    position: "topRight",
                    timeout: 5000
                });
            } else {
                // Fallback to alert if iziToast is not available
                alert(message);
            }
        }

        // Handle video type toggle
        $('input[name="video_type"]').change(function() {
            if ($(this).val() === 'file') {
                $('#file_upload_section').show();
                $('#youtube_url_section').hide();
                $('#youtube_url').removeAttr('required');
            } else {
                $('#file_upload_section').hide();
                $('#youtube_url_section').show();
                $('#youtube_url').attr('required', false); // Optional for now
            }
        });

        // YouTube URL validation and preview
        $('#youtube_url').on('blur', function() {
            const url = $(this).val();
            if (url && isValidYouTubeUrl(url)) {
                updateYouTubePreview(url);
            }
        });

        function isValidYouTubeUrl(url) {
            const pattern = /^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/;
            return pattern.test(url);
        }

        function updateYouTubePreview(url) {
            let embedUrl = url;
            if (url.includes('watch?v=')) {
                embedUrl = url.replace('watch?v=', 'embed/');
            } else if (url.includes('youtu.be/')) {
                embedUrl = url.replace('youtu.be/', 'youtube.com/embed/');
            }
            
            const preview = `<iframe width="300" height="200" src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
            $('.youtube-preview').html(preview);
        }

        }); // End of document ready

    })(jQuery)
</script>
<script src="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.js"></script>
    <script>
        alert('jodit');
        const editor = Jodit.make('#gigDescription', {
            buttons: ['bold', 'italic', 'underline', '|', 'ul', 'ol']
        });
    </script>
@endsection
