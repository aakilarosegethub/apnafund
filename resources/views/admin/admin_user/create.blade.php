@extends('admin.layouts.master')

@php
    $pageTitle = 'Add Sub Admin';
@endphp

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Add Sub Admin')</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.admin-users.store') }}" method="POST" class="row g-4">
                    @csrf
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Name')</label>
                        <input type="text" class="form--control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Username')</label>
                        <input type="text" class="form--control" name="username" value="{{ old('username') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Email')</label>
                        <input type="email" class="form--control" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Password')</label>
                        <input type="password" class="form--control" name="password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Confirm Password')</label>
                        <input type="password" class="form--control" name="password_confirmation" required>
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Permissions')</label>
                        <p class="text-muted small mb-2">@lang('Select which sections this sub admin can access. Choose "Super Admin" for full access.')</p>
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="*" id="perm_super" {{ in_array('*', old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="perm_super">@lang('Super Admin (Full Access)')</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            @foreach(config('admin_permissions', []) as $key => $routes)
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $key }}" id="perm_{{ $key }}" {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $key }}">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn--base">@lang('Add Sub Admin')</button>
                        <a href="{{ route('admin.admin-users.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
