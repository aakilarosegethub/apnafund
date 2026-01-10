<!-- NAVBAR -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm">
  <div class="container">

    <!-- Logo -->
    <a href="{{ route('home') }}" class="navbar-brand">
      <img 
        src="{{ asset('assets/universal/images/logoFavicon/logo_light.png') }}" 
        alt="Apna Crowdfunding Logo"
        style="max-height:68px;">
    </a>

    <!-- Hamburger -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible Content -->
    <div class="collapse navbar-collapse" id="mainNav">

      <!-- Center Search with Icon (END) -->
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
</nav>
