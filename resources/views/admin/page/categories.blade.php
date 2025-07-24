@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Image')</th>
                    <th>@lang('Name')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Campaigns')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>
                            <div class="table-card-with-image">
                                <div class="table-card-with-image__img">
                                    <img src="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}" alt="Image">
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold">{{ __($category->name) }}</span>
                        </td>
                        <td>
                            @php echo $category->statusBadge @endphp
                        </td>
                        <td>
                            <span class="fw-bold">{{ ($category->campaigns->count()) }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn--sm btn-outline--base editBtn" data-resource="{{ $category }}" data-image="{{ getImage(getFilePath('category') . '/' . $category->image, getFileSize('category')) }}" data-action="{{ route('admin.categories.store', $category->id) }}">
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
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="upload__img mb-2">
                                    <label for="addImage" class="upload__img__btn">
                                        <i class="ti ti-camera"></i>
                                    </label>
                                    <input type="file" id="addImage" class="image-upload" name="image" accept=".jpeg, .jpg, .png">
                                    <label class="upload__img-preview image-preview">
                                        <i class="ti ti-photo-up"></i>
                                    </label>
                                    <button type="button" class="btn btn--sm btn--icon btn--danger custom-file-input-clear d-none">
                                        <i class="ti ti-circle-x"></i>
                                    </button>
                                </div>
                                <label class="text-center small">
                                    @lang('Supported files'): <span class="fw-semibold text--base">@lang('jpeg'), @lang('jpg'), @lang('png').</span> @lang('Image size') <span class="fw-semibold text--base">{{ getFileSize('category') }}@lang('px').</span>
                                </label>
                            </div>

                            <div class="col-12">
                                <label class="form--label required">@lang('Name')</label>
                                <input type="text" class="form--control" name="name" required>
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
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="upload__img mb-2">
                                    <label for="editImage" class="upload__img__btn">
                                        <i class="ti ti-camera"></i>
                                    </label>
                                    <input type="file" id="editImage" class="image-upload" name="image" accept=".jpeg, .jpg, .png">
                                    <label class="upload__img-preview image-preview">
                                        <img src="" alt="image">
                                    </label>
                                    <button type="button" class="btn btn--sm btn--icon btn--danger custom-file-input-clear d-none">
                                        <i class="ti ti-circle-x"></i>
                                    </button>
                                </div>
                                <label class="text-center small">
                                    @lang('Supported files'): <span class="fw-semibold text--base">@lang('jpeg'), @lang('jpg'), @lang('png').</span> @lang('Image size') <span class="fw-semibold text--base">{{ getFileSize('category') }}@lang('px').</span>
                                </label>
                            </div>

                            <div class="col-12">
                                <label class="form--label required">@lang('Name')</label>
                                <input type="text" class="form--control" name="name" required>
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
                let image = $(this).data('image')
                let formAction = $(this).data('action')

                editModal.find('.image-preview img').attr("src", image)
                editModal.find('[name=name]').val(resource.name)
                editModal.find('form').attr('action', formAction)
                editModal.modal('show')
            })
        })(jQuery)
    </script>
@endpush
