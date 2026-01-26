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
        {!! apnaCrowdfundingLink() !!} exists for one simple but powerful reason:
        to help ideas, causes, and creators turn dreams into reality.
        We believe success is not measured only in revenue, but in lives impacted,
        creativity unlocked, and communities uplifted.
    </p>
</section>

<!-- CONTENT -->
<section class="kc-section">
    <div class="container">

        <!-- 1 -->
        <div class="kc-item row">
            <div class="col-md-4">
               
                <div class="kc-title">1.Our Values Shape How We Operate</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>We never sell user data to third parties.</li>
                    <li>We fiercely protect user privacy.</li>
                    <li>Transparency guides data usage.</li>
                    <li>Policies written for humans, not lawyers.</li>
                    <li>No hidden clauses or unfair advantages.</li>
                </ul>
            </div>
        </div>

        <!-- 2 -->
        <div class="kc-item row">
            <div class="col-md-4">
               
                <div class="kc-title">2.Building a More Inclusive World</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>Underserved and rural communities.</li>
                    <li>Women-led & minority creators.</li>
                    <li>Grassroots and social impact causes.</li>
                </ul>
            </div>
        </div>

        <!-- 3 -->
        <div class="kc-item row">
            <div class="col-md-4">
                
                <div class="kc-title">3.Championing Creativity</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>Supporting artists and innovators.</li>
                    <li>Encouraging bold, non-traditional ideas.</li>
                    <li>Freedom of expression and experimentation.</li>
                </ul>
            </div>
        </div>

        <!-- 4 -->
        <div class="kc-item row">
            <div class="col-md-4">
              
                <div class="kc-title">4.Accountability & Transparency</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>Publishing regular impact updates.</li>
                    <li>Listening and evolving responsibly.</li>
                    <li>Accountable to society.</li>
                </ul>
            </div>
        </div>

        <!-- 5 -->
        <div class="kc-item row">
            <div class="col-md-4">
              
                <div class="kc-title">5.Standing Against Inequality</div>
            </div>
            <div class="col-md-8">
                <ul class="kc-points">
                    <li>Fighting systemic injustice.</li>
                    <li>Promoting financial inclusion.</li>
                    <li>Collaborating for equal opportunity.</li>
                </ul>
            </div>
        </div>

    </div>
</section>

<!-- BLUE SECTION -->
<section class="kc-blue">
    <div class="kc-blue-inner">
        <h2>
            {!! apnaCrowdfundingLink() !!} is a movement of belief in people, ideas, and collective action.
            When you launch or support a campaign here, you’re not just funding a project —
            you’re helping build a more creative, compassionate, and empowered world.
            Together, let’s fund what truly matters.
        </h2>
    </div>
</section>

@endsection
