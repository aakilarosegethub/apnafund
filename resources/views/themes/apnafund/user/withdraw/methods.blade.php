@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card custom--card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-end">
                            <h3 class="title">@lang('Withdraw Balance')</h3>
                            <span class="d-block"><small>@lang('Your Current Balance Is'): {{ showAmount(auth()->user()->balance) . ' ' . __($setting->site_cur) }}</small></span>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" class="row g-4">
                                @csrf
                                <div class="col-12">
                                    <label class="form--label required">@lang('Withdraw Methods')</label>
                                    <select class="form--control form-select select-2" name="method_id" required>
                                        <option selected disabled>@lang('Select Method')</option>

                                        @foreach ($methods as $data)
                                            <option value="{{ $data->id }}" @selected(old('method_id') == @$data->id) data-resource="{{ $data }}">
                                                {{ __(@$data->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form--label required">@lang('Amount')</label>
                                    <div class="input--group">
                                        <input type="number" step="any" min="0" class="form--control" name="amount" value="{{ old('amount') }}" required>
                                        <span class="input-group-text">{{ __($setting->site_cur) }}</span>
                                    </div>
                                </div>
                                <div class="mt-4 preview-details d-none">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>@lang('Limit')</span>
                                            <span><span class="min fw-bold">0</span> {{ __($setting->site_cur) }} - <span class="max fw-bold">0</span> {{ __($setting->site_cur) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>@lang('Charge')</span>
                                            <span><span class="charge fw-bold">0</span> {{ __($setting->site_cur) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>@lang('Receivable')</span>
                                            <span><span class="receivable fw-bold">0</span> {{ __($setting->site_cur) }}</span>
                                        </li>
                                        <li class="list-group-item justify-content-between d-none rate-element"></li>
                                        <li class="list-group-item justify-content-between d-none in-site-cur">
                                            <span>@lang('In') <span class="base-currency"></span></span>
                                            <span class="final_amount fw-bold">0</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset($activeThemeTrue . 'css/select2.css') }}">
@endpush

@push('page-style')
    <style>
        .select2-results__option {
            padding-left: 16px;
        }
    </style>
@endpush

@push('page-script-lib')
    <script src="{{ asset($activeThemeTrue . 'js/select2.js') }}"></script>
@endpush

@push('page-script')
    <script type="text/javascript">
        (function($) {
            'use strict'

            $('.select-2').select2({
                containerCssClass: ':all:',
            })

            $('[name=method_id]').change(function() {
                if (!$('[name=method_id]').val()) {
                    $('.preview-details').addClass('d-none')

                    return false
                }

                var resource       = $('[name=method_id] option:selected').data('resource')
                var fixed_charge   = parseFloat(resource.fixed_charge)
                var percent_charge = parseFloat(resource.percent_charge)
                var rate           = parseFloat(resource.rate)
                var toFixedDigit   = 2

                $('.min').text(parseFloat(resource.min_amount).toFixed(2))
                $('.max').text(parseFloat(resource.max_amount).toFixed(2))

                var amount = parseFloat($('[name=amount]').val())

                if (!amount) amount = 0

                if (amount <= 0) {
                    $('.preview-details').addClass('d-none')

                    return false
                }

                $('.preview-details').removeClass('d-none')

                var charge = parseFloat(fixed_charge + ((amount * percent_charge) / 100)).toFixed(2)
                $('.charge').text(charge)

                if (resource.currency != '{{ $setting->site_cur }}') {
                    var rateElement = `<span>@lang('Conversion Rate')</span> <span class="fw-bold">1 {{ __($setting->site_cur) }} = <span class="rate">${rate}</span> <span class="base-currency">${resource.currency}</span></span>`;

                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none')
                    $('.rate-element').addClass('d-flex')
                    $('.in-site-cur').removeClass('d-none')
                    $('.in-site-cur').addClass('d-flex')
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none')
                    $('.rate-element').removeClass('d-flex')
                    $('.in-site-cur').addClass('d-none')
                    $('.in-site-cur').removeClass('d-flex')
                }

                var receivable = parseFloat(parseFloat(amount) - parseFloat(charge)).toFixed(2)
                $('.receivable').text(receivable)

                var final_amount = parseFloat(parseFloat(receivable) * rate).toFixed(toFixedDigit)

                $('.final_amount').text(final_amount)
                $('.base-currency').text(resource.currency)
            });

            $('[name=amount]').on('input', function() {
                $('[name=method_id]').change()
            })
        })(jQuery)
    </script>
@endpush
