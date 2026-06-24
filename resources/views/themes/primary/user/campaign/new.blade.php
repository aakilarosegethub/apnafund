@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="create-campaign py-60">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-9">
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
                            <form action="{{ route('user.campaign.store') }}" method="POST" class="row g-4" enctype="multipart/form-data">
                                @csrf
                                <div class="col-12">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="upload__img mb-2 ms-auto">
                                            <label for="imageUpload" class="form--label required">@lang('Campaign Image')</label>
                                            <label for="imageUpload" class="upload__img__btn"><i class="ti ti-camera"></i></label>
                                            <input type="file" id="imageUpload" name="image" required accept=".jpeg, .jpg, .png">
                                            <div class="upload__img-preview image-preview">+</div>
                                        </div>
                                        <span><em><small><i class="ti ti-info-circle-filled me-1"></i>@lang('Supported files'): <span class="text--base fw-bold">@lang('jpeg'), @lang('jpg'), @lang('png')</span>. @lang('Image size'): <span class="text--base fw-bold">{{ getFileSize('campaign') }}@lang('px')</span>. @lang('Thumbnail size'): <span class="text--base fw-bold">{{ getThumbSize('campaign') }}@lang('px')</span>.</small></em></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form--label required">@lang('Short Description')</label>
                                    <textarea class="form--control" id="CampaignShortDescription" name="short_description" rows="3" maxlength="{{ getCampaignShortDescriptionMaxLength() }}" placeholder="@lang('Describe your project in one or two sentences...')" required>{{ old('short_description') }}</textarea>
                                    <small class="form-text text-muted">@lang('Shown on your project card.') <span id="shortDescriptionCount"></span></small>
                                    <small id="shortDescriptionError" class="text-danger d-none"></small>
                                    @error('short_description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form--label required">@lang('Name')</label>
                                    <div class="input--group">
                                        <span class="input-group-text"><i class="ti ti-keyframe-align-center"></i></span>
                                        <input type="text" class="form--control" name="name" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Category')</label>
                                    <div class="input--group">
                                        <span class="input-group-text"><i class="ti ti-menu-2"></i></span>
                                        <select class="form--control form-select" name="category_id" required>
                                            <option value="" selected>@lang('Select Category')</option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                                    {{ __(@$category->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Goal Amount') ({{ $creatorSymbol ?? @$setting->cur_sym }}) - @lang('Enter amount in') {{ $creatorCurrency ?? 'USD' }}</label>
                                    <div class="input--group">
                                        <span class="input-group-text">{{ $creatorSymbol ?? @$setting->cur_sym }}</span>
                                        <input type="number" step="any" min="0" class="form--control" name="goal_amount" value="{{ old('goal_amount') }}" required>
                                    </div>
                                    <input type="hidden" name="input_currency" value="{{ $creatorCurrency ?? 'USD' }}">
                                    <small class="form-text text-muted">@lang('Stored in') {{ getPlatformCurrency() }}</small>
                                </div>
                                <div class="col-12">
                                    <label class="form--label required">@lang('Preferred Amounts') ({{ $creatorSymbol ?? @$setting->cur_sym }}) - @lang('Enter in') {{ $creatorCurrency ?? 'USD' }}</label>
                                    <div class="d-flex gap-2">
                                        <div class="input--group w-100">
                                            <span class="input-group-text">{{ $creatorSymbol ?? @$setting->cur_sym }}</span>
                                            <input type="number" step="any" min="0" class="form--control" name="preferred_amounts[]" value="" required>
                                        </div>
                                        <a role="button" class="btn btn--base px-3 d-flex align-items-center" id="addMoreAmounts">
                                            <i class="ti ti-square-rounded-plus"></i>
                                        </a>
                                    </div>
                                    <div class="add-more-amounts"></div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Start Date')</label>
                                    <div class="input--group">
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        <input type="text" class="form--control date-picker" name="start_date" value="{{ old('start_date') }}" data-language="en" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('End Date')</label>
                                    <div class="input--group">
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        <input type="text" class="form--control date-picker" name="end_date" value="{{ old('end_date') }}" data-language="en" required autocomplete="off">
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
                                    <label class="form--label required">@lang('Description')</label>
                                    <textarea class="form--control ck-editor" name="description" rows="10">
                                        @php echo old('description') @endphp
                                    </textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn--base w-100">@lang('Create Campaign')</button>
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
    <link rel="stylesheet" href="{{ asset('assets/universal/css/datepicker.css') }}">
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/dropzone.min.js') }}"></script>
    <script src="{{ asset($activeThemeTrue . 'js/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
    <script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
@endpush

@push('page-script')
    <script type="text/javascript">
        (function($) {
            "use strict"

            // Add More Preferred Amounts On Campaign Create Start
            $('#addMoreAmounts').on('click', function () {
                $('.add-more-amounts').append(`
                    <div class="extra-amount d-flex gap-2 pt-2">
                        <div class="input--group w-100">
                            <span class="input-group-text">{{ $creatorSymbol ?? $setting->cur_sym }}</span>
                            <input type="number" step="any" min="0" class="form--control" name="preferred_amounts[]" required>
                        </div>
                        <a role="button" class="btn btn--danger px-3 d-flex align-items-center close-extra-amount">
                            <i class="ti ti-square-rounded-minus"></i>
                        </a>
                    </div>
                `)
            })

            $(document).on('click', '.close-extra-amount', function () {
                $(this).closest('.extra-amount').remove()
            })
            // Add More Preferred Amounts On Campaign Create End

            $('.date-picker').datepicker({
                dateFormat: 'dd-mm-yyyy',
                minDate: new Date(),
            })

            $('.date-picker').on('input keyup keydown keypress', function() {
                return false
            })

            const SHORT_DESC_MIN = {{ getCampaignShortDescriptionMinLength() }};
            const SHORT_DESC_MAX = {{ getCampaignShortDescriptionMaxLength() }};
            const shortDescriptionField = document.getElementById('CampaignShortDescription');
            const shortDescriptionError = document.getElementById('shortDescriptionError');
            const shortDescriptionCount = document.getElementById('shortDescriptionCount');

            function normalizeShortDescription(value) {
                return (value || '').replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
            }

            function validateShortDescriptionField() {
                if (!shortDescriptionField) {
                    return true;
                }

                const text = normalizeShortDescription(shortDescriptionField.value);
                let message = '';

                if (!text) {
                    message = (shortDescriptionField.value || '').length > 0
                        ? 'Short description cannot contain only spaces.'
                        : 'Please enter a short description for your project.';
                } else if (text.length < SHORT_DESC_MIN) {
                    message = `Short description must be at least ${SHORT_DESC_MIN} characters long.`;
                } else if (text.length > SHORT_DESC_MAX) {
                    message = `Short description cannot exceed ${SHORT_DESC_MAX} characters.`;
                }

                if (message) {
                    if (shortDescriptionError) {
                        shortDescriptionError.textContent = message;
                        shortDescriptionError.classList.remove('d-none');
                    }
                    return false;
                }

                if (shortDescriptionError) {
                    shortDescriptionError.classList.add('d-none');
                }

                if (shortDescriptionCount) {
                    shortDescriptionCount.textContent = `(${text.length}/${SHORT_DESC_MAX} characters)`;
                }

                return true;
            }

            if (shortDescriptionField) {
                shortDescriptionField.addEventListener('input', validateShortDescriptionField);
                shortDescriptionField.addEventListener('blur', validateShortDescriptionField);
                $('form[action="{{ route('user.campaign.store') }}"]').on('submit', function(e) {
                    if (!validateShortDescriptionField()) {
                        e.preventDefault();
                        shortDescriptionField.focus();
                    }
                });
            }
        })(jQuery)
    </script>
@endpush
