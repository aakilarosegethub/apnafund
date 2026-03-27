@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Site Preferences')</h3>
            </div>
            <div class="card-body">
                <form class="row g-lg-4 g-3" action="" method="POST">
                    @csrf
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Site Name')</label>
                        <input type="text" class="form--control" name="site_name" value="{{ $setting->site_name }}" placeholder="@lang('Phinix Admin Template')" required>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Platform Currency')</label>
                        <input type="text" class="form--control" name="site_cur" value="{{ $setting->site_cur }}" placeholder="@lang('USD')" required>
                        <small class="form-text text-muted">@lang('All amounts (campaigns, donations, etc.) are stored in this currency in the database. Creators and contributors enter amounts in their local currency; the system converts to this before saving.')</small>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Currency Symbol')</label>
                        <input type="text" class="form--control" name="cur_sym" value="{{ $setting->cur_sym }}" placeholder="$" required>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Time Region')</label>
                        <select class="form--control form-select select-2" name="time_region" required>
                            @foreach($timeRegions as $timeRegion)
                                <option value="'{{ @$timeRegion}}'">{{ __($timeRegion) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Item Showing Per Page')</label>
                        <select class="form--control form-select" name="per_page_item" required>
                            <option value="20">20 @lang('item per page')</option>
                            <option value="50">50 @lang('item per page')</option>
                            <option value="100">100 @lang('item per page')</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Date Format')</label>
                        <select class="form--control form-select" name="date_format" required>
                            <option value="m-d-Y">MDY (Month-Day-Year)</option>
                            <option value="d-m-Y">DMY (Day-Month-Year)</option>
                            <option value="Y-m-d">YMD (Year-Month-Day)</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Fractional Digit Show')</label>
                        <input type="text" class="form--control" name="fraction_digit" value="{{ $setting->fraction_digit }}" placeholder="2" required>
                    </div>
                    @if(\Illuminate\Support\Facades\Schema::hasColumn('settings', 'registration_fee_enabled'))
                    <!-- Campaign Registration Fee -->
                    <div class="col-12 mt-3">
                        <hr>
                        <h5 class="mb-3">@lang('Campaign Registration Fee')</h5>
                        <p class="text-muted small">@lang('Charge creators when they create a campaign. Uses existing payment gateways.')</p>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label">@lang('Enable Registration Fee')</label>
                        <div class="form-check form--switch">
                            <input class="form-check-input" type="checkbox" name="registration_fee_enabled" value="1" {{ ($setting->registration_fee_enabled ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">@lang('Charge creator on campaign creation')</label>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label">@lang('Fee Amount Per Campaign')</label>
                        <input type="number" class="form--control" name="registration_fee_amount" value="{{ $setting->registration_fee_min ?? 0 }}" placeholder="0" min="0" step="0.01">
                        <small class="form-text text-muted">@lang('Amount in') {{ $setting->site_cur ?? 'USD' }} @lang('(platform currency)')</small>
                    </div>
                    @endif
                    <!-- Campaign Days Limit -->
                    <div class="col-12 mt-3">
                        <hr>
                        <h5 class="mb-3">@lang('Campaign Duration Limit')</h5>
                        <p class="text-muted small">@lang('Maximum days between campaign start date and end date. Applies to both web and API (SPUI).')</p>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label">@lang('Campaign Days Limit')</label>
                        <input type="number" class="form--control" name="campaign_days_limit" value="{{ $campaignDaysLimit ?? 30 }}" placeholder="30" min="1" max="365" required>
                        <small class="form-text text-muted">@lang('E.g. 30 = start to end date max 30 days. Error shown if exceeded.')</small>
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Required Documents List (Campaign)')</label>
                        <textarea class="form--control" name="campaign_required_documents" rows="5" placeholder="CNIC Front Copy&#10;CNIC Back Copy&#10;Business Registration Certificate">{{ $requiredDocuments ?? '' }}</textarea>
                        <small class="form-text text-muted">@lang('Add one document per line. This list will be shown on the campaign Documents step for creators.')</small>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Primary Color')</label>
                        <div class="input--group colorpicker">
                            <input type="color" class="form--control" value="#{{ $setting->first_color }}">
                            <input type="text" class="form--control" name="first_color" value="#{{ $setting->first_color }}" placeholder="@lang('Hex Code e.g. #00ffff')" required>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label required">@lang('Secondary Color')</label>
                        <div class="input--group colorpicker">
                            <input type="color" class="form--control" value="#{{ $setting->second_color }}">
                            <input type="text" class="form--control" name="second_color" value="#{{ $setting->second_color }}" placeholder="@lang('Hex Code e.g. #ffff00')" required>
                        </div>
                    </div>
                    
                    <!-- WhatsApp Settings -->
                    <div class="col-12 mt-4">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('WhatsApp Settings')</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form--label">@lang('Contact Creator - WhatsApp Start Message')</label>
                                    <textarea class="form--control" name="whatsapp_contact_creator_message" rows="3" placeholder="Hi! I'm interested in [campaign_name]">{{ $whatsappContactMessage ?? '' }}</textarea>
                                    <small class="form-text text-muted">@lang('Use') <code>[campaign_name]</code> @lang('to insert campaign name. Shown when backer clicks WhatsApp on campaign page.')</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form--label">@lang('Footer Chatbot - Admin WhatsApp Number')</label>
                                    <input type="text" class="form--control" name="whatsapp_chatbot_number" value="{{ $whatsappChatbotNumber ?? '' }}" placeholder="923001234567">
                                    <small class="form-text text-muted">@lang('Fixed chatbot icon in footer opens this number. Include country code without +.')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Allowed Countries Section -->
                    <div class="col-12 mt-4">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">@lang('Allowed Countries for Project Location')</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="use_selected_countries" id="useSelectedCountries" value="1" {{ $useSelectedOnly ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="useSelectedCountries">
                                        @lang('Use Only Selected Countries')
                                    </label>
                                    <small class="form-text text-muted d-block">@lang('If unchecked, all countries will be available. If checked, only selected countries will be shown.')</small>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form--label">@lang('Select Countries')</label>
                                    <select class="form--control form-select select-2" name="selected_countries[]" id="selectedCountries" multiple style="height: 200px;">
                                        @foreach($allCountries ?? [] as $country)
                                            <option value="{{ $country }}" {{ in_array($country, $selectedCountries ?? []) ? 'selected' : '' }}>
                                                {{ $country }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">@lang('Hold Ctrl/Cmd to select multiple countries')</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn--base px-4">@lang('Submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('System Preferences')</h3>
            </div>
            <div class="card-body">
                <form class="row g-4" action="{{ route('admin.basic.system.setting') }}" method="POST">
                    @csrf
                    <div class="col-12">
                        <div class="row g-lg-4 g-3 row-cols-xxl-5 row-cols-xl-4 row-cols-md-3 row-cols-sm-2 row-cols-1 preference-card-list justify-content-center">
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-login"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('User Signup')</span>
                                        <span class="preference-card__desc">@lang('Enable or disable user registration with this toggle for your website. If deactivated, the option to create new accounts will be disabled').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="signup" @if($setting->signup) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-lock"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Enforce Strong Password')</span>
                                        <span class="preference-card__desc">@lang('Enhance account security by enforcing the use of strong passwords with this toggle, ensuring robust user authentication').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="strong_pass" @if($setting->strong_pass) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-clipboard-text"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Accept Policy')</span>
                                        <span class="preference-card__desc">@lang('Control user access by enabling this toggle, which mandates users to agree to your terms before accessing the website').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox"  name="agree_policy" @if($setting->agree_policy) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-user-scan"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Know Your Customer Check')</span>
                                        <span class="preference-card__desc">@lang('Implement this toggle to require user identity verification, enhancing trust and compliance with regulatory standards on your website').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="kc" @if($setting->kc) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-mail-check"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Email Confirmation')</span>
                                        <span class="preference-card__desc">@lang('Ensure user authenticity by enabling this toggle, requiring users to verify their email addresses during the registration process').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="ec" @if($setting->ec) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-mail-bolt"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Email Alert')</span>
                                        <span class="preference-card__desc">@lang('Activate this toggle to notify users via email about important updates, events, and announcements on your website').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="ea" @if($setting->ea) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-message-check"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Mobile Confirmation')</span>
                                        <span class="preference-card__desc">@lang('Enhance user verification by enabling this toggle, which mandates users to confirm their identity via their mobiles during registration').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="sc" @if($setting->sc) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-message-bolt"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('SMS Alert')</span>
                                        <span class="preference-card__desc">@lang('Activate this toggle to notify users via SMS about important updates, events, and announcements on your website').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox"  name="sa" @if($setting->sa) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                        <i class="ti ti-certificate"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Enforce SSL')</span>
                                        <span class="preference-card__desc">@lang('Ensure data security by requiring all connections to your website to be encrypted using this toggle feature').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="enforce_ssl" @if($setting->enforce_ssl) checked @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="preference-card bg-img" data-background-image="{{ asset('assets/admin/images/card-bg-1.png') }}">
                                    <div class="preference-card__thumb">
                                            <i class="ti ti-language"></i>
                                    </div>
                                    <div class="preference-card__content">
                                        <span class="preference-card__title">@lang('Language Preference')</span>
                                        <span class="preference-card__desc">@lang('Control user experience by activating this toggle, allowing visitors to select their preferred language for seamless interaction').</span>
                                    </div>
                                    <div class="form-check form--switch">
                                        <input class="form-check-input" type="checkbox" name="language" @if($setting->language) checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn--base px-4" type="submit">@lang('Submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Logo and Favicon Preferences')</h3>
            </div>
            <div class="card-body">
                <form class="row g-lg-4 g-3"  action="{{ route('admin.basic.logo.favicon.setting') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        <div class="alert alert--base">
                            @lang('If the visual identifiers remain unchanged, it\'s advisable to perform a cache clearance within your browser. Typically, clearing the cache resolves this issue. However, if the previous logo or favicon persists, it could be attributed to caching mechanisms at the server or network level. Additional cache clearance may be necessary in such cases').
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label for="logoLight" class="form--label">@lang('Logo Light')</label>
                        <div class="upload__img">
                            <label for="logoLight" class="upload__img__btn"><i class="ti ti-camera"></i></label>
                            <input type="file" id="logoLight" class="image-upload" name="logo_light" accept=".png">
                            <label for="logoLight" class="upload__img-preview image-preview">
                                @php
                                    $logoLightPath = getFilePath('logoFavicon').'/logo_light.png';
                                    
                                    $logoLightFile = public_path($logoLightPath);
                                    clearstatcache(true, $logoLightFile);
                                    $logoLightVersion = file_exists($logoLightFile) ? filemtime($logoLightFile) : time();
                                    $logoLightUrl = getImage($logoLightPath) . '?v=' . $logoLightVersion . '&t=' . time();
                                @endphp
                                <img src="{{ $logoLightUrl }}" alt="logo" id="logoLightPreview" data-base-url="{{ getImage($logoLightPath) }}">
                            </label>
                            <button type="button" class="btn btn--sm btn--icon btn--danger custom-file-input-clear d-none"><i class="ti ti-circle-x"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label for="logoDark" class="form--label">@lang('Logo Dark')</label>
                        <div class="upload__img">
                            <label for="logoDark" class="upload__img__btn"><i class="ti ti-camera"></i></label>
                            <input type="file" id="logoDark" class="image-upload" name="logo_dark" accept=".png">
                            <label for="logoDark" class="upload__img-preview image-preview">
                                @php
                                    $logoDarkPath = getFilePath('logoFavicon').'/logo_dark.png';
                                    $logoDarkFile = public_path($logoDarkPath);
                                    clearstatcache(true, $logoDarkFile);
                                    $logoDarkVersion = file_exists($logoDarkFile) ? filemtime($logoDarkFile) : time();
                                    $logoDarkUrl = getImage($logoDarkPath) . '?v=' . $logoDarkVersion . '&t=' . time();
                                @endphp
                                <img src="{{ $logoDarkUrl }}" alt="logo" id="logoDarkPreview" data-base-url="{{ getImage($logoDarkPath) }}">
                            </label>
                            <button type="button" class="btn btn--sm btn--icon btn--danger custom-file-input-clear d-none"><i class="ti ti-circle-x"></i></button>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <label for="favicon" class="form--label">@lang('Favicon')</label>
                        <div class="upload__img">
                            <label for="favicon" class="upload__img__btn"><i class="ti ti-camera"></i></label>
                            <input type="file" id="favicon" class="image-upload" name="favicon" accept=".png">
                            <label for="favicon" class="upload__img-preview image-preview">
                                @php
                                    $faviconPath = getFilePath('logoFavicon').'/favicon.png';
                                    $faviconFile = public_path($faviconPath);
                                    clearstatcache(true, $faviconFile);
                                    $faviconVersion = file_exists($faviconFile) ? filemtime($faviconFile) : time();
                                    $faviconUrl = getImage($faviconPath, getFileSize('favicon')) . '?v=' . $faviconVersion . '&t=' . time();
                                @endphp
                                <img src="{{ $faviconUrl }}" alt="logo" id="faviconPreview" data-base-url="{{ getImage($faviconPath, getFileSize('favicon')) }}">
                            </label>
                            <button type="button" class="btn btn--sm btn--icon btn--danger custom-file-input-clear d-none"><i class="ti ti-circle-x"></i></button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn--base px-4">@lang('Submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
  <script>
    (function ($) {
        "use strict";

        $('.colorpicker').find('input').on('keyup', function(){
            var colorCode = $(this).val();
            $(this).siblings('input').val(colorCode);
        });

        $('.colorpicker').find('input[type=color]').on('input', function(){
            var colorCode = $(this).val();
            $(this).siblings('input').val(colorCode);
        });

        $('[name=per_page_item]').val('{{ bs('per_page_item') }}');
        $('[name=date_format]').val('{{ bs('date_format')  }}');
        $('[name=time_region]').val("'{{ config('app.timezone') }}'").select2();

        // Handle image preview update when file is selected
        $('#logoLight').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#logoLightPreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#logoDark').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#logoDarkPreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $('#favicon').on('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#faviconPreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Refresh all image previews after successful form submission
        @if(session('success') || session('toasts'))
            $(document).ready(function() {
                // Force reload all images with new timestamp to bypass cache
                function refreshImages() {
                    var timestamp = new Date().getTime();
                    var random = Math.floor(Math.random() * 1000000);
                    
                    // Refresh Logo Light
                    if ($('#logoLightPreview').length) {
                        var baseUrl = $('#logoLightPreview').data('base-url') || $('#logoLightPreview').attr('src').split('?')[0];
                        var newUrl = baseUrl + '?v=' + timestamp + '&t=' + random + '&nocache=' + Date.now();
                        $('#logoLightPreview').attr('src', newUrl);
                        // Force reload
                        $('#logoLightPreview')[0].src = '';
                        setTimeout(function() {
                            $('#logoLightPreview')[0].src = newUrl;
                        }, 100);
                    }
                    
                    // Refresh Logo Dark
                    if ($('#logoDarkPreview').length) {
                        var baseUrl = $('#logoDarkPreview').data('base-url') || $('#logoDarkPreview').attr('src').split('?')[0];
                        var newUrl = baseUrl + '?v=' + timestamp + '&t=' + random + '&nocache=' + Date.now();
                        $('#logoDarkPreview').attr('src', newUrl);
                        // Force reload
                        $('#logoDarkPreview')[0].src = '';
                        setTimeout(function() {
                            $('#logoDarkPreview')[0].src = newUrl;
                        }, 100);
                    }
                    
                    // Refresh Favicon
                    if ($('#faviconPreview').length) {
                        var baseUrl = $('#faviconPreview').data('base-url') || $('#faviconPreview').attr('src').split('?')[0];
                        var newUrl = baseUrl + '?v=' + timestamp + '&t=' + random + '&nocache=' + Date.now();
                        $('#faviconPreview').attr('src', newUrl);
                        // Force reload
                        $('#faviconPreview')[0].src = '';
                        setTimeout(function() {
                            $('#faviconPreview')[0].src = newUrl;
                        }, 100);
                    }
                }
                
                // Refresh immediately and after delay
                refreshImages();
                setTimeout(refreshImages, 500);
                setTimeout(refreshImages, 1000);
            });
        @endif
    })(jQuery);
  </script>
@endpush
