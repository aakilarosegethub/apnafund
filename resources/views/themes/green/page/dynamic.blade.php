@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')
    <div class="dynamic-page" style="padding: 80px 0; background: #f9fafb;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card" style="border: none; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.06);">
                        <div class="card-body dynamic-page--details" style="padding: 40px;">
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
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
        }

        .dynamic-page--details h1, h2, h3, h4, h5, h6 {
            margin-bottom: .5rem;
            color: #111827;
            font-weight: 600;
        }

        .dynamic-page--details p {
            color: #6b7280;
            line-height: 1.7;
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

