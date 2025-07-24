@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="success-details pt-120 pb-60">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="success-details__img">
                        <img src="{{ getImage('assets/images/site/success_story/' . @$storyData->data_info->image, '855x475') }}" alt="image">
                    </div>
                    <div class="success-details__txt">
                        <h2 class="success-details__title">{{ __(@$storyData->data_info->title) }}</h2>
                        <p class="success-details__desc">
                            @php echo @$storyData->data_info->details @endphp
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="post-sidebar">
                        <div class="post-sidebar__card">
                            <h3 class="post-sidebar__card__header">@lang('Share This Story')</h3>
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
                                        <a href="https://pinterest.com/pin/create/bookmarklet/?media={{ $seoContents['image'] }}&url={{ urlencode(url()->current()) }}&is_video=[is_video]&description={{ @$storyData->data_info->title }}" class="social-list__link flex-center" target="_blank">
                                            <i class="ti ti-brand-pinterest"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="post-sidebar__card">
                            <h3 class="post-sidebar__card__header">@lang('More Stories')</h3>
                            <div class="post-sidebar__card__body">
                                @if (count($moreStories))
                                    <ul class="post-sidebar__recent-post">
                                        @foreach ($moreStories as $moreStory)
                                            <li>
                                                <a href="{{ route('stories.show', @$moreStory->id) }}" class="post-sidebar__recent-post__link">
                                                    <span class="post-sidebar__recent-post__thumb">
                                                        <img src="{{ getImage('assets/images/site/success_story/thumb_' . @$moreStory->data_info->image, '415x230') }}" alt="image">
                                                    </span>
                                                    <span class="post-sidebar__recent-post__txt">
                                                        {{ __(strLimit(@$moreStory->data_info->title, 45)) }}
                                                    </span>
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
