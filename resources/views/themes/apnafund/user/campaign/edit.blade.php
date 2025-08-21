@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
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
                        
                        <!-- Previous Gallery Images -->
                         
                        <div class="form-group mb-4">
                            
                            <label class="form-label">Previous Gallery Images</label>
                            <div class="row">
                                @foreach ($campaign->gallery as $image)
                                    <div class="col-3 gallery-image mb-3">
                                        <div class="image-container">
                                            <div style="position: relative; display: inline-block;">
                                                <button type="button" class="remove-button" data-image="{{ json_encode($image) }}" data-action="{{ route('user.campaign.image.remove', $campaign->id) }}" title="Delete Image" style="position: absolute !important; top: -10px !important; right: -10px !important; width: 30px !important; height: 30px !important; background: #ff0000 !important; border: 2px solid #ffffff !important; border-radius: 50% !important; color: white !important; font-size: 14px !important; font-weight: bold !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; z-index: 99999999999999999 !important; box-shadow: 0 4px 8px rgba(0,0,0,0.3) !important; opacity: 1 !important; visibility: visible !important; pointer-events: auto !important;">
                                                    <i class="fas fa-times" style="font-size: 12px !important;"></i>
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
                            
                            <small class="text-muted">Supported files: JPG, JPEG, PNG. Image size: {{ getFileSize('campaign') }}px</small>
                        </div>

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

                        <div class="form-group">
                            <label for="gigDescription" class="form-label">Description *</label>
                            <textarea class="form-control ck-editor" id="gigDescription" name="description" rows="10" placeholder="Describe your gig, its purpose, and how donations will be used" required>{{ $campaign->description ?? '' }}</textarea>
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="targetAmount" class="form-label">Target Amount ($) *</label>
                                    <input type="number" name="goal_amount" class="form-control" id="targetAmount" value="{{ $campaign->goal_amount ?? '' }}" placeholder="5000" min="1" required>
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
                            <div class="preview-image">
                                @if($campaign->image)
                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" alt="{{ $campaign->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="fas fa-image"></i>
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
                                    <small class="text-muted">{{ number_format($percentage, 1) }}% of {{ bs('cur_sym') . showAmount($campaign->goal_amount) }} goal</small>
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
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/sweetalert2.min.css') }}">
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
    <script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
    <script src="{{ asset($activeThemeTrue . 'js/dropzone.min.js') }}"></script>
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

/* Image Container Styling */
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
    z-index: 99999999999999999 !important;
    box-shadow: 0 6px 12px rgba(0,0,0,0.5) !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}

.remove-button:hover {
    background: #cc0000 !important;
    transform: scale(1.1) !important;
    box-shadow: 0 8px 16px rgba(0,0,0,0.6) !important;
}

.remove-button i {
    font-size: 10px;
}

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

@push('page-script')
<script type="text/javascript">
    (function($) {
        "use strict"

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
        }

        // Dropzone Configuration
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

        // Remove Gallery Image
        $(document).on('click', '.remove-button', function(e) {
            e.preventDefault();
            
            let image = $(this).data('image')
            let url = $(this).data('action')
            let data = {
                image,
                _token: "{{ csrf_token() }}",
            }

            let _this = $(this)

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

        // Function to handle image deletion
        function deleteImage(_this, url, data) {
            // Show loading state
            _this.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.post(url, data, function(response) {
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
            }).fail(function(xhr, status, error) {
                // Reset button state
                _this.prop('disabled', false).html('<i class="fas fa-times"></i>');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error!', 'Failed to delete image. Please try again.', 'error');
                } else {
                    alert('Failed to delete image. Please try again.');
                }
            });
        }

        // Form Submission
        $('#editGigForm').on('submit', function(e) {
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
        });

    })(jQuery)
</script>
@endpush
