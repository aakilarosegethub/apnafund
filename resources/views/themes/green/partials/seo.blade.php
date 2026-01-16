@php
    // Get page-specific SEO data if available
    $pageSeo = null;
    $currentSection = request()->segment(1);
    if ($currentSection) {
        $pageSeoData = \App\Models\SiteData::where('data_key', $currentSection . '.seo')->first();
        $pageSeo = $pageSeoData ? (is_array($pageSeoData->data_info) ? (object)$pageSeoData->data_info : $pageSeoData->data_info) : null;
    }
    
    // Get global SEO data
    $seo = \App\Models\SiteData::where('data_key', 'seo.data')->first();
    $seoData = $seo ? (is_array($seo->data_info) ? (object)$seo->data_info : $seo->data_info) : null;
    
    // Set charset and viewport from SEO data or use defaults
    // Priority: pageSeo > seoData > defaults
    $metaCharset = ($pageSeo->meta_charset ?? $seoData->meta_charset ?? 'UTF-8');
    $metaViewport = ($pageSeo->meta_viewport ?? $seoData->meta_viewport ?? 'width=device-width, initial-scale=1');
    
    // Get meta title with fallback priority
    $metaTitle = $pageSeo->meta_title ?? $seoData->meta_title ?? (isset($pageTitle) ? $setting->siteName(__($pageTitle)) : bs('site_name') ?? 'ApnaCrowdfunding');
    
    // Get meta description with fallback priority
    $metaDescription = $pageSeo->meta_description ?? $seoData->meta_description ?? $seoData->description ?? '';
    
    // Get meta keywords with fallback priority
    $metaKeywords = '';
    if ($pageSeo && isset($pageSeo->meta_keywords) && $pageSeo->meta_keywords) {
        $metaKeywords = $pageSeo->meta_keywords;
    } elseif ($seoData && isset($seoData->meta_keywords) && $seoData->meta_keywords) {
        $metaKeywords = $seoData->meta_keywords;
    } elseif ($seoData && isset($seoData->keywords) && is_array($seoData->keywords)) {
        $metaKeywords = implode(',', $seoData->keywords);
    }
    
    // Get meta author
    $metaAuthor = $pageSeo->meta_author ?? $seoData->meta_author ?? null;
    
    // Get meta robots
    $metaRobots = $pageSeo->meta_robots ?? $seoData->meta_robots ?? 'index, follow';
    
    // Get canonical URL
    $canonicalUrl = $pageSeo->canonical_url ?? $seoData->canonical_url ?? url()->current();
@endphp

@if($seoData || $pageSeo)
    <!-- SEO Meta Tags -->
    @if($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    
    @if($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    
    @if($metaAuthor)
        <meta name="author" content="{{ $metaAuthor }}">
    @endif
    
    <meta name="robots" content="{{ $metaRobots }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageSeo->meta_title ?? $seoData->meta_title ?? $seoData->social_title ?? bs('site_name') ?? 'ApnaCrowdfunding' }}">
    <meta property="og:description" content="{{ $seoData->social_description ?? $pageSeo->meta_description ?? $seoData->meta_description ?? $seoData->description ?? '' }}">
    @if(isset($seoData->image) && $seoData->image)
        @php
            $seoImage = filter_var($seoData->image, FILTER_VALIDATE_URL) 
                ? $seoData->image 
                : getImage(getFilePath('seo') . '/' . $seoData->image, getFileSize('seo'));
        @endphp
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageSeo->meta_title ?? $seoData->meta_title ?? $seoData->social_title ?? bs('site_name') ?? 'ApnaCrowdfunding' }}">
    <meta name="twitter:description" content="{{ $seoData->social_description ?? $pageSeo->meta_description ?? $seoData->meta_description ?? $seoData->description ?? '' }}">
    @if(isset($seoData->image) && $seoData->image)
        <meta name="twitter:image" content="{{ $seoImage ?? '' }}">
    @endif
@else
    <!-- Default Meta Tags -->
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
@endif
