@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Authorize.Net')</h3>
                        </div>
                        <div class="card-body">
                            <div class="card-wrapper mb-4"></div>
                            <form action="{{ $data->url }}" method="{{ $data->method }}" class="row g-3" id="payment-form">
                                @csrf
                                <input type="hidden" value="{{ $data->track }}" name="track">
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Name')</label>
                                    <div class="input--group">
                                        <input type="text" class="form--control" name="name" value="{{ old('name') }}" required autocomplete="off">
                                        <span class="input-group-text"><i class="ti ti-user-star"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Card Number')</label>
                                    <div class="input--group">
                                        <input type="text" class="form--control" name="cardNumber" value="{{ old('cardNumber') }}" required autocomplete="off">
                                        <span class="input-group-text"><i class="ti ti-credit-card-pay"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Expire Date')</label>
                                    <div class="input--group">
                                        <input type="text" class="form--control" name="cardExpiry" value="{{ old('cardExpiry') }}" required autocomplete="off">
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('CVC Code')</label>
                                    <div class="input--group">
                                        <input type="text" class="form--control" name="cardCVC" value="{{ old('cardCVC') }}" required autocomplete="off">
                                        <span class="input-group-text"><i class="ti ti-list-letters"></i></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn--base w-100 mt-2">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script-lib')
    <script src="{{ asset('assets/universal/js/card.js') }}"></script>
@endpush

@push('page-script')
    <script>
        (function($) {
            "use strict";

            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="cardNumber"]',
                    expiryInput: 'input[name="cardExpiry"]',
                    cvcInput: 'input[name="cardCVC"]',
                    nameInput: 'input[name="name"]'
                }
            });
        })(jQuery);
    </script>
@endpush
