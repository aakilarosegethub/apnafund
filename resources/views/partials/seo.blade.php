@php
    if (isset($seoContents) && count($seoContents)) {
        $seoContents     = (object) $seoContents;
        $socialImageSize = isset($seoContents->image_size) ? explode('x', $seoContents->image_size) : ['1200', '630'];
    } elseif ($seo) {
        $seoContents        = (object) $seo;
        $socialImageSize    = explode('x', getFileSize('seo'));
        $seoContents->image = getImage(getFilePath('seo') . '/' . ($seo['image'] ?? ''));
    } else {
        $seoContents = null;
        $socialImageSize = ['1200', '630'];
    }

    // Get dynamic page SEO data
    $currentRoute = request()->route();
    $pageKey = null;
    $dynamicPageSEO = null;
    $path = request()->path();
    
    // First, check if current URL path matches any dynamic page slug
    $allDynamicPages = \App\Models\SiteData::where('data_key', 'dynamic_pages.element')->get();
    foreach ($allDynamicPages as $dynamicPage) {
        if (isset($dynamicPage->data_info['slug']) && $dynamicPage->data_info['slug']) {
            $pageSlug = $dynamicPage->data_info['slug'];
            // Check if current path matches the slug (exact match or with leading/trailing slashes)
            if ($path == $pageSlug || $path == trim($pageSlug, '/') || trim($path, '/') == trim($pageSlug, '/')) {
                // Found matching dynamic page, use its SEO data
                $dynamicPageSEO = [
                    'meta_title' => $dynamicPage->data_info['meta_title'] ?? '',
                    'meta_description' => $dynamicPage->data_info['meta_description'] ?? '',
                    'meta_keywords' => $dynamicPage->data_info['meta_keywords'] ?? '',
                ];
                break;
            }
        }
    }
    
    if ($currentRoute && !$dynamicPageSEO) {
        $routeName = $currentRoute->getName();
        
        // Map routes to page keys
        if ($routeName == 'home') {
            $pageKey = 'home';
        } elseif ($routeName == 'about.us') {
            $pageKey = 'about';
        } elseif ($routeName == 'faq') {
            $pageKey = 'faq';
        } elseif ($routeName == 'contact') {
            $pageKey = 'contact_us';
        } elseif ($routeName == 'stories') {
            $pageKey = 'success_story';
        } elseif ($routeName == 'business.resources') {
            $pageKey = 'business_resources';
        } elseif ($routeName == 'upcoming') {
            $pageKey = 'upcoming';
        } elseif ($routeName == 'campaign') {
            $pageKey = 'featured_campaign';
        } elseif (str_contains($path, 'terms')) {
            $pageKey = 'policy_pages';
        } elseif (str_contains($path, 'privacy')) {
            $pageKey = 'policy_pages';
        } elseif (str_contains($path, 'policy')) {
            $pageKey = 'policy_pages';
        } elseif (str_contains($path, 'page/') && $routeName == 'dynamic.pages') {
            $pageKey = 'dynamic_pages';
        }
    }
    
    // Use dynamic page SEO if found, otherwise use pageKey SEO
    $pageSEO = $dynamicPageSEO ?: ($pageKey ? getPageSEO($pageKey) : null);
@endphp

<meta name="title" Content="{{ $pageSEO && $pageSEO['meta_title'] ? $pageSEO['meta_title'] : $setting->siteName(__($pageTitle)) }}">

{{-- Always show description and keywords if available in $pageSEO or $seoContents --}}
@if($pageSEO && ($pageSEO['meta_description'] || $pageSEO['meta_keywords']))
    <meta name="description" content="{{ $pageSEO['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $pageSEO['meta_keywords'] ?? '' }}">
@elseif($seoContents)
    <meta name="description" content="{{ $seoContents->description ?? '' }}">
    <meta name="keywords" content="{{ isset($seoContents->keywords) ? implode(',', $seoContents->keywords) : '' }}">
@endif

@if($seoContents)
    <link rel="shortcut icon" href="{{ getImage(getFilePath('logoFavicon').'/favicon.png') }}" type="image/x-icon">

    {{--<!-- Apple Stuff -->--}}
    <link rel="apple-touch-icon" href="{{ getImage(getFilePath('logoFavicon').'/logo_dark.png', '?'.time()) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ $setting->siteName($pageTitle) }}">

    {{--<!-- Google / Search Engine Tags -->--}}
    <meta itemprop="name" content="{{ $setting->siteName($pageTitle) }}">
    <meta itemprop="description" content="{{ $seoContents->description ?? '' }}">
    <meta itemprop="image" content="{{ $seoContents->image ?? '' }}">

    {{--<!-- Facebook Meta Tags -->--}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo->social_title ?? '' }}">
    <meta property="og:description" content="{{ $seo->social_description ?? '' }}">
    <meta property="og:image" content="{{ ($seoContents->image ?? '') ?: getImage(getFilePath('logoFavicon').'/logo_dark.png') }}"/>
    <meta property="og:image:type" content="{{ ($seoContents->image ?? '') && pathinfo($seoContents->image ?? '', PATHINFO_EXTENSION) ? 'image/' . pathinfo($seoContents->image ?? '', PATHINFO_EXTENSION) : 'image/jpeg' }}" />
    <meta property="og:image:width" content="{{ isset($socialImageSize[0]) ? $socialImageSize[0] : '1200' }}" />
    <meta property="og:image:height" content="{{ isset($socialImageSize[1]) ? $socialImageSize[1] : '630' }}" />
    <meta property="og:url" content="{{ url()->current() }}">

    {{--<!-- Twitter Meta Tags -->--}}
    <meta name="twitter:card" content="summary_large_image">
@endif
