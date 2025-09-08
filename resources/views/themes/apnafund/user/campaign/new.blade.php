@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
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
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | image | media | link table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
    uploadcare_public_key: 'c164087b8687b1fe3294',
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
                                        <textarea class="form-control" id="gigDescription" name="description" rows="10" placeholder="Describe your gig, its purpose, and how donations will be used" required style="display: block; min-height: 300px;">@php echo old('description') @endphp</textarea>
                                    </div>

                                    <!-- Main Campaign Image -->
                                    <div class="form-group">
                                        <label for="mainImage" class="form-label">Main Campaign Image *</label>
                                        <input type="file" class="form-control" id="mainImage" name="image" accept="image/*" required>
                                        <small class="text-muted">This will be the primary image displayed for your campaign</small>
                                    </div>

                                    <!-- Video Upload Options -->
                                    <div class="form-group mb-4">
                                        <label class="form-label">Campaign Video</label>
                                        
                                        <!-- Video Upload Toggle -->
                                        <div class="mb-3">
                                            <div class="btn-group" role="group" aria-label="Video upload options">
                                                <input type="radio" class="btn-check" name="video_type" id="video_file_new" value="file" autocomplete="off" checked>
                                                <label class="btn btn-outline-primary" for="video_file_new">Upload Video File</label>
                                                
                                                <input type="radio" class="btn-check" name="video_type" id="video_youtube_new" value="youtube" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="video_youtube_new">YouTube URL</label>
                                            </div>
                                        </div>
                                        
                                        <!-- File Upload Section -->
                                        <div id="file_upload_section_new" style="display: block;">
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
                                            <input type="url" class="form-control mb-2" name="youtube_url" id="youtube_url_new" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
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
@endpush

@push('page-script-lib')
<script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
<script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
@endpush




@section('page-script')
    <script>

        // Handle video type selection
        function toggleVideoSections() {
            console.log('toggleVideoSections called');
            const fileRadio = document.getElementById('video_file_new');
            const youtubeRadio = document.getElementById('video_youtube_new');
            const fileSection = document.getElementById('file_upload_section_new');
            const youtubeSection = document.getElementById('youtube_url_section_new');

            console.log('Elements found:', {
                fileRadio: !!fileRadio,
                youtubeRadio: !!youtubeRadio,
                fileSection: !!fileSection,
                youtubeSection: !!youtubeSection
            });

            if (fileRadio && fileRadio.checked) {
                console.log('File radio checked');
                if (fileSection) fileSection.style.display = 'block';
                if (youtubeSection) youtubeSection.style.display = 'none';
            } else if (youtubeRadio && youtubeRadio.checked) {
                console.log('YouTube radio checked');
                if (fileSection) fileSection.style.display = 'none';
                if (youtubeSection) youtubeSection.style.display = 'block';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing video toggle');
            
            // Set initial state (file upload is selected by default)
            toggleVideoSections();
            
            // Add event listeners for both radio buttons and labels
            const fileRadio = document.getElementById('video_file_new');
            const youtubeRadio = document.getElementById('video_youtube_new');
            const fileLabel = document.querySelector('label[for="video_file_new"]');
            const youtubeLabel = document.querySelector('label[for="video_youtube_new"]');
            
            // Radio button change events
            if (fileRadio) {
                fileRadio.addEventListener('change', function() {
                    console.log('File radio changed');
                    toggleVideoSections();
                });
            }
            
            if (youtubeRadio) {
                youtubeRadio.addEventListener('change', function() {
                    console.log('YouTube radio changed');
                    toggleVideoSections();
                });
            }
            
            // Label click events (backup)
            if (fileLabel) {
                fileLabel.addEventListener('click', function() {
                    console.log('File label clicked');
                    setTimeout(toggleVideoSections, 10);
                });
            }
            
            if (youtubeLabel) {
                youtubeLabel.addEventListener('click', function() {
                    console.log('YouTube label clicked');
                    setTimeout(toggleVideoSections, 10);
                });
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
    </script>

<style>
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
  
  /* Move image button to right corner */
  .jodit-toolbar__box .jodit-toolbar-editor-collection {
    display: flex;
  }
  .jodit-toolbar__box .jodit-toolbar-editor-collection 
  .jodit-toolbar-button[data-ref="image"] {
    margin-left: auto !important;  /* push image to far right */
  }
  .jodit-toolbar__box .jodit-tool
</style>


@endsection