@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
@section('style')
<meta http-equiv="X-UA-Compatible" content="IE=edge">
@endsection
@section('frontend')
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
                                        <div id="editor" style="height: 400px;">
                                            {!! $campaign->description !!}
                                        </div>
                                        <textarea id="gigDescription" name="description" style="display: none;">{{ $campaign->description }}</textarea>
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
                                        <button type="button" class="btn btn-primary" id="submitBtn" onclick="showEditorContent()">
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
    <!-- Quill.js CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
@endpush

@push('page-script-lib')
<script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
<script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
    <!-- Load Dropzone from CDN for reliability -->
    <script src="https://unpkg.com/dropzone@6.0.0-beta.2/dist/dropzone-min.js"></script>
    <!-- Quill.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
@endpush



@section('page-script')
    <!-- Quill.js Editor -->
    <script>
        // Initialize Quill editor
        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['blockquote', 'code-block'],
                    ['clean']
                ]
            },
            placeholder: 'Describe your gig, its purpose, and how donations will be used...'
        });

        // Load existing content into Quill editor
        document.addEventListener('DOMContentLoaded', function() {
            const existingContent = document.getElementById('gigDescription').value;
            if (existingContent) {
                quill.root.innerHTML = existingContent;
            }
        });

        // Function to set editor content in cookie and submit form
        function showEditorContent() {
            if (quill) {
                const editorContent = quill.root.innerHTML;
                const textContent = quill.getText();
                
                // Set cookie with editor content
                document.cookie = "editor_html_content=" + encodeURIComponent(editorContent) + "; path=/";
                document.cookie = "editor_text_content=" + encodeURIComponent(textContent) + "; path=/";
                
                console.log('Editor content set in cookies');
                console.log('HTML Content:', editorContent);
                console.log('Text Content:', textContent);
                
                // Also copy content to hidden textarea
                const textarea = document.getElementById('gigDescription');
                if (textarea) {
                    textarea.value = editorContent;
                    console.log('Content also copied to textarea');
                }
                
                // Submit the form
                const form = document.querySelector('form');
                if (form) {
                    console.log('Submitting form...');
                    form.submit();
                }
            } else {
                alert('Quill editor not found!');
            }
        }

        // Handle form submission - copy Quill content to hidden textarea
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up form submission handler');
            
            const form = document.querySelector('form');
            const textarea = document.getElementById('gigDescription');
            
            console.log('Form found:', !!form);
            console.log('Textarea found:', !!textarea);
            console.log('Quill instance:', !!quill);
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submit event triggered');
                    
                    // First, copy Quill content to textarea
                    if (quill) {
                        const editorContent = quill.root.innerHTML;
                        console.log('Editor content:', editorContent);
                        
                        if (textarea) {
                            textarea.value = editorContent;
                            console.log('Textarea value set to:', textarea.value);
                        } else {
                            console.error('Textarea not found!');
                        }
                    } else {
                        console.error('Quill instance not found!');
                    }
                });
            } else {
                console.error('Form not found!');
            }
        });

        // Image upload handler for Quill
        const toolbar = quill.getModule('toolbar');
        toolbar.addHandler('image', function() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = function() {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('files', file);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/user/campaign/upload-image');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.location) {
                                    const range = quill.getSelection();
                                    quill.insertEmbed(range.index, 'image', response.location);
                                } else {
                                    alert('Upload failed: Invalid response');
                                }
                            } catch (e) {
                                alert('Upload failed: Invalid JSON response');
                            }
                        } else {
                            alert('Upload failed: ' + xhr.status);
                        }
                    };
                    xhr.onerror = function() {
                        alert('Upload failed: Network error');
                    };
                    xhr.send(formData);
                }
            };
        });
    </script>
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
    
    <style>
        /* TinyMCE Editor Styling */
        .tox-tinymce {
            height: 800px !important;
        }
        
        .tox-edit-area__iframe {
            height: 750px !important;
        }
        
        .tox-editor-container {
            height: 800px !important;
        }
    </style>
    
@endsection