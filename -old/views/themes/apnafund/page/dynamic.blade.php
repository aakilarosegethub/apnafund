@php
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dynamic-page py-120">
        <div class="container">
            <div class="row gy-5 justify-content-center align-items-center">
                <div class="col-lg-10">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-body dynamic-page--details">
                            @if(isset($page->data_info['title']))
                                <h1 class="page-title mb-4">{{ __($page->data_info['title']) }}</h1>
                            @endif
                            
                            @php 
                                $details = isset($page->data_info['details']) 
                                    ? $page->data_info['details'] 
                                    : ($page->data_info->details ?? '');
                                echo $details;
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-style')
    <style>
        .dynamic-page--details .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: hsl(var(--black)/0.9);
            margin-bottom: 1.5rem;
        }

        .dynamic-page--details h1, h2, h3, h4, h5, h6 {
            margin-bottom: .5rem;
            color: hsl(var(--black)/0.6);
            font-weight: 600;
        }

        .dynamic-page--details p {
            color: hsl(var(--secondary));
        }

        .dynamic-page--details p:not(:last-child) {
            margin-bottom: 15px;
        }

        .dynamic-page--details img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
@endpush

