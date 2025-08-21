@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="create-campaign py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 mb-4">
                    <div class="custom--card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form--label required">@lang('Previous Gallery')</label>
                                    <div class="row">
                                        @foreach ($campaign->gallery as $image)
                                            <div class="col-3 gallery-image">
                                                <div class="image-container">
                                                    @if (count($campaign->gallery) > 1)
                                                        <div class="remove-button" data-image="{{ json_encode($image) }}" data-action="{{ route('user.campaign.image.remove', $campaign->id) }}">
                                                            <button type="button" class="text-light">x</button>
                                                        </div>
                                                    @endif
    
                                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $image, getFileSize('campaign')) }}" alt="Image" class="img-fluid">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 mb-4">
                    <div class="custom--card">
                        <div class="card-body">
                            <div class="row">
                                {{-- dropzone start --}}
                                <div class="col-12">
                                    <label class="form--label required">@lang('Gallery')</label>
                                </div>
                                <form action="{{ route('user.campaign.gallery.upload') }}" method="POST" class="dropzone" enctype="multipart/form-data">
                                    @csrf
                                </form>
                                <div class="col-12">
                                    <span><em><small>*@lang('Supported files'): <span class="text--base fw-bold">@lang('jpeg'), @lang('jpg'), @lang('png')</span>. @lang('Image size'): <span class="text--base fw-bold">{{ getFileSize('campaign') }}@lang('px')</span>.</small></em></span>
                                </div>
                                {{-- dropzone end --}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="custom--card">
                        <div class="card-body">
                            <form action="{{ route('user.campaign.update', $campaign->id) }}" method="POST" class="row g-4" enctype="multipart/form-data">
                                @csrf
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="upload__img mb-2 ms-auto">
                                            <label for="imageUpload" class="form--label">@lang('Campaign Image')</label>
                                            <label for="imageUpload" class="upload__img__btn"><i class="ti ti-camera"></i></label>
                                            <input type="file" id="imageUpload" name="image" accept=".jpeg, .jpg, .png">
                                            <div class="upload__img-preview image-preview">
                                                <img src="{{ getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')) }}" alt="">
                                            </div>
                                        </div>
                                        <span><em><small><i class="ti ti-info-circle me-1"></i>@lang('Supported files'): <span class="text--base fw-bold">@lang('jpeg'), @lang('jpg'), @lang('png')</span>. @lang('Image size'): <span class="text--base fw-bold">{{ getFileSize('campaign') }}@lang('px')</span>. @lang('Thumbnail size'): <span class="text--base fw-bold">{{ getThumbSize('campaign') }}@lang('px')</span>.</small></em></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form--label">@lang('Document')</label>
                                    <div class="d-flex mb-1">
                                        <input type="file" class="form--control" name="document" accept=".pdf">
                                    </div>
                                    <span><em><small>@lang('Supported file'): <span class="text--base fw-bold">.@lang('pdf')</span>.</small></em></span>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn--base w-100">@lang('Update Campaign')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include($activeTheme . 'user.campaign.commonStyleScript')

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/sweetalert2.min.css') }}">
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/dropzone.min.js') }}"></script>
    <script src="{{ asset($activeThemeTrue . 'js/sweetalert2.min.js') }}"></script>
@endpush

@push('page-style')
    <style>
        .image-container {
            position: relative;
            margin: 3px 0 10px;
        }

        .remove-button {
            position: absolute;
            top: -3px;
            right: -3px;
            font-size: 0;
            width: 17px;
            height: 17px;
            background: hsl(var(--danger));
            border-radius: 50%;
            cursor: pointer;
        }
        .remove-button::after {
            content: '\eb55';
            font-family: "tabler-icons";
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-45%, -50%);
            font-size: 12px;
            color: hsl(var(--white));
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .swal2-confirm, .swal2-cancel {
            padding: 5px 15px;
        }

        .swal2-confirm {
            margin-right: 13px;
        }
    </style>
@endpush

@push('page-script')
    <script type="text/javascript">
        (function($) {
            "use strict"

            const swalWithCustomButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn--base",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            })

            $('.remove-button').on('click', function() {
                let image = $(this).data('image')
                let url   = $(this).data('action')
                let data  = {
                    image,
                    _token: "{{ csrf_token() }}",
                }

                let _this = $(this)

                swalWithCustomButtons.fire({
                    title: "Are you sure?",
                    text: "This will delete the gallery image permanently!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, data, function(response) {
                            if (response.status === 'success') {
                                _this.parent().closest('.gallery-image').remove()

                                if ($('.gallery-image').length == 1) {
                                    $('.gallery-image').find('.remove-button').remove()
                                }

                                showToasts('success', response.message)
                            } else {
                                showToasts('error', response.message)
                            }
                        })
                    }
                })
            })
        })(jQuery)
    </script>
@endpush
