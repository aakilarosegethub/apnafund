@extends('admin.layouts.master')

@php
    $pageTitle = 'Banner Management';
@endphp

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Image')</th>
                    <th>@lang('Title')</th>
                    <th>@lang('Link')</th>
                    <th>@lang('Order')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($banners as $banner)
                    <tr>
                        <td>
                            @if($banner->image)
                                <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}" style="max-width: 100px; max-height: 60px; object-fit: cover;">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">{{ $banner->title ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($banner->link)
                                <a href="{{ $banner->link }}" target="_blank" class="text-primary">{{ Str::limit($banner->link, 30) }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">{{ $banner->order }}</span>
                        </td>
                        <td>
                            @if($banner->status == 'active')
                                <span class="badge bg--success">@lang('Active')</span>
                            @else
                                <span class="badge bg--danger">@lang('Inactive')</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn--sm btn-outline--base">
                                    <i class="ti ti-edit"></i> @lang('Edit')
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--sm btn--danger" onclick="return confirm('Are you sure you want to delete this banner?')">
                                        <i class="ti ti-trash"></i> @lang('Delete')
                                    </button>
                                </form>
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
    <a href="{{ route('admin.banners.create') }}" class="btn btn--sm btn--base">
        <i class="ti ti-circle-plus"></i> @lang('Add New Banner')
    </a>
@endpush
@endsection

