@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.dashboard')
@section('style')
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" />

  <style>
    :root { --radius: 24px; --border:#e6e6e6; }
    .editor-shell{
      border:1px solid var(--border);
      border-radius:var(--radius);
      overflow:hidden;
      box-shadow:0 1px 0 rgba(0,0,0,.02);
      background: #fff;
      position: relative;
    }
    /* Editor */ 
    #editor{
      background:#fff;
      height: 400px;
      min-height: 400px;
    }
    .ql-container.ql-snow{
      border:none;
      font-size: 16px;
    }
    .ql-editor{
      font-size:22px;
      color:#404040;
      min-height: 350px;
      padding: 20px;
    }
    .ql-editor p {
      margin-bottom: 1em;
    }
    .ql-editor.ql-blank::before{
      color:#9aa0a6; /* placeholder color */
      left:32px; right:32px;
    }
    /* Toolbar placed at the bottom */
    .ql-toolbar{
      border-top:1px solid var(--border) !important;
      border-bottom:none !important;
      border-left:none !important;
      border-right:none !important;
      padding:10px 16px !important;
      display:flex !important;
      gap:8px;
      justify-content:flex-start;
      background:#fff !important;
      min-height: 50px;
    }
    .ql-snow .ql-picker, .ql-snow .ql-stroke{color:#111;stroke:#111}
    .ql-snow .ql-fill{fill:#111}
    .ql-snow .ql-active .ql-stroke{stroke:#0f8e6f}
    .ql-snow .ql-active .ql-fill{fill:#0f8e6f}
    /* Make buttons a bit larger, like the screenshot */
    .ql-toolbar button{
      width:36px;height:36px;border-radius:10px;
      border: 1px solid #ddd;
      background: #fff;
    }
    .ql-toolbar button:hover {
      background: #f5f5f5;
    }
    .ql-toolbar .ql-picker {
      border: 1px solid #ddd;
      border-radius: 4px;
      background: #fff;
    }
    .hidden-input{ display:none; }
    
    /* Ensure Quill editor is visible */
    .ql-container {
      display: block !important;
      visibility: visible !important;
    }
    .ql-editor {
      display: block !important;
      visibility: visible !important;
    }
    .ql-toolbar {
      display: flex !important;
      visibility: visible !important;
    }
    
    /* YouTube iframe styling in editor */
    .ql-editor iframe {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      margin: 10px 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Responsive iframe */
    .ql-editor iframe[src*="youtube.com/embed/"] {
      width: 100%;
      height: 315px;
      max-width: 560px;
    }
    
    @media (max-width: 768px) {
      .ql-editor iframe[src*="youtube.com/embed/"] {
        height: 200px;
      }
    }
    
    /* Gallery UI Styles */
    .gallery-section {
      margin-bottom: 2rem;
    }
    
    .gallery-dropzone {
      border: 2px dashed #dee2e6;
      border-radius: 12px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      transition: all 0.3s ease;
      min-height: 200px;
      position: relative;
      overflow: hidden;
    }
    
    .gallery-dropzone:hover {
      border-color: #0f8e6f;
      background: linear-gradient(135deg, #e8f5f3 0%, #d1f2eb 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(15, 142, 111, 0.15);
    }
    
    .gallery-dropzone .dz-message {
      text-align: center;
      padding: 2rem;
      margin: 0;
    }
    
    .upload-icon {
      font-size: 3rem;
      color: #0f8e6f;
      margin-bottom: 1rem;
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    .upload-title {
      color: #2c3e50;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .upload-subtitle {
      color: #6c757d;
      margin-bottom: 1rem;
    }
    
    .upload-info {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    
    .upload-info .badge {
      font-size: 0.75rem;
      padding: 0.4rem 0.8rem;
    }
    
    .gallery-help-text {
      margin-top: 1rem;
      padding: 0.75rem 1rem;
      background: #e3f2fd;
      border-radius: 8px;
      border-left: 4px solid #2196f3;
    }
    
    .gallery-help-text i {
      color: #2196f3;
    }
    
    /* Gallery Image Cards */
    .gallery-image-card {
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      height: 100%;
    }
    
    .gallery-image-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .image-container {
      position: relative;
      width: 100%;
      height: 180px;
      overflow: hidden;
    }
    
    .gallery-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .gallery-image-card:hover .gallery-image {
      transform: scale(1.05);
    }
    
    .image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .gallery-image-card:hover .image-overlay {
      opacity: 1;
    }
    
    .remove-btn {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }
    
    .remove-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }
    
    .gallery-preview {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 1rem;
      border: 1px solid #e9ecef;
    }
    
    .gallery-preview-title {
      color: #495057;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 0.75rem;
    }
    
    /* Gallery Upload Area */
    .gallery-upload-area {
      border: 2px dashed #dee2e6;
      border-radius: 12px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      transition: all 0.3s ease;
      min-height: 200px;
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }
    
    .gallery-upload-area:hover {
      border-color: #0f8e6f;
      background: linear-gradient(135deg, #e8f5f3 0%, #d1f2eb 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(15, 142, 111, 0.15);
    }
    
    .upload-content {
      text-align: center;
      padding: 2rem;
      margin: 0;
    }
    
    .upload-icon {
      font-size: 3rem;
      color: #0f8e6f;
      margin-bottom: 1rem;
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    .upload-title {
      color: #2c3e50;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .upload-subtitle {
      color: #6c757d;
      margin-bottom: 1rem;
    }
    
    .upload-info {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
    
    .upload-info .badge {
      font-size: 0.75rem;
      padding: 0.4rem 0.8rem;
    }
    
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
    
    /* Make entire label red for required fields */
    .required-field .form-label {
      color: #dc3545 !important;
      font-weight: 600;
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
    
    /* Smooth scroll behavior */
    html {
      scroll-behavior: smooth;
    }
    
    /* Toast notification positioning */
    .toast-top-right {
      top: 20px;
      right: 20px;
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
    
    .preview-progress {
      margin-top: 1rem;
    }
    
    .preview-gallery, .preview-video {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 1rem;
      border: 1px solid #e9ecef;
    }
    
    .preview-gallery-title, .preview-video-title {
      color: #495057;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 0.75rem;
    }
    
    .preview-gallery-images img {
      border-radius: 6px;
      transition: transform 0.2s ease;
    }
    
    .preview-gallery-images img:hover {
      transform: scale(1.05);
    }
    
    .preview-video-container iframe {
      border-radius: 8px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .gallery-dropzone {
        min-height: 150px;
      }
      
      .upload-icon {
        font-size: 2rem;
      }
      
      .upload-title {
        font-size: 1.1rem;
      }
      
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
@endsection
@section('frontend')

                <div class="tab-pane fade active show" id="create" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="content-card">
                                <h4 class="mb-4">Create New Campaign</h4> 
                                
                                <form action="{{ route('user.campaign.store') }}" method="POST" id="createCampignForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group required-field">
                                                <label for="CampignTitle" class="form-label">Campign Title  </label>
                                                <input type="text" class="form-control" id="CampignTitle" name="name" placeholder="Enter a compelling title for your Campign" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group required-field">
                                                <label for="CampignCategory" class="form-label">Category  </label>
                                                <select class="form--control form-select" name="category_id" id="CampignCategory" required>
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

                                    <div class="form-group required-field">
                                        <label for="CampignDescription" class="form-label">Story  </label>
                                        <div class="editor-shell">
      <!-- The editor -->
      <div id="editor" placeholder="Hi, my name is Jane and I'm fundraising for…"></div>

      <!-- Toolbar at the bottom -->
      <div id="toolbar">
        <span class="ql-formats">
          <select class="ql-header">
            <option value="1">Heading 1</option>
            <option value="2">Heading 2</option>
            <option value="3">Heading 3</option>
            <option value="4">Heading 4</option>
            <option value="5">Heading 5</option>
            <option value="6">Heading 6</option>
            <option selected>Normal</option>
          </select>
        </span>
        <span class="ql-formats">
          <button class="ql-bold"></button>
          <button class="ql-italic"></button>
          <button class="ql-underline"></button>
          <button class="ql-strike"></button>
        </span>
        <span class="ql-formats">
          <button class="ql-list" value="ordered"></button>
          <button class="ql-list" value="bullet"></button>
        </span>
        <span class="ql-formats">
          <button class="ql-link"></button>
          <button class="ql-image"></button>
          <button class="ql-video"></button>
        </span>
        <span class="ql-formats">
          <button class="ql-clean"></button>
        </span>
      </div>
    </div>
                                        <textarea id="CampignDescription" name="description" style="display: none;" required></textarea>
                                    </div>

                                    <!-- Main Campaign Image -->
                                    <div class="form-group required-field">
                                        <label for="mainImage" class="form-label">Main Campaign Image  </label>
                                        <input type="file" class="form-control" id="mainImage" name="image" accept="image/*" required>
                                        <small class="text-muted">This will be the primary image displayed for your campaign</small>
                                    </div>

                                    <!-- YouTube Video URL -->
                                    <div class="form-group mb-4">
                                        <label class="form-label">Campaign Video (YouTube URL)</label>
                                        
                                        <!-- YouTube URL Section -->
                                        <div id="youtube_url_section_new">
                                            <input type="url" class="form-control mb-2" name="youtube_url" id="youtube_url_new" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                                            <small class="text-muted">Enter YouTube video URL for better streaming performance - Optional</small>
                                            <div class="mt-2">
                                                <div class="youtube-preview-new"></div>
                                                <div id="youtube-validation-message" class="text-danger mt-1" style="display: none;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 
                                    <!-- Video Upload Options - COMMENTED OUT -->
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
                                    </div>
                                    --}}

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group required-field">
                                                <label for="targetAmount" class="form-label">Target Amount ({{ $setting->cur_sym }})  </label>
                                                <input type="number" name="goal_amount" class="form-control" id="targetAmount" placeholder="5000" min="1" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group required-field">
                                                <label for="startDate" class="form-label">Start Date  </label>
                                                <input type="date" name="start_date" class="form-control" id="startDate" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group required-field">
                                                <label for="endDate" class="form-label">End Date  </label>
                                                <input type="date" name="end_date" class="form-control" id="endDate" required>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="d-flex gap-3">
                                        <button type="button" class="btn btn-primary" id="submitBtn" onclick="showEditorContent()">
                                            <i class="fas fa-save me-2"></i>Submit 
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
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-eye me-2"></i>Live Preview
                                    </h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshPreview()">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <div id="CampignPreview">
                                    <div class="preview-card">
                                        <div class="preview-image" id="previewMainImage">
                                            <i class="fas fa-image" id="previewImageIcon"></i>
                                            <img id="previewImage" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 10px 10px 0 0;">
                                        </div>
                                        <div class="preview-content">
                                            <div class="preview-title" id="previewTitle">Your Campaign Title</div>
                                            <div class="preview-description" id="previewDescription">Your campaign description will appear here...</div>
                                            <div class="preview-meta">
                                                <span class="preview-category" id="previewCategory">Category</span>
                                                <span class="preview-amount" id="previewAmount">$0</span>
                                            </div>
                                            <div class="preview-progress">
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: 0%"></div>
                                                </div>
                                                <small class="text-muted">0% of <span id="previewGoalAmount">$0</span> goal</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <!-- YouTube Video Preview -->
                                    <div class="preview-video mt-3" id="previewVideo" style="display: none;">
                                        <h6 class="preview-video-title">
                                            <i class="fab fa-youtube me-2"></i>Video Preview
                                        </h6>
                                        <div class="preview-video-container" id="previewVideoContainer">
                                            <!-- YouTube video will be embedded here -->
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
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet" />
@endpush

@push('page-script-lib')
<script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
<script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
    <!-- Quill.js JS - Use stable version -->
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
@endpush




@section('page-script')
    <!-- Hidden file input for custom image handler -->
  <input type="file" id="imageInput" accept="image/*" class="hidden-input" />

    <!-- Quill.js already loaded in page-script-lib -->
    
    <script>
        // Wait for iziToast to load and then configure it
        document.addEventListener('DOMContentLoaded', function() {
            // Configure iziToast after it's loaded
            if (typeof iziToast !== 'undefined') {
                // iziToast is already configured globally, just log success
                console.log('iziToast configured successfully');
            } else {
                console.error('iziToast library not loaded');
            }
        });
    </script>
    
    <script>
  // Define handlers before initializing Quill
  function customImageHandler() {
    const input = document.getElementById('imageInput');
    input.click();

    input.onchange = function() {
      const file = input.files[0];
      if (!file) return;
      
      console.log('File selected:', file.name, file.size);
      
      // Validate file type
      if (!file.type.match('image.*')) {
        alert('Please select an image file');
        return;
      }
      
      // Validate file size (5MB max)
      if (file.size > 5 * 1024 * 1024) {
        alert('Image size must be less than 5MB');
        return;
      }
      
      // Create FormData for file upload
      const formData = new FormData();
      formData.append('files', file);
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

      console.log('Uploading file...');
      
      // Upload file to server using XMLHttpRequest
      const xhr = new XMLHttpRequest();
      xhr.open('POST', '{{ route("user.campaign.upload-image") }}');
      
      xhr.onload = function() {
        console.log('Upload response:', xhr.status, xhr.responseText);
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            console.log('Parsed response:', response);
            if (response.location) {
              console.log('Inserting image:', response.location);
              // Insert the uploaded image at current cursor position
              const range = quill.getSelection();
              if (range) {
                quill.insertEmbed(range.index, 'image', response.location);
                quill.setSelection(range.index + 1, 0);
              } else {
                // If no selection, insert at the end
                const length = quill.getLength();
                quill.insertEmbed(length - 1, 'image', response.location);
                quill.setSelection(length, 0);
              }
              console.log('Image inserted successfully');
            } else {
              alert('Upload failed: ' + (response.message || 'Invalid response'));
            }
          } catch (e) {
            console.error('JSON parse error:', e);
            alert('Upload failed: Invalid JSON response');
          }
        } else {
          alert('Upload failed: Server error ' + xhr.status);
        }
      };
      
      xhr.onerror = function() {
        console.error('Upload error');
        alert('Upload failed: Network error');
      };
      
      xhr.send(formData);
    };
  }

  function customVideoHandler() {
    const url = prompt('Paste video URL (YouTube, Vimeo, MP4, etc.)');
    if (!url) return;
    
    // Convert YouTube URL to embeddable format
    let embedUrl = url;
    if (url.includes('youtube.com/watch?v=')) {
      const videoId = url.split('v=')[1].split('&')[0];
      embedUrl = `https://www.youtube.com/embed/${videoId}`;
    } else if (url.includes('youtu.be/')) {
      const videoId = url.split('youtu.be/')[1].split('?')[0];
      embedUrl = `https://www.youtube.com/embed/${videoId}`;
    }
    
    const range = quill.getSelection(true);
    if (range) {
      // Create iframe element for YouTube videos
      if (embedUrl.includes('youtube.com/embed/')) {
        const iframe = document.createElement('iframe');
        iframe.src = embedUrl;
        iframe.width = '560';
        iframe.height = '315';
        iframe.frameBorder = '0';
        iframe.allowFullscreen = true;
        iframe.style.border = 'none';
        iframe.style.borderRadius = '8px';
        iframe.style.margin = '10px 0';
        
        // Insert the iframe
        quill.clipboard.dangerouslyPasteHTML(range.index, iframe.outerHTML);
        quill.setSelection(range.index + 1, 0);
      } else {
        // For other video URLs, use the standard video embed
        quill.insertEmbed(range.index, 'video', url, 'user');
        quill.setSelection(range.index + 1, 0);
      }
    }
  }

  // Global quill variable
  let quill = null;

  // Initialize Quill editor after DOM is loaded
  function initializeQuill() {
    // Check if editor element exists
    const editorElement = document.getElementById('editor');
    if (!editorElement) {
      console.error('Editor element not found');
      return false;
    }
    
    // Check if Quill is available
    if (typeof Quill === 'undefined') {
      console.error('Quill library not loaded');
      return false;
    }
    
    try {
      // Initialize Quill with custom toolbar container
      quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: "Hi, my name is Jane and I'm fundraising for…",
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
      
      console.log('Quill editor initialized successfully');
      console.log('Quill root element:', quill.root);
      
      // Make quill globally available
      window.quill = quill;
      return true;
      
    } catch (error) {
      console.error('Error initializing Quill editor:', error);
      return false;
    }
  }

  // Wait for both DOM and Quill library to be ready
  function waitForQuillAndInitialize() {
    if (typeof Quill !== 'undefined' && document.getElementById('editor')) {
      console.log('DOM loaded, initializing Quill editor...');
      
      // Initialize Quill first
      const success = initializeQuill();
      
      if (success) {
        console.log('Quill editor initialized:', quill);
      }
    } else {
      // Retry after a short delay
      setTimeout(waitForQuillAndInitialize, 100);
    }
  }

  // Start the initialization process
  document.addEventListener('DOMContentLoaded', waitForQuillAndInitialize);

  // Function to validate form fields one by one
  function validateForm() {
    const errors = [];
    const form = document.getElementById('createCampignForm');
    
    // Clear previous error styling
    clearErrorStyling();
    
    // Validate Campaign Title
    const titleField = document.getElementById('CampignTitle');
    if (!titleField.value.trim()) {
      errors.push({
        field: titleField,
        message: 'Campaign title is required'
      });
    }
    
    // Validate Category
    const categoryField = document.getElementById('CampignCategory');
    if (!categoryField.value) {
      errors.push({
        field: categoryField,
        message: 'Please select a category'
      });
    }
    
    // Validate Description (Quill Editor)
    let descriptionValid = false;
    if (window.quill) {
      const editorContent = window.quill.root.innerHTML;
      const textContent = window.quill.getText();
      const hasContent = editorContent && 
                        editorContent.trim() !== '<p><br></p>' && 
                        editorContent.trim() !== '<p></p>' &&
                        textContent.trim() !== '';
      descriptionValid = hasContent;
    }
    
    if (!descriptionValid) {
      errors.push({
        field: document.getElementById('CampignDescription'),
        message: 'Campaign description is required'
      });
    }
    
    // Validate Main Image
    const imageField = document.getElementById('mainImage');
    if (!imageField.files || imageField.files.length === 0) {
      errors.push({
        field: imageField,
        message: 'Main campaign image is required'
      });
    }
    
    // Validate Target Amount
    const amountField = document.getElementById('targetAmount');
    if (!amountField.value || parseFloat(amountField.value) <= 0) {
      errors.push({
        field: amountField,
        message: 'Please enter a valid target amount'
      });
    }
    
    // Validate Start Date
    const startDateField = document.getElementById('startDate');
    if (!startDateField.value) {
      errors.push({
        field: startDateField,
        message: 'Start date is required'
      });
    }
    
    // Validate End Date
    const endDateField = document.getElementById('endDate');
    if (!endDateField.value) {
      errors.push({
        field: endDateField,
        message: 'End date is required'
      });
    }
    
    // Check if end date is after start date
    if (startDateField.value && endDateField.value) {
      const startDate = new Date(startDateField.value);
      const endDate = new Date(endDateField.value);
      if (endDate <= startDate) {
        errors.push({
          field: endDateField,
          message: 'End date must be after start date'
        });
      }
    }
    
    // Validate YouTube URL if provided
    const youtubeField = document.getElementById('youtube_url_new');
    if (youtubeField.value.trim()) {
      const validation = validateYouTubeUrl(youtubeField.value);
      if (!validation.valid) {
        errors.push({
          field: youtubeField,
          message: validation.message
        });
      }
    }
    
    return errors;
  }
  
  // Function to clear error styling
  function clearErrorStyling() {
    const errorFields = document.querySelectorAll('.is-invalid');
    errorFields.forEach(field => {
      field.classList.remove('is-invalid');
      field.style.borderColor = '';
    });
  }
  
  // Function to scroll to field and focus
  function scrollToField(field) {
    field.scrollIntoView({ 
      behavior: 'smooth', 
      block: 'center' 
    });
    setTimeout(() => {
      field.focus();
    }, 500);
  }
  
  // Function to show validation errors
  function showValidationErrors(errors) {
    console.log('showValidationErrors called with errors:', errors);
    
    if (errors.length === 0) return;
    
    // Check if iziToast is available
    if (typeof iziToast === 'undefined') {
      console.error('iziToast not available, falling back to alert');
      alert('Validation Error: ' + errors[0].message);
      return;
    }
    
    // Show first error with toast
    const firstError = errors[0];
    console.log('Showing error toast:', firstError.message);
    iziToast.error({
      message: firstError.message,
      position: "topRight"
    });
    
    // Add error styling to all error fields
    errors.forEach(error => {
      error.field.classList.add('is-invalid');
      error.field.style.borderColor = '#dc3545';
    });
    
    // Scroll to first error field
    scrollToField(firstError.field);
    
    // Show additional errors as info toasts
    if (errors.length > 1) {
      setTimeout(() => {
        console.log('Showing additional errors toast');
        iziToast.info({
          message: `Please fix ${errors.length - 1} more field(s)`,
          position: "topRight"
        });
      }, 1000);
    }
  }
  
  // Test function to check if toast notifications are working
  function testToast() {
    console.log('Testing toast notifications...');
    
    if (typeof iziToast !== 'undefined') {
      console.log('iziToast is available, showing test notifications');
      iziToast.success({
        message: 'Success toast is working!',
        position: "topRight"
      });
      setTimeout(() => {
        iziToast.error({
          message: 'Error toast is working!',
          position: "topRight"
        });
      }, 1000);
      setTimeout(() => {
        iziToast.info({
          message: 'Info toast is working!',
          position: "topRight"
        });
      }, 2000);
    } else {
      console.error('iziToast is not available');
      alert('iziToast library is not loaded. Please check the console for errors.');
    }
  }

  // Function to set editor content and submit form
  function showEditorContent() {
    console.log('showEditorContent called');
    
    // Copy Quill content to textarea first
    if (window.quill) {
      const editorContent = window.quill.root.innerHTML;
      const textarea = document.getElementById('CampignDescription');
      if (textarea) {
        textarea.value = editorContent;
      }
    }
    
    // Validate form
    console.log('Starting form validation...');
    const errors = validateForm();
    console.log('Validation completed. Errors found:', errors.length);
    
    if (errors.length > 0) {
      console.log('Validation failed, showing errors');
      showValidationErrors(errors);
      return false;
    }
    
    // If validation passes, show success message and submit
    console.log('Validation passed, showing success message');
    if (typeof iziToast !== 'undefined') {
      iziToast.success({
        message: 'All fields validated successfully!',
        position: "topRight"
      });
    } else {
      alert('All fields validated successfully!');
    }
    
    setTimeout(() => {
      const form = document.getElementById('createCampignForm');
      if (form) {
        console.log('Submitting form...');
        form.submit();
      }
    }, 1000);
  }

  // YouTube URL Validation
  function validateYouTubeUrl(url) {
    if (!url || url.trim() === '') {
      return { valid: true, message: '' }; // Empty is allowed (optional field)
    }
    
    const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w\-]+/i;
    
    if (!youtubeRegex.test(url)) {
      return { 
        valid: false, 
        message: 'Please enter a valid YouTube URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID or https://youtu.be/VIDEO_ID)' 
      };
    }
    
    return { valid: true, message: '' };
  }

  // Handle YouTube URL validation
  function handleYouTubeValidation() {
    const youtubeInput = document.getElementById('youtube_url_new');
    const validationMessage = document.getElementById('youtube-validation-message');
    
    if (youtubeInput && validationMessage) {
      youtubeInput.addEventListener('blur', function() {
        const url = this.value.trim();
        const validation = validateYouTubeUrl(url);
        
        if (!validation.valid) {
          validationMessage.textContent = validation.message;
          validationMessage.style.display = 'block';
          this.classList.add('is-invalid');
        } else {
          validationMessage.style.display = 'none';
          this.classList.remove('is-invalid');
        }
      });
      
      youtubeInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
          const url = this.value.trim();
          const validation = validateYouTubeUrl(url);
          
          if (validation.valid) {
            validationMessage.style.display = 'none';
            this.classList.remove('is-invalid');
          }
        }
      });
    }
  }

  // Handle form submission - copy Quill content to hidden textarea
        document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up form submission handler');
    
    // Initialize YouTube URL validation
    try {
      handleYouTubeValidation();
    } catch (e) {
      console.log('YouTube validation not available:', e);
    }
    
    // Setup real-time preview
    setupRealTimePreview();
    
    // Setup gallery upload
    setupGalleryUpload();
    
    const form = document.getElementById('createCampignForm');
    const textarea = document.getElementById('CampignDescription');
    
    console.log('Form found:', !!form);
    console.log('Textarea found:', !!textarea);
    console.log('Quill instance:', !!quill);
    
    if (form) {
      form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        
        // First, copy Quill content to textarea
        if (window.quill) {
          const editorContent = window.quill.root.innerHTML;
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
        
        // Use the new validation system
        const errors = validateForm();
        
        if (errors.length > 0) {
          e.preventDefault();
          showValidationErrors(errors);
          return false;
        }
        
        // If validation passes, show success message
        iziToast.success({
          message: 'Form is being submitted...',
          position: "topRight"
        });
      });
    } else {
      console.error('Form not found!');
            }
        });
        
        // Real-time preview functionality
        function updatePreview() {
            // Update title
            const title = document.getElementById('CampignTitle').value || 'Your Campaign Title';
            const titleElement = document.getElementById('previewTitle');
            if (titleElement) titleElement.textContent = title;
            
            // Update description from Quill editor
            let description = 'Your campaign description will appear here...';
            if (window.quill) {
                const editorContent = window.quill.getText();
                description = editorContent || 'Your campaign description will appear here...';
            } else {
                description = document.getElementById('CampignDescription').value || 'Your campaign description will appear here...';
            }
            const descElement = document.getElementById('previewDescription');
            if (descElement) descElement.textContent = description;
            
            // Update category
            const categorySelect = document.getElementById('CampignCategory');
            const category = categorySelect ? categorySelect.options[categorySelect.selectedIndex].text : 'Category';
            const categoryElement = document.getElementById('previewCategory');
            if (categoryElement) categoryElement.textContent = category;
            
            // Update amount
            const amount = document.getElementById('targetAmount').value || '0';
            const currencySymbol = '{{ $setting->cur_sym }}';
            const amountElement = document.getElementById('previewAmount');
            const goalAmountElement = document.getElementById('previewGoalAmount');
            if (amountElement) amountElement.textContent = currencySymbol + parseFloat(amount).toLocaleString();
            if (goalAmountElement) goalAmountElement.textContent = currencySymbol + parseFloat(amount).toLocaleString();
            
            // Update YouTube video preview
            updateVideoPreview();
            
            // Update gallery preview
            updateGalleryPreview();
        }
        
        function updateVideoPreview() {
            const youtubeUrl = document.getElementById('youtube_url_new').value;
            const videoPreview = document.getElementById('previewVideo');
            const videoContainer = document.getElementById('previewVideoContainer');
            
            if (youtubeUrl && youtubeUrl.trim() !== '') {
                // Convert YouTube URL to embed format
                let embedUrl = youtubeUrl;
                if (youtubeUrl.includes('youtube.com/watch?v=')) {
                    const videoId = youtubeUrl.split('v=')[1].split('&')[0];
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                } else if (youtubeUrl.includes('youtu.be/')) {
                    const videoId = youtubeUrl.split('youtu.be/')[1].split('?')[0];
                    embedUrl = `https://www.youtube.com/embed/${videoId}`;
                }
                
                if (videoContainer) {
                    videoContainer.innerHTML = `
                        <div class="ratio ratio-16x9">
                            <iframe src="${embedUrl}" 
                                    title="YouTube video player" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    `;
                }
                if (videoPreview) videoPreview.style.display = 'block';
            } else {
                if (videoPreview) videoPreview.style.display = 'none';
            }
        }
        
        function updateGalleryPreview() {
            const galleryPreview = document.getElementById('previewGallery');
            const galleryImages = document.getElementById('previewGalleryImages');
            
            // Get preview images
            const previewImages = document.querySelectorAll('.preview-image-item');
            
            if (previewImages.length > 0) {
                let galleryHTML = '<div class="row g-2">';
                
                // Add preview images
                previewImages.forEach(img => {
                    const imgElement = img.querySelector('img');
                    if (imgElement) {
                        const imgSrc = imgElement.src;
                        galleryHTML += `
                            <div class="col-6">
                                <img src="${imgSrc}" class="img-fluid rounded" style="width: 100%; height: 80px; object-fit: cover;">
                            </div>
                        `;
                    }
                });
                
                galleryHTML += '</div>';
                
                if (galleryImages) galleryImages.innerHTML = galleryHTML;
                if (galleryPreview) galleryPreview.style.display = 'block';
            } else {
                if (galleryPreview) galleryPreview.style.display = 'none';
            }
        }
        
        function refreshPreview() {
            updatePreview();
        }
        
        function setupRealTimePreview() {
            // Title input
            const titleInput = document.getElementById('CampignTitle');
            if (titleInput) {
                titleInput.addEventListener('input', updatePreview);
            }
            
            // Category select
            const categorySelect = document.getElementById('CampignCategory');
            if (categorySelect) {
                categorySelect.addEventListener('change', updatePreview);
            }
            
            // Target amount input
            const amountInput = document.getElementById('targetAmount');
            if (amountInput) {
                amountInput.addEventListener('input', updatePreview);
            }
            
            // YouTube URL input
            const youtubeInput = document.getElementById('youtube_url_new');
            if (youtubeInput) {
                youtubeInput.addEventListener('input', updatePreview);
            }
            
            // Main image input
            const mainImageInput = document.getElementById('mainImage');
            if (mainImageInput) {
                mainImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewImage = document.getElementById('previewImage');
                            const previewImageIcon = document.getElementById('previewImageIcon');
                            if (previewImage && previewImageIcon) {
                                previewImage.src = e.target.result;
                                previewImage.style.display = 'block';
                                previewImageIcon.style.display = 'none';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // Quill editor content changes
            if (window.quill) {
                window.quill.on('text-change', function() {
                    updatePreview();
                });
            }
            
            // Gallery dropzone changes
            const galleryDropzone = document.getElementById('galleryDropzone');
            if (galleryDropzone) {
                // Listen for new uploads
                galleryDropzone.addEventListener('drop', function() {
                    setTimeout(updatePreview, 1000); // Wait for upload to complete
                });
            }
            
            // Initial preview update
            updatePreview();
        }
        
        // Legacy preview function for compatibility
        function previewCampign() {
            updatePreview();
        }
        
        // Gallery upload functionality for new campaign
        function setupGalleryUpload() {
            const galleryInput = document.getElementById('galleryImages');
            const uploadProgress = document.getElementById('uploadProgress');
            const galleryPreview = document.getElementById('galleryPreview');
            const galleryPreviewImages = document.getElementById('galleryPreviewImages');
            
            if (galleryInput) {
                galleryInput.addEventListener('change', function(e) {
                    const files = Array.from(e.target.files);
                    
                    if (files.length === 0) return;
                    
                    // Validate file sizes (5MB max)
                    const validFiles = files.filter(file => {
                        if (file.size > 5 * 1024 * 1024) {
                            alert(`File ${file.name} is too large. Maximum size is 5MB.`);
                            return false;
                        }
                        return true;
                    });
                    
                    if (validFiles.length === 0) return;
                    
                    // Show preview of selected images
                    showImagePreview(validFiles);
                    
                    // Show progress bar
                    if (uploadProgress) {
                        uploadProgress.style.display = 'block';
                    }
                    
                    // Upload files one by one
                    uploadFilesSequentially(validFiles, 0);
                });
            }
        }
        
        function showImagePreview(files) {
            const galleryPreview = document.getElementById('galleryPreview');
            const galleryPreviewImages = document.getElementById('galleryPreviewImages');
            
            if (!galleryPreview || !galleryPreviewImages) return;
            
            // Clear existing preview
            galleryPreviewImages.innerHTML = '';
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHTML = `
                        <div class="col-6 col-md-4 col-lg-3 preview-image-item" data-index="${index}">
                            <div class="gallery-image-card">
                                <div class="image-container">
                                    <img src="${e.target.result}" 
                                         alt="Preview Image" 
                                         class="gallery-image">
                                    <div class="image-overlay">
                                        <button type="button" 
                                                class="btn btn-danger btn-sm remove-btn" 
                                                onclick="removePreviewImage(${index})"
                                                title="Remove Image">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    galleryPreviewImages.insertAdjacentHTML('beforeend', previewHTML);
                    galleryPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        }
        
        function removePreviewImage(index) {
            const galleryPreviewImages = document.getElementById('galleryPreviewImages');
            
            if (galleryPreviewImages) {
                // Remove the preview image by data-index
                const imageToRemove = galleryPreviewImages.querySelector(`[data-index="${index}"]`);
                if (imageToRemove) {
                    imageToRemove.remove();
                }
                
                // Check if no images left
                if (galleryPreviewImages.children.length === 0) {
                    const galleryPreview = document.getElementById('galleryPreview');
                    if (galleryPreview) {
                        galleryPreview.style.display = 'none';
                    }
                }
            }
        }
        
        function uploadFilesSequentially(files, index) {
            if (index >= files.length) {
                // All files uploaded
                const uploadProgress = document.getElementById('uploadProgress');
                if (uploadProgress) {
                    uploadProgress.style.display = 'none';
                }
                updateGalleryPreview();
                return;
            }
            
            const file = files[index];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Upload file via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("user.campaign.gallery.upload") }}');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.image) {
                            console.log('Image uploaded successfully:', response.image);
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        alert('Upload failed: Invalid response format');
                    }
                } else {
                    console.error('Upload failed with status:', xhr.status);
                    alert('Upload failed: Server error ' + xhr.status);
                }
                
                // Upload next file
                uploadFilesSequentially(files, index + 1);
            };
            
            xhr.onerror = function() {
                console.error('Network error during upload');
                alert('Upload failed: Network error');
                uploadFilesSequentially(files, index + 1);
            };
            
            xhr.send(formData);
        }
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
</style>


@endsection