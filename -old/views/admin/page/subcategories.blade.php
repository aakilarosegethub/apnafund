@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Parent Category')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subcategories as $subcategory)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ __($subcategory->name) }}</span>
                        </td>
                        <td>
                            <span class="badge badge--info">{{ __($subcategory->category->name ?? 'N/A') }}</span>
                        </td>
                        <td>
                            @php echo $subcategory->statusBadge @endphp
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn--sm btn-outline--base editBtn" 
                                        data-resource="{{ $subcategory }}" 
                                        data-action="{{ route('admin.subcategories.store', $subcategory->id) }}">
                                    <i class="ti ti-edit"></i> @lang('Edit')
                                </button>

                                @if ($subcategory->status == 'active')
                                    <button type="button" class="btn btn--sm btn--warning decisionBtn" 
                                            data-question="@lang('Are you sure to inactive this subcategory?')" 
                                            data-action="{{ route('admin.subcategories.status', $subcategory->id) }}">
                                        <i class="ti ti-ban"></i> @lang('Inactive')
                                    </button>
                                @else
                                    <button type="button" class="btn btn--sm btn--success decisionBtn" 
                                            data-question="@lang('Are you sure to active this subcategory?')" 
                                            data-action="{{ route('admin.subcategories.status', $subcategory->id) }}">
                                        <i class="ti ti-circle-check"></i> @lang('Active')
                                    </button>
                                @endif

                                <button type="button" class="btn btn--sm btn--danger decisionBtn" 
                                        data-question="@lang('Are you sure to delete this subcategory?')" 
                                        data-action="{{ route('admin.subcategories.delete', $subcategory->id) }}">
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

        @if ($subcategories->hasPages())
            {{ paginateLinks($subcategories) }}
        @endif
    </div>

    {{-- Add Modal --}}
    <div class="custom--modal modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="addModalLabel">@lang('Add New Subcategory')</h2>
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="{{ route('admin.subcategories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label required">@lang('Parent Category')</label>
                                <select class="form--control form-select" name="category_id" required>
                                    <option value="">@lang('Select Category')</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form--label required">@lang('Subcategory Name')</label>
                                <input type="text" class="form--control" name="name" required placeholder="@lang('Enter subcategory name')">
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
                    <h2 class="modal-title" id="addModalLabel">@lang('Update Subcategory')</h2>
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label required">@lang('Parent Category')</label>
                                <select class="form--control form-select" name="category_id" id="editCategoryId" required>
                                    <option value="">@lang('Select Category')</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form--label required">@lang('Subcategory Name')</label>
                                <input type="text" class="form--control" name="name" id="editName" required placeholder="@lang('Enter subcategory name')">
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
    <x-searchForm placeholder="Name" />

    <button type="button" class="btn btn--sm btn--base addBtn">
        <i class="ti ti-circle-plus"></i> @lang('Add New')
    </button>
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
                editModal.find('#editCategoryId').val(resource.category_id)
                editModal.find('form').attr('action', formAction)
                editModal.modal('show')
            })
        })(jQuery)
    </script>
@endpush

