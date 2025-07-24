@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="maintenance py-120">
        <div class="container">
            <div class="row gy-5 justify-content-center align-items-center">
                <div class="col-lg-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-body maintenance--details">
                            @php echo $maintenance->data_info->details @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .maintenance--details p {
            color: hsl(var(--secondary));
        }
    </style>
@endpush
