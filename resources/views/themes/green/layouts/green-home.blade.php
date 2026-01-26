@php
if(isset($_GET['test'])){   die('home');
}
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $setting->siteName(__($pageTitle)) }}</title>
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

/* HERO */
.hero{
  position:relative;
  color:#fff;
  padding:120px 0 100px;
     background: url(https://apnacrowdfunding.com/apnafund/assets/images/banner-12.jpg);
  background-size:cover;
  background-position:center;
  background-repeat:no-repeat;
}
.hero h1{
  font-weight:700;
  line-height:1.15;
  font-size:clamp(2rem,4vw,3.2rem);
}
.hero p{
  font-size:clamp(.95rem,2.5vw,1.05rem);
  max-width:520px;
  opacity:.95;
}

/* STATS */
.stats-box h4{
  font-weight:700;
  color:var(--green);
  font-size:clamp(1.4rem,3vw,1.7rem);
}
.stats-box p{
  margin:0;
  font-size:.9rem;
  color:#6b7280;
}

/* CATEGORY */
.category-btn{
  border-radius:14px;
  padding:6px 18px;
  font-size:.85rem;
  text-decoration:none;
}

/* CAMPAIGN CARD */
.campaign-card{
  border:none;
  border-radius:14px;
  overflow:hidden;
  background:#fff;
  box-shadow:0 10px 30px rgba(0,0,0,.06);
  transition:.25s;
}
.campaign-card:hover{
  transform:translateY(-6px);
  box-shadow:0 20px 45px rgba(0,0,0,.1);
}
.campaign-img{
  height:220px;
  width:100%;
  object-fit:cover;
}
.progress{
  height:6px;
  border-radius:10px;
}
.progress-bar{
  background:var(--green);
}

/* CTA */
.cta{
  background:var(--green);
  color:#fff;
  padding:90px 15px;
  text-align:center;
}
.cta h2{
  font-weight:700;
  font-size:clamp(1.6rem,4vw,2.2rem);
}

/* FOOTER */
.fundgreen-footer{
  background:#f3f4f6;
  font-size:14px;
}
.footer-links a{
  text-decoration:none;
  color:#6b7280;
  font-size:13px;
}
.footer-links a:hover{
  color:var(--green);
}
.footer-bottom{
  border-top:1px solid #e5e7eb;
  padding:14px 0;
  background:#f3f4f6;
}

/* ===== MOBILE FIXES ===== */
@media(max-width:768px){
  .hero{
    padding:90px 0 70px;
    text-align:center;
  }
  .hero p{
    margin-left:auto;
    margin-right:auto;
  }
  .stats-box{
    padding:10px 0;
  }
}
/* ===== SMALL MOBILE ===== */
@media(max-width:480px){
  .campaign-img{
    height:190px;
  }
  .category-btn{
    font-size:.8rem;
    padding:5px 14px;
  }
}
/* =========================
   HERO SECTION FIX
========================= */

.hero{
  position: relative;
  color: #ffffff;
  padding: 120px 0 100px;
  overflow: hidden;
}

/* Overlay ONLY for background */
.hero::before{
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to right,
    rgba(0,0,0,0.65) 0%,
    rgba(0,0,0,0.55) 30%,
    rgba(0,0,0,0.30) 45%,
    rgba(0,0,0,0.05) 55%,
    rgba(0,0,0,0.00) 100%
  );
  z-index: 1;
}

/* Content above overlay */
.hero .container{
  position: relative;
  z-index: 2;
}

/* Heading clarity */
.hero h1{
  font-size: 3rem;
  font-weight: 800;
  line-height: 1.2;
  color: #ffffff;
  text-shadow: 0 6px 18px rgba(0,0,0,0.45);
}

/* First word highlight (Crowd / By / For) */
.hero h1 span{
  color: #2ecc71;
}

/* Description */
.hero p{
  font-size: 1.05rem;
  max-width: 580px;
  color: #f1f1f1;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

/* Button fix */
.hero .btn{
  font-weight: 600;
}
.project-image {
    height: 290px !important;
    background-size: contain !important;
}

</style>
</head>
<body>

<!-- NAVBAR -->
@include(activeTheme() . 'partials.header-new')

<!-- CONTENT -->
@yield('content')

<!-- FOOTER -->
@include(activeTheme() . 'partials.footer-new')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@stack('scripts')
</body>
</html>
