

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title', bs('site_name') . ' – Crowdfunding')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

@stack('styles')

<style>
:root{
  --green:#16a34a;
}

body{
  font-family:'Inter',sans-serif;
  background:#f9fafb;
  color:#111827;
}

/* NAVBAR */
.navbar{
  padding:14px 0;
}
.navbar-brand{
  font-weight:700;
  color:var(--green)!important;
  display: flex;
  align-items: center;
  padding: 0;
}
.navbar-brand img{
  height: 40px;
  max-width: 180px;
  object-fit: contain;
  display: block;
}
@media(max-width:768px){
  .navbar-brand img{
    height: 35px;
    max-width: 150px;
  }
}

/* Simple Header */
.simple-header{
  background: white;
  padding: 20px 0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.simple-header img{
  max-height: 60px;
  height: auto;
}

</style>
</head>
<body>

<!-- NAVBAR -->
@include(activeTheme() . 'partials.header-simple')

@yield('frontend')

@stack('scripts')
@yield('script')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
