@php
    $activeTheme = activeTheme();
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')
<div class="order-success-page py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="thankyou-card rounded-4 shadow-sm border-0 overflow-hidden" style="background: #fff; box-shadow: 0 10px 40px rgba(0,0,0,.08);">
                    {{-- Success Header --}}
                    <div class="thankyou-header text-center py-5 px-4" style="background: linear-gradient(135deg, #05ce78 0%, #04b367 100%);">
                        <div class="success-icon mb-3" style="width: 80px; height: 80px; margin: 0 auto; background: rgba(255,255,255,.25); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check text-white" style="font-size: 2.5rem;"></i>
                        </div>
                        <h1 class="text-white fw-bold mb-2" style="font-size: clamp(1.5rem, 4vw, 2rem);">@lang('Thank You!')</h1>
                        <p class="text-white mb-0" style="opacity: .95; font-size: 1.05rem;">
                            @if(isset($type) && $type === 'donation')
                                @lang('Your contribution was successful.')
                            @else
                                @lang('Your order was placed successfully.')
                            @endif
                        </p>
                    </div>

                    {{-- Order Details --}}
                    <div class="thankyou-body p-4 p-md-5">
                        @if(isset($deposit) && $deposit)
                            <h5 class="fw-bold mb-4" style="color: #111827;">@lang('Contribution Details')</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted py-2" style="width: 40%;">@lang('Transaction ID')</td>
                                            <td class="fw-medium py-2">{{ $deposit->trx }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-2">@lang('Amount')</td>
                                            <td class="fw-medium py-2">{{ showAmount($deposit->amount) }} {{ $deposit->method_currency }}</td>
                                        </tr>
                                        @if($deposit->campaign)
                                        <tr>
                                            <td class="text-muted py-2">@lang('Campaign')</td>
                                            <td class="fw-medium py-2">
                                                <a href="{{ route('campaign.show', $deposit->campaign->slug) }}" class="text-decoration-none" style="color: #05ce78;">{{ $deposit->campaign->name }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($deposit->gatewayCurrency())
                                        <tr>
                                            <td class="text-muted py-2">@lang('Payment Method')</td>
                                            <td class="fw-medium py-2">{{ $deposit->gatewayCurrency()->name ?? '-' }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-muted py-2">@lang('Date')</td>
                                            <td class="fw-medium py-2">{{ $deposit->created_at ? $deposit->created_at->format('M d, Y h:i A') : '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @elseif(isset($order) && $order)
                            <h5 class="fw-bold mb-4" style="color: #111827;">@lang('Order Details')</h5>
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted py-2" style="width: 40%;">@lang('Order No')</td>
                                            <td class="fw-medium py-2">{{ $order->order_no ?? $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-2">@lang('Amount')</td>
                                            <td class="fw-medium py-2">{{ bs('cur_sym') ?? '$' }}{{ showAmount($order->amount ?? 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-2">@lang('Customer')</td>
                                            <td class="fw-medium py-2">{{ $order->customer_name ?? $order->customer_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-2">@lang('Email')</td>
                                            <td class="fw-medium py-2">{{ $order->email ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-5 pt-3 d-flex flex-wrap gap-3 justify-content-center">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 fw-semibold rounded-pill">
                                <i class="fas fa-home me-2"></i> @lang('Back to Home')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
