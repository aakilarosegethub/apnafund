@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="policy py-120">
        <div class="container">
            <div class="row gy-5 justify-content-center align-items-center">
                <div class="col-lg-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-body policy--details">
                            @php echo $policy->data_info->details @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .policy--details h1, h2, h3, h4, h5, h6 {
            margin-bottom: .5rem;
            color: hsl(var(--black)/0.6);
            font-weight: 600;
        }

        .policy--details p {
            color: hsl(var(--secondary));
        }

        .policy--details p:not(:last-child) {
            margin-bottom: 15px;
        }
    </style>
@endpush
