@extends('admin.layouts.master')

@section('master')
    <div class="row">
        <!-- Hero Section Management -->
        <div class="col-12">
            <div class="custom--card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-home text--primary"></i>
                        @lang('Hero Section Management')
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.homepage.hero.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Hero Heading Line 1')</label>
                                    @php
                                        $heading1 = old('hero_heading_1', 
                                            ($heroContent && $heroContent->data_info) 
                                                ? (is_array($heroContent->data_info) ? ($heroContent->data_info['hero_heading_1'] ?? 'Crowd Funding') : ($heroContent->data_info->hero_heading_1 ?? 'Crowd Funding'))
                                                : 'Crowd Funding'
                                        );
                                    @endphp
                                    <input type="text" class="form--control @error('hero_heading_1') is-invalid @enderror" name="hero_heading_1" value="{{ $heading1 }}" required>
                                    @error('hero_heading_1')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Hero Heading Line 2')</label>
                                    <input type="text" class="form--control @error('hero_heading_2') is-invalid @enderror" name="hero_heading_2" value="{{ old('hero_heading_2', @$heroContent->data_info->hero_heading_2 ?? 'By The People,') }}" required>
                                    @error('hero_heading_2')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Hero Heading Line 3')</label>
                                    <input type="text" class="form--control @error('hero_heading_3') is-invalid @enderror" name="hero_heading_3" value="{{ old('hero_heading_3', @$heroContent->data_info->hero_heading_3 ?? 'For The People,') }}" required>
                                    @error('hero_heading_3')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Button Text')</label>
                                    <input type="text" class="form--control @error('button_text') is-invalid @enderror" name="button_text" value="{{ old('button_text', @$heroContent->data_info->button_text ?? 'Get Started Now!') }}" required>
                                    @error('button_text')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Button URL')</label>
                                    <input type="text" class="form--control @error('button_url') is-invalid @enderror" name="button_url" value="{{ old('button_url', @$heroContent->data_info->button_url ?? route('business.resources')) }}" required>
                                    @error('button_url')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <small class="text--muted">@lang('Enter full URL or route name (e.g., /campaigns or route name)')</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Hero Background Image')</label>
                                    <input type="file" class="form--control" name="hero_background_image" accept="image/*">
                                    @php
                                        $currentImage = null;
                                        if ($heroContent && $heroContent->data_info) {
                                            $currentImage = is_array($heroContent->data_info) 
                                                ? ($heroContent->data_info['hero_background_image'] ?? null)
                                                : ($heroContent->data_info->hero_background_image ?? null);
                                        }
                                    @endphp
                                    @if($currentImage)
                                        <div class="mt-2">
                                            <small class="text--success d-block mb-2">@lang('Current image:') {{ $currentImage }}</small>
                                            <img src="{{ asset('assets/images/site/home/' . $currentImage) }}" alt="Current Hero Image" style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Hero Description')</label>
                                    <textarea class="form--control @error('hero_description') is-invalid @enderror" name="hero_description" rows="3" required>{{ old('hero_description', @$heroContent->data_info->hero_description ?? 'Together, we empower small businesses— From young dreamers, bold visionaries and those who want to improve their societies.') }}</textarea>
                                    @error('hero_description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Hero Section')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Banner Section Management -->
        <div class="col-12">
            <div class="custom--card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-info-circle text--primary"></i>
                        @lang('Info Banner Section Management')
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.homepage.info-banner.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>@lang('Info Item 1 Icon')</label>
                                    <input type="text" class="form--control" name="info_item_1_icon" value="{{ @$infoBannerContent->data_info->info_item_1_icon ?? 'fas fa-bolt' }}" placeholder="fas fa-bolt" required>
                                    <small class="text--muted">@lang('FontAwesome icon class')</small>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label>@lang('Info Item 1 Text')</label>
                                    <input type="text" class="form--control" name="info_item_1_text" value="{{ @$infoBannerContent->data_info->info_item_1_text ?? 'No fee to start fundraising' }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>@lang('Info Item 2 Icon')</label>
                                    <input type="text" class="form--control" name="info_item_2_icon" value="{{ @$infoBannerContent->data_info->info_item_2_icon ?? 'fas fa-heart-circle' }}" placeholder="fas fa-heart-circle" required>
                                    <small class="text--muted">@lang('FontAwesome icon class')</small>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label>@lang('Info Item 2 Text')</label>
                                    <input type="text" class="form--control" name="info_item_2_text" value="{{ @$infoBannerContent->data_info->info_item_2_text ?? '1 Fund made every second' }}" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>@lang('Info Item 3 Icon')</label>
                                    <input type="text" class="form--control" name="info_item_3_icon" value="{{ @$infoBannerContent->data_info->info_item_3_icon ?? 'fas fa-layer-group' }}" placeholder="fas fa-layer-group" required>
                                    <small class="text--muted">@lang('FontAwesome icon class')</small>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label>@lang('Info Item 3 Text')</label>
                                    <input type="text" class="form--control" name="info_item_3_text" value="{{ @$infoBannerContent->data_info->info_item_3_text ?? '8K+ fundraisers started daily' }}" required>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <h6 class="mb-3">@lang('Stats Section (For Green Theme)')</h6>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Stat 1 Value')</label>
                                    <input type="number" class="form--control" name="stat_1_value" value="{{ @$infoBannerContent->data_info->stat_1_value ?? 12000000 }}" placeholder="12000000">
                                    <small class="text--muted">@lang('Numeric value (e.g., 12000000 for $12M+)')</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Stat 1 Label')</label>
                                    <input type="text" class="form--control" name="stat_1_label" value="{{ @$infoBannerContent->data_info->stat_1_label ?? 'Total Funded' }}" placeholder="Total Funded">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Stat 2 Value')</label>
                                    <input type="number" class="form--control" name="stat_2_value" value="{{ @$infoBannerContent->data_info->stat_2_value ?? 2500 }}" placeholder="2500">
                                    <small class="text--muted">@lang('Numeric value (e.g., 2500 for 2,500+)')</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Stat 2 Label')</label>
                                    <input type="text" class="form--control" name="stat_2_label" value="{{ @$infoBannerContent->data_info->stat_2_label ?? 'Successful Projects' }}" placeholder="Successful Projects">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Stat 3 Value')</label>
                                    <input type="number" class="form--control" name="stat_3_value" value="{{ @$infoBannerContent->data_info->stat_3_value ?? 50 }}" placeholder="50">
                                    <small class="text--muted">@lang('Numeric value (e.g., 50 for 50K+)')</small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Stat 3 Label')</label>
                                    <input type="text" class="form--control" name="stat_3_label" value="{{ @$infoBannerContent->data_info->stat_3_label ?? 'Active Backers' }}" placeholder="Active Backers">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Info Banner Section')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Featured Projects Section Management -->
        <div class="col-12">
            <div class="custom--card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-star text--primary"></i>
                        @lang('Featured Projects Section Management')
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.homepage.featured-projects.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Section Title')</label>
                                    <input type="text" class="form--control" name="section_title" value="{{ @$featuredProjectsContent->data_info->section_title ?? 'Featured Projects' }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('View All Button Text')</label>
                                    <input type="text" class="form--control" name="view_all_button_text" value="{{ @$featuredProjectsContent->data_info->view_all_button_text ?? 'View All Campaigns' }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('View All Button URL')</label>
                                    <input type="text" class="form--control" name="view_all_button_url" value="{{ @$featuredProjectsContent->data_info->view_all_button_url ?? route('campaign') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Featured Projects Section')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Trending Campaign Section Management -->
        <div class="col-12">
            <div class="custom--card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-fire text--primary"></i>
                        @lang('Trending Campaign Section Management')
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $selectedCampaignId = null;
                        $showTrending = 0;
                        if ($trendingCampaignContent && $trendingCampaignContent->data_info) {
                            $dataInfo = is_array($trendingCampaignContent->data_info) 
                                ? $trendingCampaignContent->data_info 
                                : (array)$trendingCampaignContent->data_info;
                            $selectedCampaignId = $dataInfo['trending_campaign_id'] ?? null;
                            $showTrending = $dataInfo['show_trending'] ?? 0;
                        }
                        $allCampaigns = \App\Models\Campaign::approve()->latest()->get(['id', 'name', 'slug']);
                    @endphp
                    <div id="trending"></div>
                    <form action="{{ route('admin.homepage.trending-campaign.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Show Trending Campaign Section')</label>
                                    <select class="form--control" name="show_trending" id="showTrending">
                                        <option value="0" {{ $showTrending == 0 ? 'selected' : '' }}>@lang('No')</option>
                                        <option value="1" {{ $showTrending == 1 ? 'selected' : '' }}>@lang('Yes')</option>
                                    </select>
                                    <small class="text--muted">@lang('Enable or disable trending campaign section on home page')</small>
                                </div>
                            </div>
                            <div class="col-12" id="trendingCampaignSelect" style="{{ $showTrending == 0 ? 'display: none;' : '' }}">
                                <div class="form-group">
                                    <label>@lang('Select Trending Campaign')</label>
                                    <select class="form--control select2" name="trending_campaign_id" id="trendingCampaignId" style="width: 100%;">
                                        <option value="">@lang('-- Select Campaign --')</option>
                                        @foreach($allCampaigns as $campaign)
                                            <option value="{{ $campaign->id }}" {{ $selectedCampaignId == $campaign->id ? 'selected' : '' }}>
                                                {{ $campaign->name }} ({{ $campaign->slug }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text--muted">@lang('Select a campaign to display as trending on home page')</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Trending Campaign Section')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 

@push('page-script-lib')
<script src="{{ asset('assets/admin/js/page/select2.js') }}"></script>
@endpush

@push('page-script')
<script>
    $(document).ready(function() {
        // Initialize Select2
        if ($('.select2').length) {
            $('.select2').select2({
                placeholder: '@lang("-- Select Campaign --")',
                allowClear: true
            });
        }

        // Toggle trending campaign select based on show_trending
        $('#showTrending').on('change', function() {
            if ($(this).val() == '1') {
                $('#trendingCampaignSelect').slideDown();
            } else {
                $('#trendingCampaignSelect').slideUp();
            }
        });
    });
</script>
@endpush 