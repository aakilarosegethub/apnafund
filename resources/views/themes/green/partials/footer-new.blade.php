@php
    $footerContent = getSiteData('footer.content', true);
    $footerElements = getSiteData('footer.element', false, null, true);
    $categories = \App\Models\Category::active()->get();
@endphp
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
          $logoPath = getFilePath('logoFavicon') . '/logo_light.png';
          $logoFile = public_path($logoPath);
          clearstatcache(true, $logoFile);
          $logoLightVersion = file_exists($logoFile) ? filemtime($logoFile) : time();
          $logoUrl = getImage($logoPath, getFileSize('logoFavicon')) . '?v=' . $logoLightVersion . '&t=' . time();
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
        <p class="text-muted small lh-lg">
          {{ @$footerContent->data_info->footer_text ?? 'Community-powered crowdfunding platform.' }}
        </p>
      </div>

      <!-- Explore -->
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-semibold mb-3">Explore</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="{{ route('campaign') }}">Browse Campaigns</a></li>
          <li><a href="{{ route('campaign') }}">Categories</a></li>
          <li><a href="{{ route('campaign') }}">Featured</a></li>
        </ul>
      </div>

      <!-- Support -->
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-semibold mb-3">Support</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="{{ url('faq') }}">FAQ</a></li>
          <li><a href="{{ url('contact') }}">Contact Us</a></li>
        </ul>
      </div>

      <!-- Company -->
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-semibold mb-3">Company</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="{{ url('about') }}">About Us</a></li>
          <li><a href="http://apnacrowdfunding.com/blog">Blog</a></li>
        </ul>
      </div>

    </div>
  </div>

  <!-- Bottom -->
  <div class="footer-bottom text-center py-3">
    <small class="text-muted">
      © {{ date('Y') }} FundGreen.
      {{ @$footerContent->data_info->copyright_text ?? 'All rights reserved.' }}
    </small>
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
</style>
