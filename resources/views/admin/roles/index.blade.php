@extends('admin.layouts.master')

@php
    $pageTitle = 'Roles';
@endphp

@push('breadcrumb')
    <a href="{{ route('admin.roles.create') }}" class="btn btn--sm btn--base">
        <i class="ti ti-plus"></i> @lang('Add Role')
    </a>
@endpush

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-body">
                <table class="table table-borderless table--striped table--responsive--xl">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Slug')</th>
                            <th>@lang('Admins')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td><span class="fw-bold">{{ $role->name }}</span></td>
                                <td><code>{{ $role->slug }}</code></td>
                                <td>{{ $role->admins_count }}</td>
                                <td>
                                    @if($role->is_super_admin)
                                        <span class="badge bg--info">@lang('Super Admin')</span>
                                    @else
                                        <span class="badge bg--secondary">@lang('Custom')</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn--sm btn-outline--base">
                                            <i class="ti ti-edit"></i> @lang('Edit')
                                        </a>
                                        @if($role->canDelete() && !$role->admins()->exists())
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn--sm btn--danger" onclick="return confirm('@lang('Are you sure you want to delete this role?')')">
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
        </div>
    </div>
@endsection
