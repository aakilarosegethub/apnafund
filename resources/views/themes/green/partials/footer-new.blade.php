@php
    $footerContent = getSiteData('footer.content', true);
    $footerElements = getSiteData('footer.element', false, null, true);
    $categories = \App\Models\Category::active()->get();
@endphp

<!-- Tabler Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<section class="cta">
  <h2>Ready to Start Something Big?</h2>
  <p class="mt-2">Launch your campaign and reach thousands of backers.</p>
  @auth
      <a href="{{ route('start.project') }}" class="btn btn-light px-4 mt-3">Start a Campaign</a>
  @else
      <a href="{{ route('user.login') }}" class="btn btn-light px-4 mt-3">Start a Campaign</a>
  @endauth
</section>
<!-- FOOTER -->
<footer class="fundgreen-footer bg-light">
  <div class="container py-5">
    <div class="row gy-4">

      <!-- Brand -->
      <div class="col-lg-3 col-md-6">
        @php
          // Check if footer logo exists in database
          $footerLogo = @$footerContent->data_info['footer_logo'] ?? null;
          
          if ($footerLogo) {
            // Use footer logo from database
            if (filter_var($footerLogo, FILTER_VALIDATE_URL)) {
              // External URL
              $logoUrl = $footerLogo;
            } else {
              // Local file
              $logoUrl = getImage('assets/images/site/footer/' . $footerLogo, '180x40');
            }
          } else {
            // Fallback to default logo
            $logoPath = getFilePath('logoFavicon') . '/logo_light.png';
            $logoFile = public_path($logoPath);
            clearstatcache(true, $logoFile);
            $logoLightVersion = file_exists($logoFile) ? filemtime($logoFile) : time();
            $logoUrl = getImage($logoPath, getFileSize('logoFavicon')) . '?v=' . $logoLightVersion . '&t=' . time();
          }
        @endphp
        <a href="{{ route('home') }}" class="d-inline-block mb-3">
          <img
            src="{{ $logoUrl }}"
            alt="{{ bs('site_name') ?? 'Apna Crowdfunding' }} Logo"
            style="height: 40px; max-width: 180px; object-fit: contain;"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"
          >
          <span style="display: none; font-size: 1.5rem; font-weight: bold; color: #198754;">{{ bs('site_name') ?? 'FundGreen' }}</span>
        </a>
        @if(!empty(@$footerContent->data_info['footer_text']))
        <p class="text-muted small lh-lg">
          {{ @$footerContent->data_info['footer_text'] }}
        </p>
        @endif
      </div>

      @php
        // Get footer menu items from database
        $footerMenuItems = \App\Models\SiteData::where('data_key', 'footer_menu.element')
            ->orderBy('id', 'asc')
            ->get();

        // Group menu items by section_type and filter by status
        $aboutItems = [];
        $supportItems = [];
        $moreItems = [];
        $downItems = [];

        foreach ($footerMenuItems as $item) {
            $itemData = is_array($item->data_info) ? $item->data_info : (array) $item->data_info;

            // Check if status is active (1 or '1')
            // If status field doesn't exist, consider it active by default (for backward compatibility)
            $status = isset($itemData['status']) ? $itemData['status'] : '1';

            // Only skip if status is explicitly set to '0' or 0
            if (isset($itemData['status']) && ($status == '0' || $status == 0)) {
                continue; // Skip inactive items
            }

            $sectionType = isset($itemData['section_type']) ? $itemData['section_type'] : '';

            if ($sectionType == 'about') {
                $aboutItems[] = $item;
            } elseif ($sectionType == 'support') {
                $supportItems[] = $item;
            } elseif ($sectionType == 'more_from_apnacrowdfunding') {
                $moreItems[] = $item;
            } elseif ($sectionType == 'down_section') {
                $downItems[] = $item;
            }
        }

        // Sort each section by sort_order (lower numbers first)
        usort($aboutItems, function($a, $b) {
            $sortA = isset($a->data_info['sort_order']) ? (int)$a->data_info['sort_order'] : 999999;
            $sortB = isset($b->data_info['sort_order']) ? (int)$b->data_info['sort_order'] : 999999;
            if ($sortA == $sortB) {
                return $a->id - $b->id; // If sort_order is same, sort by ID
            }
            return $sortA - $sortB;
        });

        usort($supportItems, function($a, $b) {
            $sortA = isset($a->data_info['sort_order']) ? (int)$a->data_info['sort_order'] : 999999;
            $sortB = isset($b->data_info['sort_order']) ? (int)$b->data_info['sort_order'] : 999999;
            if ($sortA == $sortB) {
                return $a->id - $b->id;
            }
            return $sortA - $sortB;
        });

        usort($moreItems, function($a, $b) {
            $sortA = isset($a->data_info['sort_order']) ? (int)$a->data_info['sort_order'] : 999999;
            $sortB = isset($b->data_info['sort_order']) ? (int)$b->data_info['sort_order'] : 999999;
            if ($sortA == $sortB) {
                return $a->id - $b->id;
            }
            return $sortA - $sortB;
        });

        usort($downItems, function($a, $b) {
            $sortA = isset($a->data_info['sort_order']) ? (int)$a->data_info['sort_order'] : 999999;
            $sortB = isset($b->data_info['sort_order']) ? (int)$b->data_info['sort_order'] : 999999;
            if ($sortA == $sortB) {
                return $a->id - $b->id;
            }
            return $sortA - $sortB;
        });
      @endphp
      <!-- Support Section -->
      @if(count($supportItems) > 0)
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-semibold mb-3">Support</h6>
          <ul class="list-unstyled footer-links">
            @foreach($supportItems as $item)
              @php
                $itemData = is_array($item->data_info) ? $item->data_info : (array) $item->data_info;
                $menuLabel = $itemData['menu_label'] ?? '';
                $slug = trim($itemData['slug'] ?? '#');

                // If slug is a full URL (http/https/www) OR already an absolute path (/xyz), use as is.
                if (preg_match('/^https?:\/\//i', $slug) || preg_match('/^www\./i', $slug) || str_starts_with($slug, '/')) {
                    $url = $slug;
                } else {
                    // Treat everything else as relative path under current domain
                    $url = url($slug);
                }
              @endphp
              <li><a href="{{ $url }}">{{ __($menuLabel) }}</a></li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- About Section -->
      @if(count($aboutItems) > 0)
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-semibold mb-3">About</h6>
          <ul class="list-unstyled footer-links">
            @foreach($aboutItems as $item)
              @php
                $itemData = is_array($item->data_info) ? $item->data_info : (array) $item->data_info;
                $menuLabel = $itemData['menu_label'] ?? '';
                $slug = trim($itemData['slug'] ?? '#');

                if (preg_match('/^https?:\/\//i', $slug) || preg_match('/^www\./i', $slug) || str_starts_with($slug, '/')) {
                    $url = $slug;
                } else {
                    $url = url($slug);
                }
              @endphp
              <li><a href="{{ $url }}">{{ __($menuLabel) }}</a></li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- More from Apnacrowdfunding Section -->
      @if(count($moreItems) > 0)
        <div class="col-lg-3 col-md-6">
          <h6 class="fw-semibold mb-3">Additional Information</h6>
          <ul class="list-unstyled footer-links">
            @foreach($moreItems as $item)
              @php
                $itemData = is_array($item->data_info) ? $item->data_info : (array) $item->data_info;
                $menuLabel = $itemData['menu_label'] ?? '';
                $slug = trim($itemData['slug'] ?? '#');

                if (preg_match('/^https?:\/\//i', $slug) || preg_match('/^www\./i', $slug) || str_starts_with($slug, '/')) {
                    $url = $slug;
                } else {
                    $url = url($slug);
                }
              @endphp
              <li><a href="{{ $url }}">{{ __($menuLabel) }}</a></li>
            @endforeach
          </ul>
        </div>
      @endif

    </div>
  </div>

  <!-- Bottom -->
  <div class="footer-bottom py-4">
    <div class="container">
      <div class="row align-items-center">
        <!-- Social Media Links -->
        @if($footerElements && count($footerElements) > 0)
          <div class="col-12 text-center mb-3 mb-md-0">
            <div class="social-media-links d-flex justify-content-center gap-3">
              @foreach ($footerElements as $socialInfo)
                @php
                  $socialDataInfo = is_array($socialInfo->data_info) ? $socialInfo->data_info : (array) $socialInfo->data_info;
                  $socialIcon = @$socialDataInfo['social_icon'] ?? '';
                  $socialUrl = @$socialDataInfo['url'] ?? '#';
                @endphp
                @if($socialIcon && $socialUrl)
                  <a href="{{ $socialUrl }}" class="social-link" target="_blank" rel="noopener noreferrer">
                    {!! $socialIcon !!}
                  </a>
                @endif
              @endforeach
            </div>
          </div>
        @endif

        <!-- Copyright -->
        <div class="col-12 text-center">
          <small class="text-muted">
            © {{ date('Y') }} ApnaCrowdfunding a subsidiary of Aakilarose, Inc. a California Company
          </small>
        </div>
        <!-- Down Section - Horizontal List -->
        @if(count($downItems) > 0)
          <div class="col-12 text-center mb-3">
            <div class="footer-down-menu d-flex justify-content-center align-items-center flex-wrap gap-3">
              @foreach($downItems as $item)
                @php
                  $itemData = is_array($item->data_info) ? $item->data_info : (array) $item->data_info;
                  $menuLabel = $itemData['menu_label'] ?? '';
                  $slug = trim($itemData['slug'] ?? '#');

                  if (preg_match('/^https?:\/\//i', $slug) || preg_match('/^www\./i', $slug) || str_starts_with($slug, '/')) {
                      $url = $slug;
                  } else {
                      $url = url($slug);
                  }
                @endphp
                <a href="{{ $url }}" class="footer-down-link">{{ __($menuLabel) }}</a>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</footer>

<!-- FOOTER STYLES -->
<style>
.fundgreen-footer {
  border-top: 1px solid #e9ecef;
}

.footer-links li {
  margin-bottom: 10px;
}

.footer-links a {
  text-decoration: none;
  color: #6c757d;
  font-size: 14px;
  transition: all 0.3s ease;
}

.footer-links a:hover {
  color: #198754;
  padding-left: 6px;
}

.footer-bottom {
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
}

.social-media-links {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
}

.social-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #198754;
  color: #fff !important;
  text-decoration: none;
  font-size: 18px;
  transition: all 0.3s ease;
}

.social-link:hover {
  background: #157347;
  color: #fff !important;
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.social-link i {
  display: inline-block;
  color: #fff !important;
}

.social-link:hover i {
  color: #fff !important;
}

.social-link * {
  color: #fff !important;
}

.social-link:hover * {
  color: #fff !important;
}

.footer-down-menu {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  padding: 15px 0;
}

.footer-down-link {
  color: #6c757d;
  text-decoration: none;
  font-size: 14px;
  padding: 5px 10px;
  transition: all 0.3s ease;
  position: relative;
}

.footer-down-link:hover {
  color: #198754;
  text-decoration: none;
}

.footer-down-link:not(:last-child)::after {
  content: '|';
  position: absolute;
  right: -12px;
  color: #dee2e6;
  font-weight: 300;
  font-size: 12px;
}
</style>
