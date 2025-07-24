@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-lg-5">
                                    <ul class="user-profile-list">
                                        <li><span><i class="ti ti-user-filled"></i> @lang('Username')</span> {{ __($user->username) }}</li>
                                        <li><span><i class="ti ti-mail-filled"></i> @lang('Email')</span> {{ $user->email }}</li>
                                        <li><span><i class="ti ti-device-mobile-filled"></i> @lang('Mobile')</span> {{ $user->mobile }}</li>
                                        <li><span><i class="ti ti-world"></i> @lang('Country')</span> {{ __($user->country_name) }}</li>
                                        <li><span><i class="ti ti-map-pin-filled"></i> @lang('Address')</span> {{ @$user->address->address }}</li>
                                    </ul>
                                </div>
                                <div class="col-lg-7">
                                    <form action="" method="POST" class="row gx-4 gy-3" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-12 text-center">
                                            <div class="upload__img mb-2">
                                                <label for="imageUpload" class="upload__img__btn"><i class="ti ti-camera"></i></label>
                                                <input type="file" id="imageUpload" name="image" accept=".jpeg, .jpg, .png">
                                                <div class="upload__img-preview image-preview">
                                                    @if ($user->image)
                                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="profile-image">
                                                    @else
                                                        +
                                                    @endif
                                                </div>
                                            </div>
                                            <span><em><small>*@lang('Supported files'): <span class="text--base fw-bold">@lang('jpeg'), @lang('jpg'), @lang('png')</span>. @lang('Image size'): <span class="text--base fw-bold">{{ getFileSize('userProfile') }}@lang('px')</span>.</small></em></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form--label required">@lang('First Name')</label>
                                            <input type="text" class="form--control" name="firstname" value="{{ $user->firstname }}" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form--label required">@lang('Last Name')</label>
                                            <input type="text" class="form--control" name="lastname" value="{{ $user->lastname }}" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form--label">@lang('State')</label>
                                            <input type="text" class="form--control" name="state" value="{{ @$user->address->state }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form--label">@lang('City')</label>
                                            <input type="text" class="form--control" name="city" value="{{ @$user->address->city }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form--label">@lang('Zip Code')</label>
                                            <input type="text" class="form--control" name="zip" value="{{ @$user->address->zip }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form--label">@lang('Address')</label>
                                            <input type="text" class="form--control" name="address" value="{{ @$user->address->address }}">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn--base w-100 mt-2">@lang('Submit')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .upload__img input, 
        .image-preview {
            width: 200px;
        }

        .upload__img .image-preview {
            color: rgb(136, 134, 134);
        }
    </style>
@endpush
