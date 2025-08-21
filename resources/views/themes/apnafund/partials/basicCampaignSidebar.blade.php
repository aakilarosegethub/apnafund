<div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
    <h3 class="post-sidebar__card__header">@lang('Time Left')</h3>
    <div class="post-sidebar__card__body">
        <div class="campaign__countdown" data-target-date="{{ showDateTime(@$campaignData->end_date, 'Y-m-d\TH:i:s') }}"></div>

        @php
            $percentage = donationPercentage($campaignData->goal_amount, $campaignData->raised_amount);
        @endphp

        <div class="progress custom--progress my-4" role="progressbar" aria-label="Basic example" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar" style="width: {{ $percentage }}%"><span class="progress-txt">{{ $percentage . '%' }}</span></div>
        </div>
        <ul class="campaign-status">
            <li>
                <span><i class="ti ti-cash-register"></i> @lang('Goal'):</span> {{ $setting->cur_sym . showAmount(@$campaignData->goal_amount) }}
            </li>
            <li>
                <span><i class="ti ti-building-bank"></i> @lang('Raised'):</span> {{ $setting->cur_sym . showAmount(@$campaignData->raised_amount) }}
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
