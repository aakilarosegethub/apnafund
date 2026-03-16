<link rel="stylesheet" href="{{ asset('assets/universal/css/iziToast.min.css') }}">
<script src="{{ asset('assets/universal/js/iziToast.min.js') }}"></script>

@if(session()->has('toasts'))
    @foreach(session('toasts') as $msg)
        <script>
            "use strict";
            (function(){ if(typeof iziToast!=='undefined') iziToast.{{ $msg[0] }}({message: {!! json_encode(__($msg[1])) !!}, position: "topRight"}); else setTimeout(arguments.callee, 50); })();
        </script>
    @endforeach
@elseif(request('payment_status') === 'error')
    {{-- Fallback: show payment error when redirected from failed payment (desktop / campaigns page) --}}
    <script>
        "use strict";
        (function showPaymentErrorToast() {
            if (typeof iziToast !== 'undefined') {
                iziToast.error({message: {!! json_encode(__('Payment could not be completed. Please try again.')) !!}, position: "topRight", timeout: 5000});
            } else {
                setTimeout(showPaymentErrorToast, 50);
            }
        })();
    </script>
@endif


@if (isset($errors) && $errors->any())
    @php
        $collection = collect($errors->all());
        $errors     = $collection->unique();
    @endphp

    <script>
        "use strict";
        @foreach ($errors as $error)
            iziToast.error({
                message: '{{ __($error) }}',
                position: "topRight"
            });
        @endforeach
    </script>

@endif
<script>
    "use strict";
    function showToasts(status,message) {
        if (typeof message == 'string') {
            iziToast[status]({
                message: message,
                position: "topRight"
            });
        } else {
            $.each(message, function(i, val) {
                iziToast[status]({
                    message: val,
                    position: "topRight"
                });
            });
        }
    }
</script>
