@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
@section('frontend')
    <!-- Create Gig Tab -->
                <div class="tab-pane fade active show" id="create" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="content-card">
                                <h4 class="mb-4">Create New Donation Gig</h4>
                                
                                <form action="{{ route('user.campaign.store') }}" method="POST" id="createGigForm" enctype="multipart/form-data">
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
                                                <select class="form--control form-select" name="category_id" required>
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
                                        <textarea class="form-control ck-editor" id="gigDescription" name="description" rows="10" placeholder="Describe your gig, its purpose, and how donations will be used" required>
                                            @php echo old('description') @endphp
                                        </textarea>
                                    </div>

                                    <!-- Main Campaign Image -->
                                    <div class="form-group">
                                        <label for="mainImage" class="form-label">Main Campaign Image *</label>
                                        <input type="file" class="form-control" id="mainImage" name="image" accept="image/*" required>
                                        <small class="text-muted">This will be the primary image displayed for your campaign</small>
                                    </div>
                                    <div class="row">
                                    <div class="col-12">
                                    <label class="form--label required" for="preferred_amounts[]">@lang('Preferred Amounts')</label>
                                    <div class="d-flex gap-2">
                                        <div class="input--group w-100">
                                            <span class="input-group-text">{{ @$setting->cur_sym }}</span>
                                            <input type="number" step="any" min="0" class="form--control" name="preferred_amounts[]" value="" required>
                                        </div>
                                        <button type="button" class="btn btn-success rounded-circle" id="addMoreAmounts" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="add-more-amounts"></div>
                                </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="targetAmount" class="form-label">Target Amount ($) *</label>
                                                <input type="number" name="goal_amount" class="form-control" id="targetAmount" placeholder="5000" min="1" required>
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

                                    <!-- Gallery Images Section -->
                                    <div class="form-group gallery-section">
                                        <label class="form-label">Gallery Images *</label>
                                        
                                        <!-- Simple Multiple File Input (Primary) -->
                                        <div id="simpleFileInput">
                                            <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple required>
                                            <small class="text-muted">Select multiple images for your campaign gallery (JPG, JPEG, PNG - Max 5MB each)</small>
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
                                        
                                        <!-- Fallback file input in case Dropzone fails -->
                                        <div id="fallbackFileInput" style="display: none;">
                                            <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                                            <small class="text-muted">Select multiple images for your campaign gallery</small>
                                        </div>
                                        <small class="text-muted">* Minimum one gallery image is required</small>
                                    </div>

                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fas fa-save me-2"></i>Save as Draft
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
                                        <div class="preview-image">
                                            <i class="fas fa-image"></i>
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
    <script src="{{ asset($activeThemeTrue . 'js/ckeditor.js') }}"></script>
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

/* Gallery Section Styling */
.gallery-section {
    margin-bottom: 30px;
}

.gallery-section .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 10px;
}
</style>

@section('page-script')
    <script type="text/javascript">
        console.log('Script starting...');
        console.log('jQuery available:', typeof $ !== 'undefined');
        
        (function($) {

            // Add More Preferred Amounts On Campaign Create Start
            $('#addMoreAmounts').on('click', function () {
                $('.add-more-amounts').append(`
                    <div class="extra-amount d-flex gap-2 pt-2">
                        <div class="input--group w-100">
                            <span class="input-group-text">{{ $setting->cur_sym }}</span>
                            <input type="number" step="any" min="0" class="form--control" name="preferred_amounts[]" required>
                        </div>
                        <button type="button" class="btn btn-danger rounded-circle close-extra-amount" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `)
            })

            $(document).on('click', '.close-extra-amount', function () {
                $(this).closest('.extra-amount').remove()
            })
            // Add More Preferred Amounts On Campaign Create End

            $('.date-picker').datepicker({
                dateFormat: 'dd-mm-yyyy',
                minDate: new Date(),
            })

            $('.date-picker').on('input keyup keydown keypress', function() {
                return false
            })
        })(jQuery)

        // Form Submission with Alerts
        $('#createGigForm').on('submit', function(e) {
            e.preventDefault();
            
            // Check if gallery images are selected (for simple file input)
            var fileInput = $('input[name="gallery_images[]"]');
            if (fileInput.length > 0 && fileInput[0].files.length === 0) {
                alert('❌ Please select at least one gallery image!');
                return false;
            }
            
            // Check if gallery images are uploaded (only if Dropzone is available)
            if (typeof gigImagesDropzone !== 'undefined') {
                var uploadedFiles = gigImagesDropzone.getAcceptedFiles();
                if (uploadedFiles.length === 0) {
                    alert('❌ Please upload at least one gallery image!');
                    return false;
                }
            }
            
            // Show loading state
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');
            
            // Get form data
            var formData = new FormData(this);
            
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
                    // Success alert
                    alert('✅ Campaign created successfully!');
                    
                    // Redirect to campaigns list or show success message
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '{{ route("user.campaign.index") }}';
                    }
                },
                error: function(xhr, status, error) {
                    // Error alert
                    var errorMessage = '❌ Error occurred while creating campaign!';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = '❌ ' + xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMessage = '❌ ' + xhr.responseText;
                    }
                    
                    alert(errorMessage);
                    
                    // Reset button state
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Save as Draft');
                }
            });
        });

        // CKEditor Initialization
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.replace('gigDescription', {
                height: 300,
                removePlugins: 'elementspath,resize',
                toolbarGroups: [
                    { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                    { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                    { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                    { name: 'forms', groups: [ 'forms' ] },
                    '/',
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                    { name: 'links', groups: [ 'links' ] },
                    { name: 'insert', groups: [ 'insert' ] },
                    '/',
                    { name: 'styles', groups: [ 'styles' ] },
                    { name: 'colors', groups: [ 'colors' ] },
                    { name: 'tools', groups: [ 'tools' ] },
                    { name: 'others', groups: [ 'others' ] }
                ],
                removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About'
            });
        } else {
            console.error('CKEditor is not loaded!');
        }

        // Dropzone Configuration
        console.log('Checking Dropzone availability...');
        console.log('Dropzone type:', typeof Dropzone);
        
        // Use simple file input by default to avoid Dropzone issues
        $('#simpleFileInput').show();
        $('#gigImagesDropzone').hide();
        
        // Optional: Try to initialize Dropzone if needed (commented out to avoid errors)
        /*
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
