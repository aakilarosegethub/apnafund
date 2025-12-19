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
                                    <input type="text" class="form--control @error('hero_heading_1') is-invalid @enderror" name="hero_heading_1" value="{{ old('hero_heading_1', @$heroContent->data_info->hero_heading_1 ?? 'Crowd Funding') }}" required>
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
                                    @if(@$heroContent->data_info->hero_background_image)
                                        <small class="text--success">@lang('Current image:') {{ @$heroContent->data_info->hero_background_image }}</small>
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
    </div>
@endsection 