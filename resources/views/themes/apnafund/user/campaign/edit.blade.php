@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
@section('style')
<meta http-equiv="X-UA-Compatible" content="IE=edge">
@endsection
@section('frontend')
<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/tbbnzs0lggltrfknci0wuhmwxhod5797lrzvw9epadovnya5/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea#gigDescription',
    height: 600, // Increased height for typing area
    min_height: 600, // Minimum height
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount', 'image', 'mediaembed',
      // Your account includes a free trial of TinyMCE premium features
      // Try the most popular premium features until Sep 19, 2025:
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | image | media | link table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
    // Image upload configuration
    images_upload_url: '/upload-image', // Laravel route for image upload
    images_upload_handler: function (blobInfo, success, failure) {
      var xhr, formData;
      xhr = new XMLHttpRequest();
      xhr.withCredentials = false;
      xhr.open('POST', '/upload-image');
      
      xhr.onload = function() {
        var json;
        if (xhr.status != 200) {
          failure('HTTP Error: ' + xhr.status);
          return;
        }
        json = JSON.parse(xhr.responseText);
        if (!json || typeof json.location != 'string') {
          failure('Invalid JSON: ' + xhr.responseText);
          return;
        }
        success(json.location);
      };
      
      formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());
      xhr.send(formData);
    },
    // Image upload settings
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function (cb, value, meta) {
      var input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      
      input.onchange = function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function () {
          var id = 'blobid' + (new Date()).getTime();
          var blobCache = tinymce.activeEditor.editorUpload.blobCache;
          var base64 = reader.result.split(',')[1];
          var blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);
          cb(blobInfo.blobUri(), { title: file.name });
        };
        reader.readAsDataURL(file);
      };
      input.click();
    },
    // Media/Video configuration
    media_live_embeds: true,
    media_url_resolver: function (data, resolve) {
      if (data.url.indexOf('youtube.com') !== -1 || data.url.indexOf('youtu.be') !== -1) {
        var embedHtml = '<iframe src="' + data.url + '" width="560" height="315" frameborder="0" allowfullscreen></iframe>';
        resolve({ html: embedHtml });
      } else if (data.url.indexOf('vimeo.com') !== -1) {
        var embedHtml = '<iframe src="' + data.url + '" width="560" height="315" frameborder="0" allowfullscreen></iframe>';
        resolve({ html: embedHtml });
      } else {
        resolve({ html: '' });
      }
    }
  });
</script>
                <div class="tab-pane fade active show" id="create" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="content-card">
                                <h4 class="mb-4">Edit Donation Gig</h4>
                                
                                <form action="{{ route('user.campaign.update', $campaign->id) }}" method="POST" id="createGigForm" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="gigTitle" class="form-label">Gig Title *</label>
                                                <input type="text" class="form-control" id="gigTitle" name="name" placeholder="Enter a compelling title for your gig" value="{{ $campaign->name }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="gigCategory" class="form-label">Category *</label>
                                                <select class="form--control form-select" name="category_id" id="gigCategory" required>
                                                    <option value="">@lang('Select Category')</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" @selected($campaign->category_id == $category->id)>
                                                            {{ __(@$category->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="gigDescription" class="form-label">Description *</label>
                                        <textarea class="form-control" id="gigDescription" name="description" rows="10" placeholder="Describe your gig, its purpose, and how donations will be used" required style="display: block; min-height: 300px;">{{ $campaign->description }}</textarea>
                                    </div>

                                    <!-- Main Campaign Image -->
                                    <div class="form-group">
                                        <label for="mainImage" class="form-label">Main Campaign Image</label>
                                        <input type="file" class="form-control" id="mainImage" name="image" accept="image/*">
                                        <small class="text-muted">Upload a new image to replace the current one, or leave empty to keep existing image</small>
                                        @if($campaign->image)
                                            <div class="mt-2">
                                                <small class="text-info">Current image: {{ $campaign->image }}</small>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Video Upload Options -->
                                    <div class="form-group mb-4">
                                        <label class="form-label">Campaign Video</label>
                                        
                                        <!-- Video Upload Toggle -->
                                        <div class="mb-3">
                                            <div class="btn-group" role="group" aria-label="Video upload options">
                                                <input type="radio" class="btn-check" name="video_type" id="video_file_new" value="file" autocomplete="off" @checked($campaign->youtube_url ? false : true)>
                                                <label class="btn btn-outline-primary" for="video_file_new">Upload Video File</label>
                                                
                                                <input type="radio" class="btn-check" name="video_type" id="video_youtube_new" value="youtube" autocomplete="off" @checked($campaign->youtube_url ? true : false)>
                                                <label class="btn btn-outline-primary" for="video_youtube_new">YouTube URL</label>
                                            </div>
                                        </div>
                                        
                                        <!-- File Upload Section -->
                                        <div id="file_upload_section_new">
                                            <input type="file" class="form-control mb-2" name="video" accept="video/*">
                                            <small class="text-muted">Upload a video file (MP4, AVI, MOV, etc.) - Optional</small>
                                            
                                            <!-- Auto Upload to YouTube Option -->
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="auto_upload_youtube" id="auto_upload_youtube_new" value="1">
                                                <label class="form-check-label" for="auto_upload_youtube_new">
                                                    <strong>🚀 Auto-upload to YouTube</strong> (Better streaming performance)
                                                </label>
                                                <small class="text-muted d-block">Video will be automatically uploaded to YouTube and streamed from there</small>
                                            </div>
                                        </div>
                                        
                                        <!-- YouTube URL Section -->
                                        <div id="youtube_url_section_new" style="display: none;">
                                            <input type="url" class="form-control mb-2" name="youtube_url" id="youtube_url_new" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ" value="{{ $campaign->youtube_url }}">
                                            <small class="text-muted">Enter YouTube video URL for better streaming performance - Optional</small>
                                            <div class="mt-2">
                                                <div class="youtube-preview-new"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="targetAmount" class="form-label">Target Amount ({{ $setting->cur_sym }}) *</label>
                                                <input type="number" name="goal_amount" class="form-control" id="targetAmount" placeholder="5000" min="1" step="0.01" value="{{ $campaign->goal_amount }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="startDate" class="form-label">Start Date *</label>
                                                <input type="date" name="start_date" class="form-control" id="startDate" value="{{ $campaign->start_date->format('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="endDate" class="form-label">End Date *</label>
                                                <input type="date" name="end_date" class="form-control" id="endDate" value="{{ $campaign->end_date->format('Y-m-d') }}" required>
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
                                            <i class="fas fa-image" id="previewImageIcon" style="{{ $campaign->image ? 'display: none;' : '' }}"></i>
                                            <img id="previewImage" src="{{ $campaign->image ? getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) : '' }}" alt="Preview" style="{{ $campaign->image ? 'display: block;' : 'display: none;' }} width: 100%; height: 100%; object-fit: cover; border-radius: 10px 10px 0 0;">
                                        </div>
                                        <div class="preview-content">
                                            <div class="preview-title">{{ $campaign->name ?: 'Your Gig Title' }}</div>
                                            <div class="preview-description">{{ $campaign->description ? strip_tags(Str::limit($campaign->description, 100)) : 'Your gig description will appear here...' }}</div>
                                            <div class="preview-meta">
                                                <span class="preview-category">{{ $campaign->category->name ?? 'Category' }}</span>
                                                <span class="preview-amount">{{ $setting->cur_sym }}{{ number_format($campaign->goal_amount, 2) }}</span>
                                            </div>
                                            <div class="preview-progress">
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 0%"></div>
                                                </div>
                                                <small class="text-muted">0% of {{ $setting->cur_sym }}{{ number_format($campaign->goal_amount, 2) }} goal</small>
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

/* TinyMCE Editor Styling */
.tox-tinymce {
    height: 600px !important;
}

.tox-edit-area__iframe {
    height: 550px !important;
}

.tox-editor-container {
    height: 600px !important;
}

/* Jodit Editor Specific Styling */
.jodit-container {
    border: 1px solid #ced4da !important;
    border-radius: 6px !important;
    box-shadow: none !important;
}

.jodit-toolbar {
    background: #f8f9fa !important;
    border-bottom: 1px solid #ced4da !important;
    border-radius: 6px 6px 0 0 !important;
}

.jodit-workplace {
    border-radius: 0 0 6px 6px !important;
}

.jodit-wysiwyg {
    min-height: 250px !important;
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
    <script>
        // Handle video type selection
        function toggleVideoSections() {
            const fileRadio = document.getElementById('video_file_new');
            const youtubeRadio = document.getElementById('video_youtube_new');
            const fileSection = document.getElementById('file_upload_section_new');
            const youtubeSection = document.getElementById('youtube_url_section_new');

            if (fileRadio && fileRadio.checked) {
                fileSection.style.display = 'block';
                youtubeSection.style.display = 'none';
            } else if (youtubeRadio && youtubeRadio.checked) {
                fileSection.style.display = 'none';
                youtubeSection.style.display = 'block';
            }
        }

        // Set initial state based on existing data
        document.addEventListener('DOMContentLoaded', function() {
            // Check if campaign has YouTube URL
            const hasYoutubeUrl = '{{ $campaign->youtube_url }}' !== '';
            
            const fileRadio = document.getElementById('video_file_new');
            const youtubeRadio = document.getElementById('video_youtube_new');
            
            if (hasYoutubeUrl && youtubeRadio) {
                youtubeRadio.checked = true;
            } else if (fileRadio) {
                fileRadio.checked = true;
            }
            
            // Toggle sections based on initial state
            toggleVideoSections();
            
            // Add event listeners
            if (fileRadio) {
                fileRadio.addEventListener('change', toggleVideoSections);
            }
            if (youtubeRadio) {
                youtubeRadio.addEventListener('change', toggleVideoSections);
            }


        });

        // Preview functionality
        function previewGig() {
            const title = document.getElementById('gigTitle').value || 'Your Gig Title';
            
            // Get description from TinyMCE editor
            let description = 'Your gig description will appear here...';
            if (typeof tinymce !== 'undefined' && tinymce.get('gigDescription')) {
                description = tinymce.get('gigDescription').getContent({format: 'text'}) || 'Your gig description will appear here...';
            } else {
                description = document.getElementById('gigDescription').value || 'Your gig description will appear here...';
            }
            
            const categorySelect = document.getElementById('gigCategory');
            const category = categorySelect ? categorySelect.options[categorySelect.selectedIndex].text : 'Category';
            const amount = document.getElementById('targetAmount').value || '0';
            const currencySymbol = '{{ $setting->cur_sym }}';
            
            const previewCard = document.querySelector('#gigPreview .preview-card');
            if (previewCard) {
                previewCard.querySelector('.preview-title').textContent = title;
                previewCard.querySelector('.preview-description').textContent = description;
                previewCard.querySelector('.preview-category').textContent = category;
                previewCard.querySelector('.preview-amount').textContent = currencySymbol + parseFloat(amount).toLocaleString();
            }
        }


        // Image preview functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mainImageInput = document.getElementById('mainImage');
            const previewImage = document.getElementById('previewImage');
            const previewImageIcon = document.getElementById('previewImageIcon');

            if (mainImageInput && previewImage && previewImageIcon) {
                mainImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                            previewImage.style.display = 'block';
                            previewImageIcon.style.display = 'none';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
    
@endsection