@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" />

  <style>
    :root { --radius: 24px; --border:#e6e6e6; }
    body{
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, Inter, Arial, sans-serif;
      background:#fff;
      color:#111;
      padding:48px 20px;
      line-height:1.4;
    }
    .wrap{max-width:1000px;margin:0 auto;}
    h1{font-size:44px;margin:0 0 20px 0;letter-spacing:-.3px}
    .editor-shell{
      border:1px solid var(--border);
      border-radius:var(--radius);
      overflow:hidden;
      box-shadow:0 1px 0 rgba(0,0,0,.02);
    }
    /* Editor */
    #editor{
      background:#fff;
      height: 400px;
    }
    .ql-container.ql-snow{border:none;}
    .ql-editor{
      font-size:22px;
      color:#404040;
    }
    .ql-editor.ql-blank::before{
      color:#9aa0a6; /* placeholder color */
      left:32px; right:32px;
    }
    /* Toolbar placed at the bottom */
    .ql-toolbar{
      border-top:1px solid var(--border) !important;
      border-bottom:none !important;
      padding:10px 16px !important;
      display:flex; gap:8px; justify-content:flex-start;
      background:#fff;
    }
    .ql-snow .ql-picker, .ql-snow .ql-stroke{color:#111;stroke:#111}
    .ql-snow .ql-fill{fill:#111}
    .ql-snow .ql-active .ql-stroke{stroke:#0f8e6f}
    .ql-snow .ql-active .ql-fill{fill:#0f8e6f}
    /* Make buttons a bit larger, like the screenshot */
    .ql-toolbar button{
      width:36px;height:36px;border-radius:10px;
    }
    .hidden-input{ display:none; }
  </style>
@endsection
@section('frontend')

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
                                        <div class="editor-shell">
      <!-- The editor -->
      <div id="editor" placeholder="Hi, my name is Jane and I'm fundraising for…"></div>

      <!-- Toolbar at the bottom -->
      <div id="toolbar">
        <span class="ql-formats">
          <button class="ql-image"></button>
          <button class="ql-video"></button>
        </span>
        <span class="ql-formats">
          <button class="ql-bold"></button>
          <button class="ql-italic"></button>
          <button class="ql-link"></button>
          <button class="ql-list" value="bullet"></button>
          <button class="ql-list" value="ordered"></button>
        </span>
      </div>
    </div>
                                        <textarea id="gigDescription" name="description" style="visibility: hidden;" required></textarea>
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
    <!-- Quill.js CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
@endpush

@push('page-script-lib')
<script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
<script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
<!-- Quill.js JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
@endpush




@section('page-script')
    <!-- Hidden file input for custom image handler -->
  <input type="file" id="imageInput" accept="image/*" class="hidden-input" />

<!-- Quill JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
  // Init Quill with custom toolbar container (placed after the editor)
  const quill = new Quill('#editor', {
    theme: 'snow',
    placeholder: 'Hi, my name is Jane and I’m fundraising for…',
    modules: {
      toolbar: {
        container: '#toolbar',
        handlers: {
          image: customImageHandler,
          video: customVideoHandler
        }
      }
    }
  });

  // === Image handler (local file -> base64 embed) ===
  function customImageHandler() {
    const fileInput = document.getElementById('imageInput');
    fileInput.value = '';
    fileInput.click();
    fileInput.onchange = () => {
      const file = fileInput.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        const range = quill.getSelection(true);
        quill.insertEmbed(range.index, 'image', e.target.result, 'user');
        quill.setSelection(range.index + 1, 0);
      };
      reader.readAsDataURL(file);
    };
  }

  // === Video handler (prompt for URL; Quill will embed iframe for YouTube/Vimeo) ===
  function customVideoHandler() {
    const url = prompt('Paste video URL (YouTube, Vimeo, MP4, etc.)');
    if (!url) return;
    const range = quill.getSelection(true);
    quill.insertEmbed(range.index, 'video', url, 'user');
    quill.setSelection(range.index + 1, 0);
  }

  // Function to show editor content in alert
  function showEditorContent() {
    if (quill) {
      const editorContent = quill.root.innerHTML;
      const textContent = quill.getText();
      
      alert('Editor Content (HTML):\n' + editorContent + '\n\nEditor Content (Text):\n' + textContent);
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
        
        // Then check if all required fields are filled
        const requiredFields = form.querySelectorAll('[required]');
        let allFieldsValid = true;
        
        requiredFields.forEach(function(field) {
          if (!field.value.trim()) {
            console.error('Required field is empty:', field.name || field.id);
            allFieldsValid = false;
            field.style.borderColor = 'red';
          } else {
            field.style.borderColor = '';
          }
        });
        
        if (!allFieldsValid) {
          e.preventDefault();
          alert('Please fill in all required fields');
          return false;
        }
      });
    } else {
      console.error('Form not found!');
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