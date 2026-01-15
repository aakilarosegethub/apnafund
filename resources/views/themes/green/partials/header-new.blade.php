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
          alt="Apna Crowdfunding Logo"
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
          <form class="position-relative">
            <input 
              type="text" 
              class="form-control rounded-pill ps-4 pe-5"
              placeholder="Search campaigns..."
            >
            <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
          </form>
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
