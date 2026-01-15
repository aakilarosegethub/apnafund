<div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
    <h3 class="post-sidebar__card__header">@lang('Support This Campaign')</h3>
    <div class="post-sidebar__card__body">
        <div class="text-center mb-3">
            <p class="text-muted">@lang('Make a difference today')</p>
        </div>
        <a href="{{ route('campaign.donate', $campaignData->slug) }}" class="btn btn--base w-100 btn-lg">
            <i class="ti ti-heart me-2"></i>@lang('Donate Now')
        </a>
        <div class="text-center mt-3">
            <small class="text-muted">@lang('Secure payment via multiple gateways')</small>
        </div>
    </div>
</div>

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

<!-- New Share Button and AddToAny Service -->
<div class="post-sidebar__card" data-aos="fade-up" data-aos-duration="1500">
    <h3 class="post-sidebar__card__header">@lang('Quick Share')</h3>
    <div class="post-sidebar__card__body">
        <button class="btn btn--base w-100 mb-3" id="toggleShareBtn" onclick="toggleAddToAny()">
            <i class="ti ti-share me-2"></i>@lang('Share Campaign')
        </button>
        
        <div id="addToAnyDiv" class="addtoany-div" style="display: none;">
            <div class="text-center mb-3">
                <p class="text-muted small">@lang('Share this campaign on your favorite platforms')</p>
            </div>
            
            <!-- AddToAny Share Buttons -->
            <div class="a2a_kit a2a_kit_size_32 a2a_default_style" data-a2a-url="{{ url()->current() }}" data-a2a-title="{{ $campaignData->name }}" data-a2a-description="{{ Str::limit(strip_tags($campaignData->description), 150) }}">
                <a class="a2a_button_facebook"></a>
                <a class="a2a_button_twitter"></a>
                <a class="a2a_button_whatsapp"></a>
                <a class="a2a_button_telegram"></a>
                <a class="a2a_button_email"></a>
                <a class="a2a_button_copy_link"></a>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-muted">@lang('Powered by AddToAny')</small>
            </div>
        </div>
    </div>
</div>

<style>
.addtoany-div {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.addtoany-div.show {
    display: block !important;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.a2a_kit {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}

.a2a_kit a {
    display: inline-block;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    text-align: center;
    line-height: 32px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.a2a_kit a:hover {
    transform: scale(1.1);
    background: #0056b3;
}

.a2a_button_facebook { background: #1877f2 !important; }
.a2a_button_twitter { background: #1da1f2 !important; }
.a2a_button_whatsapp { background: #25d366 !important; }
.a2a_button_telegram { background: #0088cc !important; }
.a2a_button_email { background: #ea4335 !important; }
.a2a_button_copy_link { background: #6c757d !important; }
</style>

<script>
function toggleAddToAny() {
    const addToAnyDiv = document.getElementById('addToAnyDiv');
    const toggleBtn = document.getElementById('toggleShareBtn');
    
    if (addToAnyDiv.style.display === 'none') {
        addToAnyDiv.style.display = 'block';
        addToAnyDiv.classList.add('show');
        toggleBtn.innerHTML = '<i class="ti ti-chevron-up me-2"></i>@lang("Hide Share Options")';
    } else {
        addToAnyDiv.style.display = 'none';
        addToAnyDiv.classList.remove('show');
        toggleBtn.innerHTML = '<i class="ti ti-share me-2"></i>@lang("Share Campaign")';
    }
}

// Load AddToAny script
(function() {
    var a2a = document.createElement('script');
    a2a.type = 'text/javascript';
    a2a.async = true;
    a2a.src = 'https://static.addtoany.com/menu/page.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(a2a, s);
})();
</script>
