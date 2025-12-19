<header class="header">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <!-- Left: Logo -->
            <div class="col-12 col-md-2 text-center text-md-start">
                <a href="/">
                    <img src="{{ asset('apnafund/assets/images/White Logo.png') }}" alt="Apna Fund Logo" class="logo-img"
                        style="max-height:50px;">
                </a>
            </div>

            <!-- Center: Search Bar -->
            <div class="col-12 col-md-8 d-none">
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
                @if(auth()->check())
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ auth()->user()->name ?? 'User' }}
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
                    <a href="{{ route('creators') }}" class="btn btn-outline-light me-2">Creators</a>
                    <a href="{{ route('user.login.form') }}" class="btn btn-outline-light">Creator Login</a>
                @endif
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
});
</script>
        </div>
    </div>
</header> 