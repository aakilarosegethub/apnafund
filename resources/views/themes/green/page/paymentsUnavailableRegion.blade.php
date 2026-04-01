@extends($activeTheme . 'layouts.frontend')

@section('style')
    <style>
        .payments-unavailable-wrap {
            min-height: 55vh;
            padding-top: 13rem !important;
            padding-bottom: 3rem !important;
        }
        .payments-unavailable-card {
            border: 1px solid #e8ecea !important;
        }
    </style>
@endsection

@section('frontend')
    <div class="payments-unavailable-wrap py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9 text-center">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 payments-unavailable-card">
                        <div class="mb-4 text-muted">
                            <i class="fas fa-ban fa-3x" aria-hidden="true"></i>
                        </div>
                        <h1 class="h3 fw-bold mb-3">@lang('Not functional in your region.')</h1>
                        <p class="text-muted mb-4 lh-lg">
                            @lang('Contributions are not available for your current currency and region. You can try changing country or display currency from the site footer, or return to the homepage.')
                        </p>
                        <a href="{{ route('home') }}" class="btn btn--base px-4">@lang('Back to home')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
