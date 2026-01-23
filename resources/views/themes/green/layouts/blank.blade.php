@php
if(isset($_GET['test'])){   die('home');
}
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title', (bs('site_name') ?? 'FundGreen') . ' – Crowdfunding')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

@yield('custom-css');

<style>
/* Header Simple Styles */
.simple-header {
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-flex {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.header-left {
    flex: 0 0 auto;
    min-width: 100px;
}

.header-center {
    flex: 1 1 auto;
    text-align: center;
}

.header-center img {
    max-height: 50px;
    width: auto;
    object-fit: contain;
}

.header-right {
    flex: 0 0 auto;
    min-width: 100px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

/* User Wrapper */
.user-wrapper {
    position: relative;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

/* User Dropdown */
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
    color: #28a745;
    padding-left: 25px;
}

/* Login Button */
.header-right .btn-outline-success {
    border-color: #28a745;
    color: #28a745;
    font-weight: 500;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.header-right .btn-outline-success:hover {
    background: #28a745;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .header-flex {
        gap: 10px;
    }
    
    .header-left,
    .header-right {
        min-width: 60px;
    }
    
    .header-center img {
        max-height: 40px;
    }
    
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

@media (max-width: 480px) {
    .simple-header {
        padding: 10px 0;
    }
    
    .header-center img {
        max-height: 35px;
    }
    
    .header-right .btn-outline-success {
        padding: 6px 15px;
        font-size: 13px;
    }
}
</style>
</head>
<body>

<!-- NAVBAR -->
@include(activeTheme() . 'partials.header-simple')

@yield('frontend')

<!-- FOOTER -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@yield('script')
</body>
</html>
