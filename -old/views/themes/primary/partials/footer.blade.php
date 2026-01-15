<!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-logo">
                        <img src="{{ getImage(getFilePath('logoFavicon') . '/logo_light.png') }}" alt="Apna Crowdfunding Logo" class="footer-logo-img">
                        <p class="footer-tagline">{{ __(@$footerContent->data_info->footer_text) }}</p>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="footer-info">
                        <p class="copyright">{{ __(@$footerContent->data_info->copyright_text) }}</p>
                        @if($footerContactElements && count($footerContactElements) > 0)
                            @foreach($footerContactElements as $contact)
                                @if(str_contains(strtolower($contact->data_info->data), 'email'))
                                    <p class="email">{{ $contact->data_info->data }}</p>
                                    @break
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </footer>