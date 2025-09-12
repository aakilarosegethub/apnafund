# Assets URL Configuration Guide

## Overview
This project now supports a configurable assets URL that allows you to easily switch between local and live server assets without changing code.

## Configuration

### 1. Environment Variable
In your `.env` file, set the `ASSETS_URL` variable:

```env
# For local development
ASSETS_URL=http://localhost:8000

# For live server
ASSETS_URL=https://yourdomain.com
```

### 2. Usage in Blade Templates

#### Method 1: Using custom_asset() helper (Recommended)
```blade
<!-- Instead of: -->
<img src="{{ asset('assets/images/logo.png') }}" alt="Logo">

<!-- Use: -->
<img src="{{ custom_asset('assets/images/logo.png') }}" alt="Logo">
```

#### Method 2: Using config() directly
```blade
<img src="{{ config('app.assets_url') }}/assets/images/logo.png" alt="Logo">
```

#### Method 3: Using existing getImage() helper (if available)
```blade
<!-- This will continue to work as before -->
<img src="{{ getImage(getFilePath('logoFavicon') . '/logo_light.png', getFileSize('logoFavicon')) }}" alt="Logo">
```

## Examples

### CSS Files
```blade
<!-- Before -->
<link rel="stylesheet" href="{{ asset('assets/universal/css/bootstrap.css') }}">

<!-- After -->
<link rel="stylesheet" href="{{ custom_asset('assets/universal/css/bootstrap.css') }}">
```

### JavaScript Files
```blade
<!-- Before -->
<script src="{{ asset('assets/universal/js/jquery-3.7.1.min.js') }}"></script>

<!-- After -->
<script src="{{ custom_asset('assets/universal/js/jquery-3.7.1.min.js') }}"></script>
```

### Images
```blade
<!-- Before -->
<img src="{{ asset('assets/images/site/home/hero_bg.png') }}" alt="Hero">

<!-- After -->
<img src="{{ custom_asset('assets/images/site/home/hero_bg.png') }}" alt="Hero">
```

## Switching Between Environments

### Local Development
```env
ASSETS_URL=http://localhost:8000
```

### Live Server
```env
ASSETS_URL=https://yourdomain.com
```

### CDN
```env
ASSETS_URL=https://cdn.yourdomain.com
```

## Benefits

1. **Easy Environment Switching**: Change one variable to switch between local and live assets
2. **CDN Support**: Easily point to CDN URLs for better performance
3. **No Code Changes**: Switch environments without modifying Blade templates
4. **Backward Compatibility**: Existing `asset()` helper continues to work

## Migration

To migrate existing templates:

1. Find all instances of `{{ asset('assets/...') }}`
2. Replace with `{{ custom_asset('assets/...') }}`
3. Update your `.env` file with the appropriate `ASSETS_URL`

## Testing

After making changes, test by:
1. Setting `ASSETS_URL=http://localhost:8000` in `.env`
2. Refreshing the page and checking image sources in browser dev tools
3. Changing to live URL and verifying assets load from correct domain
