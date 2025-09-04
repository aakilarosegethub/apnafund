<!-- Footer -->
<footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>ApnaFund</h4>
                    <img src="{{ getImage(getFilePath('logoFavicon') . '/logo_light.png') }}" alt="Apna Fund Logo" class="footer-logo-img">
                        <p class="footer-tagline">{{ __(@$footerContent->data_info->footer_text) }}</p>
                    
                </div>

                <div class="footer-section">
                    <h4>About</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ url('about') }}">About</a></li>
                        <li><a href="{{ url('contact') }}">Contact</a></li>
                        <li><a href="http://apnafund.com/blog">Blog</a></li>
                        <li><a href="{{ url('faq') }}">FAQ</a></li>
                        
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Categories</h4>
                    <ul>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ url('campaigns?category=' . urlencode($category->slug)) }}">
                                        {{ __($category->name) }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                    <li><a href="{{ url('policy/terms-of-service/12') }}">Terms of Service</a></li>
                        <li><a href="{{ url('policy/privacy-policy/11') }}">Privacy Policy</a></li>
                        <li><a href="{{ url('policy/support-policy/82') }}">Support Policy
                        </a></li>
                        <li><a href="{{ url('help') }}">Help Center</a></li>
                        <li><a href="{{ url('sitemap') }}">Sitemap</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2010-2025 Apna Fund. All rights reserved.</p>
            </div>
        </div>
    </footer>