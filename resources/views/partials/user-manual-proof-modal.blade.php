{{-- Multipart proof upload: used on user transactions & payments (same page, no instructions redirect for logged-in owner). --}}
<div class="modal fade custom--modal" id="manualProofUploadModal" tabindex="-1" aria-labelledby="manualProofUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('user.deposit.manual.proof.submit') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="manualProofUploadModalLabel">@lang('Submit payment proof')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="trx" id="manualProofModalTrx" value="">
                    <p class="small text-muted mb-3">
                        @lang('Transaction'): <strong id="manualProofModalTrxLabel">—</strong>
                    </p>
                    <div class="mb-3">
                        <label class="form--label" for="manualProofModalFile">@lang('Payment proof') (@lang('image or PDF'))</label>
                        <input type="file" name="payment_proof" id="manualProofModalFile" class="form--control" accept=".jpeg,.jpg,.png,.pdf,.webp" required>
                    </div>
                    <div class="mb-0">
                        <label class="form--label" for="manualProofModalNote">@lang('Note') <span class="text-muted">(@lang('optional'))</span></label>
                        <textarea name="note" id="manualProofModalNote" class="form--control" rows="3" maxlength="1000" placeholder="@lang('Optional note for admin')"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--sm btn--secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--sm btn--base">
                        <i class="ti ti-upload"></i> @lang('Submit')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
