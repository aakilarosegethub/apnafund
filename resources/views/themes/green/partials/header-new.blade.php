<!-- NAVBAR -->
<style type="">
  /* CATEGORY BAR */
.navbar-categories{
    display:flex;
    gap:20px;
    padding:10px 0 8px;
    border-top:1px solid #e5e5e5;
    justify-content:center;
    flex-wrap:wrap;
}

.navbar-categories a{
    font-size:14px;
    font-weight:500;
    color:#000;
    text-decoration:none;
    white-space:nowrap;
    transition:color .2s ease;
}

.navbar-categories a:hover{
    color:#05ce78;
}

/* Mobile horizontal scroll */
@media(max-width:768px){
    .navbar-categories{
        justify-content:flex-start;
        flex-wrap:nowrap;
        overflow-x:auto;
        padding-bottom:6px;
    }
}

/* Search Container */
.search-container {
    position: relative;
    width: 100%;
}

.search-box {
    position: relative;
    width: 100%;
}

.search-input {
    width: 100%;
    padding: 12px 50px 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
    transition: all 0.3s ease;
    background: #fff;
    color: #000;
}

.search-input:focus {
    border-color: #05ce78;
    box-shadow: 0 0 0 2px rgba(5, 206, 120, 0.2);
}

.search-input::placeholder {
    color: #999;
}

.search-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #666;
    font-size: 16px;
    cursor: pointer;
    padding: 4px;
    transition: color 0.3s ease;
}

.search-btn:hover {
    color: #05ce78;
}

/* Mobile adjustments for search */
@media (max-width: 768px) {
    .search-input {
        font-size: 16px;
        padding: 12px 45px 12px 16px;
    }
    
    /* Mobile login/signup links styling */
    .navbar-collapse .nav-link {
        padding: 8px 0;
        display: inline-block;
    }
    
    .navbar-collapse .btn {
        margin-top: 8px;
    }
}

@media (max-width: 576px) {
    .search-input {
        font-size: 15px;
        padding: 10px 40px 10px 14px;
    }
}

/* User Avatar & Dropdown */
.user-wrapper {
    position: relative;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
    border: 2px solid #e9ecef;
}

.user-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.user-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    min-width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1001;
    padding: 8px 0;
}

.user-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.user-dropdown a {
    display: block;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.user-dropdown a:last-child {
    border-bottom: none;
}

.user-dropdown a:hover {
    background: #f8f9fa;
    color: #16a34a;
    padding-left: 25px;
}

/* Responsive for user avatar */
@media (max-width: 768px) {
    .user-avatar {
        width: 35px;
        height: 35px;
        font-size: 12px;
    }
    
    .user-dropdown {
        min-width: 160px;
        right: -10px;
    }
}

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm">
  <div class="container flex-column">

    <!-- TOP ROW -->
    <div class="w-100 d-flex align-items-center">

      <!-- Logo -->
      <a href="{{ route('home') }}" class="navbar-brand me-3">
        <img 
          src="{{ asset('assets/universal/images/logoFavicon/logo_light.png') }}" 
          alt="{{ bs('site_name') ?? 'Apna Crowdfunding' }}"
          style="max-height:68px;">
      </a>

      <!-- Hamburger -->
      <button class="navbar-toggler border-0 shadow-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Collapsible Content -->
      <div class="collapse navbar-collapse" id="mainNav">

        <!-- Center Search -->
        <div class="mx-lg-auto my-3 my-lg-0" style="max-width:420px; width:100%;">
          <div class="search-container">
            <form class="search-box" method="get" action="{{ url('campaigns') }}">
              <input type="text" class="search-input" name="name" placeholder="Search projects, creators, and categories..." aria-label="Search">
              <button class="search-btn" type="submit">
                <i class="fas fa-search"></i>
              </button>
            </form>
          </div>
        </div>

        <!-- Right Buttons -->
        <div class="ms-lg-3 d-flex align-items-center gap-2 flex-wrap justify-content-end">
          @auth
            @php
                $user = auth()->user();
                $userName = $user ? ($user->fullname ?? $user->name ?? null) : null;
                $initials = 'U';

                if ($userName) {
                    $parts = explode(' ', $userName);
                    $initials = '';
                    foreach ($parts as $part) {
                        if (! empty($part)) {
                            $initials .= mb_substr($part, 0, 1);
                        }
                    }
                    $initials = strtoupper($initials);
                }
            @endphp

            <a class="btn btn-success rounded-pill px-4" href="{{ route('start.project') }}">
              Start a Campaign
            </a>

            <div class="user-wrapper">
              <div class="user-avatar" id="userAvatar">
                @if(!empty($user->image))
                  <img
                    src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile')) }}"
                    alt="{{ $userName }}"
                  >
                @else
                  {{ $initials }}
                @endif
              </div>

              <div class="user-dropdown" id="userDropdown">
                <a href="{{ route('user.dashboard') }}">Dashboard</a>
                <a href="{{ route('user.profile') }}">Profile</a>
                <a href="{{ route('user.logout') }}">Logout</a>
              </div>
            </div>
          @else
            <a class="nav-link text-dark fw-medium" href="{{ route('user.login') }}" style="text-decoration: none; color: #374151 !important; font-size: 14px;">
              Log in
            </a>
            <a class="btn btn-success rounded-pill px-4" href="{{ route('user.register') }}" style="background: #16a34a; border-color: #16a34a;">
              Sign up
            </a>
          @endauth
        </div>

      </div>
    </div>

    <!-- 🔽 CATEGORIES ROW (DYNAMIC) -->
    <div class="navbar-categories w-100 mt-2">
      @php
        $headerCategories = \App\Models\Admins\HeaderCategory::where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
      @endphp
      
      @forelse($headerCategories as $headerCategory)
      

        <a href="{{ url('/') }}/campaigns/category/{{ $headerCategory->slug }}">{{ __($headerCategory->label) }}</a>
      @empty
        {{-- Fallback to default categories if no header categories exist --}}
        <a href="#">Art/Crafts</a>
        <a href="#">Games/Comics</a>
        <a href="#">Film/Theatre</a>
        <a href="#">Dance/Music</a>
        <a href="#">Fashion/Design</a>
        <a href="#">Education/Journalism</a>
        <a href="#">Photography/Publishing</a>
        <a href="#">Software/Technology</a>
      @endforelse
    </div>

  </div>
</nav>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle search form in header
    const searchForms = document.querySelectorAll('.search-box');
    searchForms.forEach(function(form) {
      const searchInput = form.querySelector('.search-input');
      
      if (searchInput) {
        // Submit on Enter key (form will auto-submit, but we can add validation)
        searchInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter' && !searchInput.value.trim()) {
            e.preventDefault();
            return false;
          }
        });
      }
    });

    // Handle user avatar dropdown
    var avatar = document.getElementById('userAvatar');
    var dropdown = document.getElementById('userDropdown');

    if (avatar && dropdown) {
      avatar.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
      });

      document.addEventListener('click', function () {
        dropdown.classList.remove('show');
      });
    }
  });
</script>
@endpush
