@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('Report Fundraiser Content Management')</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.report.fundraiser') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title" class="form-label">@lang('Page Title')</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="title" 
                                           name="title" 
                                           value="{{ @$reportContent->data_info->title ?? 'Report a Fundraiser' }}"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="content" class="form-label">@lang('Content')</label>
                                    <textarea class="form-control" 
                                              id="content" 
                                              name="content" 
                                              rows="15" 
                                              required>{{ @$reportContent->data_info->content ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="status" 
                                               name="status" 
                                               value="1"
                                               {{ @$reportContent->data_info->status == ManageStatus::ACTIVE ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">
                                            @lang('Enable Report Fundraiser Page')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy"></i> @lang('Update Content')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-style')
<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #333;
    }
    
    .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #05ce78;
        box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1);
    }
    
    .form-check-input:checked {
        background-color: #05ce78;
        border-color: #05ce78;
    }
    
    .btn-primary {
        background-color: #05ce78;
        border-color: #05ce78;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #04b368;
        border-color: #04b368;
        transform: translateY(-2px);
    }
    
    /* CKEditor styling */
    .ck-editor__editable {
        min-height: 300px;
    }
    
    .ck-editor__editable:focus {
        border-color: #05ce78 !important;
        box-shadow: 0 0 0 3px rgba(5, 206, 120, 0.1) !important;
    }
</style>
@endpush

@push('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#content'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'link', 'blockQuote', '|',
                        'undo', 'redo'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .then(editor => {
                console.log('CKEditor initialized successfully');
                // Store editor instance globally for form submission
                window.reportEditor = editor;
            })
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
    });
    
    // Update textarea before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        if (window.reportEditor) {
            document.querySelector('#content').value = window.reportEditor.getData();
        }
    });
</script>
@endpush
