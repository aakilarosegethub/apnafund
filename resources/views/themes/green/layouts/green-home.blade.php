

<!DOCTYPE html>
<html lang="en">
<head>
@php
    // Get page-specific SEO data if available
    $pageSeo = null;
    $currentSection = request()->segment(1);
    $currentSlug = null;
    $currentPath = trim(request()->path(), '/'); // Get full path without leading/trailing slashes
    
    // Check if this is a /page/{slug} route and fetch SEO from page_seo section
    if ($currentSection == 'page' && request()->segment(2)) {
        $currentSlug = request()->segment(2);
    } else {
        // For direct routes like /about, /faq, etc., use the first segment as slug
        $currentSlug = $currentSection;
    }
    $currentSlug = $currentPath;
    // Check if this is home page
    $isHomePage = ($currentSection == '' || $currentSection == '/' || request()->path() == '/' || $currentPath == '');
    
    // Fetch SEO data from page_seo section if slug is available
    if ($currentSlug || $isHomePage || $currentPath) {
        $allPageSeo = \App\Models\SiteData::where('data_key', 'page_seo.element')->get();
        
        foreach ($allPageSeo as $seoItem) {
            $seoInfo = is_array($seoItem->data_info) ? $seoItem->data_info : (array)$seoItem->data_info;
            $seoSlug = isset($seoInfo['slug']) ? trim($seoInfo['slug'], '/') : '';
            
            // Normalize paths for comparison (remove leading/trailing slashes, lowercase)
            $normalizedCurrentPath = strtolower(trim($currentPath, '/'));
            $normalizedSeoSlug = strtolower(trim($seoSlug, '/'));
            $normalizedCurrentSlug = strtolower(trim($currentSlug, '/'));
            
            // Special handling for home page - check for "justv/" or empty slug
            if ($isHomePage) {
                if ($normalizedSeoSlug == 'justv' || $normalizedSeoSlug == '' || $normalizedSeoSlug == '/') {
                    $pageSeo = (object)$seoInfo;
                    break;
                }
            } else {
                // Check for exact match with full path (e.g., "user/login")
                if ($normalizedCurrentPath && $normalizedSeoSlug == $normalizedCurrentPath) {
                    $pageSeo = (object)$seoInfo;
                    break;
                }
                // Also check for single segment match (e.g., "about")
                elseif ($normalizedCurrentSlug && $normalizedSeoSlug == $normalizedCurrentSlug) {
                    $pageSeo = (object)$seoInfo;
                    break;
                }
                // Check for "page/{slug}" format match (e.g., "page/about" matches "about")
                elseif ($currentSection == 'page' && $currentSlug && $normalizedSeoSlug == $normalizedCurrentSlug) {
                    $pageSeo = (object)$seoInfo;
                    break;
                }
                // Check if SEO slug is "page/{slug}" format and matches current path
                elseif (strpos($normalizedSeoSlug, 'page/') === 0) {
                    $seoSlugWithoutPage = substr($normalizedSeoSlug, 5); // Remove "page/" prefix
                    if ($seoSlugWithoutPage == $normalizedCurrentSlug || $seoSlugWithoutPage == $normalizedCurrentPath) {
                        $pageSeo = (object)$seoInfo;
                        break;
                    }
                }
            }
        }
    }
    
    // If no page_seo data found, check for $pageSEO variable passed from controller (e.g., for category pages)
    if (!$pageSeo && isset($pageSEO) && is_array($pageSEO) && !empty($pageSEO)) {
        $pageSeo = (object)$pageSEO;
    }
    
    // If still no page_seo data found, try to get from section-specific SEO
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
    
    // Get canonical URL (fallback to current URL if empty)
    $canonicalUrl = $pageSeo->canonical_url ?? $seoData->canonical_url ?? null;
    if (!$canonicalUrl) {
        $canonicalUrl = url()->current();
    }
    
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
    $isHomePage = ($currentSection == '' || $currentSection == '/' || request()->path() == '/' || $currentPath == '');
    
    // Build full slug path for /page/{slug} routes
    $fullSlug = null;
    if ($currentSection == 'page' && request()->segment(2)) {
        $fullSlug = 'page/' . request()->segment(2);
    } elseif ($currentPath) {
        $fullSlug = $currentPath;
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
        
        // Match full slug path (e.g., "page/about", "user/login") or simple slug (e.g., "about")
        if ($schemaSlug && !$isHomePage) {
            // Check if schema slug matches full path (e.g., "user/login")
            if ($currentPath && $schemaSlug == $currentPath) {
                $schemaMarkup = $schemaInfo;
                break;
            }
            // Check if schema slug matches full slug path (e.g., "page/about")
            elseif ($fullSlug && $schemaSlug == $fullSlug) {
                $schemaMarkup = $schemaInfo;
                break;
            }
            // Check if schema slug matches simple slug (e.g., "about")
            elseif ($currentSlug && $schemaSlug == $currentSlug) {
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
.project-image {
    height: 290px !important;
    background-size: contain !important;
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
}

/* Button fix */
.hero .btn{
  font-weight: 600;
}

</style>
@yield('custom-css')

{{-- Custom Header Code from Admin Panel --}}
@if(getCustomCode('header'))
{!! getCustomCode('header') !!}
@endif
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
@include('partials.toasts')

@stack('scripts')
@stack('page-script')

@if(isset($inboxFirebaseConfig) && $inboxFirebaseConfig && isset($inboxUserId) && $inboxUserId)
<script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.0/firebase-firestore-compat.js"></script>
<script>
(function() {
    var cfg = @json($inboxFirebaseConfig ?? []);
    var tokenUrl = @json($inboxTokenUrl ?? '');
    var currentUserId = @json($inboxUserId ?? '');
    if (!cfg.apiKey || !cfg.projectId || !tokenUrl) return;
    if (typeof firebase === 'undefined') return;
    var app = firebase.apps && firebase.apps.length > 0 ? firebase.app() : firebase.initializeApp(cfg);
    var auth = firebase.auth(app);
    var db = firebase.firestore(app);
    var convColl = (cfg.chatCollectionPrefix || 'apnacrowdfunding') + '_conversations';
    var lastKnownMessageAt = {};
    var _inboxAudioCtx = null;
    try { _inboxAudioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) {}
    function _initInboxAudio() {
        if (!_inboxAudioCtx) try { _inboxAudioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) {}
        if (_inboxAudioCtx && _inboxAudioCtx.state === 'suspended') _inboxAudioCtx.resume();
    }
    ['click','keydown','touchstart','mousedown'].forEach(function(ev) {
        document.addEventListener(ev, function() { _initInboxAudio(); try { sessionStorage.setItem('inboxSoundEnabled','1'); } catch(e) {} }, { once: true });
    });
    function playNotificationSound() {
        try {
            if (!_inboxAudioCtx) return;
            if (_inboxAudioCtx.state === 'suspended') _inboxAudioCtx.resume();
            var osc = _inboxAudioCtx.createOscillator();
            var gain = _inboxAudioCtx.createGain();
            osc.connect(gain);
            gain.connect(_inboxAudioCtx.destination);
            osc.frequency.value = 800;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.3, _inboxAudioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, _inboxAudioCtx.currentTime + 0.2);
            osc.start(_inboxAudioCtx.currentTime);
            osc.stop(_inboxAudioCtx.currentTime + 0.2);
        } catch (e) {}
    }
    function showInboxToast(senderName, preview) {
        var el = document.getElementById('inbox-msg-toast');
        if (el) el.remove();
        el = document.createElement('div');
        el.id = 'inbox-msg-toast';
        el.style.cssText = 'position:fixed;top:20px;right:20px;padding:14px 20px;border-radius:10px;z-index:99999;font-size:14px;box-shadow:0 4px 16px rgba(0,0,0,0.2);background:linear-gradient(135deg,#05ce78,#04b367);color:#fff;cursor:pointer;max-width:320px;';
        el.innerHTML = '<strong>' + (senderName ? ('New message from ' + senderName.replace(/</g,'&lt;')) : 'New message') + '</strong><br><span style="opacity:0.95;font-size:13px;">' + (preview || 'You have a new message').substring(0, 60).replace(/</g,'&lt;') + '</span>';
        el.onclick = function() { el.remove(); window.location.href = '{{ route("user.inbox.index") }}'; };
        document.body.appendChild(el);
        setTimeout(function() { if (el.parentNode) el.remove(); }, 6000);
    }
    function showMessageNotification(senderName, preview) {
        showInboxToast(senderName, preview);
        if (!('Notification' in window)) return;
        if (Notification.permission === 'granted') {
            var n = new Notification(senderName ? ('New message from ' + senderName) : 'New message', {
                body: (preview || 'You have a new message').substring(0, 80),
                tag: 'inbox-msg'
            });
            n.onclick = function() { window.focus(); window.location.href = '{{ route("user.inbox.index") }}'; };
        }
    }
    function notifyNewMessage(doc, otherName, lastMsg) {
        playNotificationSound();
        showMessageNotification(otherName, lastMsg);
    }
    function requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') Notification.requestPermission();
    }
    function startInboxListener() {
        var q = db.collection(convColl).where('participants', 'array-contains', currentUserId).orderBy('last_message_at', 'desc');
        q.onSnapshot(function(snap) {
            var changes = snap.docChanges ? snap.docChanges() : [];
            changes.forEach(function(change) {
                var doc = change.doc;
                var d = doc.data();
                var lastSenderId = d.last_sender_id;
                var lastAt = d.last_message_at ? (d.last_message_at.toDate ? d.last_message_at.toDate().getTime() : 0) : 0;
                if (change.type === 'modified' && lastSenderId && lastSenderId !== currentUserId) {
                    var prevAt = lastKnownMessageAt[doc.id] || 0;
                    if (lastAt > prevAt) {
                        var oid = (d.participants || []).find(function(p) { return p !== currentUserId; });
                        var otherName = (d.participant_names || {})[oid] || d.campaign_title || '';
                        notifyNewMessage(doc, otherName, d.last_message);
                    }
                }
                lastKnownMessageAt[doc.id] = lastAt;
            });
            if (changes.length === 0) {
                snap.docs.forEach(function(doc) {
                    var d = doc.data();
                    lastKnownMessageAt[doc.id] = d.last_message_at ? (d.last_message_at.toDate ? d.last_message_at.toDate().getTime() : 0) : 0;
                });
            }
        }, function() {});
    }
    requestNotificationPermission();
    if (typeof sessionStorage !== 'undefined' && !sessionStorage.getItem('inboxSoundEnabled')) {
        var hint = document.createElement('div');
        hint.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);padding:10px 18px;border-radius:8px;background:rgba(0,0,0,0.75);color:#fff;font-size:13px;z-index:99998;box-shadow:0 2px 12px rgba(0,0,0,0.2);';
        hint.textContent = 'Click anywhere to enable message sound';
        document.body.appendChild(hint);
        var hideHint = function() { if (hint.parentNode) hint.remove(); };
        setTimeout(hideHint, 6000);
        document.addEventListener('click', hideHint, { once: true });
        document.addEventListener('keydown', hideHint, { once: true });
        document.addEventListener('touchstart', hideHint, { once: true });
    }
    fetch(tokenUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.token) return auth.signInWithCustomToken(data.token);
        })
        .then(function() { startInboxListener(); })
        .catch(function() {});
})();
</script>
@endif

{{-- Custom Footer Code from Admin Panel --}}
@if(getCustomCode('footer'))
{!! getCustomCode('footer') !!}
@endif

@include('partials.brand-name-emphasize')
</body>
</html>
