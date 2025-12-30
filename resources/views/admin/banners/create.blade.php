@extends('admin.layouts.master')

@php
    $pageTitle = 'Add New Banner';
@endphp

@section('master')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form--label required">@lang('Title')</label>
                            <input type="text" class="form--control" name="title" value="{{ old('title') }}" placeholder="Enter banner title">
                        </div>

                        <div class="col-12">
                            <label class="form--label required">@lang('Image')</label>
                            <input type="file" class="form--control" name="image" accept="image/*" required>
                            <small class="text-muted">Max size: 2MB, Formats: JPEG, PNG, JPG, GIF</small>
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('Link')</label>
                            <input type="url" class="form--control" name="link" value="{{ old('link') }}" placeholder="https://example.com">
                        </div>

                        <div class="col-12">
                            <label class="form--label">@lang('Description')</label>
                            <textarea class="form--control" name="description" rows="3" placeholder="Enter description">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form--label">@lang('Order')</label>
                            <input type="number" class="form--control" name="order" value="{{ old('order', 0) }}" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form--label required">@lang('Status')</label>
                            <select class="form--control" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>@lang('Active')</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>@lang('Inactive')</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.banners.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                                <button type="submit" class="btn btn--base">@lang('Create Banner')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@push('breadcrumb')
    <a href="{{ route('admin.banners.index') }}" class="btn btn--sm btn--secondary">
        <i class="ti ti-arrow-left"></i> @lang('Back')
    </a>
@endpush
@endsection

