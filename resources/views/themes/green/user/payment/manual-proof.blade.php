@extends($activeTheme . 'layouts.frontend')

@section('frontend')
@php $setting = bs(); @endphp
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card custom--card manual-instructions-card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Submit payment proof') — {{ __(@$gateway->name) }}</h3>
                        </div>
                        <div class="card-body">
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

                            <p class="text-muted small mb-4">@lang('Attach a screenshot or PDF of your transfer receipt. Admin will review it under Pending donations.')</p>

                            <form action="{{ route('user.deposit.manual.proof.submit') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-3">
                                @csrf
                                <input type="hidden" name="trx" value="{{ $deposit->trx }}">

                                <div>
                                    <label class="form-label fw-semibold">@lang('Payment proof') <span class="text-danger">*</span></label>
                                    <input type="file" name="payment_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.webp" required>
                                    <small class="text-muted d-block mt-1">@lang('Accepted: JPG, PNG, PDF, WebP. Max size: 5 MB.')</small>
                                    @error('payment_proof')
                                        <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label fw-semibold">@lang('Note') <span class="text-muted fw-normal">(@lang('optional'))</span></label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="@lang('Reference number, bank name, etc.')">{{ old('note') }}</textarea>
                                    @error('note')
                                        <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2 pt-2">
                                    <button type="submit" class="btn btn--base btn-lg w-100">@lang('Submit proof')</button>
                                    <a href="{{ route('user.deposit.manual.instructions') }}" class="btn btn-outline-secondary w-100">@lang('Back to instructions')</a>
                                </div>
                            </form>
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
            margin:140px 0px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,.08);
        }
        .manual-instructions-card .manual-guideline-body { font-size: 0.95rem; line-height: 1.6; }
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

@section('script')
<script>
(function () {
    var input = document.querySelector('input[name="payment_proof"]');
    var form = input && input.closest('form');
    var maxBytes = 5 * 1024 * 1024;
    var msg = @json(__('The payment proof file must not be larger than 5 MB.'));
    function enforce() {
        var f = input.files && input.files[0];
        if (f && f.size > maxBytes) {
            alert(msg);
            input.value = '';
            return false;
        }
        return true;
    }
    if (input) input.addEventListener('change', enforce);
    if (form) form.addEventListener('submit', function (e) {
        if (!enforce()) e.preventDefault();
    });
})();
</script>
@endsection
