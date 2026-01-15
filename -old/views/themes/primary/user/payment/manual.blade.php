@extends($activeTheme . 'layouts.frontend')

@section('frontend')

    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-7 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Donation via') {{ __(@$gateway->name) }}</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.deposit.manual.update') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                                @csrf
                                <div class="text-center">
                                    <p class="fw-bold payment-preview-text">
                                        @lang('You have requested a donation of') <span class="text--base">{{ showAmount(@$deposit['amount']) . ' ' . __(@$setting->site_cur) }}</span>, @lang('Please pay') <span class="text--base">{{ showAmount(@$deposit['final_amount']) . ' ' . @$deposit['method_currency'] }}</span> @lang('for the successful payment.')
                                    </p>
                                    <h5 class="payment-preview-text mt-4 mb-1">@lang('Please follow the instruction below')</h5>
                                </div>

                                @php echo @$gateway->guideline @endphp

                                <x-phinix-form identifier="id" identifierValue="{{ @$gateway->form_id }}" />

                                <div class="col-12">
                                    <button type="submit" class="btn btn--base w-100 mt-2">@lang('Pay Now')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .payment-preview-text {
            color: hsl(var(--black) / 0.6);
        }
    </style>
@endpush
