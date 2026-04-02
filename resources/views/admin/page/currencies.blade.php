@extends('admin.layouts.master')

@section('master')
    <div class="col-12 mb-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <form method="GET" action="{{ route('admin.currencies.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
            <input type="text" name="search" class="form--control form-control-sm" placeholder="@lang('Search code')" value="{{ request('search') }}">
            <select name="source" class="form--control form-select form-select-sm">
                <option value="">@lang('All Sources')</option>
                <option value="api" @selected(request('source') === 'api')>@lang('API')</option>
                <option value="manual" @selected(request('source') === 'manual')>@lang('Manual')</option>
                <option value="default" @selected(request('source') === 'default')>@lang('Default')</option>
            </select>
            <button type="submit" class="btn btn--sm btn--secondary">
                <i class="ti ti-search"></i> @lang('Filter')
            </button>
            @if(request()->has('search') || request()->has('source'))
                <a href="{{ route('admin.currencies.index') }}" class="btn btn--sm btn--light">
                    <i class="ti ti-x"></i> @lang('Clear')
                </a>
            @endif
        </form>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            @php
                $cronUrl = url('/cron/currencies-sync');
            @endphp
            <div class="input-group input-group-sm w-auto">
                <input type="text" class="form--control form-control-sm cron-link-input" value="{{ $cronUrl }}" readonly>
                <button type="button" class="btn btn--sm btn--secondary copyCronLink" data-link="{{ $cronUrl }}">
                    <i class="ti ti-copy"></i> @lang('Copy Cron Link')
                </button>
            </div>
            <button type="button" class="btn btn--sm btn--base addCurrencyBtn">
                <i class="ti ti-circle-plus"></i> @lang('Add New')
            </button>
            <form action="{{ route('admin.currencies.sync') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn--sm btn--primary">
                    <i class="ti ti-download"></i> @lang('Fetch Latest Rates')
                </button>
            </form>
        </div>
    </div>
    <div class="col-12">
        <table class="table table-borderless table--striped table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Currency')</th>
                    <th>@lang('Rate To USD')</th>
                    <th>@lang('Source')</th>
                    <th>@lang('Updated')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($currencies as $currency)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $currency->code }}</span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ showAmount($currency->rate_to_usd, 8) }}</span>
                            @if($currency->source === 'default')
                                <span class="badge badge--warning ms-1">@lang('Default')</span>
                            @endif
                        </td>
                        <td>
                            @if($currency->source === 'api')
                                <span class="badge badge--success">@lang('API')</span>
                            @elseif($currency->source === 'manual')
                                <span class="badge badge--primary">@lang('Manual')</span>
                            @else
                                <span class="badge badge--warning">@lang('Default')</span>
                            @endif
                        </td>
                        <td>
                            <span>{{ showDateTime($currency->updated_at) }}</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.currencies.update', $currency->id) }}" method="POST" class="d-flex gap-2 justify-content-end">
                                @csrf
                                <input type="number" step="0.00000001" min="0.00000001" name="rate_to_usd" class="form--control form--control--sm w-auto" value="{{ $currency->rate_to_usd }}" required>
                                <button type="submit" class="btn btn--sm btn--base">
                                    <i class="ti ti-refresh"></i> @lang('Update')
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    @include('admin.partials.noData')
                @endforelse
            </tbody>
        </table>

        @if ($currencies->hasPages())
            {{ paginateLinks($currencies->appends(request()->query())) }}
        @endif
    </div>

    <div class="modal fade" id="addCurrencyModal" tabindex="-1" role="dialog" aria-labelledby="addCurrencyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.currencies.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCurrencyModalLabel">@lang('Add New Currency')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form--label">@lang('Country')</label>
                                <select name="country" class="form--control form-select" required>
                                    <option value="" selected disabled>@lang('Select Country')</option>
                                    <option value="United States">@lang('United States')</option>
                                    <option value="Pakistan">@lang('Pakistan')</option>
                                    <option value="India">@lang('India')</option>
                                    <option value="Bangladesh">@lang('Bangladesh')</option>
                                    <option value="United Arab Emirates">@lang('United Arab Emirates')</option>
                                    <option value="Saudi Arabia">@lang('Saudi Arabia')</option>
                                    <option value="United Kingdom">@lang('United Kingdom')</option>
                                    <option value="Canada">@lang('Canada')</option>
                                    <option value="Australia">@lang('Australia')</option>
                                    <option value="New Zealand">@lang('New Zealand')</option>
                                    <option value="Germany">@lang('Germany')</option>
                                    <option value="France">@lang('France')</option>
                                    <option value="Spain">@lang('Spain')</option>
                                    <option value="Italy">@lang('Italy')</option>
                                    <option value="Netherlands">@lang('Netherlands')</option>
                                    <option value="Ireland">@lang('Ireland')</option>
                                    <option value="Sweden">@lang('Sweden')</option>
                                    <option value="Norway">@lang('Norway')</option>
                                    <option value="Denmark">@lang('Denmark')</option>
                                    <option value="Switzerland">@lang('Switzerland')</option>
                                    <option value="Japan">@lang('Japan')</option>
                                    <option value="China">@lang('China')</option>
                                    <option value="Hong Kong">@lang('Hong Kong')</option>
                                    <option value="Singapore">@lang('Singapore')</option>
                                    <option value="Malaysia">@lang('Malaysia')</option>
                                    <option value="Indonesia">@lang('Indonesia')</option>
                                    <option value="Thailand">@lang('Thailand')</option>
                                    <option value="Philippines">@lang('Philippines')</option>
                                    <option value="South Africa">@lang('South Africa')</option>
                                    <option value="Nigeria">@lang('Nigeria')</option>
                                    <option value="Kenya">@lang('Kenya')</option>
                                    <option value="Egypt">@lang('Egypt')</option>
                                    <option value="Brazil">@lang('Brazil')</option>
                                    <option value="Mexico">@lang('Mexico')</option>
                                    <option value="Turkey">@lang('Turkey')</option>
                                    <option value="Russia">@lang('Russia')</option>
                                </select>
                                <small class="text-muted">@lang('System will detect the currency code from this country value.')</small>
                            </div>
                            <div class="col-12">
                                <label class="form--label">@lang('Rate To USD')</label>
                                <input type="number" step="0.00000001" min="0.00000001" name="rate_to_usd" class="form--control" placeholder="@lang('e.g., 0.0036')" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--sm btn--base">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        (function ($) {
            "use strict"

            $('.addCurrencyBtn').on('click', function () {
                $('#addCurrencyModal').modal('show')
            })

            $('.copyCronLink').on('click', function () {
                const copiedText = @json(__('Copied'));
                const copyText = @json(__('Copy Cron Link'));
                const failedText = @json(__('Failed to copy'));
                const link = $(this).data('link') || $('.cron-link-input').val();
                if (!link) {
                    return;
                }
                const button = $(this);
                const resetBtn = () => {
                    button.addClass('btn--secondary').removeClass('btn--success').html(`<i class="ti ti-copy"></i> ${copyText}`);
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(link).then(() => {
                        button.addClass('btn--success').removeClass('btn--secondary').html(`<i class="ti ti-copy-check"></i> ${copiedText}`);
                        if (typeof showToasts === 'function') {
                            showToasts('success', copiedText);
                        }
                        setTimeout(resetBtn, 1500);
                    });
                    return;
                }

                const input = $('.cron-link-input');
                input.trigger('focus');
                input.trigger('select');
                const copied = document.execCommand('copy');
                if (copied) {
                    button.addClass('btn--success').removeClass('btn--secondary').html(`<i class="ti ti-copy-check"></i> ${copiedText}`);
                    if (typeof showToasts === 'function') {
                        showToasts('success', copiedText);
                    }
                    setTimeout(resetBtn, 1500);
                } else if (typeof showToasts === 'function') {
                    showToasts('error', failedText);
                }
            });
        })(jQuery)
    </script>
@endpush
