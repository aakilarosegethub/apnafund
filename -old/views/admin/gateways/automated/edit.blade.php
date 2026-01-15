@extends('admin.layouts.master')

@section('master')
    @if($gateway->guideline)
        <div class="col-12">
            <div class="custom--card">
                <div class="card-header">
                    <h3 class="title">@lang('Guidelines')</h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <p>{{ __($gateway->guideline) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($gateway->code < 1000 && $gateway->extra)
        <div class="col-12">
            <div class="custom--card">
                <div class="card-header">
                    <h3 class="title">@lang('Configurations')</h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach($gateway->extra as $key => $param)
                            <div class="col-lg-6">
                                <div class="row align-items-center gy-2">
                                    <div class="col-lg-4">
                                        <label class="col-form--label required">{{ __(@$param->title) }}</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input--group">
                                            <input type="text" class="form--control" value="{{ route($param->value) }}" readonly>
                                            <span class="input-group-text bg--base text-white copyInput" role="button"><i class="ti ti-copy"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="col-12">
        <form class="row g-4" action="{{ route('admin.gateway.automated.update', $gateway->code) }}" method="POST">
            <div class="col-12">
                <div class="custom--card">
                    @csrf
                    <input type="hidden" name="alias" value="{{ $gateway->alias }}">
                    <input type="hidden" name="description" value="{{ $gateway->guideline }}">
                    
                    <div class="card-header">
                        <h3 class="title">@lang('Global Setting for') {{ __($gateway->name) }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @foreach($parameters->where('global', true) as $key => $param)
                                <div class="col-lg-6">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-lg-4">
                                            <label class="col-form--label {{ $key == 'sandbox' ? '' : 'required' }}">{{ __(@$param->title) }}</label>
                                        </div>
                                        <div class="col-lg-8">
                                            @if($key == 'sandbox')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="global[{{ $key }}]" value="1" id="sandbox_{{ $key }}" {{ @$param->value == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sandbox_{{ $key }}">
                                                        <span class="sandbox-status">{{ @$param->value == '1' ? 'Enabled (Test Mode)' : 'Disabled (Live Mode)' }}</span>
                                                    </label>
                                                </div>
                                            @else
                                                <input type="text" class="form--control" name="global[{{ $key }}]" value="{{ @$param->value }}" required>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Country Restrictions -->
            <div class="col-12">
                <div class="custom--card">
                    <div class="card-header">
                        <h3 class="title">@lang('Country Restrictions')</h3>
                        <p class="text-muted">@lang('Select countries where this payment gateway should be available. Leave empty to make it available in all countries.')</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form--label">@lang('Available Countries')</label>
                                <select class="form--control select2-multiple" name="countries[]" multiple data-placeholder="@lang('Select countries or leave empty for all countries')">
                                    @php
                                        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));
                                        $selectedCountries = $gateway->countries ?? [];
                                    @endphp
                                    @foreach($countryData as $code => $country)
                                        <option value="{{ $country->country }}" @selected(in_array($country->country, $selectedCountries))>
                                            {{ $country->country }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">@lang('If no countries are selected, this gateway will be available in all countries.')</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @isset($gateway->currencies)
                @foreach($gateway->currencies as $gatewayCurrency)
                    <div class="col-12 currencyBody">
                        <input type="hidden" class="currencyText" name="currency[{{ $currencyIndex }}][currency]" value="{{ $gatewayCurrency->currency }}">
                        <input type="hidden" name="currency[{{ $currencyIndex }}][name]" value="{{ $gatewayCurrency->name }}">

                        <div class="custom--card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="title">{{ __($gateway->name) }} - {{ __($gatewayCurrency->currency) }}</h3>

                                <button type="button" class="btn btn--sm btn--danger decisionBtn" data-question="@lang('Are you confirming the removal of this gateway currency')?" data-action="{{ route('admin.gateway.automated.remove', $gatewayCurrency->id) }}"><i class="ti ti-trash"></i> @lang('Delete')</button>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <div class="custom--card">
                                            <div class="card-body">
                                                <h3 class="card-subtitle">@lang('Limit')</h3>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form--label required">@lang('Minimum')</label>
                                                        <div class="input--group">
                                                            <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][min_amount]" value="{{ getAmount($gatewayCurrency->min_amount) }}" required>
                                                            <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form--label required">@lang('Maximum')</label>
                                                        <div class="input--group">
                                                            <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][max_amount]" value="{{ getAmount($gatewayCurrency->max_amount) }}" required>
                                                            <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="custom--card">
                                            <div class="card-body">
                                                <h3 class="card-subtitle">@lang('Charges')</h3>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form--label required">@lang('Fixed')</label>
                                                        <div class="input--group">
                                                            <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][fixed_charge]" value="{{ getAmount($gatewayCurrency->fixed_charge) }}" required>
                                                            <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form--label required">@lang('Percent')</label>
                                                        <div class="input--group">
                                                            <input type="number" step="0.01" min="0" class="form--control" name="currency[{{ $currencyIndex }}][percent_charge]" value="{{ getAmount($gatewayCurrency->percent_charge) }}" required>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="custom--card">
                                            <div class="card-body">
                                                <h3 class="card-subtitle">@lang('Currency')</h3>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form--label required">@lang('Symbol')</label>
                                                        <input type="text" class="form--control dynamic-symbol" name="currency[{{ $currencyIndex }}][symbol]" value="{{ $gatewayCurrency->symbol }}" data-crypto="{{ $gateway->crypto }}" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form--label required">@lang('Rate')</label>
                                                        <div class="input--group">
                                                            <span class="input-group-text">1 {{ __($setting->site_cur ) }} =</span>
                                                            <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][rate]" value="{{ getAmount($gatewayCurrency->rate) }}" required>
                                                            <span class="input-group-text currency_symbol">{{ __($gatewayCurrency->baseSymbol()) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($parameters->where('global', false)->count() != 0)
                                        @php
                                            $globalParameters = json_decode($gatewayCurrency->gateway_parameter);
                                        @endphp

                                        <div class="col-12">
                                            <div class="border-top">
                                                <div class="row g-4 mt-2">
                                                    <div class="col-12">
                                                        <div class="card-subtitle">
                                                            <span>@lang('Configuration')</span>
                                                        </div>
                                                        <div class="row g-4">
                                                            @foreach($parameters->where('global', false) as $key => $param)
                                                                <div class="col-lg-6">
                                                                    <div class="row align-items-center gy-2">
                                                                        <div class="col-lg-4">
                                                                            <label class="col-form--label required">{{ __(@$param->title) }}</label>
                                                                        </div>
                                                                        <div class="col-lg-8">
                                                                            <input type="text" class="form--control" name="currency[{{ $currencyIndex }}][param][{{ $key }}]" value="{{ $globalParameters->$key }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @php $currencyIndex++ @endphp
                @endforeach
            @endisset

            <div class="col-12 newMethodCurrency currencyBody d-none">
                <input type="hidden" class="currencyText" name="currency[{{ $currencyIndex }}][currency]" disabled>
                <input type="hidden" id="payment_currency_name" name="currency[{{ $currencyIndex }}][name]" disabled>

                <div class="custom--card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="title payment_currency_name">@lang('Gateway Name')</h3>
                        <button type="button" class="btn btn--sm btn--danger newCurrencyRemove"><i class="ti ti-trash"></i> @lang('Delete')</button>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="custom--card">
                                    <div class="card-body">
                                        <h3 class="card-subtitle">@lang('Limit')</h3>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form--label required">@lang('Minimum')</label>
                                                <div class="input--group">
                                                    <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][min_amount]" disabled required>
                                                    <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form--label required">@lang('Maximum')</label>
                                                <div class="input--group">
                                                    <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][max_amount]" disabled required>
                                                    <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="custom--card">
                                    <div class="card-body">
                                        <h3 class="card-subtitle">@lang('Charges')</h3>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form--label required">@lang('Fixed')</label>
                                                <div class="input--group">
                                                    <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][fixed_charge]" disabled required>
                                                    <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form--label required">@lang('Percent')</label>
                                                <div class="input--group">
                                                    <input type="number" step="0.01" min="0" class="form--control" name="currency[{{ $currencyIndex }}][percent_charge]" disabled required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="custom--card">
                                    <div class="card-body">
                                        <h3 class="card-subtitle">@lang('Currency')</h3>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form--label required">@lang('Symbol')</label>
                                                <input type="text" class="form--control dynamic-symbol" name="currency[{{ $currencyIndex }}][symbol]" data-crypto="{{ $gateway->crypto }}" disabled required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form--label required">@lang('Rate')</label>
                                                <div class="input--group">
                                                    <span class="input-group-text">1 {{ __($setting->site_cur ) }} =</span>
                                                    <input type="number" step="any" min="0" class="form--control" name="currency[{{ $currencyIndex }}][rate]" disabled required>
                                                    <span class="input-group-text currency_symbol">{{ __($gatewayCurrency && method_exists($gatewayCurrency, 'baseSymbol') ? $gatewayCurrency->baseSymbol() : '$') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($parameters->where('global', false)->count() != 0)
                                <div class="col-12">
                                    <div class="border-top">
                                        <div class="row g-4 mt-2">
                                            <div class="col-12">
                                                <div class="card-subtitle">
                                                    <span>@lang('Configuration')</span>
                                                </div>
                                                <div class="row g-4">
                                                    @foreach($parameters->where('global', false) as $key => $param)
                                                        <div class="col-lg-6">
                                                            <div class="row align-items-center gy-2">
                                                                <div class="col-lg-4">
                                                                    <label class="col-form--label required">{{ __(@$param->title) }}</label>
                                                                </div>
                                                                <div class="col-lg-8">
                                                                    <input type="text" class="form--control" name="currency[{{ $currencyIndex }}][param][{{ $key }}]" disabled required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="d-flex justify-content-center">
                    <button class="btn btn--base px-4">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>

    <x-decisionModal />
@endsection

@if(count($supportedCurrencies) > 0)
    @push('breadcrumb')
        <div class="input--group">
            <select class="form--control form--control--sm form-select newCurrencyVal">
                <option value="">@lang('Select one')</option>
                
                @forelse($supportedCurrencies as $currency => $symbol)
                    <option value="{{$currency}}" data-symbol="{{ $symbol }}">{{ __($currency) }} </option>
                @empty
                    <option value="">@lang('No available currency support')</option>
                @endforelse
           </select>

           <button class="btn btn--sm btn--base newCurrencyBtn" data-crypto="{{ $gateway->crypto }}" data-name="{{ $gateway->name }}">
                <i class="ti ti-circle-plus"></i> @lang('Add new')
            </button>
        </div>
    @endpush
@endif

@push('page-script')
    <script>
        (function ($) {
            "use strict";

            $('.newCurrencyBtn').on('click', function () {
                var form                = $('.newMethodCurrency');
                var getCurrencySelected = $('.newCurrencyVal').find(':selected').val();
                var currency            = $(this).data('crypto') == 1 ? 'USD' : `${getCurrencySelected}`;

                if (!getCurrencySelected) return;

                form.find('input').removeAttr('disabled');
                var symbol = $('.newCurrencyVal').find(':selected').data('symbol');

                form.find('.currencyText').val(getCurrencySelected);

                $('.payment_currency_name').text(`${$(this).data('name')} - ${getCurrencySelected}`);
                $('#payment_currency_name').val(`${$(this).data('name')} - ${getCurrencySelected}`);
                form.removeClass('d-none');

                $('html, body').animate({scrollTop: $('html, body').height()}, 'slow');

                $('.newCurrencyRemove').on('click', function () {
                    form.find('input').val('');
                    form.remove();
                });
            });

            $('.dynamic-symbol').on('input', function () {
                var curText = $(this).val();
                $(this).parents('.currencyBody').find('.currency_symbol').text(curText);
            });

            $('.copyInput').on('click', function (e) {
                var copybtn = $(this);
                var input = copybtn.closest('.input--group').find('input');
                if (input && input.select) {
                    input.select();
                    try {
                        document.execCommand('SelectAll')
                        document.execCommand('Copy', false, null);
                        input.blur();
                        showToasts('success',`Copied: ${copybtn.closest('.input--group').find('input').val()}`);
                    } catch (err) {
                        alert('Please press Ctrl/Cmd + C to copy');
                    }
                }
            });
        })(jQuery);

        // Sandbox Mode Toggle
        $(document).ready(function() {
            $('input[name="global[sandbox]"]').on('change', function() {
                const statusSpan = $(this).siblings('label').find('.sandbox-status');
                if ($(this).is(':checked')) {
                    statusSpan.text('Enabled (Test Mode)').removeClass('text-danger').addClass('text-warning');
                } else {
                    statusSpan.text('Disabled (Live Mode)').removeClass('text-warning').addClass('text-success');
                }
            });
        });
    </script>

    <style>
        .form-check-input:checked {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .sandbox-status {
            font-weight: 500;
            font-size: 14px;
        }
        .form-check-label {
            cursor: pointer;
        }
    </style>
@endpush

