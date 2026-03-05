@extends('admin.layouts.master')

@php
    $pageTitle = 'Add Role';
@endphp

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Add Role')</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.store') }}" method="POST" class="row g-4">
                    @csrf
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Name')</label>
                        <input type="text" class="form--control" name="name" value="{{ old('name') }}" required>
                        @error('name')<span class="text--danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Slug')</label>
                        <input type="text" class="form--control" name="slug" value="{{ old('slug') }}" placeholder="e.g. content-manager" required>
                        @error('slug')<span class="text--danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Description')</label>
                        <textarea class="form--control" name="description" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Dashboard Widgets')</label>
                        <p class="text-muted small mb-2">@lang('Select which widgets this role can see on the dashboard.')</p>
                        <div class="row g-2">
                            @foreach($dashboardWidgets as $key => $config)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="dashboard_widgets[]" value="{{ $key }}" id="widget_{{ $key }}" {{ in_array($key, old('dashboard_widgets', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="widget_{{ $key }}">{{ __($config['label']) }}</label>
                                        @if(!empty($config['description']))
                                            <small class="text-muted d-block">{{ __($config['description']) }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Permissions')</label>
                        <p class="text-muted small mb-2">@lang('Select which permissions this role should have. Permissions are grouped by module.')</p>
                        <div class="row g-3">
                            @foreach($permissions as $module => $modulePerms)
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <strong class="text-capitalize">{{ str_replace('_', ' ', $module) }}</strong>
                                        <div class="row g-2 mt-2">
                                            @foreach($modulePerms as $perm)
                                                <div class="col-md-4 col-lg-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->action }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn--base">@lang('Create Role')</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
