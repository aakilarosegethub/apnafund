@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Label')</th>
                    <th>@lang('Categories')</th>
                    <th>@lang('Slug')</th>
                    <th>@lang('Sort')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($footerCategories as $footerCategory)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ __($footerCategory->label) }}</span>
                        </td>
                        <td>
                            @php
                                $ids = $footerCategory->getCategoryIdsForFilter();
                                $catNames = $ids ? \App\Models\Category::whereIn('id', $ids)->pluck('name')->map(fn($n) => __($n))->join(', ') : '—';
                            @endphp
                            <span class="text-muted small">{{ $catNames }}</span>
                        </td>
                        <td>
                            <code>{{ $footerCategory->slug }}</code>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $footerCategory->sort_order ?? 0 }}</span>
                        </td>
                        <td>
                            @php echo $footerCategory->statusBadge @endphp
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn--sm btn-outline--base editBtn" 
                                        data-resource="{{ $footerCategory }}" 
                                        data-action="{{ route('admin.footer-categories.store', $footerCategory->id) }}">
                                    <i class="ti ti-edit"></i> @lang('Edit')
                                </button>

                                @if ($footerCategory->status == 'active')
                                    <button type="button" class="btn btn--sm btn--warning decisionBtn" 
                                            data-question="@lang('Are you sure to inactive this footer category?')" 
                                            data-action="{{ route('admin.footer-categories.status', $footerCategory->id) }}">
                                        <i class="ti ti-ban"></i> @lang('Inactive')
                                    </button>
                                @else
                                    <button type="button" class="btn btn--sm btn--success decisionBtn" 
                                            data-question="@lang('Are you sure to active this footer category?')" 
                                            data-action="{{ route('admin.footer-categories.status', $footerCategory->id) }}">
                                        <i class="ti ti-circle-check"></i> @lang('Active')
                                    </button>
                                @endif

                                <button type="button" class="btn btn--sm btn--danger decisionBtn" 
                                        data-question="@lang('Are you sure to delete this footer category?')" 
                                        data-action="{{ route('admin.footer-categories.delete', $footerCategory->id) }}">
                                    <i class="ti ti-trash"></i> @lang('Delete')
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    @include('admin.partials.noData')
                @endforelse
            </tbody>
        </table>

        @if ($footerCategories->hasPages())
            {{ paginateLinks($footerCategories) }}
        @endif
    </div>

    {{-- Add Modal --}}
    <div class="custom--modal modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="addModalLabel">@lang('Add New Footer Category')</h2>
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="{{ route('admin.footer-categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label required">@lang('Label')</label>
                                <input type="text" class="form--control" name="label" required placeholder="@lang('e.g. Health')">
                            </div>

                            <div class="col-12">
                                <label class="form--label">@lang('Campaign Categories')</label>
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($categories as $cat)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $cat->id }}" id="addFcat{{ $cat->id }}">
                                            <label class="form-check-label" for="addFcat{{ $cat->id }}">{{ __($cat->name) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="form-text text-muted">@lang('Select one or more. Link will show campaigns from these categories.')</small>
                            </div>

                            <div class="col-12">
                                <label class="form--label required">@lang('Slug')</label>
                                <input type="text" class="form--control" name="slug" required placeholder="@lang('e.g. discover or health-tech')">
                                <small class="form-text text-muted">@lang('URL path for this link')</small>
                            </div>

                            <div class="col-12">
                                <label class="form--label">@lang('Sort Order')</label>
                                <input type="number" class="form--control" name="sort_order" min="0" value="0" placeholder="@lang('e.g. 1, 2, 3')">
                            </div>

                            <div class="col-12">
                                <label class="form--label">@lang('Status')</label>
                                <select class="form--control form-select" name="status">
                                    <option value="active" selected>@lang('Active')</option>
                                    <option value="inactive">@lang('Inactive')</option>
                                </select>
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
                    <h2 class="modal-title" id="addModalLabel">@lang('Update Footer Category')</h2>
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label required">@lang('Label')</label>
                                <input type="text" class="form--control" name="label" id="editLabel" required placeholder="@lang('e.g. Health')">
                            </div>

                            <div class="col-12">
                                <label class="form--label">@lang('Campaign Categories')</label>
                                <div class="border rounded p-3 edit-footer-category-ids" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($categories as $cat)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $cat->id }}" id="editFcat{{ $cat->id }}">
                                            <label class="form-check-label" for="editFcat{{ $cat->id }}">{{ __($cat->name) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form--label required">@lang('Slug')</label>
                                <input type="text" class="form--control" name="slug" id="editSlug" required placeholder="@lang('e.g. discover')">
                            </div>

                            <div class="col-12">
                                <label class="form--label">@lang('Sort Order')</label>
                                <input type="number" class="form--control" name="sort_order" id="editSortOrder" min="0" placeholder="@lang('e.g. 1, 2, 3')">
                            </div>

                            <div class="col-12">
                                <label class="form--label">@lang('Status')</label>
                                <select class="form--control form-select" name="status" id="editStatus">
                                    <option value="active">@lang('Active')</option>
                                    <option value="inactive">@lang('Inactive')</option>
                                </select>
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
        <x-searchForm placeholder="Label" />

        <form method="GET" action="{{ route('admin.footer-categories.index') }}" class="d-flex flex-wrap align-items-center gap-2">
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

                editModal.find('#editLabel').val(resource.label)
                editModal.find('#editSlug').val(resource.slug)
                editModal.find('#editSortOrder').val(resource.sort_order ?? 0)
                editModal.find('#editStatus').val(resource.status)
                editModal.find('.edit-footer-category-ids input[type="checkbox"]').prop('checked', false)
                let ids = resource.category_ids || []
                if (ids.length === 0 && resource.category_id) ids = [resource.category_id]
                ids.forEach(function(id) {
                    editModal.find('.edit-footer-category-ids input[value="' + id + '"]').prop('checked', true)
                })
                editModal.find('form').attr('action', formAction)
                editModal.modal('show')
            })
        })(jQuery)
    </script>
@endpush
