@php
    $activeTheme = 'themes.apnafund.';
@endphp

@extends($activeTheme . 'layouts.frontend')

@section('content')

<style>
:root{
    --primary:#05ce78;
    --primary-dark:#04995a;
    --deep:#064e3b;
    --border:#e6e9ee;
    --text:#1f2937;
    --muted:#6b7280;
}

/* ================= BODY ================= */
body{
    margin:0;
    min-height:100vh;
    font-family:'Inter',sans-serif;
    background:linear-gradient(135deg,#f4fff9 0%,#dff8ec 100%);
}

/* ================= LAYOUT ================= */
.login-page-container{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
}

/* ================= LEFT ================= */
.login-illustration{
    flex:1;
    max-width:620px;
    padding:30px;
}

.illustration-container{
    height:520px;
    background: radial-gradient(circle at top, rgba(255, 255, 255, .15), transparent 60%), linear-gradient(160deg, #ffffff 0%, #6adaba 40%, #05ce78 100%);
    border-radius:28px;
    box-shadow:0 40px 90px rgba(0,0,0,.35);
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
    overflow:hidden;
}

/* subtle dark overlay */
.illustration-container::after{
    content:'';
    position:absolute;
    inset:0;
    background:rgba(0,0,0,.12);
}

/* ================= CONTENT ================= */
.illustration-content{
    text-align:center;
    color:#fff;
    position:relative;
    z-index:2;
}

/* 🔥 LOGO CLEAR & STRONG */
.illustration-content img{
    max-width:260px;
    margin-bottom:30px;
    filter:
        drop-shadow(0 10px 30px rgba(0,0,0,.6))
        drop-shadow(0 0 20px rgba(5,206,120,.5));
}

.illustration-title{
    font-size:2.6rem;
    font-weight:800;
    margin-bottom:10px;
    letter-spacing:.5px;
}

.illustration-subtitle{
    font-size:1.15rem;
    opacity:.95;
}

/* ================= LOGIN CARD ================= */
.login-form-section{
    flex:1;
    max-width:460px;
    background:#fff;
    border-radius:26px;
    box-shadow:0 35px 80px rgba(0,0,0,.15);
    padding:48px;
}

/* ================= HEADER ================= */
.login-header{
    text-align:center;
    margin-bottom:34px;
}
.login-title{
    font-size:1.9rem;
    font-weight:700;
    color:var(--text);
}
.login-subtitle{
    color:var(--muted);
    font-size:.95rem;
}

/* ================= FORM ================= */
.form-group{margin-bottom:18px;}
.form-label{
    font-size:.9rem;
    font-weight:600;
    margin-bottom:6px;
    display:block;
}

.form-control{
    width:100%;
    padding:15px 18px;
    border-radius:14px;
    border:1.6px solid var(--border);
    font-size:1rem;
}

.form-control:focus{
    outline:none;
    border-color:var(--primary);
    box-shadow:0 0 0 5px rgba(5,206,120,.15);
}

/* ================= PASSWORD ================= */
.input-group{position:relative;}
.input-group-text{
    position:absolute;
    right:16px;
    top:50%;
    transform:translateY(-50%);
    border:none;
    background:none;
    cursor:pointer;
    color:#6b7280;
}

/* ================= CHECK ================= */
.form-check{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin:18px 0;
}

/* ================= BUTTON ================= */
.btn-theme{
    width:100%;
    height:54px;
    border-radius:16px;
    border:none;
    background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    color:#fff;
    font-weight:600;
    font-size:16px;
    cursor:pointer;
    box-shadow:0 18px 40px rgba(5,206,120,.45);
    transition:.25s;
}
.btn-theme:hover{transform:translateY(-2px);}

/* ================= SOCIAL ================= */
.divider{
    text-align:center;
    margin:30px 0;
    position:relative;
}
.divider::before{
    content:'';
    height:1px;
    background:var(--border);
    position:absolute;
    top:50%;
    left:0;
    right:0;
}
.divider span{
    background:#fff;
    padding:0 14px;
    color:var(--muted);
    font-size:14px;
    position:relative;
}

.social-login-buttons{
    display:flex;
    gap:14px;
    justify-content:center;
}

.social-btn{
    padding:11px 20px;
    border-radius:30px;
    border:1px solid var(--border);
    text-decoration:none;
    font-weight:500;
    color:#111;
    display:flex;
    align-items:center;
    gap:8px;
    transition:.25s;
}
.facebook-btn:hover{background:#1877f2;color:#fff;}
.google-btn:hover{background:#db4437;color:#fff;}

/* ================= FOOTER ================= */
.login-footer{
    text-align:center;
    margin-top:26px;
    font-size:.9rem;
}

/* ================= MOBILE ================= */
@media(max-width:768px){
    .login-illustration{display:none;}
    .login-form-section{padding:36px;}
    }
</style>

<div class="login-page-container">

    {{-- LEFT --}}
    <div class="login-illustration">
        <div class="illustration-container">
            <div class="illustration-content">
                <a href="{{ url('/') }}">
                    <img src="{{ getImage(getFilePath('logoFavicon').'/logo_light.png',getFileSize('logoFavicon')) }}">
                </a>
              
                <p class="illustration-subtitle">Secure & Easy Fundraising Platform</p>
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="login-form-section">

        <div class="login-header">
            <h2 class="login-title">Welcome Back 👋</h2>
            <p class="login-subtitle">Login to continue</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
            </div>
        @endif

        <form action="{{ route('user.login') }}" method="POST">
            @csrf
            @if(!empty($redirectUrl))
                <input type="hidden" name="redirect" value="{{ $redirectUrl }}">
            @endif

            <div class="form-group">
                <label class="form-label">Username or Email</label>
                <input class="form-control" name="username" value="{{ old('username') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password" required>
                    <button type="button" class="input-group-text" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-check">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="{{ route('user.password.request.form') }}">Forgot?</a>
            </div>

            <x-captcha />

            <button class="btn-theme">Log In</button>
        </form>

        <div class="divider"><span>Or</span></div>
            
            <div class="social-login-buttons">
                <a href="{{ route('user.social.facebook') }}" class="social-btn facebook-btn">
                <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="{{ route('user.social.google') }}" class="social-btn google-btn">
                <i class="fab fa-google"></i> Google
                </a>
        </div>

        <div class="login-footer">
            Don’t have an account?
            <a href="{{ route('user.register') }}">Create Account</a>
        </div>

    </div>
</div>

<script>
document.getElementById('togglePassword').onclick=function(){
    const p=document.getElementById('password');
    p.type=p.type==='password'?'text':'password';
}
</script>

@endsection
