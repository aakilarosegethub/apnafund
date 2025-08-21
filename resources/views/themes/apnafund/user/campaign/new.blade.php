@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
@section('frontend')
    <!-- Create Gig Tab -->
     <style>
        .input-group-text {
    display: flex
;
    align-items: center;
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: var(--bs-body-color);
    text-align: center;
    white-space: nowrap;
    background-color: var(--bs-tertiary-bg);
    border: var(--bs-border-width) solid var(--bs-border-color);
    border-radius: var(--bs-border-radius);
}
    </style>
                <div class="tab-pane fade active show" id="create" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="content-card">
                                <h4 class="mb-4">Create New Donation Gig</h4>
                                
                                <form action="{{ route('user.campaign.store') }}" method="POST" id="createGigForm" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="gigTitle" class="form-label">Gig Title *</label>
                                                <input type="text" class="form-control" id="gigTitle" name="name" placeholder="Enter a compelling title for your gig" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="gigCategory" class="form-label">Category *</label>
                                                <select class="form--control form-select" name="category_id" id="gigCategory" required>
                                                    <option value="" selected>@lang('Select Category')</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                                            {{ __(@$category->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="gigDescription" class="form-label">Description *</label>
                                        <textarea class="form-control ck-editor" id="gigDescription" name="description" rows="10" placeholder="Describe your gig, its purpose, and how donations will be used" required style="display: block; min-height: 300px;">@php echo old('description') @endphp</textarea>
                                    </div>

                                    <!-- Main Campaign Image -->
                                    <div class="form-group">
                                        <label for="mainImage" class="form-label">Main Campaign Image *</label>
                                        <input type="file" class="form-control" id="mainImage" name="image" accept="image/*" required>
                                        <small class="text-muted">This will be the primary image displayed for your campaign</small>
                                    </div>



                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="targetAmount" class="form-label">Target Amount ({{ $setting->cur_sym }}) *</label>
                                                <input type="number" name="goal_amount" class="form-control" id="targetAmount" placeholder="5000" min="1" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="startDate" class="form-label">Start Date *</label>
                                                <input type="date" name="start_date" class="form-control" id="startDate" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="endDate" class="form-label">End Date *</label>
                                                <input type="date" name="end_date" class="form-control" id="endDate" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gallery Images Section - COMMENTED OUT -->
                                    {{-- 
                                    <div class="form-group gallery-section">
                                        <label class="form-label">Gallery Images *</label>
                                        
                                        <!-- Simple Multiple File Input (Primary) -->
                                        <div id="simpleFileInput">
                                            <input type="file" class="form-control" name="gallery_images[]" id="galleryImages" accept="image/*" multiple required>
                                            <small class="text-muted">Select multiple images for your campaign gallery (JPG, JPEG, PNG - Max 5MB each)</small>
                                        </div>
                                        
                                        <!-- Dropzone (Fallback) -->
                                        <div class="dropzone" id="" style="display: none;">
                                            <div class="dz-message">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <h4>Drop images here or click to upload</h4>
                                                <p>Upload multiple images for your campaign gallery</p>
                                                <small>Supported: JPG, JPEG, PNG (Max: 5MB each)</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Fallback file input in case Dropzone fails -->
                                        <div id="fallbackFileInput" style="display: none;">
                                            <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                                            <small class="text-muted">Select multiple images for your campaign gallery</small>
                                        </div>
                                        <small class="text-muted">* Minimum one gallery image is required</small>
                                    </div>
                                    --}}

                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>Submit 
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="previewGig()">
                                            <i class="fas fa-eye me-2"></i>Preview
                                        </button>
                                        <button type="button" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                                                            <div class="col-lg-4">
                            <div class="content-card">
                                <h5 class="mb-3">Preview</h5>
                                <div id="gigPreview">
                                    <div class="preview-card">
                                        <div class="preview-image" id="previewMainImage">
                                            <i class="fas fa-image" id="previewImageIcon"></i>
                                            <img id="previewImage" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 10px 10px 0 0;">
                                        </div>
                                        <div class="preview-content">
                                            <div class="preview-title">Your Gig Title</div>
                                            <div class="preview-description">Your gig description will appear here...</div>
                                            <div class="preview-meta">
                                                <span class="preview-category">Category</span>
                                                <span class="preview-amount">$0</span>
                                            </div>
                                            <div class="preview-progress">
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 0%"></div>
                                                </div>
                                                <small class="text-muted">0% of $0 goal</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@endsection

@include($activeTheme . 'user.campaign.commonStyleScript')

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset('assets/universal/css/datepicker.css') }}">
    <!-- Load Dropzone CSS from CDN for reliability -->
    <link rel="stylesheet" href="https://unpkg.com/dropzone@6.0.0-beta.2/dist/dropzone.css">
@endpush

@push('page-script-lib')
<!-- CKEditor 4 CDN - Latest LTS Version -->
<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

<script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
<script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
    <!-- Load Dropzone from CDN for reliability -->
    <script src="https://unpkg.com/dropzone@6.0.0-beta.2/dist/dropzone-min.js"></script>
@endpush

<style>
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

.dz-message {
    margin: 0 !important;
}

.dz-message i {
    font-size: 2rem;
    color: #6c757d;
    margin-bottom: 10px;
    display: block;
}

.dz-message h4 {
    color: #495057;
    margin-bottom: 5px;
    font-weight: 500;
    font-size: 1rem;
}

.dz-message p {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.dz-message small {
    color: #adb5bd;
    font-size: 0.8rem;
}

.dz-preview {
    margin: 5px !important;
}

.dz-preview .dz-image {
    border-radius: 6px !important;
    overflow: hidden !important;
}

.dz-preview .dz-details {
    color: #333 !important;
    font-size: 0.85rem !important;
}

.dz-preview .dz-remove {
    color: #dc3545 !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    border: 1px solid #dc3545 !important;
    border-radius: 4px !important;
    padding: 3px 8px !important;
    font-size: 0.8rem !important;
    transition: all 0.3s ease !important;
}

.dz-preview .dz-remove:hover {
    background: #dc3545 !important;
    color: white !important;
}

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

/* File Input Styling */
.form-control[type="file"] {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background-color: #fff;
}

.form-control[type="file"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Gallery Section Styling - COMMENTED OUT */
/*
.gallery-section {
    margin-bottom: 30px;
}

.gallery-section .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
}
*/

/* Form Group Spacing */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

/* Button Styling */
.btn {
    border-radius: 8px;
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

/* Input Styling */
.form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Preview Card Enhancements */
.preview-card {
    transition: all 0.3s ease;
}

.preview-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* CKEditor Specific Styling */
.cke_chrome {
    border: 1px solid #ced4da !important;
    border-radius: 6px !important;
    box-shadow: none !important;
}

.cke_top {
    background: #f8f9fa !important;
    border-bottom: 1px solid #ced4da !important;
    border-radius: 6px 6px 0 0 !important;
}

.cke_bottom {
    background: #f8f9fa !important;
    border-top: 1px solid #ced4da !important;
    border-radius: 0 0 6px 6px !important;
}

.cke_editor_gigDescription {
    margin-bottom: 20px !important;
}

/* Ensure CKEditor is visible */
.cke {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Validation Error Styling */
.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}
</style>

@section('page-script')
    <script type="text/javascript">
        
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
                        if ($('#gigDescription').length > 0) {
                            CKEDITOR.replace('gigDescription', {
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
                                        setupRealTimeDescriptionPreview();
                                    }
                                }
                            });
                        } else {
                            console.error('gigDescription textarea not found!');
                        }
                    } catch (error) {
                        console.error('Error initializing CKEditor:', error);
                        // Fallback to regular textarea
                        $('#gigDescription').show();
                    }
                } else {
                    console.error('CKEditor is not loaded! Using fallback textarea');
                    // Show the original textarea if CKEditor fails
                    $('#gigDescription').show();
                }
            }
            
            // Initialize CKEditor
            initializeCKEditor();
            
            // Fallback: If CKEditor doesn't load within 3 seconds, show regular textarea
            setTimeout(function() {
                if (typeof CKEDITOR === 'undefined' || !CKEDITOR.instances.gigDescription) {
                    $('#gigDescription').show().css({
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
                $('.preview-title').text($(this).val() || 'Your Gig Title');
            });
            
            // Yeh code targetAmount input field ki value change hone par preview section ko update karta hai.
            // Jab user targetAmount field mein koi value dalta hai, to yeh function chalta hai:
            // 1. amount variable mein input ki value store hoti hai (agar khali ho to '0' set hota hai).
            // 2. currencySymbol variable mein currency ka symbol aata hai (Laravel variable se).
            // 3. '.preview-amount' element mein updated amount show hota hai (currency ke sath).
            // 4. progress bar ki width update hoti hai (lekin abhi yahan 0/amount hai, to hamesha 0% hi rahegi).
            // 5. '.text-muted' element mein goal ka text update hota hai (0% of $amount goal).
            $('#targetAmount').on('input', function() {
                var amount = $(this).val() || '0';
                var currencySymbol = '{{ $setting->cur_sym }}';
                $('.preview-amount').text(currencySymbol + amount);
                var progress = amount > 0 ? Math.min((0 / amount) * 100, 100) : 0;
                $('.progress-bar').css('width', progress + '%');
                // $('.text-muted').text('0% of ' + currencySymbol + amount + ' goal');
            });
            
            $('#gigCategory').on('change', function() {
                var categoryText = $(this).find('option:selected').text();
                $('.preview-category').text(categoryText || 'Category');
            });
            
            // Description preview update (for CKEditor and fallback textarea)
            function setupDescriptionPreview() {
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.gigDescription) {
                    const editor = CKEDITOR.instances.gigDescription;
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
                    $('#gigDescription').on('input keyup keydown change paste', function() {
                        updateDescriptionPreview();
                    });
                }
            }
            
            // Setup description preview
            setupDescriptionPreview();
            
            // Description preview update function
            function updateDescriptionPreview() {
                let description = '';
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.gigDescription) {
                    const editor = CKEDITOR.instances.gigDescription;
                    if (editor && typeof editor.getData === 'function') {
                        description = editor.getData();
                    } else {
                        description = $('#gigDescription').val();
                    }
                } else {
                    description = $('#gigDescription').val();
                }
                
                // Remove HTML tags and get plain text
                let plainText = description.replace(/<[^>]*>/g, '').trim();
                
                // Limit to 150 characters for preview
                if (plainText.length > 150) {
                    plainText = plainText.substring(0, 150) + '...';
                }
                
                $('.preview-description').text(plainText || 'Your gig description will appear here...');
            }
            
            // Add real-time typing preview with debouncing
            let typingTimer;
            function setupRealTimeDescriptionPreview() {
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.gigDescription) {
                    const editor = CKEDITOR.instances.gigDescription;
                    
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
                    if (evt.editor && evt.editor.name === 'gigDescription') {
                        setupRealTimeDescriptionPreview();
                    }
                });
            }
            
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
            const category = $('#gigCategory').val();
            if (!category) {
                $('#gigCategory').addClass('is-invalid');
                $('#gigCategory').after('<div class="invalid-feedback">Please select a category</div>');
                errors.push('Please select a category');
                isValid = false;
            } else {
                $('#gigCategory').addClass('is-valid');
            }
            
            // Validate Description
            let description = '';
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.gigDescription) {
                const editor = CKEDITOR.instances.gigDescription;
                if (editor && typeof editor.getData === 'function') {
                    description = editor.getData().replace(/<[^>]*>/g, '').trim();
                } else {
                    description = $('#gigDescription').val().trim();
                }
            } else {
                description = $('#gigDescription').val().trim();
            }
            
            if (!description) {
                $('#gigDescription').addClass('is-invalid');
                $('#gigDescription').after('<div class="invalid-feedback">Description is required</div>');
                errors.push('Description is required');
                isValid = false;
            } else if (description.length < 50) {
                $('#gigDescription').addClass('is-invalid');
                $('#gigDescription').after('<div class="invalid-feedback">Description must be at least 50 characters long</div>');
                errors.push('Description must be at least 50 characters long');
                isValid = false;
            } else {
                $('#gigDescription').addClass('is-valid');
            }
            
            // Validate Main Image
            const mainImage = $('#mainImage')[0].files[0];
            if (!mainImage) {
                $('#mainImage').addClass('is-invalid');
                $('#mainImage').after('<div class="invalid-feedback">Main campaign image is required</div>');
                errors.push('Main campaign image is required');
                isValid = false;
            } else {
                // Check file size (5MB limit)
                if (mainImage.size > 5 * 1024 * 1024) {
                    $('#mainImage').addClass('is-invalid');
                    $('#mainImage').after('<div class="invalid-feedback">Main image size must be less than 5MB</div>');
                    errors.push('Main image size must be less than 5MB');
                    isValid = false;
                } else {
                    $('#mainImage').addClass('is-valid');
                }
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
                const today = new Date();
                const selectedDate = new Date(startDate);
                if (selectedDate < today) {
                    $('#startDate').addClass('is-invalid');
                    $('#startDate').after('<div class="invalid-feedback">Start date cannot be in the past</div>');
                    errors.push('Start date cannot be in the past');
                    isValid = false;
                } else {
                    $('#startDate').addClass('is-valid');
                }
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
            
            // Validate Gallery Images - COMMENTED OUT
            /*
            const galleryImages = $('#galleryImages')[0].files;
            if (!galleryImages || galleryImages.length === 0) {
                $('#galleryImages').addClass('is-invalid');
                $('#galleryImages').after('<div class="invalid-feedback">At least one gallery image is required</div>');
                errors.push('At least one gallery image is required');
                isValid = false;
            } else {
                // Check each gallery image
                for (let i = 0; i < galleryImages.length; i++) {
                    if (galleryImages[i].size > 5 * 1024 * 1024) {
                        $('#galleryImages').addClass('is-invalid');
                        $('#galleryImages').after('<div class="invalid-feedback">Gallery image size must be less than 5MB each</div>');
                        errors.push('Gallery image size must be less than 5MB each');
                        isValid = false;
                        break;
                    }
                }
                if (isValid) {
                    $('#galleryImages').addClass('is-valid');
                }
            }
            */
            
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

        // Form Submission with Validation
        $(document).on('submit', '#createGigForm', function(e) {
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
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');
            
            // Get form data
            var formData = new FormData(this);
            
            // Add CKEditor content to form data
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.gigDescription) {
                const editor = CKEDITOR.instances.gigDescription;
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
                    showToast('success', '✅ Campaign created successfully!');
                    
                    // Redirect to campaigns list or show success message
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '{{ route("user.campaign.index") }}';
                    }
                },
                error: function(xhr, status, error) {
                    // Error toast
                    var errorMessage = '❌ Error occurred while creating campaign!';
                    
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
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Submit');
                }
            });
        });

        // Preview function
        window.previewGig = function() {
            var title = $('#gigTitle').val() || 'Your Gig Title';
            var category = $('#gigCategory option:selected').text() || 'Category';
            var amount = $('#targetAmount').val() || '0';
            var currencySymbol = '{{ $setting->cur_sym }}';
            
            $('.preview-title').text(title);
            $('.preview-category').text(category);
            $('.preview-amount').text(currencySymbol + amount);
            
            // Update description preview
            updateDescriptionPreview();
            
            // Update progress bar
            var progress = amount > 0 ? Math.min((0 / amount) * 100, 100) : 0;
            $('.progress-bar').css('width', progress + '%');
            $('.text-muted').text('0% of ' + currencySymbol + amount + ' goal');
            
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
        
        // Dropzone Configuration - COMMENTED OUT
        /*
        console.log('Checking Dropzone availability...');
        console.log('Dropzone type:', typeof Dropzone);
        
        // Use simple file input by default to avoid Dropzone issues
        $('#simpleFileInput').show();
        $('#gigImagesDropzone').hide();
        
        // Optional: Try to initialize Dropzone if needed (commented out to avoid errors)
        if (typeof Dropzone === 'undefined') {
            console.error('Dropzone is not loaded! Using simple file input.');
        } else {
            console.log('Dropzone is available, but using simple file input for reliability.');
            Dropzone.autoDiscover = false;

            // Create a simple Dropzone without URL first
            try {
                const gigImagesDropzone = new Dropzone("#gigImagesDropzone", {
                    url: "{{ url('user/campaign/gallery-upload') }}",
                    paramName: "file",
                    maxFilesize: 5, // MB
                    acceptedFiles: "image/*",
                    addRemoveLinks: true,
                    maxFiles: 10, // Maximum 10 images
                    parallelUploads: 3, // Upload 3 files at once
                    dictDefaultMessage: "Drop images here or click to upload",
                    dictRemoveFile: "Remove",
                    dictCancelUpload: "Cancel",
                    dictFileTooBig: "File is too big (@{{filesize}}MB). Max filesize: @{{maxFilesize}}MB.",
                    dictInvalidFileType: "You can't upload files of this type.",
                    dictMaxFilesExceeded: "You can not upload any more files.",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    init: function() {
                        this.on("success", function(file, response) {
                            file.serverFileName = response.image;
                            console.log('File uploaded successfully:', response.image);
                        });
                        this.on("error", function(file, errorMessage) {
                            console.error('Upload error:', errorMessage);
                            alert('❌ Error uploading file: ' + errorMessage);
                        });
                        this.on("removedfile", function(file) {
                            if (file.serverFileName) {
                                $.ajax({
                                    url: "{{ url('user/campaign/gallery-remove') }}",
                                    type: "POST",
                                    data: {
                                        file: file.serverFileName,
                                        _token: $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(response) {
                                        console.log('File removed successfully');
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error removing file:', error);
                                    }
                                });
                            }
                        });
                        this.on("addedfile", function(file) {
                            console.log('File added to dropzone:', file.name);
                        });
                    }
                });
                console.log('Dropzone initialized successfully');
            } catch (error) {
                console.error('Error initializing Dropzone:', error);
                console.log('Using simple file input instead.');
            }
        }
        */
    </script>
@endsection
