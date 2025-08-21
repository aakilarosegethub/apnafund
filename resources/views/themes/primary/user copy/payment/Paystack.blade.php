@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Paystack')</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('ipn.' . $deposit->gateway->alias) }}" method="POST">
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
                                <button type="button" class="btn btn--base w-100 mt-4" id="btn-confirm">
                                    @lang('Pay Now')
                                </button>
                                <script src="//js.paystack.co/v1/inline.js" data-key="{{ $data->key }}" data-email="{{ $data->email }}" data-amount="{{ round($data->amount) }}" data-currency="{{ $data->currency }}" data-ref="{{ $data->ref }}" data-custom-button="btn-confirm"></script>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
