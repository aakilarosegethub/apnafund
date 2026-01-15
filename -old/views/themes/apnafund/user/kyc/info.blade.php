@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="kyc py-120">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">@lang('KYC Information')</h2>
                    </div>
                </div>
            </div>
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Your provided information')</h3>
                        </div>
                        <div class="card-body">
                            @if ($user->kyc_data)
                                <ul class="list-group">
                                    @foreach ($user->kyc_data as $val)
                                        @continue(!$val->value)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ __($val->name) }}
                                            <span>
                                                @if ($val->type == 'checkbox')
                                                    {{ implode(',', $val->value) }}
                                                @elseif ($val->type == 'file')
                                                    <a href="{{ route('user.file.download') }}?filePath=verify&fileName={{ $val->value }}">
                                                        <i class="ti ti-circle-arrow-down"></i> @lang('Attachment')
                                                    </a>
                                                @else
                                                    <p>{{ __($val->value) }}</p>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <h5 class="text-center">@lang('KYC data not found')</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
