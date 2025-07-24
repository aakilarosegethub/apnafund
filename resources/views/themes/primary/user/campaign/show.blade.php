@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="donation-details pt-120 pb-60">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    @include($activeTheme . 'partials.basicCampaignShow')
                </div>
                <div class="col-lg-4">
                    <div class="post-sidebar">
                        @include($activeTheme . 'partials.basicCampaignSidebar')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .donation-details__comments {
            border-bottom: none;
        }
    </style>
@endpush
