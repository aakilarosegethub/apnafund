<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="{{ route('home') }}">FundGreen</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon">
        <i class="fas fa-bars"></i>
      </span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="{{ route('campaign') }}">Explore</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('campaign') }}">Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('about') }}">How it Works</a></li>
        @auth
        <li class="nav-item">
          <a class="btn btn-success ms-lg-3" href="{{ route('user.campaign.new') }}">Start a Campaign</a>
        </li>
        @else
        <li class="nav-item">
          <a class="btn btn-success ms-lg-3" href="{{ route('user.login') }}">Start a Campaign</a>
        </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

