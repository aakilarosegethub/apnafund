@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="kyc py-120">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">@lang('KYC Form')</h2>
                    </div>
                </div>
            </div>
            <div class="row gy-5 justify-content-lg-around justify-content-center align-items-center">
                <div class="col-lg-6 col-md-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">@lang('Please provide the following information')</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" class="row g-3" enctype="multipart/form-data">
                                @csrf

                                <x-phinixForm identifier="act" identifierValue="kyc" />

                                <div class="col-12">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
