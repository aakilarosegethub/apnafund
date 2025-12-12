<header class="header">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <!-- Left: Logo -->
            <div class="col-12 col-md-2 text-center text-md-start">
                <a href="/">
                    <img src="{{ asset('apnafund/assets/images/White Logo.png') }}" alt="Apna Crowdfunding Logo" class="logo-img"
                        style="max-height:50px;">
                </a>
            </div>

            <!-- Center: Search Bar -->
            <div class="col-12 col-md-8">
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" class="search-input"
                            placeholder="Search projects, creators, and categories..." aria-label="Search">
                        <button class="search-btn" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Buttons -->
            <div class="col-12 col-md-2 text-center text-md-end">
                <a href="creator.html" class="btn btn-outline-light me-2">Creator</a>
                <a href="{{ route('user.login.form') }}" class="btn text-white">Login</a>
            </div>
        </div>
    </div>
</header> 