@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <form action="{{ $actionRoute }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-12">
                    <div class="custom--card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3">
                                            <label class="col-form--label required">@lang('Name')</label>
                                        </div>
                                        <div class="col-xxl-9">
                                            <input type="text" class="form--control" name="name" value="{{ $method ? @$method->name : old('name') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3">
                                            <label class="col-form--label required">@lang('Currency')</label>
                                        </div>
                                        <div class="col-xxl-9">
                                            <input type="text" class="form--control" name="currency" value="{{ $method ? @$methodRelation->currency : old('currency') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3">
                                            <label class="col-form--label required">@lang('Rate')</label>
                                        </div>
                                        <div class="col-xxl-9">
                                            <div class="input--group">
                                                <span class="input-group-text">1 {{ __($setting->site_cur ) }} =</span>
                                                <input type="number" step="any" min="0" class="form--control" name="rate" value="{{ $method ? getAmount(@$methodRelation->rate) : old('rate') }}" required>
                                                <span class="input-group-text currencySymbol">{{ @$methodRelation->currency }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="custom--card">
                        <div class="card-header">
                            <h3 class="title">@lang('Limit')</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3 col-sm-4"><label class="col-form--label required">@lang('Minimum Amount')</label></div>
                                        <div class="col-xxl-9 col-sm-8">
                                            <div class="input--group">
                                                <input type="number" step="any" min="0" class="form--control" name="min_amount" value="{{ $method ? getAmount(@$methodRelation->min_amount) : old('min_amount') }}" required>
                                                <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3 col-sm-4"><label class="col-form--label required">@lang('Maximum Amount')</label></div>
                                        <div class="col-xxl-9 col-sm-8">
                                            <div class="input--group">
                                                <input type="number" step="any" min="0" class="form--control" name="max_amount" value="{{ $method ? getAmount(@$methodRelation->max_amount) : old('max_amount') }}" required>
                                                <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="custom--card">
                        <div class="card-header">
                            <h3 class="title">@lang('Charges')</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="row align-items-center gy-2">
                                        <div class="col-xxl-3 col-sm-4"><label class="col-form--label required">@lang('Fixed Charge')</label></div>
                                        <div class="col-xxl-9 col-sm-8">
                                            <div class="input--group">
                                                <input type="number" step="any" min="0" class="form--control" name="fixed_charge" value="{{ $method ? getAmount(@$methodRelation->fixed_charge) : old('fixed_charge') }}" required>
                                                <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="row align-items-center gy-2">
                                            <div class="col-xxl-3 col-sm-4"><label class="col-form--label required">@lang('Percent Charge')</label></div>
                                            <div class="col-xxl-9 col-sm-8">
                                                <div class="input--group">
                                                    <input type="number" step="0.01" min="0" class="form--control" name="percent_charge" value="{{ $method ? getAmount(@$methodRelation->percent_charge) : old('percent_charge') }}" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                    </div>
                                </div>
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
                                            $selectedCountries = $method ? ($method->countries ?? []) : [];
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
                
                <div class="col-12">
                    <div class="custom--card">
                        <div class="card-header">
                            <h3 class="title">@lang('Guidelines')</h3>
                        </div>
                        <div class="card-body editor-wrapper">
                            <textarea class="form--control trumEdit" name="guideline">{{ @$method->guideline  }}</textarea>
                        </div>
                    </div>
                </div>

                @include('admin.partials.formData', [$formHeading])

                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn--base px-4" type="submit">@lang('Submit')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <x-formGenerator />
@endsection

@push('breadcrumb')
    <a href="{{ $backRoute }}" class="btn btn--sm btn--base"><i class="ti ti-circle-arrow-left"></i> @lang('Back')</a>
@endpush

@push('page-script-lib')
    <script src="{{asset('assets/admin/js/page/ckEditor.js')}}"></script>
@endpush

@push('page-script')
    <script>
        (function ($) {
            "use strict";

            $('[name=currency]').on('input', function () {
                $('.currencySymbol').text($(this).val());
            });

            @if(old('currency'))
                $('[name=currency]').trigger('input');
            @endif

            if ($(".trumEdit")[0]) {
                $('.editor-wrapper').find('.ck-editor').remove();
                window.editors = {};
                document.querySelectorAll('.trumEdit').forEach((node, index) => {
                    ClassicEditor
                        .create(node)
                        .then(newEditor => {
                            window.editors[index] = newEditor;
                        });
                });
            }
        })(jQuery);
    </script>
@endpush
