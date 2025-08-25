@php
    if (isset($seoContents) && count($seoContents)) {
        $seoContents     = (object) $seoContents;
        $socialImageSize = explode('x', $seoContents->image_size);
    } elseif ($seo) {
        $seoContents        = $seo;
        $socialImageSize    = explode('x', getFileSize('seo'));
        $seoContents->image = getImage(getFilePath('seo') . '/' . $seo->image);
    } else {
        $seoContents = null;
    }

    // Get dynamic page SEO data
    $currentRoute = request()->route();
    $pageKey = null;
    
    if ($currentRoute) {
        $routeName = $currentRoute->getName();
        $path = request()->path();
        
        // Map routes to page keys
        if ($routeName == 'home') {
            $pageKey = 'home';
        } elseif ($routeName == 'about.us') {
            $pageKey = 'about';
        } elseif ($routeName == 'faq') {
            $pageKey = 'faq';
        } elseif ($routeName == 'contact') {
            $pageKey = 'contact_us';
        } elseif ($routeName == 'volunteers') {
            $pageKey = 'volunteer';
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
        }
    }
    
    $pageSEO = $pageKey ? getPageSEO($pageKey) : null;
@endphp

<meta name="title" Content="{{ $pageSEO && $pageSEO['meta_title'] ? $pageSEO['meta_title'] : $setting->siteName(__($pageTitle)) }}">

@if($seoContents)
    <meta name="description" content="{{ $pageSEO && $pageSEO['meta_description'] ? $pageSEO['meta_description'] : $seoContents->description }}">
    <meta name="keywords" content="{{ $pageSEO && $pageSEO['meta_keywords'] ? $pageSEO['meta_keywords'] : implode(',', $seoContents->keywords) }}">
    <link rel="shortcut icon" href="{{ getImage(getFilePath('logoFavicon').'/favicon.png') }}" type="image/x-icon">

    {{--<!-- Apple Stuff -->--}}
    <link rel="apple-touch-icon" href="{{ getImage(getFilePath('logoFavicon').'/logo_dark.png', '?'.time()) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ $setting->siteName($pageTitle) }}">

    {{--<!-- Google / Search Engine Tags -->--}}
    <meta itemprop="name" content="{{ $setting->siteName($pageTitle) }}">
    <meta itemprop="description" content="{{ $seoContents->description }}">
    <meta itemprop="image" content="{{ $seoContents->image  }}">

    {{--<!-- Facebook Meta Tags -->--}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo->social_title }}">
    <meta property="og:description" content="{{ $seo->social_description }}">
    <meta property="og:image" content="{{ $seoContents->image ?: getImage(getFilePath('logoFavicon').'/logo_dark.png') }}"/>
    <meta property="og:image:type" content="{{ $seoContents->image && pathinfo($seoContents->image, PATHINFO_EXTENSION) ? 'image/' . pathinfo($seoContents->image, PATHINFO_EXTENSION) : 'image/jpeg' }}" />
    <meta property="og:image:width" content="{{ isset($socialImageSize[0]) ? $socialImageSize[0] : '1200' }}" />
    <meta property="og:image:height" content="{{ isset($socialImageSize[1]) ? $socialImageSize[1] : '630' }}" />
    <meta property="og:url" content="{{ url()->current() }}">

    {{--<!-- Twitter Meta Tags -->--}}
    <meta name="twitter:card" content="summary_large_image">
@endif
