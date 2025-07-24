@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="donation pt-120 pb-60">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="post-sidebar">
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Filter By Category')</h3>
                            <div class="post-sidebar__card__body">
                                <ul class="d-flex flex-column gap-2">
                                    <li class="campaign-category" data-category_slug="all">
                                        <a href="#" class="d-flex align-items-center gap-2">
                                            <i class="ti ti-arrow-right"></i> @lang('All')
                                        </a>
                                    </li>

                                    @foreach ($categories as $category)
                                        <li class="campaign-category" data-category_slug="{{ $category->slug }}">
                                            <a href="#" class="d-flex align-items-center gap-2">
                                                <i class="ti ti-arrow-right"></i> {{ __($category->name) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Filter by name')</h3>
                            <div class="post-sidebar__card__body">
                                <div class="input--group">
                                    <input type="text" class="form--control" placeholder="@lang('Campaign Name')" value="{{ request('name') }}" id="campaign-name">
                                    <button class="btn btn--base px-3 search-campaign">
                                        <i class="ti ti-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Filter by date')</h3>
                            <div class="post-sidebar__card__body">
                                <div class="input--group">
                                    <input type="text" class="form--control date-picker" placeholder="@lang('Start Date - End Date')" data-language="en" data-range="true" data-multiple-dates-separator=" - " autocomplete="off" id="date-range" value="{{ request('date_range') }}">
                                    <button class="btn btn--base px-3 filter-by-date">
                                        <i class="ti ti-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row g-4">
                        @forelse ($campaigns as $campaign)
                            <div class="col-sm-6" data-aos="fade-up" data-aos-duration="1500">
                                <div class="campaign-card">
                                    @include($activeTheme . 'partials.basicCampaign')
                                </div>
                            </div>
                        @empty
                            <div class="col-12" data-aos="fade-up" data-aos-duration="1500">
                                <div class="pt-5">
                                    <p class="text-center">{{ __($emptyMessage) }}</p>
                                </div>
                            </div>
                        @endforelse

                        @if ($campaigns->hasPages())
                            <div class="col-12" data-aos="fade-up" data-aos-duration="1500">
                                {{ paginateLinks($campaigns) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('campaign') }}" method="GET" class="d-none search-form">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="name" value="{{ request('name') }}">
        <input type="hidden" name="date_range" value="{{ request('date_range') }}">
    </form>
@endsection

@push('page-style-lib')
    <link rel="stylesheet" href="{{ asset('assets/universal/css/datepicker.css') }}">
@endpush

@push('page-style')
    <style>
        .date-picker {
            caret-color: transparent;
            cursor: pointer;
        }
    </style>
@endpush

@push('page-script-lib')
    <script src="{{ asset('assets/universal/js/datepicker.js') }}"></script>
    <script src="{{ asset('assets/universal/js/datepicker.en.js') }}"></script>
@endpush

@push('page-script')
    <script>
        (function ($) {
            'use strict'

            $('.date-picker').datepicker({
                dateFormat: 'dd-mm-yyyy',
            })

            $('.date-picker').on('input keyup keydown keypress', function() {
                return false
            })

            $('.campaign-category').on('click', function (event) {
                event.preventDefault()

                let slug = $(this).data('category_slug')
                $('input[name="category"]').val(slug)

                $('.search-form').submit()
            })

            $('.search-campaign').on('click', function () {
                let name = $('#campaign-name').val()
                $('input[name="name"]').val(name)

                $('.search-form').submit()
            })

            $('.filter-by-date').on('click', function () {
                let range = $('#date-range').val()
                $('input[name="date_range"]').val(range)

                $('.search-form').submit()
            })
        })(jQuery)
    </script>
@endpush
