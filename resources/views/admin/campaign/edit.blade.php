@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Edit Campaign')</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form--label required">@lang('Campaign Name')</label>
                            <input type="text" class="form--control" name="name" value="{{ old('name', $campaign->name) }}" placeholder="@lang('Enter campaign name')" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form--label required">@lang('Category')</label>
                            <select class="form--control" name="category_id" required>
                                <option value="">@lang('Select Category')</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $campaign->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ __($category->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form--label required">@lang('Description')</label>
                            <textarea class="form--control" name="description" rows="8" placeholder="@lang('Enter campaign description')" required>{{ old('description', $campaign->description) }}</textarea>
                            <small class="text-muted">@lang('Minimum 30 characters required')</small>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form--label required">@lang('Goal Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ bs()->site_cur }}</span>
                                <input type="number" class="form--control" name="goal_amount" value="{{ old('goal_amount', $campaign->goal_amount) }}" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            @error('goal_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form--label">@lang('Location')</label>
                            <input type="text" class="form--control" name="location" value="{{ old('location', $campaign->location) }}" placeholder="@lang('Enter location')">
                            @error('location')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form--label required">@lang('Start Date')</label>
                            <input type="date" class="form--control" name="start_date" value="{{ old('start_date', $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '') }}" required>
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form--label required">@lang('End Date')</label>
                            <input type="date" class="form--control" name="end_date" value="{{ old('end_date', $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}" required>
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('Current Image')</label>
                            <div class="mb-3" id="adminCampaignImagePreview">
                                @if($campaign->image)
                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" alt="{{ $campaign->name }}" id="adminCurrentCampaignImage" style="max-width: 400px; max-height: 300px; object-fit: cover; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                                @endif
                            </div>
                            <label class="form--label">@lang('Change Image')</label>
                            <input type="file" class="form--control" name="image" id="adminCampaignImageInput" accept="image/png,image/jpg,image/jpeg,image/webp">
                            <input type="hidden" name="uploaded_image" id="adminUploadedImageName" value="">
                            <input type="hidden" name="uploaded_image_original" id="adminUploadedImageOriginalName" value="">
                            <small class="text-muted">@lang('Leave empty to keep current image. Max 5MB. Formats: PNG, JPG, JPEG, WEBP')</small>
                            <div id="adminImageErrorMsg" class="text-danger mt-2" style="display: none;"></div>
                            <div id="adminImageUploadStatus" class="text-muted mt-1" style="display: none;"></div>
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('YouTube URL')</label>
                            <input type="url" class="form--control" name="youtube_url" value="{{ old('youtube_url', $campaign->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                            <small class="text-muted">@lang('Enter a valid YouTube URL')</small>
                            @error('youtube_url')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('Current Video')</label>
                            @if($campaign->video)
                                <div class="mb-3">
                                    <video controls style="max-width: 400px; max-height: 300px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                                        <source src="{{ getImage(getFilePath('campaignVideo') . '/' . $campaign->video, getFileSize('campaignVideo')) }}" type="video/mp4">
                                        @lang('Your browser does not support the video tag.')
                                    </video>
                                </div>
                            @endif
                            <label class="form--label">@lang('Change Video')</label>
                            <input type="file" class="form--control" name="video" accept="video/mp4,video/avi,video/mov,video/wmv,video/flv,video/3gp">
                            <small class="text-muted">@lang('Leave empty to keep current video. Max size: 500MB. Formats: MP4, AVI, MOV, WMV, FLV, 3GP')</small>
                            @error('video')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.campaigns.index') }}" class="btn btn--secondary">
                                    <i class="ti ti-arrow-left"></i> @lang('Back')
                                </a>
                                <button type="submit" class="btn btn--base">
                                    <i class="ti ti-device-floppy"></i> @lang('Update Campaign')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    (function() {
        const imageInput = document.getElementById("adminCampaignImageInput");
        const imageErrorMsg = document.getElementById("adminImageErrorMsg");
        const imageUploadStatus = document.getElementById("adminImageUploadStatus");
        const previewWrap = document.getElementById("adminCampaignImagePreview");
        const uploadedImageName = document.getElementById("adminUploadedImageName");
        const uploadedImageOriginalName = document.getElementById("adminUploadedImageOriginalName");
        const submitBtn = document.querySelector('button[type="submit"]');

        if (!imageInput) return;

        imageInput.addEventListener("change", function() {
            const file = this.files[0];
            if (!file) {
                if (imageErrorMsg) imageErrorMsg.style.display = "none";
                if (imageUploadStatus) imageUploadStatus.style.display = "none";
                if (uploadedImageName) uploadedImageName.value = "";
                if (uploadedImageOriginalName) uploadedImageOriginalName.value = "";
                return;
            }
            const maxSizeBytes = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSizeBytes) {
                if (imageErrorMsg) {
                    imageErrorMsg.textContent = "Image must be under 5MB. Selected file: " + (file.size / 1024 / 1024).toFixed(2) + " MB";
                    imageErrorMsg.style.display = "block";
                }
                if (imageUploadStatus) imageUploadStatus.style.display = "none";
                this.value = "";
                return;
            }

            if (imageErrorMsg) imageErrorMsg.style.display = "none";
            if (imageUploadStatus) {
                imageUploadStatus.textContent = "Uploading image...";
                imageUploadStatus.style.display = "block";
            }
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("admin.campaigns.upload-campaign-image") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (uploadedImageName) uploadedImageName.value = data.image;
                    if (uploadedImageOriginalName) uploadedImageOriginalName.value = data.image_original || "";

                    if (previewWrap && data.image_url) {
                        let previewImg = previewWrap.querySelector('img:not(#adminCurrentCampaignImage)');
                        const currentImg = document.getElementById("adminCurrentCampaignImage");
                        if (currentImg) currentImg.style.display = "none";
                        if (!previewImg) {
                            previewImg = document.createElement('img');
                            previewImg.style.cssText = "max-width: 400px; max-height: 300px; object-fit: cover; border: 1px solid #ddd; padding: 5px; border-radius: 5px;";
                            previewWrap.appendChild(previewImg);
                        }
                        previewImg.src = data.image_url;
                        previewImg.alt = "Image Preview";
                    }

                    if (imageUploadStatus) {
                        imageUploadStatus.textContent = "✓ Image uploaded successfully";
                    }
                } else {
                    if (imageErrorMsg) {
                        imageErrorMsg.textContent = data.message || "Image upload failed";
                        imageErrorMsg.style.display = "block";
                    }
                    if (imageUploadStatus) imageUploadStatus.style.display = "none";
                }
            })
            .catch(() => {
                if (imageErrorMsg) {
                    imageErrorMsg.textContent = "Failed to upload image. Please try again.";
                    imageErrorMsg.style.display = "block";
                }
                if (imageUploadStatus) imageUploadStatus.style.display = "none";
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
        });
    })();
</script>
@endpush

@push('breadcrumb')
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn--sm btn--secondary">
        <i class="ti ti-arrow-left"></i> @lang('Back to Campaigns')
    </a>
@endpush
