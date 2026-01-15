<!-- Simple Header with Centered Logo -->
<header class="simple-header" style="background: white; padding: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('home') }}" class="d-inline-block">
                <img 
                    src="{{ getImage(getFilePath('logoFavicon') . '/logo_light.png', getFileSize('logoFavicon')) }}" 
                    alt="{{ bs('site_name') ?? 'Apna Crowdfunding' }} Logo"
                    style="max-height: 60px; height: auto;"
                >
            </a>
        </div>
    </div>
</header>
