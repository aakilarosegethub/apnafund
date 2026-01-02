@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Flutterwave')</h3>
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
                            <button type="button" class="btn btn--base w-100 mt-4" onClick="payWithRave()">
                                @lang('Pay Now')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script-lib')
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
@endpush

@push('page-script')
    <script>
        "use strict"

        function payWithRave() {
            getpaidSetup({
                PBFPubKey: "{{ $data->API_publicKey }}",
                customer_email: "{{ $data->customer_email }}",
                amount: "{{ $data->amount }}",
                customer_phone: "{{ $data->customer_phone }}",
                currency: "{{ $data->currency }}",
                txref: "{{ $data->txref }}",
                onclose: function() {},
                callback: function(response) {
                    var txRef = response.tx.txRef;
                    var status = response.tx.status;
                    var chargeResponse = response.tx.chargeResponseCode;

                    window.location = "{{ url('ipn/flutterwave') }}/" + txRef + "/" + status;
                }
            })
        }
    </script>
@endpush
