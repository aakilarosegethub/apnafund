@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="py-120">
        <div class="container">
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-7 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Withdraw via') {{ __(@$withdraw->method->name) }}</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" class="row g-3" enctype="multipart/form-data">
                                @csrf
                                <div class="text-center">
                                    <p class="fw-bold withdraw-preview-text">
                                        @lang('You have requested a withdrawal of') <span class="text--base">{{ showAmount(@$withdraw->amount) . ' ' . __($setting->site_cur) }}</span>, @lang('You will get') <span class="text--base">{{ showAmount(@$withdraw->final_amount) . ' ' . @$withdraw->currency }}</span>.
                                    </p>
                                    <h5 class="withdraw-preview-text mt-4 mb-1">@lang('Please follow the instruction below')</h5>
                                </div>

                                @php echo @$withdraw->method->guideline @endphp

                                <x-phinix-form identifier="id" identifierValue="{{ @$withdraw->method->form_id }}" />

                                @if (auth()->user()->ts)
                                    <div class="col-12">
                                        <label class="form--label required">@lang('Google Authenticator Code')</label>
                                        <input type="text" class="form--control" name="authenticator_code" required>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <button type="submit" class="btn btn--base w-100 mt-2">@lang('Submit')</button>
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
        .withdraw-preview-text {
            color: hsl(var(--black) / 0.6);
        }
    </style>
@endpush
