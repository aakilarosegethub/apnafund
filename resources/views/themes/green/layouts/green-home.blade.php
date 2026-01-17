@php
if(isset($_GET['test'])){   die('home');
}
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
@php
    // Get page-specific SEO data if available
    $pageSeo = null;
    $currentSection = request()->segment(1);
    $currentSlug = null;
    
    // Check if this is a /page/{slug} route and fetch SEO from page_seo section
    if ($currentSection == 'page' && request()->segment(2)) {
        $currentSlug = request()->segment(2);
    } else {
        // For direct routes like /about, /faq, etc., use the first segment as slug
        $currentSlug = $currentSection;
    }
    
    // Check if this is home page
    $isHomePage = ($currentSection == '' || $currentSection == '/' || request()->path() == '/');
    
    // Fetch SEO data from page_seo section if slug is available
    if ($currentSlug || $isHomePage) {
        $allPageSeo = \App\Models\SiteData::where('data_key', 'page_seo.element')->get();
        
        foreach ($allPageSeo as $seoItem) {
            $seoInfo = is_array($seoItem->data_info) ? $seoItem->data_info : (array)$seoItem->data_info;
            $seoSlug = isset($seoInfo['slug']) ? trim($seoInfo['slug']) : '';
            
            // Special handling for home page - check for "justv/" or empty slug
            if ($isHomePage) {
                if ($seoSlug == 'justv/' || $seoSlug == 'justv' || $seoSlug == '' || $seoSlug == '/') {
                    $pageSeo = (object)$seoInfo;
                    break;
                }
            } elseif ($currentSlug && $seoSlug == $currentSlug) {
                $pageSeo = (object)$seoInfo;
                break;
            }
        }
    }
    
    // If no page_seo data found, try to get from section-specific SEO
    if (!$pageSeo && $currentSection) {
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
    $metaTitle = $pageSeo->meta_title ?? $seoData->meta_title ?? bs('site_name') ?? 'ApnaCrowdfunding';
    
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
    
    // Get OG and Twitter images from page_seo if available
    $ogImage = null;
    $twitterImage = null;
    if ($pageSeo) {
        if (isset($pageSeo->og_image_url) && $pageSeo->og_image_url) {
            $ogImage = filter_var($pageSeo->og_image_url, FILTER_VALIDATE_URL) 
                ? $pageSeo->og_image_url 
                : url($pageSeo->og_image_url);
        }
        if (isset($pageSeo->twitter_image_url) && $pageSeo->twitter_image_url) {
            $twitterImage = filter_var($pageSeo->twitter_image_url, FILTER_VALIDATE_URL) 
                ? $pageSeo->twitter_image_url 
                : url($pageSeo->twitter_image_url);
        }
    }
    
    // Get Schema Markup for current page
    $schemaMarkup = null;
    
    // Check if this is home page
    $isHomePage = ($currentSection == '' || $currentSection == '/' || request()->path() == '/');
    
    // Build full slug path for /page/{slug} routes
    $fullSlug = null;
    if ($currentSection == 'page' && request()->segment(2)) {
        $fullSlug = 'page/' . request()->segment(2);
    } elseif ($currentSlug) {
        $fullSlug = $currentSlug;
    }
    
    // Get all schema markup entries
    $allSchemaMarkup = \App\Models\SiteData::where('data_key', 'schema_markup.element')->get();
    
    foreach ($allSchemaMarkup as $schemaItem) {
        $schemaInfo = is_array($schemaItem->data_info) ? $schemaItem->data_info : (array)$schemaItem->data_info;
        $schemaSlug = isset($schemaInfo['slug']) ? trim($schemaInfo['slug']) : '';
        
        // Special handling for home page - check for "justv/" or empty slug
        if ($isHomePage) {
            if ($schemaSlug == 'justv/' || $schemaSlug == 'justv' || $schemaSlug == '' || $schemaSlug == '/') {
                $schemaMarkup = $schemaInfo;
                break;
            }
        }
        
        // Match full slug path (e.g., "page/about") or simple slug (e.g., "about")
        if ($schemaSlug && !$isHomePage) {
            // Check if schema slug matches full path or simple slug
            if ($fullSlug && $schemaSlug == $fullSlug) {
                $schemaMarkup = $schemaInfo;
                break;
            } elseif ($currentSlug && $schemaSlug == $currentSlug) {
                $schemaMarkup = $schemaInfo;
                break;
            }
        }
    }
@endphp
<meta charset="{{ $metaCharset }}">
<title>@yield('title', $metaTitle)</title>
<meta name="viewport" content="{{ $metaViewport }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

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
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ getSiteFavicon() }}" type="image/png">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageSeo->og_title ?? $pageSeo->meta_title ?? $seoData->meta_title ?? $seoData->social_title ?? bs('site_name') ?? 'ApnaCrowdfunding' }}">
    <meta property="og:description" content="{{ $pageSeo->og_description ?? $pageSeo->meta_description ?? $seoData->social_description ?? $seoData->meta_description ?? $seoData->description ?? '' }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @elseif(isset($seoData->image) && $seoData->image)
        @php
            $seoImage = filter_var($seoData->image, FILTER_VALIDATE_URL) 
                ? $seoData->image 
                : getImage(getFilePath('seo') . '/' . $seoData->image, getFileSize('seo'));
        @endphp
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <meta property="og:url" content="{{ $canonicalUrl }}">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageSeo->twitter_title ?? $pageSeo->meta_title ?? $seoData->meta_title ?? $seoData->social_title ?? bs('site_name') ?? 'ApnaCrowdfunding' }}">
    <meta name="twitter:description" content="{{ $pageSeo->twitter_description ?? $pageSeo->meta_description ?? $seoData->social_description ?? $seoData->meta_description ?? $seoData->description ?? '' }}">
    @if($twitterImage)
        <meta name="twitter:image" content="{{ $twitterImage }}">
    @elseif(isset($seoData->image) && $seoData->image)
        <meta name="twitter:image" content="{{ $seoImage ?? '' }}">
    @endif
@else
    <!-- Default Meta Tags -->
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ getSiteFavicon() }}" type="image/png">
@endif

@if($schemaMarkup && isset($schemaMarkup['schema_json']) && !empty($schemaMarkup['schema_json']))
    <!-- Schema Markup (JSON-LD) -->
    <script type="application/ld+json">
    {!! $schemaMarkup['schema_json'] !!}
    </script>
@endif

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

@stack('styles')

<style>
:root{
  --green:#16a34a;
}

body{
  font-family:'Inter',sans-serif;
  background:#f9fafb;
  color:#111827;
}

/* NAVBAR */
.navbar{
  padding:14px 0;
}
.navbar-brand{
  font-weight:700;
  color:var(--green)!important;
  display: flex;
  align-items: center;
  padding: 0;
}
.navbar-brand img{
  height: 40px;
  max-width: 180px;
  object-fit: contain;
  display: block;
}
@media(max-width:768px){
  .navbar-brand img{
    height: 35px;
    max-width: 150px;
  }
}

/* HERO */
.hero{
  position:relative;
  color:#fff;
  padding:120px 0 100px;
     background: url(https://apnacrowdfunding.com/apnafund/assets/images/banner-12.jpg);
  background-size:cover;
  background-position:center;
  background-repeat:no-repeat;
}
.hero h1{
  font-weight:700;
  line-height:1.15;
  font-size:clamp(2rem,4vw,3.2rem);
}
.hero p{
  font-size:clamp(.95rem,2.5vw,1.05rem);
  max-width:520px;
  opacity:.95;
}

/* STATS */
.stats-box h4{
  font-weight:700;
  color:var(--green);
  font-size:clamp(1.4rem,3vw,1.7rem);
}
.stats-box p{
  margin:0;
  font-size:.9rem;
  color:#6b7280;
}

/* CATEGORY */
.category-btn{
  border-radius:14px;
  padding:6px 18px;
  font-size:.85rem;
  text-decoration:none;
}

/* CAMPAIGN CARD */
.campaign-card{
  border:none;
  border-radius:14px;
  overflow:hidden;
  background:#fff;
  box-shadow:0 10px 30px rgba(0,0,0,.06);
  transition:.25s;
}
.campaign-card:hover{
  transform:translateY(-6px);
  box-shadow:0 20px 45px rgba(0,0,0,.1);
}
.campaign-img{
  height:220px;
  width:100%;
  object-fit:cover;
}
.progress{
  height:6px;
  border-radius:10px;
}
.progress-bar{
  background:var(--green);
}

/* CTA */
.cta{
  background:var(--green);
  color:#fff;
  padding:90px 15px;
  text-align:center;
}
.cta h2{
  font-weight:700;
  font-size:clamp(1.6rem,4vw,2.2rem);
}

/* FOOTER */
.fundgreen-footer{
  background:#f3f4f6;
  font-size:14px;
}
.footer-links a{
  text-decoration:none;
  color:#6b7280;
  font-size:13px;
}
.footer-links a:hover{
  color:var(--green);
}
.footer-bottom{
  border-top:1px solid #e5e7eb;
  padding:14px 0;
  background:#f3f4f6;
}

/* ===== MOBILE FIXES ===== */
@media(max-width:768px){
  .hero{
    padding:90px 0 70px;
    text-align:center;
  }
  .hero p{
    margin-left:auto;
    margin-right:auto;
  }
  .stats-box{
    padding:10px 0;
  }
}
span{
	color: #05ce78;
}
/* ===== SMALL MOBILE ===== */
@media(max-width:480px){
  .campaign-img{
    height:190px;
  }
  .category-btn{
    font-size:.8rem;
    padding:5px 14px;
  }
}
/* =========================
   HERO SECTION FIX
========================= */

.hero{
  position: relative;
  color: #ffffff;
  padding: 120px 0 100px;
  overflow: hidden;
}

/* Overlay ONLY for background */
.hero::before{
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to right,
    rgba(0,0,0,0.65) 0%,
    rgba(0,0,0,0.55) 30%,
    rgba(0,0,0,0.30) 45%,
    rgba(0,0,0,0.05) 55%,
    rgba(0,0,0,0.00) 100%
  );
  z-index: 1;
}

/* Content above overlay */
.hero .container{
  position: relative;
  z-index: 2;
}

/* Heading clarity */
.hero h1{
  font-size: 3rem;
  font-weight: 800;
  line-height: 1.2;
  color: #ffffff;
  text-shadow: 0 6px 18px rgba(0,0,0,0.45);
}

/* First word highlight (Crowd / By / For) */
.hero h1 span{
  color: #2ecc71;
}

/* Description */
.hero p{
  font-size: 1.05rem;
  max-width: 580px;
  color: #f1f1f1;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

/* Button fix */
.hero .btn{
  font-weight: 600;
}

</style>
</head>
<body>

<!-- NAVBAR -->
@include(activeTheme() . 'partials.header-new')

<!-- CONTENT -->
@yield('content')

<!-- FOOTER -->
@include(activeTheme() . 'partials.footer-new')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
@stack('page-script')
</body>
</html>
