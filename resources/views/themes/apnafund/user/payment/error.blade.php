{{-- Payment Error Page - Mobile/Webview friendly. Flutter app detects payment_status=error in URL. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex">
    <title>{{ $pageTitle ?? __('Payment Failed') }} - {{ bs('site_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(180deg, #fef2f2 0%, #fff 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-tap-highlight-color: transparent;
        }
        .error-card {
            max-width: 400px;
            width: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            text-align: center;
        }
        .error-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 40px 24px 32px;
            color: #fff;
        }
        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-icon i { font-size: 2.5rem; }
        .error-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
        .error-body { padding: 28px 24px; }
        .error-body p {
            color: #6b7280;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .btn-close-webview {
            display: inline-block;
            padding: 14px 32px;
            background: #dc2626;
            color: #fff !important;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .btn-close-webview:hover { background: #b91c1c; color: #fff; }
        .btn-try-again {
            display: inline-block;
            padding: 12px 24px;
            background: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-weight: 500;
            text-decoration: none;
            margin-top: 12px;
            font-size: 0.95rem;
        }
        .btn-try-again:hover { background: #f9fafb; color: #374151; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-header">
            <div class="error-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1>{{ $pageTitle ?? __('Payment Failed') }}</h1>
            <p class="mb-0 opacity-90" style="font-size: 0.95rem;">{{ __('Something went wrong') }}</p>
        </div>
        <div class="error-body">
            <p>{{ $message }}</p>
            <a href="javascript:void(0)" id="btnClose" class="btn-close-webview">{{ __('Close') }}</a>
            <br>
            <a href="{{ route('campaign') }}" class="btn-try-again">{{ __('Browse Campaigns') }}</a>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script>
        (function() {
            var toastMsg = {!! json_encode($message ?? __('Payment could not be completed. Please try again.')) !!};
            var attempts = 0;
            function showToast() {
                if (typeof iziToast !== 'undefined') {
                    iziToast.error({ message: toastMsg, position: "topRight", timeout: 6000 });
                    return;
                }
                attempts++;
                if (attempts < 100) {
                    setTimeout(showToast, 50);
                } else {
                    alert(toastMsg);
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showToast);
            } else {
                showToast();
            }
        })();
        document.getElementById('btnClose').addEventListener('click', function() {
            if (window.opener) {
                window.close();
            } else if (window.parent !== window) {
                try { window.parent.postMessage({ type: 'payment_status', status: 'error' }, '*'); } catch(e) {}
                window.close();
            } else {
                window.history.back();
            }
        });
    </script>
</body>
</html>
