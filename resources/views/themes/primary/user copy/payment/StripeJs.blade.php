@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Stripe Storefront')</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ $data->url }}" method="{{ $data->method }}">
                                @csrf
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
                                <script src="{{ $data->src }}" class="stripe-button" @foreach ($data->val as $key => $value) data-{{ $key }}="{{ $value }}" @endforeach></script>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@push('page-script')
    <script>
        (function($) {
            "use strict"

            $('button[type="submit"]').removeClass().addClass("btn btn--base w-100 mt-4").text("Pay Now")
        })(jQuery)
    </script>
@endpush
