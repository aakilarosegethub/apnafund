@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="event-details py-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    @include($activeTheme . 'partials.basicCampaignShow')
                </div>
                <div class="col-lg-4">
                    <div class="post-sidebar">
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Start In')</h3>
                            <div class="post-sidebar__card__body">
                                <div class="campaign__countdown" data-target-date="{{ showDateTime(@$campaignData->start_date, 'Y-m-d\TH:i:s') }}"></div>
                                <ul class="campaign-status mt-4">
                                    <li>
                                        <span><i class="ti ti-cash-register"></i> @lang('Goal'):</span> {{ $setting->cur_sym . showAmount(@$campaignData->goal_amount) }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('Share This Campaign')</h3>
                            <div class="post-sidebar__card__body">
                                <div class="input--group mb-4">
                                    <input type="text" class="form--control" id="shareLink" readonly>
                                    <span class="badge bg--success share-link__badge">@lang('Copied')</span>
                                    <button class="btn btn--base share-link__copy px-3">
                                        <i class="ti ti-copy"></i>
                                    </button>
                                </div>
                                <ul class="social-list social-list-2">
                                    <li class="social-list__item">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" class="social-list__link flex-center" target="_blank">
                                            <i class="ti ti-brand-facebook"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="https://twitter.com/intent/tweet?text=my share text&amp;url={{ urlencode(url()->current()) }}" class="social-list__link flex-center" target="_blank">
                                            <i class="ti ti-brand-x"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="http://www.linkedin.com/shareArticle?url={{ urlencode(url()->current()) }}" class="social-list__link flex-center" target="_blank">
                                            <i class="ti ti-brand-linkedin"></i>
                                        </a>
                                    </li>
                                    <li class="social-list__item">
                                        <a href="https://pinterest.com/pin/create/bookmarklet/?media={{ $seoContents['image'] }}&url={{ urlencode(url()->current()) }}&is_video=[is_video]&description={{ @$campaignData->name }}" class="social-list__link flex-center" target="_blank">
                                            <i class="ti ti-brand-pinterest"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
                            <h3 class="post-sidebar__card__header">@lang('More Upcoming')</h3>
                            <div class="post-sidebar__card__body">
                                @if (count($moreUpcomingCampaigns))
                                    <ul class="post-sidebar__recent-event">
                                        @foreach ($moreUpcomingCampaigns as $moreUpcomingCampaign)
                                            <li>
                                                <a href="{{ route('upcoming.show', $moreUpcomingCampaign->slug) }}" class="post-sidebar__recent-event__link">
                                                    <span class="post-sidebar__recent-event__thumb">
                                                        <img src="{{ getImage(getFilePath('campaign') . '/' . @$moreUpcomingCampaign->image, getThumbSize('campaign')) }}" alt="image">
                                                    </span>
                                                    <div class="post-sidebar__recent-event__txt">
                                                        <span class="post-sidebar__recent-event__title">{{ __(strLimit(@$moreUpcomingCampaign->name, 30)) }}</span>
                                                        <span class="post-sidebar__recent-event__date">{{ showDateTime(@$moreUpcomingCampaign->start_date, 'd M, Y') }}</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-center">{{ __($emptyMessage) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
