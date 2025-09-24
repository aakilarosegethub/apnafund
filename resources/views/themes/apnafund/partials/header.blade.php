<style>
        body {
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            line-height: 1.6;
            color: #333;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Touch-friendly improvements */
        button, 
        .btn-donate,
        .btn-share,
        .btn-share-card,
        .btn-donate-card,
        .btn-submit-review,
        .btn-like-review,
        .btn-reply-review {
            min-height: 44px;
            touch-action: manipulation;
        }

        /* Prevent horizontal scroll on mobile */
        html, body {
            overflow-x: hidden;
        }

        * {
            box-sizing: border-box;
        }

        /* Header Styling */
        .header {
            background: #05ce78;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 100px;
        }

        .header .container {
            max-width: 1200px;
        }

        /* Logo Styling */
        .logo-img {
            height: 25px;
            width: auto;
            transition: all 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        /* Hide mobile elements on desktop */
        .mobile-nav-toggle,
        .mobile-menu {
            display: none;
        }

        /* Mobile Menu Styling */
        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: #05ce78;
            z-index: 9999;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .mobile-logo {
            display: flex;
            align-items: center;
        }

        .mobile-logo-img {
            height: 32px;
            margin-right: 10px;
        }

        .mobile-logo-text {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
        }

        .mobile-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
        }

        .mobile-menu-content {
            padding: 30px 20px;
        }

        .mobile-nav-links {
            margin-bottom: 40px;
        }

        .mobile-nav-link {
            display: block;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            text-decoration: none;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .mobile-nav-link:hover {
            color: #fff;
            opacity: 0.8;
            padding-left: 10px;
        }

        .mobile-start-fundraiser {
            display: block;
            background: #fff;
            color: #05ce78;
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .mobile-start-fundraiser:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        .search-container {
            position: relative;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 12px 50px 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            background: #05ce78;
            color: #fff;
        }

        .search-input:focus {
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
        }

        .search-input::placeholder {
            color: #fff;
        }

        .search-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s ease;
        }

        .search-btn:hover {
            color: #f0f0f0;
        }

        /* Mobile Navigation Toggle */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
        }

        /* Navigation - GoFundMe Style */
        .nav-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0;
            margin: 0;
            gap: 40px;
        }

        .nav-left,
        .nav-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-link {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            padding: 8px 0;
            position: relative;
        }

        .nav-link:hover {
            color: #fff;
            opacity: 0.8;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #fff;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-start-fundraiser {
            background: #fff;
            color: #05ce78;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 2px solid #fff;
        }

        .btn-start-fundraiser:hover {
            background: transparent;
            color: #fff;
            border-color: #fff;
        }

        .search-below-header {
            display: none;
        }

        /* Mobile Layout */
        @media (max-width: 768px) {
            .header {
                height: auto;
                padding: 15px 0;
            }

            /* Mobile logo size */
            .logo-img {
                height: 40px !important;
            }

            /* Hide mobile toggle and menu by default */
            .mobile-nav-toggle,
            .mobile-menu {
                display: none;
            }

            /* Stack elements vertically on mobile */
            .row {
                flex-direction: column;
                gap: 15px;
            }

            .col-md-2,
            .col-md-8 {
                width: 100%;
                max-width: 100%;
                text-align: center;
            }

            /* Hide desktop login button on mobile */
            .col-md-2:last-child {
                display: none;
            }

            /* Show mobile menu toggle */
            .mobile-nav-toggle {
                display: block;
                position: absolute;
                top: 15px;
                right: 15px;
            }

            .nav-toggle {
                display: block;
                background: none;
                border: none;
                color: #fff;
                font-size: 20px;
                cursor: pointer;
                padding: 8px;
            }

            /* Mobile menu styling */
            .mobile-menu.active {
                display: block;
                margin-top: 15px;
                text-align: center;
            }

            /* Adjust search input for mobile */
            .search-input {
                font-size: 16px;
                padding: 12px 45px 12px 16px;
            }
        }

        /* Small Mobile Screens */
        @media (max-width: 576px) {
            .header {
                padding: 12px 0;
            }

            .logo-img {
                height: 35px !important;
            }

            .nav-toggle {
                font-size: 18px;
                padding: 6px;
            }

            .search-input {
                font-size: 15px;
                padding: 10px 40px 10px 14px;
            }
        }

        /* Content area styling */
        .content-area {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .content-area h1 {
            color: #1a1a1a;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .content-area p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .content-area {
                padding: 30px 16px;
            }

            .content-area h1 {
                font-size: 2rem;
            }

            .content-area p {
                font-size: 1rem;
            }
        }

     
  
    </style>
<header class="header">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <!-- Left: Logo -->
            <div class="col-12 col-md-2 text-center text-md-start">
                <a href="{{ url('/') }}">
                    <img src="{{ getImage(getFilePath('logoFavicon') . '/logo_light.png', getFileSize('logoFavicon')) }}" alt="Apna Fund Logo" class="logo-img">
                </a>
            </div>

            <!-- Center: Search Bar -->
            <div class="col-12 col-md-8">
                <div class="search-container">
                    <form class="search-box" method="get" action="{{ url('campaigns') }}">
                        <input type="text" class="search-input" name="name" placeholder="Search projects, creators, and categories..." aria-label="Search">
                        <button class="search-btn" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Buttons -->
            <div class="col-12 col-md-2 text-center text-md-end">
                @if(auth()->check())
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ (auth()->user()->firstname)?auth()->user()->firstname:'User' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.profile') }}">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.logout') }}">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('user.login.form') }}" class="btn btn-outline-light">Creator Login</a>
                @endif
            </div>
        </div>

        <!-- Mobile Navigation Toggle (Hidden on desktop) -->
        <div class="mobile-nav-toggle">
            <button class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-header">
                <div class="mobile-logo">
                    <img src="{{ getImage(getFilePath('logoFavicon') . '/logo_light.png', getFileSize('logoFavicon')) }}" alt="Apna Fund Logo" class="mobile-logo-img">
                </div>
                <button class="mobile-close" id="mobileClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mobile-menu-content">
                <div class="mobile-nav-links">
                    <a href="{{ url('campaigns') }}" class="mobile-nav-link">Discover</a>
                    <a href="{{ url('categories') }}" class="mobile-nav-link">Categories</a>
                    <a href="{{ url('how-it-works') }}" class="mobile-nav-link">How it works</a>
                    @if(auth()->check())
                        <a href="{{ route('user.dashboard') }}" class="mobile-nav-link">Dashboard</a>
                        <a href="{{ route('user.profile') }}" class="mobile-nav-link">Profile</a>
                        <a href="{{ route('user.logout') }}" class="mobile-nav-link">Logout</a>
                    @else
                        <a href="{{ route('user.login.form') }}" class="mobile-nav-link">Sign in</a>
                    @endif
                </div>
                
                <div class="mobile-cta">
                    @if(auth()->check())
                        <a href="{{ route('user.campaign.create') }}" class="mobile-start-fundraiser">Start a fundraiser</a>
                    @else
                        <a href="{{ route('user.register') }}" class="mobile-start-fundraiser">Start a fundraiser</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Alternative method if Bootstrap is not available
    var userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            var dropdownMenu = this.nextElementSibling;
            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                dropdownMenu.classList.toggle('show');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                var dropdownMenu = userDropdown.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    dropdownMenu.classList.remove('show');
                }
            }
        });
    }

    // Mobile Navigation Toggle
    const navToggle = document.getElementById('navToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileClose = document.getElementById('mobileClose');
    
    // Open mobile menu
    if (navToggle && mobileMenu) {
        navToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
    }

    // Close mobile menu
    if (mobileClose && mobileMenu) {
        mobileClose.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        });
    }

    // Close mobile menu when clicking on a navigation link
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link, .mobile-start-fundraiser');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // Close mobile menu when clicking outside (on overlay)
    if (mobileMenu) {
        mobileMenu.addEventListener('click', function(event) {
            if (event.target === mobileMenu) {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Close mobile menu with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Search functionality
    const searchForms = document.querySelectorAll('.search-box');
    
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const searchInput = form.querySelector('.search-input');
            if (!searchInput.value.trim()) {
                e.preventDefault();
                searchInput.focus();
                return false;
            }
            // Form will submit naturally to the action URL
        });
    });
});
</script> 