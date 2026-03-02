@extends('admin.layouts.master')

@php
    $pageTitle = 'Sub Admins';
@endphp

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Username')</th>
                    <th>@lang('Email')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $admin->name }}</span>
                        </td>
                        <td>
                            <span>{{ $admin->username }}</span>
                        </td>
                        <td>
                            <span>{{ $admin->email }}</span>
                        </td>
                        <td>
                            @if($admin->id === auth()->guard('admin')->id())
                                <span class="badge bg--info">@lang('You')</span>
                            @else
                                <form action="{{ route('admin.admin-users.status', $admin->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($admin->status)
                                        <button type="submit" class="badge bg--success border-0" title="Click to deactivate">@lang('Active')</button>
                                    @else
                                        <button type="submit" class="badge bg--danger border-0" title="Click to activate">@lang('Inactive')</button>
                                    @endif
                                </form>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.admin-users.edit', $admin) }}" class="btn btn--sm btn-outline--base">
                                    <i class="ti ti-edit"></i> @lang('Edit')
                                </a>
                                @if($admin->id !== auth()->guard('admin')->id())
                                    <form action="{{ route('admin.admin-users.destroy', $admin) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn--sm btn--danger" onclick="return confirm('Are you sure you want to delete this sub admin?')">
                                            <i class="ti ti-trash"></i> @lang('Delete')
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    @include('admin.partials.noData')
                @endforelse
            </tbody>
        </table>
    </div>
@push('breadcrumb')
    <a href="{{ route('admin.admin-users.create') }}" class="btn btn--sm btn--base">
        <i class="ti ti-circle-plus"></i> @lang('Add Sub Admin')
    </a>
@endpush
@endsection
