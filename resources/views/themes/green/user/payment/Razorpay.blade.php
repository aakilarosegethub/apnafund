@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Razorpay')</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('You have to pay'):</span>
                                    <span class="fw-bold">{{ showAmount($deposit->final_amount) . ' ' . __($deposit->method_currency) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>@lang('Your donation amount will be'):</span>
                                    <span class="fw-bold">{{ showAmount($deposit->amount) . ' ' . __($setting->site_cur) }}</span>
                                </li>
                            </ul>
                            <form action="{{ $data->url }}" method="{{ $data->method }}">
                                @csrf
                                <input type="hidden" custom="{{ $data->custom }}" name="trx">
                                <script src="{{ $data->checkout_js }}" @foreach ($data->val as $key => $value) data-{{ $key }}="{{ $value }}" @endforeach></script>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        (function($) {
            "use strict"

            $('input[type="submit"]').addClass("btn btn--base w-100 mt-3");
        })(jQuery)
    </script>
@endpush
