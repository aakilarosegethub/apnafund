@php
    $footerContent = getSiteData('footer.content', true);
    $footerElements = getSiteData('footer.element', false, null, true);
    $footerCategories = \App\Models\Admins\FooterCategory::with('category')->where('status', 'active')->orderBy('sort_order')->orderBy('id')->take(8)->get();
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
                    
                    <a href="{{ route('home') }}" class="footer-logo mb-4 d-block">
                        <img src="{{ $logoUrl }}" alt="Logo" style="height: 40px; filter: brightness(0) invert(1);">
                    </a>

                    <div class="ks-selector mb-2">
                        <span>English</span>
                        <i class="ti ti-chevron-down"></i>
                    </div>
                    <div class="ks-selector">
                        <span>$ US Dollar (USD)</span>
                        <i class="ti ti-chevron-down"></i>
                    </div>
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
    border-top: 1px solid #333;
}

.footer-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #fff;
}

.footer-list {
    list-style: none;
    padding: 0;
}

.footer-list li {
    margin-bottom: 12px;
}

.footer-list a {
    color: #fff;
    text-decoration: none;
    font-size: 14px;
}

.footer-list a:hover {
    text-decoration: underline;
}

.ks-selector {
    border: 1px solid #333;
    padding: 8px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    max-width: 200px;
    cursor: pointer;
}

.ks-huge-branding {
    padding: 60px 0;
    text-align: center;
}

.ks-huge-branding img {
    width: 100%;
    max-width: 1116px;
    height: auto;
    filter: brightness(0) invert(1);
    opacity: 0.9;
}

.footer-bottom-flex {
    border-top: 1px solid #222;
    padding-top: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.social-links a {
    color: #fff;
    font-size: 20px;
    margin-right: 20px;
    text-decoration: none;
}

.app-badges img {
    height: 35px;
    margin-left: 10px;
}

.footer-legal-bar {
    margin-top: 30px;
    font-size: 13px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
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

@media (max-width: 768px) {
    .ks-huge-branding img {
        max-width: 100%;
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
