@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Payment Preview')</h3>
                        </div>
                        <div class="card-body text-center">
                            <p class="fw-bold payment-preview-text">
                                @lang('Please send exactly') <span class="text--base">{{ $data->amount . ' ' . __($data->currency) }}</span> @lang('to') <span class="text--base">{{ $data->sendTo }}</span>
                            </p>
                            <img src="{{ $data->img }}" alt="@lang('QR Code')" class="mt-3">
                            <p class="fw-bold payment-preview-text mt-3">@lang('Scan To Send')</p>
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
