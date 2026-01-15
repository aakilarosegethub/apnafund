@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-body">
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
                                                $imageUrl = '';
                                                if ($currentImage) {
                                                    // Check if it's a URL
                                                    if (filter_var($currentImage, FILTER_VALIDATE_URL)) {
                                                        $imageUrl = $currentImage;
                                                    } else {
                                                        $imageUrl = getImage('assets/images/site/' . $key .'/'. $currentImage, @$section->element->images->$imgKey->size);
                                                    }
                                                } else {
                                                    $imageUrl = getImage('assets/images/site/' . $key .'/'. $currentImage, @$section->element->images->$imgKey->size);
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
                                                        <label class="form--label required">{{ __(keyToTitle($k)) }}</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <textarea class="form--control" name="{{ $k }}" required>{{ @$data->data_info[$k] ?? '' }}</textarea>
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
                                                            $isRequired = ($key == 'dynamic_pages' && ($k == 'title' || $k == 'slug'));
                                                        @endphp
                                                        <label class="form--label {{ $isRequired ? 'required' : '' }}">{{ __(keyToTitle($k)) }}</label>
                                                    </div>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form--control" name="{{ $k }}" value="{{ @$data->data_info[$k] ?? '' }}" {{ $isRequired ? 'required' : '' }}>
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

            if ($(".trumEdit")[0]) {
                $('.editor-wrapper').find('.ck-editor').remove();
                window.editors = {};
                document.querySelectorAll('.trumEdit').forEach((node, index) => {
                    ClassicEditor
                        .create(node)
                        .then(newEditor => {
                            window.editors[index] = newEditor;
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
        })(jQuery);
    </script>
@endpush
