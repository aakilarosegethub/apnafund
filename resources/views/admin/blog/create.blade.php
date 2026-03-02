@extends('admin.layouts.master')

@php
    $pageTitle = 'Add New DSA Post';
@endphp

@section('master')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" id="blogForm">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form--label required">@lang('Title')</label>
                            <input type="text" class="form--control" name="title" value="{{ old('title') }}" placeholder="Enter post title" required>
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('Feature Image')</label>
                            <input type="file" class="form--control" name="feature_image" id="feature_image" accept="image/*">
                            <small class="text-muted">Max size: 2MB, Formats: JPEG, PNG, JPG, GIF, WebP. Recommended: 855x475px</small>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" style="max-width: 300px; max-height: 200px; object-fit: cover; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('Excerpt')</label>
                            <textarea class="form--control" name="excerpt" rows="2" placeholder="Short description (optional)">{{ old('excerpt') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('Content')</label>
                            <div class="editor-wrapper">
                                <textarea class="form--control" name="content" id="content" rows="10" placeholder="Write your DSA post content...">{{ old('content') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form--label">@lang('Order')</label>
                            <input type="number" class="form--control" name="order" value="{{ old('order', 0) }}" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form--label required">@lang('Status')</label>
                            <select class="form--control" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>@lang('Active')</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>@lang('Inactive')</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.blog.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                                <button type="submit" class="btn btn--base">@lang('Create Post')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@push('breadcrumb')
    <a href="{{ route('admin.blog.index') }}" class="btn btn--sm btn--secondary">
        <i class="ti ti-arrow-left"></i> @lang('Back')
    </a>
@endpush
@endsection

@push('page-style')
<style>
    .ck-editor__editable { min-height: 250px; }
</style>
@endpush

@push('page-script')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview
        document.getElementById('feature_image').addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });

        // CKEditor
        if (document.querySelector('#content')) {
            ClassicEditor.create(document.querySelector('#content'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'bulletedList', 'numberedList', '|', 'link', 'blockQuote', '|', 'undo', 'redo']
            }).then(editor => {
                window.blogEditor = editor;
            }).catch(err => console.error(err));
        }

        document.getElementById('blogForm').addEventListener('submit', function() {
            if (window.blogEditor) {
                document.querySelector('#content').value = window.blogEditor.getData();
            }
        });
    });
</script>
@endpush
