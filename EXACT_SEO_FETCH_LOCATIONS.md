# Exact Files - URL Se Text Cut Karke Database Se SEO Fetch

## 🎯 Main File - URL Se Slug Extract Karke SEO Fetch

### File 1: `resources/views/themes/green/layouts/green-home.blade.php`

**Lines 9-72**: Yahan URL se text cut karke database se SEO fetch ho raha hai

```php
@php
    // Get page-specific SEO data if available
    $pageSeo = null;
    
    // ⬇️ YAHAN URL SE TEXT CUT HO RAHA HAI
    $currentSection = request()->segment(1);  // URL ka pehla part (e.g., "page", "about")
    $currentSlug = null;
    $currentPath = trim(request()->path(), '/'); // Full path (e.g., "page/about", "user/login")
    
    // ⬇️ YAHAN URL SE SLUG EXTRACT HO RAHA HAI
    if ($currentSection == 'page' && request()->segment(2)) {
        $currentSlug = request()->segment(2);  // "page/about" se "about" extract
    } else {
        $currentSlug = $currentSection;  // Direct route se slug (e.g., "about")
    }
    
    // ⬇️ YAHAN DATABASE SE SEO DATA FETCH HO RAHA HAI
    if ($currentSlug || $isHomePage || $currentPath) {
        $allPageSeo = \App\Models\SiteData::where('data_key', 'page_seo.element')->get();
        
        foreach ($allPageSeo as $seoItem) {
            $seoInfo = is_array($seoItem->data_info) ? $seoItem->data_info : (array)$seoItem->data_info;
            $seoSlug = isset($seoInfo['slug']) ? trim($seoInfo['slug'], '/') : '';
            
            // ⬇️ YAHAN URL SLUG AUR DATABASE SLUG MATCH HO RAHA HAI
            $normalizedCurrentPath = strtolower(trim($currentPath, '/'));
            $normalizedSeoSlug = strtolower(trim($seoSlug, '/'));
            $normalizedCurrentSlug = strtolower(trim($currentSlug, '/'));
            
            // Match karke SEO data assign ho raha hai
            if ($normalizedCurrentPath && $normalizedSeoSlug == $normalizedCurrentPath) {
                $pageSeo = (object)$seoInfo;  // ⬅️ YAHAN SEO DATA ASSIGN HO RAHA HAI
                break;
            }
            elseif ($normalizedCurrentSlug && $normalizedSeoSlug == $normalizedCurrentSlug) {
                $pageSeo = (object)$seoInfo;  // ⬅️ YAHAN SEO DATA ASSIGN HO RAHA HAI
                break;
            }
        }
    }
@endphp
```

**Key Functions:**
- `request()->path()` - Full URL path return karta hai (e.g., "page/about")
- `request()->segment(1)` - URL ka pehla part (e.g., "page")
- `request()->segment(2)` - URL ka doosra part (e.g., "about")
- `trim($path, '/')` - Leading/trailing slashes remove karta hai

---

## 🎯 Controller File - Page By Slug Function

### File 2: `app/Http/Controllers/WebsiteController.php`

**Lines 1405-1448**: Yahan bhi URL se slug extract karke SEO fetch ho raha hai

```php
function pageBySlug($slug) {
    // ⬇️ $slug parameter mein URL se extracted slug aa raha hai
    // Example: URL "/page/about" se $slug = "about"
    
    // ⬇️ YAHAN DATABASE SE SEO DATA FETCH HO RAHA HAI
    $pageSeoData = null;
    $allPageSeo = SiteData::where('data_key', 'page_seo.element')->get();
    
    // ⬇️ YAHAN SLUG NORMALIZE HO RAHA HAI (lowercase, trim)
    $normalizedSlug = strtolower(trim($slug, '/'));
    
    foreach ($allPageSeo as $seoItem) {
        $seoInfo = is_array($seoItem->data_info) ? $seoItem->data_info : (array)$seoItem->data_info;
        if (isset($seoInfo['slug'])) {
            $seoSlug = trim($seoInfo['slug'], '/');
            $normalizedSeoSlug = strtolower($seoSlug);
            
            // ⬇️ YAHAN MATCH HO RAHA HAI AUR SEO DATA ASSIGN HO RAHA HAI
            if ($normalizedSeoSlug == $normalizedSlug) {
                $pageSeoData = $seoInfo;  // ⬅️ YAHAN SEO DATA ASSIGN HO RAHA HAI
                break;
            }
        }
    }
    
    // ⬇️ YAHAN SEO DATA USE HO RAHA HAI
    if ($pageSeoData && isset($pageSeoData['meta_title'])) {
        $pageTitle = $pageSeoData['meta_title'];
    }
}
```

**Route Definition:**
- `routes/web.php` Line 165: `Route::get('page/{slug}', ...)`
- Yahan `{slug}` URL se extract ho kar `$slug` parameter mein aa jata hai

---

## 📊 Database Query Location

### Database Se Data Fetch:

```php
// ⬇️ YAHAN DATABASE SE SAB SEO ENTRIES FETCH HO RAHI HAIN
$allPageSeo = \App\Models\SiteData::where('data_key', 'page_seo.element')->get();

// ⬇️ YAHAN EACH ENTRY KA DATA ACCESS HO RAHA HAI
foreach ($allPageSeo as $seoItem) {
    $seoInfo = is_array($seoItem->data_info) ? $seoItem->data_info : (array)$seoItem->data_info;
    $seoSlug = isset($seoInfo['slug']) ? trim($seoInfo['slug'], '/') : '';
    
    // ⬇️ YAHAN URL SLUG AUR DB SLUG COMPARE HO RAHA HAI
    if ($normalizedSeoSlug == $normalizedCurrentPath) {
        // Match mil gaya, SEO data assign karo
        $pageSeo = (object)$seoInfo;
    }
}
```

**Database Table:** `site_data`
**Column:** `data_key` = `'page_seo.element'`
**Data Format:** JSON in `data_info` column
```json
{
    "slug": "about",
    "meta_title": "About Us",
    "meta_description": "About page description",
    "meta_keywords": "about, us"
}
```

---

## 🔍 Complete Flow

### Step-by-Step Process:

1. **URL Hit:** User visits `http://yoursite.com/page/about`

2. **URL Extract (Line 14):**
   ```php
   $currentPath = trim(request()->path(), '/');  // Result: "page/about"
   $currentSection = request()->segment(1);      // Result: "page"
   $currentSlug = request()->segment(2);        // Result: "about"
   ```

3. **Database Query (Line 29):**
   ```php
   $allPageSeo = \App\Models\SiteData::where('data_key', 'page_seo.element')->get();
   ```

4. **Slug Normalization (Lines 36-38):**
   ```php
   $normalizedCurrentPath = strtolower(trim($currentPath, '/'));  // "page/about"
   $normalizedSeoSlug = strtolower(trim($seoSlug, '/'));          // "about" (from DB)
   ```

5. **Matching (Lines 48-55):**
   ```php
   if ($normalizedSeoSlug == $normalizedCurrentPath) {
       $pageSeo = (object)$seoInfo;  // Match found!
   }
   ```

6. **SEO Data Use (Lines 77-96):**
   ```php
   $metaTitle = $pageSeo->meta_title ?? 'Default Title';
   $metaDescription = $pageSeo->meta_description ?? '';
   ```

---

## 📝 Exact Code Snippets

### URL Se Text Cut:
```php
// File: green-home.blade.php, Line 12-14
$currentSection = request()->segment(1);
$currentSlug = null;
$currentPath = trim(request()->path(), '/');
```

### Database Se Fetch:
```php
// File: green-home.blade.php, Line 29
$allPageSeo = \App\Models\SiteData::where('data_key', 'page_seo.element')->get();
```

### Matching Logic:
```php
// File: green-home.blade.php, Lines 36-55
$normalizedCurrentPath = strtolower(trim($currentPath, '/'));
$normalizedSeoSlug = strtolower(trim($seoSlug, '/'));

if ($normalizedSeoSlug == $normalizedCurrentPath) {
    $pageSeo = (object)$seoInfo;
    break;
}
```

---

## 🎯 Summary

**Main File:** `resources/views/themes/green/layouts/green-home.blade.php`
- **Lines 12-14**: URL se text extract
- **Line 29**: Database se SEO data fetch
- **Lines 36-70**: Matching logic aur SEO data assign

**Secondary File:** `app/Http/Controllers/WebsiteController.php`
- **Lines 1420-1448**: Controller mein SEO fetch logic

**Database:** `site_data` table
- **Key:** `page_seo.element`
- **Data:** JSON format mein `data_info` column mein
