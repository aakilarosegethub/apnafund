@php
    $footerContent = getSiteData('footer.content', true);
    $footerElements = getSiteData('footer.element', false, null, true);
    $footerCategories = \App\Models\Admins\FooterCategory::with('category')->where('status', 'active')->orderBy('sort_order')->orderBy('id')->take(8)->get();

    $allowedCountriesForCurrency = getSiteAllowedCountryNames();
    $currentLocalCode = strtoupper(getLocalCurrencyCode());
    $currentLocalSym = getLocalCurrencySymbol();
    $selectedCountryForFooter = resolveFooterCountryForLocalCurrency($allowedCountriesForCurrency);
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<footer class="ks-footer-dark">
    <div class="container py-5">
        <div class="row gy-4">
            <div class="col-lg-3">
                <div class="brand-section">
                    @php
                        $footerLogo = @$footerContent->data_info['footer_logo'] ?? null;
                        if ($footerLogo) {
                            $logoUrl = filter_var($footerLogo, FILTER_VALIDATE_URL) ? $footerLogo : getImage('assets/images/site/footer/' . $footerLogo, '180x40');
                        } else {
                            $logoPath = getFilePath('logoFavicon') . '/logo_light.png';
                            $logoUrl = getImage($logoPath, getFileSize('logoFavicon'));
                        }
                    @endphp
                    @if(empty($allowedCountriesForCurrency))
                        <div class="ks-selector ks-selector--readonly">
                            <span>{{ $currentLocalSym }} {{ $currentLocalCode }}</span>
                        </div>
                    @else
                        <div class="footer-currency-wrap">
                            <label class="visually-hidden" for="footer-currency-country">{{ __('Allowed countries') }}</label>
                            <select
                                id="footer-currency-country"
                                class="ks-selector ks-selector-select"
                                data-url="{{ route('update.user.currency') }}"
                                aria-label="{{ __('Select allowed country') }}"
                            >
                                @foreach($allowedCountriesForCurrency as $countryName)
                                    @php
                                        $cc = getCurrencyCodeForCountryName($countryName);
                                        $sym = \App\Services\CurrencyService::getSymbolForCode($cc);
                                    @endphp
                                    <option
                                        value="{{ $countryName }}"
                                        title="{{ $sym }} {{ $cc }}"
                                        @selected($selectedCountryForFooter === $countryName)
                                    >{{ $countryName }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row">
                    @php
                        $footerMenuItems = \App\Models\SiteData::where('data_key', 'footer_menu.element')->orderBy('id', 'asc')->get();
                        $sections = ['about' => [], 'support' => [], 'more_from_apnacrowdfunding' => [], 'down_section' => []];
                        
                        foreach ($footerMenuItems as $item) {
                            $itemData = (array) $item->data_info;
                            if (($itemData['status'] ?? '1') == '0') continue;
                            $sections[$itemData['section_type'] ?? ''][] = $item;
                        }
                    @endphp

                    <div class="col-md-3 col-6">
                        <h6 class="footer-title">Discover</h6>
                        <ul class="footer-list">
                            @foreach($footerCategories as $cat)
                                @php
                                    $hasCategories = count($cat->getCategoryIdsForFilter()) > 0;
                                    if ($hasCategories) {
                                        $catUrl = route('campaign.category', $cat->slug);
                                    } else {
                                        $catUrl = (str_starts_with($cat->slug ?? '', 'http') || str_starts_with($cat->slug ?? '', '/')) ? $cat->slug : url($cat->slug ?? '#');
                                    }
                                @endphp
                                <li><a href="{{ $catUrl }}">{{ __($cat->label) }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-md-3 col-6">
                        <h6 class="footer-title">About</h6>
                        <ul class="footer-list">
                            @foreach($sections['about'] as $item)
                                @php $slug = trim($item->data_info['slug'] ?? '#'); $url = (str_starts_with($slug, 'http') || str_starts_with($slug, '/')) ? $slug : url($slug); @endphp
                                <li><a href="{{ $url }}">{{ __($item->data_info['menu_label'] ?? '') }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-md-3 col-6">
                        <h6 class="footer-title">Support</h6>
                        <ul class="footer-list">
                            @foreach($sections['support'] as $item)
                                @php $slug = trim($item->data_info['slug'] ?? '#'); $url = (str_starts_with($slug, 'http') || str_starts_with($slug, '/')) ? $slug : url($slug); @endphp
                                <li><a href="{{ $url }}">{{ __($item->data_info['menu_label'] ?? '') }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-md-3 col-6">
                        <h6 class="footer-title">More</h6>
                        <ul class="footer-list">
                            @foreach($sections['more_from_apnacrowdfunding'] as $item)
                                @php $slug = trim($item->data_info['slug'] ?? '#'); $url = (str_starts_with($slug, 'http') || str_starts_with($slug, '/')) ? $slug : url($slug); @endphp
                                <li><a href="{{ $url }}">{{ __($item->data_info['menu_label'] ?? '') }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="ks-huge-branding">
            <img src="{{ $logoUrl }}" alt="Large Logo">
        </div>

        <div class="footer-bottom-flex">
            <div class="social-links">
                @if($footerElements)
                    @foreach ($footerElements as $socialInfo)
                        <a href="{{ $socialInfo->data_info['url'] ?? '#' }}" target="_blank">
                            {!! $socialInfo->data_info['social_icon'] !!}
                        </a>
                    @endforeach
                @endif
            </div>

            <div class="app-badges">
                <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="App Store">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Play Store">
            </div>
        </div>

        <div class="footer-legal-bar">
            <span class="me-3">{{ bs('site_name') }} © {{ date('Y') }}</span>
            
            @foreach($sections['down_section'] as $item)
                @php $slug = trim($item->data_info['slug'] ?? '#'); @endphp
                <a href="{{ url($slug) }}">{{ __($item->data_info['menu_label'] ?? '') }}</a>
            @endforeach
        </div>
    </div>
</footer>

@if(!empty($allowedCountriesForCurrency))
<script>
(function () {
    var el = document.getElementById('footer-currency-country');
    if (!el) return;
    var url = el.getAttribute('data-url');
    var prev = el.value;
    var csrf = '{{ csrf_token() }}';
    el.addEventListener('change', function () {
        var country = this.value;
        el.disabled = true;
        var payload = new URLSearchParams();
        payload.append('country', country);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept': 'application/json, text/plain, */*',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: payload.toString()
        }).then(function (r) {
            return r.text().then(function (text) {
                var data = {};
                try { data = JSON.parse(text || '{}'); } catch (e) {}
                return { ok: r.ok, data: data };
            });
        }).then(function (result) {
            if (result.ok && result.data && result.data.success) {
                window.location.reload();
            } else {
                alert((result.data && result.data.message) ? result.data.message : 'Unable to update currency');
                el.value = prev;
                el.disabled = false;
            }
        }).catch(function () {
            alert('Network error while updating currency');
            el.value = prev;
            el.disabled = false;
        });
    });
})();
</script>
@endif

@if(!empty($whatsappChatbotNumber))
<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappChatbotNumber) }}" target="_blank" class="whatsapp-chatbot-fab" title="Chat with us">
    <i class="fab fa-whatsapp"></i>
</a>
@endif

<style>
.ks-footer-dark {
    background-color: #000000;
    color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    border-top: none !important;
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* Footer overall compact size */
.ks-footer-dark .container.py-5 {
    padding-top: 1.75rem !important;
    padding-bottom: 1.75rem !important;
}

.footer-title {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #fff;
    line-height: 1.2;
}

.footer-list {
    list-style: none;
    padding: 0;
    margin-bottom: 0;
}

.footer-list li {
    margin-bottom: 6px;
}

.footer-list a {
    color: #fff;
    text-decoration: none;
    font-size: 12px;
    line-height: 1.25;
}

.footer-list a:hover {
    text-decoration: underline;
}

.ks-selector {
    border: 1px solid #333;
    padding: 6px 9px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    max-width: 220px;
    cursor: pointer;
}
.ks-selector--readonly {
    cursor: default;
    opacity: 0.95;
}
.ks-selector-select {
    width: 100%;
    max-width: 220px;
    background: #000;
    color: #fff;
    border: 1px solid #333;
    appearance: auto;
    cursor: pointer;
    font-size: 12px;
    padding: 5px 7px;
}
.ks-selector-select:focus {
    outline: 2px solid #666;
    outline-offset: 2px;
}
.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.footer-currency-wrap {
    max-width: 220px;
}
.footer-currency-label {
    display: block;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #999;
    margin-bottom: 5px;
}

/* Logo section smaller + remove green tint */
.ks-huge-branding {
    padding: 24px 0 20px;
    text-align: center;
}

.ks-huge-branding img {
    width: 100%;
    max-width: 700px;
    height: auto;

    opacity: 0.9;
}

.footer-bottom-flex {
    border-top: 1px solid #222;
    padding-top: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.social-links a {
    color: #fff;
    font-size: 16px;
    margin-right: 12px;
    text-decoration: none;
}

.app-badges img {
    height: 26px;
    margin-left: 6px;
}

.footer-legal-bar {
    margin-top: 12px;
    padding-bottom: 2px;
    font-size: 11px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.footer-legal-bar a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
}

.footer-legal-bar a:hover {
    text-decoration: underline;
}

.footer-logo img {
    filter: brightness(0) invert(1);
}

.whatsapp-chatbot-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 56px;
    height: 56px;
    background: #25d366;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.5);
    z-index: 9999;
    transition: transform 0.2s;
}
.whatsapp-chatbot-fab:hover {
    color: #fff;
    transform: scale(1.08);
}

@media (max-width: 992px) {
    .ks-huge-branding {
        padding: 20px 0 18px;
    }

    .ks-huge-branding img {
        max-width: 560px;
    }
}

@media (max-width: 768px) {
    .ks-footer-dark .container.py-5 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }

    .footer-title {
        margin-bottom: 8px;
    }

    .footer-list li {
        margin-bottom: 5px;
    }

    .ks-huge-branding img {
        max-width: 420px;
    }

    .app-badges img {
        height: 24px;
    }

    .whatsapp-chatbot-fab {
        bottom: 16px;
        right: 16px;
        width: 48px;
        height: 48px;
        font-size: 24px;
    }
}
</style>