@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')

<style>
    body{
        background:#fff;
        overflow-x:hidden;
    }

    /* HERO */
    .kc-hero{
        background:#f7f7f6;
        padding:160px 15px;
        text-align:center;
    }

    .kc-hero h1{
        font-size:34px;
        font-weight:700;
        color:#1f2937;
        margin-bottom:30px;
    }

    .kc-hero p{
        max-width:820px;
        margin:0 auto;
        font-size:16px;
        line-height:1.9;
        color:#4b5563;
    }

    /* MAIN SECTION */
    .kc-section{
        padding:120px 0 180px;
    }

    .kc-item{
        margin-bottom:120px;
    }

    .kc-item .col-md-4,
    .kc-item .col-md-8{
        padding-top:20px;
    }

    .kc-number{
        font-size:14px;
        font-weight:600;
        color:#2563eb;
        margin-bottom:14px;
    }

    .kc-title{
        font-size:26px;
        font-weight:600;
        color:#111827;
        line-height:1.45;
        margin-bottom:10px;
    }

    .kc-points{
        padding-left:220px;
        margin-top:10px;
    }

    .kc-points li{
        margin-bottom:18px;
        font-size:15px;
        color:#374151;
        line-height:1.75;
    }

    /* BLUE SECTION */
    .kc-blue{
        background:#2563eb;
        margin:0 auto;
        width:54%;
        position:relative;
        top:-145px;
        display:flex;
        justify-content:center;
        align-items:center;
        border-radius:8px;
    }

    .kc-blue-inner{
        max-width:900px;
        width:100%;
        text-align:center;
        padding:45px 40px;
    }

    .kc-blue-inner h2{
        margin:0;
        font-size:21px;
        font-weight:600;
        line-height:1.65;
        color:#fff;
    }

    /* =====================
       RESPONSIVE FIXES
    ====================== */

    @media(max-width:991px){
        .kc-points{
            padding-left:40px;
        }

        .kc-blue{
            width:80%;
            top:-100px;
        }
    }

    @media(max-width:768px){
        .kc-hero{
            padding:120px 15px;
        }

        .kc-hero h1{
            font-size:28px;
        }

        .kc-title{
            font-size:22px;
        }

        .kc-item{
            margin-bottom:90px;
        }

        .kc-points{
            padding-left:20px;
        }

        /* 🔥 MOBILE RESET */
        .kc-blue{
            width:100%;
            top:0;
            border-radius:0;
            margin-top:60px;
        }

        .kc-blue-inner{
            padding:40px 20px;
        }
    }
</style>

<!-- HERO -->
<section class="kc-hero">
    <h1>
        Funding Dreams. Empowering Communities.<br>
        Building the Future Together.
    </h1>
    <p>
        True success is measured by the positive impact we create in people’s lives.
        ApnaCrowdfunding exists to help dreamers convert their ideas into reality
        through community-driven support and shared belief.
    </p>
</section>

<!-- CONTENT -->
<section class="kc-section">
    <div class="container">

        <!-- INTRO -->
        <div class="kc-item row">
            <div class="col-md-12">
                <p>
                    This mission defines what we stand for, who we are, and the commitments
                    we make to our creators, backers, and communities.
                </p>
            </div>
        </div>

        <!-- 1 -->
        <div class="kc-item row">
            <div class="col-md-4">
                <div class="kc-title">1. Our Values Shape How We Operate</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>We do things practically for our people.</li>
                    <li>We never sell user data.</li>
                    <li>We protect users’ privacy and personal information.</li>
                    <li>Consent and transparency are mandatory.</li>
                    <li>Policies are written in clear, human language.</li>
                    <li>No hidden clauses or unfair advantages.</li>
                    <li>Fairness is promised in everything we do.</li>
                </ul>

                <ul class="kc-points">
                    <li>Ethics always come before easy profits.</li>
                    <li>No misleading or exploitative campaigns.</li>
                    <li>No unethical manipulation of users.</li>
                    <li>Integrity is our top priority.</li>
                </ul>

                <ul class="kc-points">
                    <li>We encourage eco-friendly packaging and production.</li>
                    <li>Reduced environmental impact where possible.</li>
                    <li>Sustainability is our shared responsibility.</li>
                </ul>
            </div>
        </div>

        <!-- 2 -->
        <div class="kc-item row">
            <div class="col-md-4">
                <div class="kc-title">2. Inclusive & Equitable World</div>
            </div>
            <div class="col-md-8">
                <p>
                    ApnaCrowdfunding believes opportunity should never depend on privilege.
                </p>
                <ul class="kc-points">
                    <li>Underserved and rural communities.</li>
                    <li>Women-led, LGBTQ, minority, and special creators.</li>
                    <li>Grassroots causes and social welfare projects.</li>
                </ul>
            </div>
        </div>

        <!-- 3 -->
        <div class="kc-item row">
            <div class="col-md-4">
                <div class="kc-title">3. Championing Creativity & Innovation</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>Supporting artists, filmmakers, musicians, writers, and designers.</li>
                    <li>Backing ideas that don’t fit traditional commercial molds.</li>
                    <li>Providing space for creative freedom and expression.</li>
                </ul>
            </div>
        </div>

        <!-- 4 -->
        <div class="kc-item row">
            <div class="col-md-4">
                <div class="kc-title">4. Standing Against Inequality</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>Supporting initiatives that fight injustice.</li>
                    <li>Promoting digital empowerment.</li>
                    <li>Collaborating with nonprofits, educators, and community leaders.</li>
                </ul>
            </div>
        </div>

        <!-- 5 -->
        <div class="kc-item row">
            <div class="col-md-4">
                <div class="kc-title">5. Accountability & Transparency</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>Publishing regular impact updates.</li>
                    <li>Listening carefully to our community.</li>
                    <li>Holding ourselves accountable to creators and society.</li>
                </ul>
            </div>
        </div>

    </div>
</section>

<!-- BLUE SECTION -->
<section class="kc-blue">
    <div class="kc-blue-inner">
        <h2>
            ApnaCrowdfunding is more than a platform — it’s a movement built on belief in people
            and ideas. When you launch or support a campaign, you’re helping fund dreams,
            empower communities, and shape a better future.
            <br><br>
            Let’s fund what truly matters.
        </h2>
    </div>
</section>

@endsection
