@extends('admin.layouts.master')

@php
    $pageTitle = 'DSA Blog Posts';
@endphp

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Feature Image')</th>
                    <th>@lang('Title')</th>
                    <th>@lang('Excerpt')</th>
                    <th>@lang('Order')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>
                            @if($post->feature_image)
                                <img src="{{ getImage(getFilePath('blog') . '/' . $post->feature_image, getFileSize('blog')) }}" alt="{{ $post->title }}" style="max-width: 100px; max-height: 60px; object-fit: cover; border-radius: 4px;">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">{{ $post->title }}</span>
                        </td>
                        <td>
                            <span>{{ Str::limit($post->excerpt ?? strip_tags($post->content), 50) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $post->order }}</span>
                        </td>
                        <td>
                            @if($post->status == 'active')
                                <span class="badge bg--success">@lang('Active')</span>
                            @else
                                <span class="badge bg--danger">@lang('Inactive')</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn--sm btn-outline--base">
                                    <i class="ti ti-edit"></i> @lang('Edit')
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn--sm btn--danger" onclick="return confirm('Are you sure you want to delete this post?')">
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
    <a href="{{ route('admin.blog.create') }}" class="btn btn--sm btn--base">
        <i class="ti ti-circle-plus"></i> @lang('Add New DSA Post')
    </a>
@endpush
@endsection
