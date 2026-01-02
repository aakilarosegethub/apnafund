@php
    $footerContent = getSiteData('footer.content', true);
    $footerElements = getSiteData('footer.element', false, null, true);
    $categories = \App\Models\Category::active()->get();
@endphp

<!-- FOOTER -->
<footer class="fundgreen-footer">
  <div class="container py-5">
    <div class="row gy-4">
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-bold text-success">FundGreen</h6>
        <p class="text-muted small">{{ @$footerContent->data_info->footer_text ?? 'Community-powered crowdfunding platform.' }}</p>
      </div>
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-semibold">Explore</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="{{ route('campaign') }}">Browse</a></li>
          <li><a href="{{ route('campaign') }}">Categories</a></li>
          <li><a href="{{ route('campaign') }}">Featured</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-semibold">Support</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="{{ url('faq') }}">FAQ</a></li>
          <li><a href="{{ url('contact') }}">Contact</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h6 class="fw-semibold">Company</h6>
        <ul class="list-unstyled footer-links">
          <li><a href="{{ url('about') }}">About</a></li>
          <li><a href="http://apnacrowdfunding.com/blog">Blog</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-bottom text-center">
    <small class="text-muted">© {{ date('Y') }} FundGreen. {{ @$footerContent->data_info->copyright_text ?? 'All rights reserved.' }}</small>
  </div>
</footer>

