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
}

@media (max-width: 576px) {
    .search-input {
        font-size: 15px;
        padding: 10px 40px 10px 14px;
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
          alt="{{ bs('site_name') ?? 'Apna Crowdfunding' }} Logo"
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

        <!-- Right Button -->
        <div class="ms-lg-3 text-lg-end">
          @auth
            <a class="btn btn-success rounded-pill px-4 w-100 w-lg-auto" href="{{ route('start.project') }}">
              Start a Campaign
            </a>
          @else
            <a class="btn btn-success rounded-pill px-4 w-100 w-lg-auto" href="{{ route('user.login') }}">
              Start a Campaign
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
        <a href="{{ route('home') }}?category={{ $headerCategory->slug }}">{{ __($headerCategory->label) }}</a>
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
  });
</script>
@endpush
