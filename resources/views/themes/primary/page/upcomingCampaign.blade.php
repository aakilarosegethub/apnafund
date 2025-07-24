@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="event py-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="event__list">
                        @forelse ($upcomingCampaigns as $upcomingCampaign)
                            <div class="event__card" data-aos="fade-up" data-aos-duration="1500">
                                <div class="event__img">
                                    <a href="{{ route('upcoming.show', $upcomingCampaign->slug) }}">
                                        <img src="{{ getImage(getFilePath('campaign') . '/' . @$upcomingCampaign->image, getThumbSize('campaign')) }}" alt="image">
                                    </a>
                                    <div class="event__date">
                                        <span>{{ @$upcomingCampaign->start_date->format('d') }}</span> {{ __(@$upcomingCampaign->start_date->format('F')) }}
                                    </div>
                                </div>
                                <div class="event__txt">
                                    <a href="{{ route('upcoming.show', $upcomingCampaign->slug) }}" class="event__title">{{ __(strLimit(@$upcomingCampaign->name, 60)) }}</a>
                                    <p class="event__description">{{ __(strLimit(strip_tags(@$upcomingCampaign->description), 150)) }}</p>
                                    <a href="{{ route('upcoming.show', $upcomingCampaign->slug) }}" class="btn btn--sm btn--base">
                                        @lang('Explore')
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="pt-5">
                                <p class="text-center" data-aos="fade-up" data-aos-duration="1500">{{ __($emptyMessage) }}</p>
                            </div>
                        @endforelse

                        @if ($upcomingCampaigns->hasPages())
                            <div class="col-12" data-aos="fade-up" data-aos-duration="1500">
                                {{ paginateLinks($upcomingCampaigns) }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="post-sidebar">
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Filter by category')</h3>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('upcoming') }}" method="GET" class="d-none search-form">
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="name" value="{{ request('name') }}">
    </form>
@endsection

@push('page-script')
    <script>
        (function ($) {
            'use strict'

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
        })(jQuery)
    </script>
@endpush
