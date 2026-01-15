@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="cookie-policy py-120">
        <div class="container">
            <div class="row gy-5 justify-content-center align-items-center">
                <div class="col-lg-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-body cookie--details">
                            @php echo $cookie->data_info->details @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .cookie--details p {
            color: hsl(var(--secondary));
        }
    </style>
@endpush
