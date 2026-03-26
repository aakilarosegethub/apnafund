@extends('admin.layouts.master')

@section('master')
<div class="col-12">
    <div class="custom--card mb-4">
        <div class="card-header">
            <h3 class="title">@lang('Add Document Requirement')</h3>
        </div>
        <div class="card-body">
            <form class="row g-3" method="POST" action="{{ route('admin.campaigns.document.requirements.store') }}">
                @csrf
                <div class="col-md-4">
                    <label class="form--label required">@lang('Display Label')</label>
                    <input type="text" class="form--control" name="label" required placeholder="@lang('e.g. CNIC Front Copy')">
                </div>
                <div class="col-md-4">
                    <label class="form--label">@lang('Field Key (optional)')</label>
                    <input type="text" class="form--control" name="field_key" placeholder="@lang('e.g. cnic_front_copy')">
                    <small class="text-muted">@lang('Lowercase, underscore only. Leave empty to auto-generate from label.')</small>
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('Required')</label>
                    <select class="form--control form-select" name="is_required">
                        <option value="1">@lang('Required')</option>
                        <option value="0">@lang('Optional')</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('Status')</label>
                    <select class="form--control form-select" name="is_active">
                        <option value="1">@lang('Active')</option>
                        <option value="0">@lang('Inactive')</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form--label">@lang('Visibility')</label>
                    <select class="form--control form-select js-country-scope" name="is_global" data-target="#createCountrySelect">
                        <option value="1">@lang('Global (All Countries)')</option>
                        <option value="0">@lang('Country Specific')</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form--label">@lang('Countries')</label>
                    <select class="form--control form-select select-2-countries" id="createCountrySelect" name="countries[]" multiple disabled>
                        @foreach(($allCountries ?? []) as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">@lang('Used only when visibility is Country Specific.')</small>
                </div>
                <div class="col-md-2">
                    <label class="form--label">@lang('Sort')</label>
                    <input type="number" class="form--control" name="sort_order" value="0" min="0">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn--base">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>

    <div class="custom--card">
        <div class="card-header">
            <h3 class="title">@lang('Current Requirements')</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderless table--striped table--responsive--xl">
                <thead>
                    <tr>
                        <th>@lang('Field')</th>
                        <th>@lang('Label')</th>
                        <th>@lang('Type')</th>
                        <th>@lang('Scope')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Sort')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><code>{{ $item->field_key }}</code></td>
                            <td>{{ $item->label }}</td>
                            <td>
                                @if($item->is_required)
                                    <span class="badge badge--danger">@lang('Required')</span>
                                @else
                                    <span class="badge badge--warning">@lang('Optional')</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_global)
                                    <span class="badge badge--info">@lang('Global')</span>
                                @else
                                    <span class="badge badge--base">{{ implode(', ', (array) ($item->countries ?? [])) ?: __('Country Specific') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge badge--success">@lang('Active')</span>
                                @else
                                    <span class="badge badge--secondary">@lang('Inactive')</span>
                                @endif
                            </td>
                            <td>{{ $item->sort_order }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn--sm btn-outline--base editBtn"
                                            data-id="{{ $item->id }}"
                                            data-label="{{ $item->label }}"
                                            data-field-key="{{ $item->field_key }}"
                                            data-required="{{ $item->is_required ? 1 : 0 }}"
                                            data-active="{{ $item->is_active ? 1 : 0 }}"
                                            data-global="{{ $item->is_global ? 1 : 0 }}"
                                            data-countries='@json((array) ($item->countries ?? []))'
                                            data-sort="{{ $item->sort_order }}">
                                        @lang('Edit')
                                    </button>
                                    <form method="POST" action="{{ route('admin.campaigns.document.requirements.delete', $item->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn--sm btn--danger">@lang('Delete')</button>
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
    </div>
</div>

<div class="custom--modal modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">@lang('Update Document Requirement')</h2>
                <button type="button" class="btn btn--sm btn--icon btn-outline--secondary modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form method="POST" id="editForm" action="">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form--label required">@lang('Display Label')</label>
                            <input type="text" class="form--control" name="label" id="editLabel" required>
                        </div>
                        <div class="col-12">
                            <label class="form--label required">@lang('Field Key')</label>
                            <input type="text" class="form--control" name="field_key" id="editFieldKey" required>
                        </div>
                        <div class="col-6">
                            <label class="form--label">@lang('Required')</label>
                            <select class="form--control form-select" name="is_required" id="editRequired">
                                <option value="1">@lang('Required')</option>
                                <option value="0">@lang('Optional')</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form--label">@lang('Status')</label>
                            <select class="form--control form-select" name="is_active" id="editActive">
                                <option value="1">@lang('Active')</option>
                                <option value="0">@lang('Inactive')</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form--label">@lang('Visibility')</label>
                            <select class="form--control form-select js-country-scope" name="is_global" id="editIsGlobal" data-target="#editCountries">
                                <option value="1">@lang('Global (All Countries)')</option>
                                <option value="0">@lang('Country Specific')</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form--label">@lang('Countries')</label>
                            <select class="form--control form-select select-2-countries" name="countries[]" id="editCountries" multiple disabled>
                                @foreach(($allCountries ?? []) as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">@lang('Used only when visibility is Country Specific.')</small>
                        </div>
                        <div class="col-12">
                            <label class="form--label">@lang('Sort')</label>
                            <input type="number" class="form--control" name="sort_order" id="editSortOrder" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-script')
<script>
    (function($){
        "use strict";
        $('.editBtn').on('click', function () {
            const id = $(this).data('id');
            const label = $(this).data('label');
            const fieldKey = $(this).data('field-key');
            const required = $(this).data('required');
            const active = $(this).data('active');
            const isGlobal = $(this).data('global');
            const countries = $(this).data('countries') || [];
            const sort = $(this).data('sort');

            $('#editLabel').val(label);
            $('#editFieldKey').val(fieldKey);
            $('#editRequired').val(String(required));
            $('#editActive').val(String(active));
            $('#editIsGlobal').val(String(isGlobal));
            $('#editCountries').val(countries);
            $('#editSortOrder').val(String(sort));
            $('#editForm').attr('action', "{{ route('admin.campaigns.document.requirements.update', ':id') }}".replace(':id', id));
            toggleCountrySelect($('#editIsGlobal'));
            $('#editModal').modal('show');
        });

        function toggleCountrySelect($select) {
            const target = $($select.data('target'));
            if (!target.length) return;
            const isGlobal = String($select.val()) === '1';
            target.prop('disabled', isGlobal);
            if (isGlobal) {
                target.val([]);
            }
        }

        $('.js-country-scope').on('change', function () {
            toggleCountrySelect($(this));
        });
        $('.js-country-scope').each(function(){
            toggleCountrySelect($(this));
        });

        const $createCountrySelect = $('#createCountrySelect');
        const $editCountrySelect = $('#editCountries');

        if ($createCountrySelect.length) {
            $createCountrySelect.select2({
                placeholder: '@lang("Select countries")',
                width: '100%',
                allowClear: true
            });
        }
        if ($editCountrySelect.length) {
            $editCountrySelect.select2({
                placeholder: '@lang("Select countries")',
                width: '100%',
                allowClear: true,
                dropdownParent: $('#editModal')
            });
        }

        $('#editModal').on('shown.bs.modal', function () {
            if ($editCountrySelect.length) {
                $editCountrySelect.trigger('change.select2');
            }
        });
    })(jQuery);
</script>
@endpush

