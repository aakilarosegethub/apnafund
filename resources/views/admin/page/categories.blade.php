@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Sort')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Subcategories')</th>
                    <th>@lang('Campaigns')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ __($category->name) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $category->sort_order ?? 0 }}</span>
                        </td>
                        <td>
                            @php echo $category->statusBadge @endphp
                        </td>
                        <td>
                            @if(($category->subcategories_count ?? 0) > 0)
                                <a href="{{ route('admin.subcategories.index', ['category_id' => $category->id]) }}"
                                   class="btn btn--xs btn-outline--primary">
                                    {{ $category->subcategories_count }} @lang('View')
                                </a>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">{{ ($category->campaigns->count()) }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn--sm btn-outline--base editBtn"
                                        data-resource="{{ $category }}"
                                        data-action="{{ route('admin.categories.store', $category->id) }}">
                                    <i class="ti ti-edit"></i> @lang('Edit')
                                </button>

                                @if ($category->status)
                                    <button type="button" class="btn btn--sm btn--warning decisionBtn" data-question="@lang('Are you sure to inactive this category?')" data-action="{{ route('admin.categories.status', $category->id) }}">
                                        <i class="ti ti-ban"></i> @lang('Inactive')
                                    </button>
                                @else
                                    <button type="button" class="btn btn--sm btn--success decisionBtn" data-question="@lang('Are you sure to active this category?')" data-action="{{ route('admin.categories.status', $category->id) }}">
                                        <i class="ti ti-circle-check"></i> @lang('Active')
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    @include('admin.partials.noData')
                @endforelse
            </tbody>
        </table>

        @if ($categories->hasPages())
            {{ paginateLinks($categories) }}
        @endif
    </div>

    {{-- Add Modal --}}
    <div class="custom--modal modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="addModalLabel">@lang('Add New Category')</h2>
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label required">@lang('Name')</label>
                                <input type="text" class="form--control" name="name" required>
                            </div>
                            <div class="col-12">
                                <label class="form--label">@lang('Sort Order')</label>
                                <input type="number" class="form--control" name="sort_order" min="0" placeholder="@lang('e.g. 1, 2, 3')">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--sm btn--base">@lang('Add')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="custom--modal modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="addModalLabel">@lang('Update Category')</h2>
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label required">@lang('Name')</label>
                                <input type="text" class="form--control" name="name" id="editName" required>
                            </div>
                            <div class="col-12">
                                <label class="form--label">@lang('Sort Order')</label>
                                <input type="number" class="form--control" name="sort_order" id="editSortOrder" min="0" placeholder="@lang('e.g. 1, 2, 3')">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--sm btn--base">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-decisionModal />
@endsection

@push('breadcrumb')
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <x-searchForm placeholder="Name" />

        <form method="GET" action="{{ route('admin.categories.index') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <label class="form--label mb-0">@lang('Sort By')</label>
            <select name="sort_by" class="form--control form-select form-select-sm" onchange="this.form.submit()">
                <option value="sort_order" @selected(request('sort_by', 'sort_order') === 'sort_order')>@lang('Sort Order')</option>
                <option value="id" @selected(request('sort_by') === 'id')>@lang('ID')</option>
            </select>
            <select name="sort_dir" class="form--control form-select form-select-sm" onchange="this.form.submit()">
                <option value="asc" @selected(request('sort_dir', 'asc') === 'asc')>@lang('ASC')</option>
                <option value="desc" @selected(request('sort_dir') === 'desc')>@lang('DESC')</option>
            </select>
        </form>

        <button type="button" class="btn btn--sm btn--base addBtn">
            <i class="ti ti-circle-plus"></i> @lang('Add New')
        </button>
    </div>
@endpush

@push('page-script')
    <script>
        (function ($) {
            "use strict"

            $('.addBtn').on('click', function() {
                $('#addModal').modal('show')
            })

            let editModal = $('#editModal')

            $('.editBtn').on('click', function() {
                let resource = $(this).data('resource')
                let formAction = $(this).data('action')

                editModal.find('#editName').val(resource.name)
                editModal.find('#editSortOrder').val(resource.sort_order ?? 0)
                editModal.find('form').attr('action', formAction)
                editModal.modal('show')
            })
        })(jQuery)
    </script>
@endpush
