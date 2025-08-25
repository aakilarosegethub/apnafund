@extends($activeTheme . 'layouts.dashboard')

@section('frontend')
<!--New design start-->
<div class="dashboard py-60">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="content-card">
                    <h4 class="mb-4">Account Settings</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Business Information</h5>
                            <form action="{{ route('user.profile') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Profile Image Upload -->
                                <div class="form-group mb-3 text-center">
                                    <label class="form-label">Profile Image</label>
                                    <div class="upload__img mb-2">
                                        <label for="imageUpload" class="upload__img__btn">
                                            <i class="ti ti-camera"></i>
                                        </label>
                                        <input type="file" id="imageUpload" name="image" accept=".jpeg, .jpg, .png">
                                        <div class="upload__img-preview image-preview">
                                            @if ($user->image)
                                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}" alt="profile-image">
                                            @else
                                                <i class="ti ti-user" style="font-size: 48px; color: #ccc;"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <small class="text-muted">*Supported files: jpeg, jpg, png. Image size: {{ getFileSize('userProfile') }}px</small>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="{{ $user->firstname }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $user->lastname }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="businessEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="businessEmail" value="{{ $user->email }}" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="businessPhone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="businessPhone" value="{{ $user->mobile }}" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{ @$user->address->state }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ @$user->address->city }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="zip" class="form-label">Zip Code</label>
                                    <input type="text" class="form-control" id="zip" name="zip" value="{{ @$user->address->zip }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ @$user->address->address }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Information</button>
                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Notification Preferences</h5>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                <label class="form-check-label" for="emailNotifications">
                                    Email notifications for new donations
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="goalAlerts" checked>
                                <label class="form-check-label" for="goalAlerts">
                                    Alerts when reaching funding goals
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="weeklyReports">
                                <label class="form-check-label" for="weeklyReports">
                                    Weekly performance reports
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="marketingEmails">
                                <label class="form-check-label" for="marketingEmails">
                                    Marketing and promotional emails
                                </label>
                            </div>
                            
                            <h5 class="mt-4">Security</h5>
                            
                            <!-- Password Change Form -->
                            <form action="{{ route('user.change.password') }}" method="POST" class="mb-3">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @if ($setting->strong_pass) secure-password @endif" id="password" name="password" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                                <button type="submit" class="btn btn-secondary mb-2">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </form>
                            
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--New design end-->

<!-- Old design (keeping for reference) -->
<div class="dashboard py-60" style="display: none;">
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
                                <form action="{{ route('user.profile') }}" method="POST" class="row gx-4 gy-3" enctype="multipart/form-data">
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
        
        /* New design styles */
        .content-card {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.75rem;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        .btn {
            border-radius: 5px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }
        
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        /* Image upload styles for new design */
        .upload__img {
            position: relative;
            display: inline-block;
        }
        
        .upload__img input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .upload__img__btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 2;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .upload__img:hover .upload__img__btn {
            opacity: 1;
        }
        
        .image-preview {
            width: 120px;
            height: 120px;
            border: 2px dashed #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            overflow: hidden;
            background: #f8f9fa;
        }
        
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .image-preview i {
            font-size: 48px;
            color: #ccc;
        }
    </style>
@endpush

@push('page-script')
<script>
    // Image preview functionality
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.querySelector('.image-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="profile-image">`;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush

@if ($setting->strong_pass)
    @push('page-style-lib')
        <link rel="stylesheet" href="{{ asset('assets/universal/css/strongPassword.css') }}">
    @endpush

    @push('page-script-lib')
        <script src="{{asset('assets/universal/js/strongPassword.js')}}"></script>
    @endpush
@endif
