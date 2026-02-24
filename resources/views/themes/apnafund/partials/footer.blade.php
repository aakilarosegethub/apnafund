<!-- Footer -->
<footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>ApnaCrowdfunding</h4>
                    <img src="{{ getImage(getFilePath('logoFavicon') . '/logo_light.png', getFileSize('logoFavicon')) }}" alt="Apna Crowdfunding Logo" class="footer-logo-img">
                        <p class="footer-tagline">{{ __(@$footerContent->data_info->footer_text) }}</p>
                    
                </div>

                <div class="footer-section">
                    <h4>About</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ url('about') }}">About</a></li>
                        <li><a href="{{ url('contact') }}">Contact</a></li>
                        <li><a href="http://apnacrowdfunding.com/blog">Blog</a></li>
                        
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
                <p>&copy; 2010-2025 Apna Crowdfunding. All rights reserved.</p>
            </div>
        </div>
    </footer>

@if(!empty($whatsappChatbotNumber))
<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappChatbotNumber) }}" target="_blank" class="whatsapp-chatbot-fab" title="Chat with us">
    <i class="fab fa-whatsapp"></i>
</a>
<style>
.whatsapp-chatbot-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 56px;
    height: 56px;
    background: #25d366;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.5);
    z-index: 9999;
    transition: transform 0.2s;
}
.whatsapp-chatbot-fab:hover {
    color: #fff;
    transform: scale(1.08);
}
@media (max-width: 768px) {
    .whatsapp-chatbot-fab {
        bottom: 16px;
        right: 16px;
        width: 48px;
        height: 48px;
        font-size: 24px;
    }
}
</style>
@endif