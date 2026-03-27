@extends($activeTheme . 'layouts.frontend')

@section('frontend')
@php $setting = bs(); @endphp
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card custom--card manual-instructions-card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Payment instructions') — {{ __(@$gateway->name) }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success border-0 mb-4" style="background:#e8f8f0;color:#0d4f32;border-radius:10px;">
                                <strong>@lang('Request received')</strong> — @lang('Your manual payment request has been sent to the admin for approval. Read the guidelines below, then continue to upload your payment proof.')
                            </div>

                            @if($deposit->campaign)
                                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                                    <img src="{{ getImage(getFilePath('campaign') . '/' . $deposit->campaign->image, getFileSize('campaign')) }}" alt="" class="rounded" style="width:88px;height:64px;object-fit:cover;">
                                    <div>
                                        <div class="fw-bold">{{ $deposit->campaign->name }}</div>
                                        <small class="text-muted">TRX: {{ $deposit->trx }}</small>
                                    </div>
                                </div>
                            @endif

                            <div class="manual-payment-summary mb-4">
                                <div class="manual-summary-item">
                                    <span class="manual-summary-label">@lang('Selected Amount')</span>
                                    <span class="manual-summary-value">{{ formatPlatformForDisplay($deposit->amount, 2) }} {{ getLocalCurrencyCode() }}</span>
                                </div>
                                <div class="manual-summary-item">
                                    <span class="manual-summary-label">@lang('Platform Fee')</span>
                                    <span class="manual-summary-value">{{ formatPlatformForDisplay($deposit->charge, 2) }} {{ getLocalCurrencyCode() }}</span>
                                </div>
                                <div class="manual-summary-item">
                                    <span class="manual-summary-label">@lang('Final Amount')</span>
                                    <span class="manual-summary-value text--base">{{ formatPlatformForDisplay($deposit->final_amount, 2) }} {{ getLocalCurrencyCode() }}</span>
                                </div>
                            </div>

                            <h5 class="payment-preview-text mb-3">@lang('Admin guidelines')</h5>
                            <div class="manual-guideline-body mb-4">
                                @if(!empty($gateway->guideline))
                                    {!! $gateway->guideline !!}
                                @else
                                    <p class="text-muted">@lang('No additional guidelines were provided for this payment method.')</p>
                                @endif
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('user.deposit.manual.proof', ['trx' => $deposit->trx]) }}" class="btn btn--base btn-lg w-100">@lang('Continue to submit payment proof')</a>
                                <a href="{{ $deposit->campaign ? route('campaign.show', $deposit->campaign->slug) : url('/') }}" class="btn btn-outline-secondary w-100">@lang('Back to campaign')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .manual-instructions-card{
            marhgin:140px 0px;
            padding: 140px 0px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
        }
        .manual-instructions-card .manual-guideline-body { font-size: 0.95rem; line-height: 1.6; }
        .manual-instructions-card .manual-guideline-body img { max-width: 100%; height: auto; }
        .payment-preview-text { color: hsl(var(--black) / 0.6); }
        .manual-payment-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            padding: 16px;
            border-radius: 12px;
            background: hsl(var(--base) / 0.05);
            border: 1px solid hsl(var(--base) / 0.15);
        }
        .manual-summary-item {
            padding: 12px;
            border-radius: 10px;
            background: hsl(var(--white));
            border: 1px solid hsl(var(--black) / 0.08);
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .manual-summary-label {
            font-size: 0.82rem;
            color: hsl(var(--black) / 0.6);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-weight: 600;
        }
        .manual-summary-value {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.4;
            word-break: break-word;
        }
    </style>
@endsection
