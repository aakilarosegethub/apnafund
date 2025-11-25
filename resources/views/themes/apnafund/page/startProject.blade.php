@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.app')

@section('content')
<div class="start-project-page">
    <!-- Hero Section -->
    <div class="start-project-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">Start Your Project</h1>
                    <p class="hero-subtitle">Turn your idea into reality. Create a compelling campaign and start raising funds today.</p>
                    <div class="hero-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Easy Setup</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Secure Payments</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>24/7 Support</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="{{ asset('assets/images/site/home/hero_bg.png') }}" alt="Start Project" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Form Section -->
    <div class="campaign-form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="campaign-form-card">
                        <div class="form-header">
                            <h2>Create Your Campaign</h2>
                            <p>Fill in the details below to get started</p>
                        </div>

                        @if(!auth()->check())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <a href="{{ route('user.login') }}" class="alert-link">Login</a> or 
                            <a href="{{ route('user.register') }}" class="alert-link">Register</a> to create a campaign
                        </div>
                        @endif

                        <!-- Step Progress Indicator -->
                        <div class="step-progress-container">
                            <div class="step-progress">
                                <div class="step-item active" data-step="1">
                                    <div class="step-circle">
                                        <span class="step-icon"><i class="fas fa-folder"></i></span>
                                        <span class="step-check"><i class="fas fa-check"></i></span>
                                    </div>
                                    <div class="step-label">Category</div>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-item" data-step="2">
                                    <div class="step-circle">
                                        <span class="step-icon"><i class="fas fa-map-marker-alt"></i></span>
                                        <span class="step-check"><i class="fas fa-check"></i></span>
                                    </div>
                                    <div class="step-label">Location</div>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step-item" data-step="3">
                                    <div class="step-circle">
                                        <span class="step-icon"><i class="fas fa-file-alt"></i></span>
                                        <span class="step-check"><i class="fas fa-check"></i></span>
                                    </div>
                                    <div class="step-label">Basics</div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ auth()->check() ? route('user.campaign.store') : route('user.register') }}" method="POST" id="startProjectForm" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Step 1: Category Selection -->
                            <div class="form-step active" id="step1">
                                <div class="step-intro">
                                <h3 class="step-title">
                                    <span class="step-number">1</span>
                                        First, let's get you set up
                                </h3>
                                    <p class="step-description">Select a primary category and subcategory for your new project.</p>
                                    <p class="step-hint">These will help backers find your project, and you can change them later if you need to.</p>
                                </div>
                                
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                <div class="row">
                                            <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="campaignCategory" class="form-label">
                                                        Primary Category <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                                    name="category_id" 
                                                    id="campaignCategory" 
                                                    required>
                                                        <option value="">Select</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                            <div class="col-md-6">
                                <div class="form-group">
                                                    <label for="campaignSubCategory" class="form-label">
                                                        Subcategory <span class="text-danger">*</span>
                                    </label>
                                                    <select class="form-control @error('subcategory_id') is-invalid @enderror" 
                                                            name="subcategory_id" 
                                                            id="campaignSubCategory" 
                                                            required>
                                                        <option value="">Select</option>
                                                        @if(isset($subcategories) && count($subcategories) > 0)
                                                            @foreach ($subcategories as $subcategory)
                                                                <option value="{{ $subcategory->id }}" 
                                                                        data-category-id="{{ $subcategory->category_id ?? '' }}"
                                                                        {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                                                    {{ $subcategory->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('subcategory_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <div class="step-footer-text">
                                        <span>Your first project! Welcome.</span>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-next" id="step1NextBtn" onclick="nextStep(); return false;" disabled>
                                        Next: Additional subcategory <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Location -->
                            <div class="form-step" id="step2">
                                <div class="step-intro">
                                    <h3 class="step-title">
                                        <span class="step-number">2</span>
                                        Last one—set a location for your project
                                    </h3>
                                    <p class="step-description">Pick your country of legal residence if you're raising funds as an individual. If you're raising funds for a business or nonprofit, select the country where the entity's tax ID is registered.</p>
                                </div>
                                
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="campaignCountry" class="form-label">
                                                Country <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control @error('location') is-invalid @enderror" 
                                                    id="campaignCountry" 
                                                    name="location" 
                                                    required>
                                                <option value="">Select</option>
                                                @php
                                                    $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
                                                @endphp
                                                @foreach($countryData as $code => $country)
                                                    <option value="{{ $country->country }}" {{ old('location') == $country->country ? 'selected' : '' }}>
                                                        {{ $country->country }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <a href="javascript:void(0)" class="step-footer-link" onclick="prevStep(); return false;">
                                        ← Additional subcategory
                                    </a>
                                    <button type="button" class="btn btn-primary btn-next" id="step2NextBtn" onclick="nextStep(); return false;" disabled>
                                        Continue <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Campaign Details (Final Step) -->
                            <div class="form-step" id="step3">
                                <div class="step-intro">
                                    <h3 class="step-title">
                                        <span class="step-number">3</span>
                                        Campaign Basics
                                    </h3>
                                    <p class="step-description">Tell us about your campaign. You can add more details and media later in the edit page.</p>
                                </div>
                                
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="campaignName" class="form-label">
                                                Campaign Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="campaignName" 
                                                   name="name" 
                                                   placeholder="Enter a compelling title for your campaign" 
                                                   value="{{ old('name') }}"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Make it catchy and descriptive</small>
                                </div>

                                <div class="form-group">
                                    <label for="campaignDescription" class="form-label">
                                        Campaign Story <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="campaignDescription" 
                                              name="description" 
                                                      rows="10" 
                                                      placeholder="Tell your story. Why are you raising funds? What will the money be used for? Be detailed and engaging." 
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                            <small class="form-text text-muted">
                                                <span id="charCount">0</span> / Minimum 30 characters. Be detailed and engaging.
                                            </small>
                                        </div>

                                        <!-- Hidden fields for required data (will be set to defaults) -->
                                        <input type="hidden" name="goal_amount" value="1000">
                                        <input type="hidden" name="start_date" value="{{ date('Y-m-d') }}">
                                        <input type="hidden" name="end_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}">

                                        <div class="info-box mt-4">
                                            <div class="info-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="info-content">
                                                <strong>Note:</strong> Your campaign will be created with default settings. You can add images, set goals, and customize all details in the edit page after creation.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-outline-secondary btn-prev" onclick="prevStep(); return false;">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button type="submit" class="btn btn-success btn-submit" id="step3SubmitBtn">
                                        <span class="btn-text"><i class="fas fa-rocket"></i> Create Campaign</span>
                                        <span class="btn-loading" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i> Creating Campaign...
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 4: Goals & Timeline (Hidden - can be edited later) -->
                            <div class="form-step" id="step4">
                                <h3 class="step-title">
                                    <span class="step-number">4</span>
                                    Goals & Timeline
                                </h3>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="goalAmount" class="form-label">
                                                Goal Amount <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $setting->cur_sym ?? '$' }}</span>
                                                <input type="number" 
                                                       class="form-control @error('goal_amount') is-invalid @enderror" 
                                                       id="goalAmount" 
                                                       name="goal_amount" 
                                                       placeholder="0.00" 
                                                       step="0.01" 
                                                       min="1"
                                                       value="{{ old('goal_amount') }}"
                                                       required>
                                            </div>
                                            @error('goal_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">How much do you need to raise?</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="startDate" class="form-label">
                                                Start Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="startDate" 
                                                   name="start_date" 
                                                   value="{{ old('start_date', date('Y-m-d')) }}"
                                                   min="{{ date('Y-m-d') }}"
                                                   required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="endDate" class="form-label">
                                                End Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="endDate" 
                                                   name="end_date" 
                                                   value="{{ old('end_date') }}"
                                                   required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-outline-secondary btn-prev" onclick="prevStep()">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-primary btn-next" onclick="nextStep(); return false;">
                                        Next: Add Media <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Step 5: Media -->
                            <div class="form-step" id="step5">
                                <h3 class="step-title">
                                    <span class="step-number">5</span>
                                    Add Media
                                </h3>
                                
                                <div class="form-group">
                                    <label for="campaignImage" class="form-label">
                                        Campaign Image <span class="text-danger">*</span>
                                    </label>
                                    <div class="file-upload-wrapper">
                                    <input type="file" 
                                               class="form-control file-input @error('image') is-invalid @enderror" 
                                           id="campaignImage" 
                                           name="image" 
                                           accept="image/*" 
                                           required>
                                        <div class="file-upload-area" id="fileUploadArea">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p class="upload-text">Click to upload or drag and drop</p>
                                            <p class="upload-hint">PNG, JPG, JPEG (Max 5MB)</p>
                                        </div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    </div>
                                    <div class="image-preview mt-3" id="imagePreview" style="display: none;">
                                        <div class="preview-wrapper">
                                            <img src="" alt="Preview" id="previewImage">
                                            <button type="button" class="remove-image-btn" onclick="removeImage()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="youtubeUrl" class="form-label">
                                        YouTube Video URL (Optional)
                                    </label>
                                    <input type="url" 
                                           class="form-control @error('youtube_url') is-invalid @enderror" 
                                           id="youtubeUrl" 
                                           name="youtube_url" 
                                           placeholder="https://www.youtube.com/watch?v=..." 
                                           value="{{ old('youtube_url') }}">
                                    @error('youtube_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Add a video to make your campaign more engaging</small>
                                </div>

                                <div class="form-actions">
                                    <button type="button" class="btn btn-outline-secondary btn-prev" onclick="prevStep(); return false;">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button type="submit" class="btn btn-success btn-submit" id="submitBtn">
                                        <span class="btn-text"><i class="fas fa-rocket"></i> Launch Campaign</span>
                                        <span class="btn-loading" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i> Creating Campaign...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.start-project-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding-bottom: 50px;
}

.start-project-hero {
    background: linear-gradient(135deg, #05ce78 0%, #04b869 100%);
    color: white;
    padding: 80px 0;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.hero-subtitle {
    font-size: 1.25rem;
    margin-bottom: 30px;
    opacity: 0.95;
}

.hero-features {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
}

.feature-item i {
    font-size: 1.5rem;
}

.campaign-form-section {
    margin-top: -50px;
    position: relative;
    z-index: 10;
}

.campaign-form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-bottom: 30px;
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.form-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.form-header p {
    color: #666;
    font-size: 1.1rem;
}

.form-step {
    display: none;
    animation: fadeOut 0.3s ease-out;
}

.form-step.active {
    display: block;
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

.step-title {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 1.8rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #05ce78;
}

.step-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: #05ce78;
    color: white;
    border-radius: 50%;
    font-size: 1.5rem;
    font-weight: 700;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 12px 15px;
    font-size: 1rem;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: #05ce78;
    box-shadow: 0 0 0 0.2rem rgba(5, 206, 120, 0.25);
}

.form-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s;
}

.btn-primary {
    background: #05ce78;
    border: none;
    color: white;
}

.btn-primary:hover {
    background: #04b869;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(5, 206, 120, 0.3);
}

.btn-success {
    background: #28a745;
    border: none;
    color: white;
}

.btn-success:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

.alert {
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 30px;
}

/* Step Progress Indicator */
.step-progress-container {
    margin-bottom: 40px;
    padding: 20px 0;
}

.step-progress {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    cursor: pointer;
}

.step-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e9ecef;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.3s ease;
    cursor: pointer;
}

.step-item.active .step-circle {
    background: #05ce78;
    border-color: #05ce78;
    transform: scale(1.1);
    box-shadow: 0 0 0 8px rgba(5, 206, 120, 0.2);
}

.step-item.completed .step-circle {
    background: #05ce78;
    border-color: #05ce78;
}

.step-icon {
    color: #6c757d;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.step-item.active .step-icon,
.step-item.completed .step-icon {
    color: white;
}

.step-check {
    position: absolute;
    color: white;
    font-size: 1.2rem;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

.step-item.completed .step-check {
    opacity: 1;
    transform: scale(1);
}

.step-item.completed .step-icon {
    opacity: 0;
    transform: scale(0);
}

.step-label {
    margin-top: 10px;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.step-intro {
    text-align: center;
    margin-bottom: 40px;
}

.step-intro .step-title {
    justify-content: center;
    border-bottom: none;
    margin-bottom: 15px;
}

.step-description {
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 10px;
    font-weight: 500;
}

.step-hint {
    font-size: 0.95rem;
    color: #6c757d;
    margin: 0;
}

.step-footer-text {
    color: #6c757d;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.step-footer-link {
    color: #6c757d;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.step-footer-link:hover {
    color: #05ce78;
    text-decoration: underline;
}

.info-box {
    background: rgba(5, 206, 120, 0.1);
    border-left: 4px solid #05ce78;
    padding: 15px 20px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.info-icon {
    color: #05ce78;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.info-content {
    color: #333;
    font-size: 0.95rem;
    line-height: 1.6;
}

.info-content strong {
    color: #05ce78;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn:disabled:hover {
    transform: none;
    box-shadow: none;
}

.step-item.active .step-label {
    color: #05ce78;
    font-size: 1rem;
}

.step-connector {
    flex: 1;
    height: 3px;
    background: #e9ecef;
    margin: 0 10px;
    position: relative;
    transition: all 0.3s ease;
}

.step-connector.completed {
    background: #05ce78;
}

/* File Upload Styling */
.file-upload-wrapper {
    position: relative;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
}

.file-upload-area {
    border: 3px dashed #e0e0e0;
    border-radius: 15px;
    padding: 40px 20px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #05ce78;
    background: rgba(5, 206, 120, 0.05);
}

.file-upload-area.has-file {
    border-color: #05ce78;
    background: rgba(5, 206, 120, 0.1);
}

.file-upload-area i {
    font-size: 3.5rem;
    color: #6c757d;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.file-upload-area:hover i,
.file-upload-area.has-file i {
    color: #05ce78;
    transform: scale(1.1);
}

.upload-text {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.upload-hint {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}

.image-preview {
    margin-top: 20px;
}

.preview-wrapper {
    position: relative;
    display: inline-block;
}

.preview-wrapper img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.remove-image-btn {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(220, 53, 69, 0.3);
}

.remove-image-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .campaign-form-card {
        padding: 25px 15px;
    }
    
    .form-header h2 {
        font-size: 1.8rem;
    }
    
    .step-progress {
        max-width: 100%;
        padding: 0 10px;
    }
    
    .step-circle {
        width: 50px;
        height: 50px;
    }
    
    .step-icon {
        font-size: 1.2rem;
    }
    
    .step-label {
        font-size: 0.75rem;
    }
    
    .step-connector {
        margin: 0 5px;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 15px;
    }
    
    .btn {
        width: 100%;
    }
    
    .file-upload-area {
        padding: 30px 15px;
    }
    
    .file-upload-area i {
        font-size: 2.5rem;
    }
    
    .upload-text {
        font-size: 0.95rem;
    }
    
    .upload-hint {
        font-size: 0.8rem;
    }
}
</style>

<script>
let currentStep = 1;
const totalSteps = 3;

function updateStepProgress() {
    // Update step indicators
    for (let i = 1; i <= totalSteps; i++) {
        const stepItem = document.querySelector(`.step-item[data-step="${i}"]`);
        const connector = stepItem?.nextElementSibling;
        
        if (i < currentStep) {
            stepItem.classList.add('completed');
            stepItem.classList.remove('active');
            if (connector && connector.classList.contains('step-connector')) {
                connector.classList.add('completed');
            }
        } else if (i === currentStep) {
            stepItem.classList.add('active');
            stepItem.classList.remove('completed');
        } else {
            stepItem.classList.remove('active', 'completed');
            if (connector && connector.classList.contains('step-connector')) {
                connector.classList.remove('completed');
            }
        }
    }
}

function nextStep() {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            document.getElementById('step' + currentStep).classList.remove('active');
            currentStep++;
            document.getElementById('step' + currentStep).classList.add('active');
            updateStepProgress();
            // Scroll to top of form
            document.querySelector('.campaign-form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    return false;
}

function prevStep() {
    if (currentStep > 1) {
        document.getElementById('step' + currentStep).classList.remove('active');
        currentStep--;
        document.getElementById('step' + currentStep).classList.add('active');
        updateStepProgress();
        // Scroll to top of form
        document.querySelector('.campaign-form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    return false;
}

// Initialize step progress on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStepProgress();
    
    // Category and Subcategory handling for Step 1
    const categorySelect = document.getElementById('campaignCategory');
    const subcategorySelect = document.getElementById('campaignSubCategory');
    const step1NextBtn = document.getElementById('step1NextBtn');
    
    if (categorySelect && subcategorySelect && step1NextBtn) {
        // Store all subcategory options
        const allSubcategories = Array.from(subcategorySelect.options);
        
        // Filter subcategories based on selected category
        categorySelect.addEventListener('change', function() {
            const selectedCategoryId = this.value;
            
            // Clear subcategory selection
            subcategorySelect.innerHTML = '<option value="">Select</option>';
            
            // Filter and add matching subcategories
            allSubcategories.forEach(option => {
                if (option.value === '') return; // Skip the default option
                const optionCategoryId = option.getAttribute('data-category-id');
                if (!optionCategoryId || optionCategoryId === selectedCategoryId || selectedCategoryId === '') {
                    subcategorySelect.appendChild(option.cloneNode(true));
                }
            });
            
            // Reset subcategory selection
            subcategorySelect.value = '';
            checkStep1Validation();
        });
        
        // Check validation when subcategory changes
        subcategorySelect.addEventListener('change', function() {
            checkStep1Validation();
        });
        
        // Function to check if Step 1 is valid
        function checkStep1Validation() {
            const hasCategory = categorySelect.value !== '';
            const hasSubcategory = subcategorySelect.value !== '';
            
            if (hasCategory && hasSubcategory) {
                step1NextBtn.disabled = false;
            } else {
                step1NextBtn.disabled = true;
            }
        }
        
        // Initial check
        checkStep1Validation();
        
        // Add click event listener for step 1 next button
        step1NextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!step1NextBtn.disabled) {
                nextStep();
            }
            return false;
        });
    }
    
    // Make step indicators clickable
    document.querySelectorAll('.step-item').forEach((item, index) => {
        item.addEventListener('click', function() {
            const stepNum = parseInt(this.getAttribute('data-step'));
            if (stepNum < currentStep) {
                // Allow going back to previous steps
                document.getElementById('step' + currentStep).classList.remove('active');
                currentStep = stepNum;
                document.getElementById('step' + currentStep).classList.add('active');
                updateStepProgress();
                document.querySelector('.campaign-form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
    
    // Step 2 Country selection handling
    const countrySelect = document.getElementById('campaignCountry');
    const step2NextBtn = document.getElementById('step2NextBtn');
    
    if (countrySelect && step2NextBtn) {
        // Check validation when country changes
        countrySelect.addEventListener('change', function() {
            checkStep2Validation();
        });
        
        // Function to check if Step 2 is valid
        function checkStep2Validation() {
            const hasCountry = countrySelect.value !== '';
            
            if (hasCountry) {
                step2NextBtn.disabled = false;
            } else {
                step2NextBtn.disabled = true;
            }
        }
        
        // Initial check
        checkStep2Validation();
        
        // Add click event listener for step 2 next button
        step2NextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!step2NextBtn.disabled) {
                nextStep();
            }
            return false;
        });
    }
    
    // Character counter for description
    const descriptionField = document.getElementById('campaignDescription');
    const charCount = document.getElementById('charCount');
    if (descriptionField && charCount) {
        descriptionField.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            if (length < 30) {
                charCount.style.color = '#dc3545';
            } else {
                charCount.style.color = '#05ce78';
            }
        });
        // Initialize count
        if (descriptionField.value) {
            const length = descriptionField.value.length;
            charCount.textContent = length;
            if (length < 30) {
                charCount.style.color = '#dc3545';
            } else {
                charCount.style.color = '#05ce78';
            }
        }
    }
});

function validateStep(step) {
    let isValid = true;
    const errorMessages = [];
    
    if (step === 1) {
        const category = document.getElementById('campaignCategory');
        const subcategory = document.getElementById('campaignSubCategory');
        
        // Remove previous error states
        [category, subcategory].forEach(field => {
            if (field) field.classList.remove('is-invalid');
        });
        
        if (!category.value) {
            category.classList.add('is-invalid');
            errorMessages.push('Please select a primary category');
            isValid = false;
        }
        
        if (!subcategory.value) {
            subcategory.classList.add('is-invalid');
            errorMessages.push('Please select a subcategory');
            isValid = false;
        }
        
        if (errorMessages.length > 0) {
            showValidationError(errorMessages[0]);
        }
    } else if (step === 2) {
        const country = document.getElementById('campaignCountry');
        
        // Remove previous error states
        if (country) country.classList.remove('is-invalid');
        
        if (!country.value) {
            country.classList.add('is-invalid');
            errorMessages.push('Please select your country');
            isValid = false;
        }
        
        if (errorMessages.length > 0) {
            showValidationError(errorMessages[0]);
        }
    } else if (step === 3) {
        const name = document.getElementById('campaignName');
        const description = document.getElementById('campaignDescription');
        
        // Remove previous error states
        [name, description].forEach(field => {
            if (field) field.classList.remove('is-invalid');
        });
        
        if (!name.value.trim()) {
            name.classList.add('is-invalid');
            errorMessages.push('Please enter a campaign title');
            isValid = false;
        }
        
        if (!description.value.trim() || description.value.trim().length < 30) {
            description.classList.add('is-invalid');
            errorMessages.push('Please enter a description with at least 30 characters');
            isValid = false;
        }
        
        if (errorMessages.length > 0) {
            showValidationError(errorMessages[0]);
        }
    } else if (step === 4) {
        const goalAmount = document.getElementById('goalAmount');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        
        // Remove previous error states
        [goalAmount, startDate, endDate].forEach(field => {
            if (field) field.classList.remove('is-invalid');
        });
        
        if (!goalAmount.value || parseFloat(goalAmount.value) <= 0) {
            goalAmount.classList.add('is-invalid');
            errorMessages.push('Please enter a valid goal amount');
            isValid = false;
        }
        
        if (!startDate.value) {
            startDate.classList.add('is-invalid');
            errorMessages.push('Please select a start date');
            isValid = false;
        }
        
        if (!endDate.value) {
            endDate.classList.add('is-invalid');
            errorMessages.push('Please select an end date');
            isValid = false;
        } else if (startDate.value && new Date(endDate.value) <= new Date(startDate.value)) {
            endDate.classList.add('is-invalid');
            errorMessages.push('End date must be after start date');
            isValid = false;
        }
        
        if (errorMessages.length > 0) {
            showValidationError(errorMessages[0]);
        }
    } else if (step === 5) {
        const imageInput = document.getElementById('campaignImage');
        
        if (imageInput) {
            imageInput.classList.remove('is-invalid');
            
            if (!imageInput.files || !imageInput.files[0]) {
                imageInput.classList.add('is-invalid');
                errorMessages.push('Please upload a campaign image');
                isValid = false;
            }
        }
        
        if (errorMessages.length > 0) {
            showValidationError(errorMessages[0]);
        }
    }
    
    return isValid;
}

function showValidationError(message) {
    // Create or update error toast
    if (typeof iziToast !== 'undefined') {
        iziToast.error({
            title: 'Validation Error',
            message: message,
            position: 'topRight',
            timeout: 3000
        });
    } else {
        alert(message);
    }
}

// Enhanced Image preview and file upload
const fileInput = document.getElementById('campaignImage');
const fileUploadArea = document.getElementById('fileUploadArea');
const imagePreview = document.getElementById('imagePreview');
const previewImage = document.getElementById('previewImage');

if (fileInput && fileUploadArea) {
    // Click on upload area to trigger file input
    fileUploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileUploadArea.style.borderColor = '#05ce78';
        fileUploadArea.style.background = 'rgba(5, 206, 120, 0.1)';
    });

    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        if (!fileInput.files.length) {
            fileUploadArea.style.borderColor = '#e0e0e0';
            fileUploadArea.style.background = '#f8f9fa';
        }
    });

    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
            handleFileSelect(file);
        }
    });

    function handleFileSelect(file) {
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('Please select an image file');
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Image size must be less than 5MB');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            imagePreview.style.display = 'block';
            fileUploadArea.classList.add('has-file');
            fileUploadArea.querySelector('.upload-text').textContent = file.name;
            fileUploadArea.querySelector('.upload-hint').textContent = (file.size / 1024).toFixed(2) + ' KB';
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    fileInput.value = '';
    imagePreview.style.display = 'none';
    fileUploadArea.classList.remove('has-file');
    fileUploadArea.querySelector('.upload-text').textContent = 'Click to upload or drag and drop';
    fileUploadArea.querySelector('.upload-hint').textContent = 'PNG, JPG, JPEG (Max 5MB)';
}

// Set minimum end date based on start date (only if startDate field exists)
const startDateField = document.getElementById('startDate');
if (startDateField) {
    startDateField.addEventListener('change', function() {
    const startDate = new Date(this.value);
    startDate.setDate(startDate.getDate() + 1);
        const endDateField = document.getElementById('endDate');
        if (endDateField) {
            endDateField.min = startDate.toISOString().split('T')[0];
        }
    });
}

// Form submission handling
const startProjectForm = document.getElementById('startProjectForm');
const step3SubmitBtn = document.getElementById('step3SubmitBtn');
const submitBtn = document.getElementById('submitBtn');

// Step 3 submit button (Create Campaign)
if (startProjectForm && step3SubmitBtn) {
    step3SubmitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Validate step 3 before submission
        if (!validateStep(3)) {
            return false;
        }
        
        // Show loading state
        step3SubmitBtn.disabled = true;
        step3SubmitBtn.querySelector('.btn-text').style.display = 'none';
        step3SubmitBtn.querySelector('.btn-loading').style.display = 'inline-block';
        
        // Submit the form
        startProjectForm.submit();
    });
}

// Final submit button (if exists - for step 5)
if (startProjectForm && submitBtn) {
    startProjectForm.addEventListener('submit', function(e) {
        // Only validate if we're on step 5 (media step)
        if (currentStep === 5) {
            // Validate step 5 before submission
            if (!validateStep(5)) {
                e.preventDefault();
                return false;
            }
            
            // Check if image is uploaded
            const imageInput = document.getElementById('campaignImage');
            if (imageInput && (!imageInput.files || !imageInput.files[0])) {
                e.preventDefault();
                showValidationError('Please upload a campaign image');
                imageInput.classList.add('is-invalid');
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.querySelector('.btn-text').style.display = 'none';
            submitBtn.querySelector('.btn-loading').style.display = 'inline-block';
        }
        
        // Form will submit normally, server will handle response
    });
}
</script>
@endsection

