@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-body">
                @if($key == 'page_seo' || $key == 'footer_menu' || $key == 'schema_markup')
                    <div class="mb-3">
                        <a href="{{ route('admin.site.sections', $key) }}" class="btn btn--base btn--sm">
                            <i class="ti ti-arrow-left"></i> @lang('Back')
                        </a>
                    </div>
                @endif
                
                <form class="row g-4" action="{{ route('admin.site.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="type" value="element">

                    @if(@$data)
                        <input type="hidden" name="id" value="{{$data->id}}">
                    @endif

                    @php $imgCount = 0; @endphp

                    @foreach($section->element as $k => $item)
                        @if($k == 'images')
                            @php $imgCount = collect($item)->count(); @endphp

                            @foreach($item as $imgKey => $image)
                                <div class="col-lg-4 col-md-4 col-sm-6">
                                    <div class="upload__img mb-2">
                                        <label for="image{{ $loop->index }}" class="upload__img__btn"><i class="ti ti-camera"></i></label>

                                        <input type="file" id="image{{ $loop->index }}" class="image-upload" name="image_input[{{ @$imgKey }}]" accept=".jpeg, .jpg, .png">

                                        <label for="image{{ $loop->index }}" class="upload__img-preview image-preview">
                                            @php
                                                $currentImage = @$data->data_info[$imgKey] ?? '';
                                                $imageSize = @$section->element->images->$imgKey->size;
                                                if ($currentImage && filter_var($currentImage, FILTER_VALIDATE_URL)) {
                                                    $imageUrl = $currentImage;
                                                } elseif ($currentImage) {
                                                    $imageUrl = siteImageUrl('assets/images/site/' . $key . '/' . $currentImage, $imageSize);
                                                } else {
                                                    $imageUrl = siteImageUrl('', $imageSize);
                                                }
                                            @endphp
                                            <img src="{{ $imageUrl }}" alt="{{ @$data->data_info[$imgKey.'_alt'] ?? 'image' }}">
                                        </label>

                                        <button type="button" class="btn btn--sm btn--icon btn--danger custom-file-input-clear d-none"><i class="ti ti-circle-x"></i></button>
                                    </div>
                                    <label class="text-center small">@lang('Supported files'):
                                        <span class="fw-semibold text--base">@lang('jpeg'), @lang('jpg'), @lang('png').</span>
                                        @if(@$section->element->images->$imgKey->size)
                                            @lang('Image size') <span class="fw-semibold text--base">{{ @$section->element->images->$imgKey->size }}@lang('px').</span>
                                        @endif

                                        @if(@$section->element->images->$imgKey->thumb)
                                            @lang('Thumb size') <span class="fw-semibold text--base">{{ @$section->element->images->$imgKey->thumb }}@lang('px').</span>
                                        @endif
                                    </label>
                                    
                                    <!-- Image URL Field -->
                                    <div class="mt-2">
                                        <label class="form--label">@lang('Or Enter Image URL') ({{ keyToTitle($imgKey) }})</label>
                                        <input type="url" class="form--control" name="{{ $imgKey }}_url" value="{{ (filter_var(@$data->data_info[$imgKey] ?? '', FILTER_VALIDATE_URL)) ? @$data->data_info[$imgKey] : '' }}" placeholder="@lang('https://example.com/image.jpg')">
                                        <small class="text-muted">@lang('Leave empty if uploading file above')</small>
                                    </div>
                                    
                                    <!-- Image Alt Text Field -->
                                    <div class="mt-2">
                                        <label class="form--label">@lang('Image Alt Text') ({{ keyToTitle($imgKey) }})</label>
                                        <input type="text" class="form--control" name="{{ $imgKey }}_alt" value="{{ @$data->data_info[$imgKey.'_alt'] ?? '' }}" placeholder="@lang('Enter alt text for accessibility')">
                                    </div>
                                </div>
                            @endforeach

                            <div class="@if($imgCount > 1) col-lg-12 col-md-12 @else col-lg-8 col-md-8 @endif">
                                <div class="row g-lg-4 g-3">
                                    @push('divend')
                                </div>
                            </div>
                            @endpush
                        @else
                            <div class="col-12">
                                <div class="row g-lg-4 g-3">
                                    @if($k != 'images')
                                        @if($item == 'icon')
                                            <div class="col-12">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-3">
                                                        <label class="form--label required">{{ __(keyToTitle($k)) }}</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <div class="input--group">
                                                            <input type="text" class="form--control iconPicker icon" name="{{ $k }}" value="{{ @$data->data_info[$k] ?? '' }}" autocomplete="off" required>
                                                            <span class="input-group-text input-group-addon" data-icon="ti ti-home" role="iconpicker">@php echo @$data->data_info[$k] ?? ''; @endphp</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($item == 'textarea')
                                            <div class="col-12">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-3">
                                                        @php
                                                            // Make fields optional for page_seo and schema_markup
                                                            $isRequired = ($key != 'page_seo' && $key != 'schema_markup');
                                                            // For schema_json, add rows and placeholder
                                                            $isSchemaJson = ($key == 'schema_markup' && $k == 'schema_json');
                                                        @endphp
                                                        <label class="form--label {{ $isRequired ? 'required' : '' }}">{{ __(keyToTitle($k)) }}</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        @if($isSchemaJson)
                                                            <textarea class="form--control" name="{{ $k }}" rows="15" placeholder='{"@context": "https://schema.org", "@type": "Organization", "name": "Your Company", ...}' style="font-family: monospace; font-size: 13px;">{{ @$data->data_info[$k] ?? '' }}</textarea>
                                                            <small class="text--muted d-block mt-1">@lang('Enter valid JSON-LD schema markup. Example: {"@context": "https://schema.org", "@type": "Organization", ...}')</small>
                                                        @else
                                                            <textarea class="form--control" name="{{ $k }}" {{ $isRequired ? 'required' : '' }}>{{ @$data->data_info[$k] ?? '' }}</textarea>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($item == 'textarea-editor')
                                            <div class="col-12 editor-wrapper">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-3">
                                                        <label class="form--label required">{{ __(keyToTitle($k)) }}</label>
                                                    </div>
                                                    <div class="col-lg-9 editor-wrapper">
                                                        <textarea class="form--control trumEdit" name="{{ $k }}">{{ @$data->data_info[$k] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($k == 'select')
                                            @php $selectName = $item->name; @endphp

                                            <div class="col-12">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-3">
                                                        <label class="form--label required">{{ __(keyToTitle(@$selectName)) }}</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <select class="form--control form-select" name="{{ @$selectName }}" required>
                                                            @foreach($item->options as $selectItemKey => $selectOption)
                                                                <option value="{{ $selectItemKey }}" @if((@$data->data_info[$selectName] ?? '') == $selectItemKey) selected @endif>{{ $selectOption }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(is_object($item) && isset($item->name) && isset($item->options))
                                            <div class="col-12">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-3">
                                                        <label class="form--label required">{{ __(keyToTitle($item->name)) }}</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <select class="form--control form-select" name="{{ $item->name }}" required>
                                                            @foreach((array)$item->options as $selectItemKey => $selectOption)
                                                                <option value="{{ $selectItemKey }}" @if((@$data->data_info[$item->name] ?? '') == $selectItemKey) selected @endif>{{ $selectOption }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-lg-3">
                                                        @php
                                                            // Make title and slug required for dynamic_pages
                                                            // Make slug required for schema_markup only
                                                            $isRequired = false;
                                                            if ($key == 'dynamic_pages' && ($k == 'title' || $k == 'slug')) {
                                                                $isRequired = true;
                                                            } elseif ($key == 'schema_markup' && $k == 'slug') {
                                                                $isRequired = true;
                                                            }
                                                            // footer_menu and page_seo slug are never required - no validations
                                                            if (($key == 'footer_menu' || $key == 'page_seo') && $k == 'slug') {
                                                                $isRequired = false;
                                                            }

                                                            // Special label for footer_menu slug: can be slug OR full URL
                                                            $labelText = ($key == 'footer_menu' && $k == 'slug')
                                                                ? 'Slug / URL'
                                                                : keyToTitle($k);
                                                        @endphp
                                                        <label class="form--label {{ $isRequired ? 'required' : '' }}">{{ __($labelText) }}</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <input
                                                            type="text"
                                                            class="form--control"
                                                            name="{{ $k }}"
                                                            value="{{ @$data->data_info[$k] ?? '' }}"
                                                            {{ $isRequired ? 'required' : '' }}
                                                            @if($key == 'footer_menu' && $k == 'slug')
                                                                placeholder="@lang('Example: /our-rules, /path/to/page, or https://apnacrowdfunding.com/our-rules')"
                                                            @endif
                                                        >
                                                        @if($key == 'footer_menu' && $k == 'slug')
                                                            <small class="text--muted d-block mt-1">
                                                                @lang('You can enter any value: /path, /path/to/page, full URL, or simple slug. No validations applied.')
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @stack('divend')

                    @if($key == 'footer_menu')
                        <div class="col-12">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-3">
                                    <label class="form--label">@lang('Sort Order')</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number" class="form--control" name="sort_order" value="{{ @$data->data_info['sort_order'] ?? 0 }}" min="0" placeholder="@lang('Enter sort order (lower numbers appear first)')">
                                    <small class="text--muted">@lang('Lower numbers appear first. Default: 0')</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($key == 'success_story')
                        <!-- Slug Field for Success Story -->
                        <div class="col-12">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-3">
                                    <label class="form--label">@lang('Slug')</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form--control" name="slug" id="story_slug" value="{{ @$data->data_info['slug'] ?? '' }}" placeholder="@lang('Enter URL-friendly slug (e.g., my-success-story)')">
                                    <small class="text--muted">@lang('Leave empty to auto-generate from title. Use lowercase letters, numbers, and hyphens only.')</small>
                                    <button type="button" class="btn btn--sm btn--base mt-2" id="generate_slug_btn">@lang('Generate from Title')</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO Fields for Success Story -->
                        <div class="col-12 mt-4">
                            <h5 class="mb-3">@lang('SEO Settings')</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-lg-3">
                                            <label class="form--label">@lang('Meta Title')</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text" class="form--control" name="meta_title" value="{{ @$data->data_info['meta_title'] ?? '' }}" placeholder="@lang('Enter meta title for SEO')">
                                            <small class="text--muted">@lang('Recommended: 50-60 characters')</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-lg-3">
                                            <label class="form--label">@lang('Meta Description')</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea class="form--control" name="meta_description" rows="3" placeholder="@lang('Enter meta description for SEO')">{{ @$data->data_info['meta_description'] ?? '' }}</textarea>
                                            <small class="text--muted">@lang('Recommended: 150-160 characters')</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-lg-3">
                                            <label class="form--label">@lang('Meta Keywords')</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text" class="form--control" name="meta_keywords" value="{{ @$data->data_info['meta_keywords'] ?? '' }}" placeholder="@lang('Enter keywords separated by commas')">
                                            <small class="text--muted">@lang('Example: success story, crowdfunding, campaign')</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn--base px-4" type="submit">@lang('Submit')</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .iconpicker-popover.fade {
            opacity: 1;
        }
    </style>
@endpush

@push('page-style-lib')
    <link href="{{ asset('assets/admin/css/page/iconpicker.css') }}" rel="stylesheet">
@endpush

@push('page-script-lib')
    <script src="{{asset('assets/admin/js/page/iconpicker.js')}}"></script>
    <script src="{{asset('assets/admin/js/page/ckEditor.js')}}"></script>
@endpush

@push('page-script')
    <script>
        (function ($) {
            "use strict";

            // Custom Upload Adapter for CKEditor
            function MyUploadAdapter(loader) {
                this.loader = loader;
            }

            MyUploadAdapter.prototype.upload = function() {
                return this.loader.file
                    .then(file => new Promise((resolve, reject) => {
                        const data = new FormData();
                        data.append('upload', file);

                        fetch('{{ route("admin.admin.upload.file") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.uploaded && result.url) {
                                resolve({
                                    default: result.url
                                });
                            } else {
                                reject(result.error ? result.error.message : 'Upload failed');
                            }
                        })
                        .catch(error => {
                            reject('Upload failed: ' + error);
                        });
                    }));
            };

            MyUploadAdapter.prototype.abort = function() {
                // Handle abort if needed
            };

            function attachManualSourceToggle(editor, node, index) {
                const editorWrapper = node.closest('.editor-wrapper') || node.parentElement;
                if (!editorWrapper) {
                    return;
                }

                const sourceToggle = document.createElement('button');
                sourceToggle.type = 'button';
                sourceToggle.className = 'btn btn--sm btn--base mb-2 source-code-toggle';
                sourceToggle.dataset.mode = 'visual';
                sourceToggle.dataset.editorIndex = index;
                sourceToggle.textContent = 'Source Code';

                const sourceTextarea = document.createElement('textarea');
                sourceTextarea.className = 'form--control d-none source-code-textarea';
                sourceTextarea.dataset.editorIndex = index;
                sourceTextarea.rows = 14;
                sourceTextarea.style.fontFamily = 'monospace';
                sourceTextarea.style.marginTop = '10px';

                editorWrapper.insertBefore(sourceToggle, node);
                editorWrapper.appendChild(sourceTextarea);

                sourceToggle.addEventListener('click', function () {
                    const editorContainer = editor.ui.view.editable.element.closest('.ck-editor');
                    const isVisualMode = sourceToggle.dataset.mode === 'visual';

                    if (isVisualMode) {
                        sourceTextarea.value = editor.getData();
                        if (editorContainer) {
                            editorContainer.style.display = 'none';
                        }
                        sourceTextarea.classList.remove('d-none');
                        sourceToggle.dataset.mode = 'source';
                        sourceToggle.textContent = 'WYSIWYG Editor';
                    } else {
                        editor.setData(sourceTextarea.value);
                        if (editorContainer) {
                            editorContainer.style.display = '';
                        }
                        sourceTextarea.classList.add('d-none');
                        sourceToggle.dataset.mode = 'visual';
                        sourceToggle.textContent = 'Source Code';
                    }
                });
            }

            if ($(".trumEdit")[0]) {
                $('.editor-wrapper').find('.ck-editor').remove();
                window.editors = {};
                const pluginNames = (ClassicEditor.builtinPlugins || []).map(plugin => plugin.pluginName).filter(Boolean);
                const supportsSourceEditing = pluginNames.includes('SourceEditing');
                
                // Configure FileRepository globally before creating editors
                ClassicEditor.builtinPlugins.map(plugin => {
                    if (plugin.pluginName === 'FileRepository') {
                        plugin.prototype.createUploadAdapter = function(loader) {
                            return new MyUploadAdapter(loader);
                        };
                    }
                });
                
                document.querySelectorAll('.trumEdit').forEach((node, index) => {
                    const toolbarItems = [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'imageUpload', 'blockQuote', 'insertTable', '|',
                    ];

                    if (supportsSourceEditing) {
                        toolbarItems.push('sourceEditing', '|');
                    }

                    toolbarItems.push('undo', 'redo');

                    // Create editor with custom upload adapter
                    ClassicEditor
                        .create(node, {
                            image: {
                                toolbar: [
                                    'imageTextAlternative',
                                    'toggleImageCaption',
                                    'imageStyle:inline',
                                    'imageStyle:block',
                                    'imageStyle:side',
                                    'linkImage'
                                ],
                                upload: {
                                    types: ['png', 'jpg', 'jpeg', 'gif', 'webp']
                                }
                            },
                            toolbar: {
                                items: toolbarItems
                            }
                        })
                        .then(newEditor => {
                            // Set up custom upload adapter for this editor instance
                            const fileRepository = newEditor.plugins.get('FileRepository');
                            fileRepository.createUploadAdapter = function(loader) {
                                return new MyUploadAdapter(loader);
                            };
                            
                            // Handle base64 and external images - convert to server URLs
                            newEditor.model.document.on('change:data', () => {
                                setTimeout(() => {
                                    const viewFragment = newEditor.getData();
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(viewFragment, 'text/html');
                                    const images = doc.querySelectorAll('img');
                                    
                                    images.forEach(async (img) => {
                                        const imgSrc = img.getAttribute('src');
                                        // Check if it's a base64 image or external URL
                                        if (imgSrc && (imgSrc.startsWith('data:image/') || (imgSrc.startsWith('http') && !imgSrc.includes(window.location.origin)))) {
                                            try {
                                                let uploadUrl = imgSrc;
                                                
                                                // If base64, convert to blob and upload
                                                if (imgSrc.startsWith('data:image/')) {
                                                    const response = await fetch(imgSrc);
                                                    const blob = await response.blob();
                                                    const formData = new FormData();
                                                    formData.append('upload', blob, 'image.png');
                                                    
                                                    const uploadResponse = await fetch('{{ route("admin.admin.upload.file") }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: formData
                                                    });
                                                    
                                                    const uploadData = await uploadResponse.json();
                                                    if (uploadData.uploaded && uploadData.url) {
                                                        uploadUrl = uploadData.url;
                                                    } else {
                                                        return;
                                                    }
                                                } else {
                                                    // External URL - use external upload endpoint
                                                    const response = await fetch('{{ route("admin.admin.upload.external-image") }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({
                                                            external_url: imgSrc
                                                        })
                                                    });
                                                    
                                                    const data = await response.json();
                                                    if (data.success && data.url) {
                                                        uploadUrl = data.url;
                                                    } else {
                                                        return;
                                                    }
                                                }
                                                
                                                // Replace image source with server URL
                                                const currentContent = newEditor.getData();
                                                const updatedContent = currentContent.replace(imgSrc, uploadUrl);
                                                newEditor.setData(updatedContent);
                                            } catch (error) {
                                                console.error('Error uploading image:', error);
                                            }
                                        }
                                    });
                                }, 100);
                            });
                            
                            if (!supportsSourceEditing) {
                                attachManualSourceToggle(newEditor, node, index);
                            }

                            window.editors[index] = newEditor;
                        })
                        .catch(error => {
                            console.error('Error initializing CKEditor:', error);
                        });
                });

                // If manual source mode is active, sync source textarea content back to editor before submit.
                $('form').on('submit', function () {
                    document.querySelectorAll('.source-code-toggle[data-mode="source"]').forEach((toggleBtn) => {
                        const editorIndex = toggleBtn.dataset.editorIndex;
                        const sourceTextarea = document.querySelector(`.source-code-textarea[data-editor-index="${editorIndex}"]`);
                        const editorInstance = window.editors && window.editors[editorIndex];

                        if (sourceTextarea && editorInstance) {
                            editorInstance.setData(sourceTextarea.value);
                        }
                    });
                });
            }

            $('.iconPicker').iconpicker().on('iconpickerSelected', function (e) {
                $(this).closest('.input--group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
            });

            // Update image preview when URL is entered
            $('input[type="url"][name$="_url"]').on('input', function() {
                var url = $(this).val();
                var imgKey = $(this).attr('name').replace('_url', '');
                var previewImg = $(this).closest('.col-lg-4, .col-md-4, .col-sm-6').find('.image-preview img');
                
                if (url && isValidUrl(url)) {
                    previewImg.attr('src', url);
                }
            });

            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }

            // Slug generation for success story
            @if($key == 'success_story')
            $('#generate_slug_btn').on('click', function() {
                var title = $('input[name="title"]').val();
                if (title) {
                    // Simple slug generation (you can enhance this)
                    var slug = title.toLowerCase()
                        .replace(/[^\w\s-]/g, '') // Remove special characters
                        .replace(/\s+/g, '-')     // Replace spaces with hyphens
                        .replace(/-+/g, '-')      // Replace multiple hyphens with single
                        .trim();
                    $('#story_slug').val(slug);
                }
            });

            // Auto-generate slug when title changes (if slug is empty)
            $('input[name="title"]').on('blur', function() {
                if (!$('#story_slug').val()) {
                    $('#generate_slug_btn').click();
                }
            });
            @endif
        })(jQuery);
    </script>
@endpush
