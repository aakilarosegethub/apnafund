@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Edit Sub Admin')</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.admin-users.update', $admin_user) }}" method="POST" class="row g-4">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Name')</label>
                        <input type="text" class="form--control" name="name" value="{{ old('name', $admin_user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Username')</label>
                        <input type="text" class="form--control" name="username" value="{{ old('username', $admin_user->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Email')</label>
                        <input type="email" class="form--control" name="email" value="{{ old('email', $admin_user->email) }}" required>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('New Password')</label>
                        <input type="password" class="form--control" name="password" placeholder="Leave blank to keep current">
                    </div>
                    <div class="col-md-6">
                        <label class="form--label">@lang('Confirm Password')</label>
                        <input type="password" class="form--control" name="password_confirmation" placeholder="Leave blank to keep current">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn--base">@lang('Update Sub Admin')</button>
                        <a href="{{ route('admin.admin-users.index') }}" class="btn btn--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
