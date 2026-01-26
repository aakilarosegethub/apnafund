@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Code')</th>
                    <th>@lang('Description')</th>
                    <th>@lang('Sort')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Campaigns')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payoutBanks as $bank)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ __($bank->name) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $bank->code }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ strLimit($bank->description ?? '', 50) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $bank->sort_order ?? 0 }}</span>
                        </td>
                        <td>
                            @php echo $bank->statusBadge @endphp
                        </td>
                        <td>
                            <span class="fw-bold">{{ $bank->campaigns->count() }}</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn--sm btn-outline--base editBtn"
                                        data-resource="{{ $bank }}"
                                        data-action="{{ route('admin.payout-banks.store', $bank->id) }}">
                                    <i class="ti ti-edit"></i> @lang('Edit')
                                </button>

                                @if ($bank->status)
                                    <button type="button" class="btn btn--sm btn--warning decisionBtn" data-question="@lang('Are you sure to inactive this payout bank?')" data-action="{{ route('admin.payout-banks.status', $bank->id) }}">
                                        <i class="ti ti-ban"></i> @lang('Inactive')
                                    </button>
                                @else
                                    <button type="button" class="btn btn--sm btn--success decisionBtn" data-question="@lang('Are you sure to active this payout bank?')" data-action="{{ route('admin.payout-banks.status', $bank->id) }}">
                                        <i class="ti ti-circle-check"></i> @lang('Active')
                                    </button>
                                @endif

                                @if ($bank->campaigns->count() == 0)
                                    <button type="button" class="btn btn--sm btn--danger decisionBtn" data-question="@lang('Are you sure to delete this payout bank?')" data-action="{{ route('admin.payout-banks.delete', $bank->id) }}">
                                        <i class="ti ti-trash"></i> @lang('Delete')
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

        @if ($payoutBanks->hasPages())
            {{ paginateLinks($payoutBanks) }}
        @endif
    </div>

    {{-- Add Modal --}}
    <div class="custom--modal modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="addModalLabel">@lang('Add New Payout Bank')</h2>
                    <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <form action="{{ route('admin.payout-banks.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label required">@lang('Name')</label>
                                <input type="text" class="form--control" name="name" required placeholder="@lang('e.g. Bank of America, Chase Bank')">
                            </div>
                            <div class="col-12">
                                <label class="form--label required">@lang('Code')</label>
                                <input type="text" class="form--control" name="code" required placeholder="@lang('e.g. BOA, CHASE')">
                                <small class="text-muted">@lang('Unique code for this bank')</small>
                            </div>
                            <div class="col-12">
                                <label class="form--label">@lang('Description')</label>
                                <textarea class="form--control" name="description" rows="3" placeholder="@lang('Optional description')"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form--label">@lang('Sort Order')</label>
                                <input type="number" class="form--control" name="sort_order" min="0" value="0" placeholder="@lang('e.g. 1, 2, 3')">
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
                    <h2 class="modal-title" id="editModalLabel">@lang('Update Payout Bank')</h2>
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
                                <label class="form--label required">@lang('Code')</label>
                                <input type="text" class="form--control" name="code" id="editCode" required>
                            </div>
                            <div class="col-12">
                                <label class="form--label">@lang('Description')</label>
                                <textarea class="form--control" name="description" id="editDescription" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form--label">@lang('Sort Order')</label>
                                <input type="number" class="form--control" name="sort_order" id="editSortOrder" min="0" value="0">
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
                editModal.find('#editCode').val(resource.code)
                editModal.find('#editDescription').val(resource.description ?? '')
                editModal.find('#editSortOrder').val(resource.sort_order ?? 0)
                editModal.find('form').attr('action', formAction)
                editModal.modal('show')
            })
        })(jQuery)
    </script>
@endpush
