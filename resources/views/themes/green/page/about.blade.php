@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')
<style type="text/css">
    body{
        font-family: Arial, sans-serif;
        color:#222;
    }

    .italic-text {
        font-style: italic;
    }

    /* HERO IMAGE */
    .hero-img{
        position: relative;
    }

    .hero-img img{
        width:100%;
        height:600px;
        object-fit:cover;
    }

    .hero-overlay{
        position:absolute;
        top:50%;
        left:50%;
        transform:translate(-50%, -50%);
        color:#fff;
        text-align:center;
        background:rgba(0,0,0,0.45);
        padding:30px 50px;
        border-radius:6px;
    }

    .hero-overlay h1{
        font-size:42px;
        font-weight:700;
        margin-bottom:10px;
    }

    .hero-overlay p{
        font-size:18px;
        margin:0;
    }

    /* COLOR BLOCKS */
    .blue-box{
        background:#1a2bdc;
        color:#fff;
        padding:80px 20px;
        text-align:center;
        font-size:28px;
        font-weight:600;
    }

    .peach-box{
        background:#ff8a65;
        padding:60px 20px;
        text-align:center;
        font-size:22px;
    }

    .pink-box{
        background:#ff5a7d;
        color:#fff;
        padding:60px 20px;
        text-align:center;
    }

    .deep-blue-box{
        background:#2949ff;
        color:#fff;
        padding:80px 20px;
        text-align:center;
    }

    .section{
        padding:80px 0;
    }

    .section h2{
        font-size:28px;
        font-weight:600;
    }

    .section p{
        font-size:16px;
        line-height:1.7;
        color:#555;
    }

    .new img{
        height:1050px;
        width:100%;
        object-fit:cover;
    }
</style>

<!-- HERO IMAGE -->
<div class="hero-img">
    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f"
         alt="About ApnaCrowdfunding reward-based crowdfunding platform supporting creators and startups">
    <div class="hero-overlay">
        <h1>About Us</h1>
        <p>Together, We Can Bring Your Idea or Dream to Life</p>
    </div>
</div>

<!-- TITLE -->
<div class="blue-box">
    Together, We Can Bring Your Idea Or Dream to Life
</div>

<!-- INTRO -->
<div class="container section">
    <h2>ApnaCrowdfunding – Reward-Based Crowdfunding</h2>
    <p>
        We are proud to be the creators of 
        <a href="https://apnacrowdfunding.com/" class="italic-text"><em>ApnaCrowdfunding</em></a>,
        a first-of-its-kind, community-driven platform designed to bring reward-based crowdfunding
        to underserved and underdeveloped countries.
        <br><br>
        Our platform is built for people with ideas and dreams but limited access to capital.
        <em>ApnaCrowdfunding</em> bridges this gap by empowering creators, startups, non-profits,
        and changemakers with the tools and support they need to turn vision into reality.
        <br><br>
        As a trusted crowdfunding website, we connect passionate innovators with supporters
        who believe in meaningful change. Inspired by global crowdfunding platforms,
        our mission focuses on local empowerment and long-term impact.
    </p>
</div>

<!-- IMAGE -->
<!-- IMAGE -->
<div class="container-fluid p-0 new">
    <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846"
         class="w-100"
         alt="Community-driven crowdfunding platform empowering nonprofits and entrepreneurs">
</div>

<!-- WHO WE ARE -->
<div class="peach-box">
    Who We Are – A Trusted Crowdfunding Website
</div>

<div class="container section">
    <p>
        <span class="italic-text"><em>ApnaCrowdfunding</em></span> is a creator-first crowdfunding website
        built on transparency, independence, and community trust.
        We remove barriers that prevent great ideas from being discovered and supported.
        Unlike traditional funding models, our platform allows creators to retain full control
        while supporters directly back projects they believe in.
    </p>
</div>

<!-- WHAT WE DO -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>What We Do</h2>
        </div>
        <div class="col-md-7">
            <p>
                ApnaCrowdfunding connects ideas with funding through modern crowdfunding platforms.
                Creators launch campaigns, share their stories, and gain support through crowd fundraising,
                crowd financing, and community sharing.
                Supporters discover meaningful projects and help bring promising ideas to life.
            </p>
        </div>
    </div>
</div>

<!-- MISSION -->
<div class="pink-box">
    Our Mission – Powering Crowd-Sourced Funding
</div>

<div class="container section">
    <p>
        Our mission is to support innovation and progress through crowd-sourced funding.
        We believe opportunity should be accessible to everyone—not just those with capital or connections.
        By strengthening crowdfunding sources and encouraging group funding,
        we empower communities to shape the future together.
    </p>
</div>

<!-- WHO WE HELP -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Who We Help</h2>
        </div>
        <div class="col-md-7">
            <ul>
                <li>Independent creators and entrepreneurs</li>
                <li>Startups and early-stage ideas</li>
                <li>Social causes and crowdfunding for nonprofits</li>
            </ul>
            <p>
                As an inclusive alternative to traditional non-profit crowdfunding platforms,
                we support non-profits, community initiatives, and purpose-driven projects
                through one reliable crowdfunding website.
            </p>
        </div>
    </div>
</div>

<!-- COMMUNITY -->
<div class="deep-blue-box">
    Powered by Community and Crowd Sharing
</div>

<div class="container section">
    <p>
        Successful projects grow through crowd sharing.
        Every campaign begins with early believers and expands as stories spread.
        Our global network proves that crowdfunding is about people standing behind ideas—not just money.
    </p>
</div>

<!-- WHY US -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Why Choose Us</h2>
        </div>
        <div class="col-md-7">
            <ul>
                <li>Simple and easy campaign creation</li>
                <li>Transparent and fair processes</li>
                <li>Community-first project discovery</li>
                <li>Complete creator independence</li>
            </ul>
            <p>
                We aim to stand among the best crowdfunding sites by focusing on people, not profits,
                and by building a trusted crowdfunding platform for long-term impact.
            </p>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Start Your Crowdfunding Campaign Today</h2>
        </div>
        <div class="col-md-7">
            <p>
                Whether you want to support an idea or launch your own campaign,
                <span class="italic-text"><em>ApnaCrowdfunding</em></span> gives you the tools,
                reach, and community to succeed.
                <br><br>
                <strong>Your idea. Our community. Shared success.</strong>
            </p>
            <button class="btn-otline mt-3">Discover Projects</button>
        </div>
    </div>
</div>

@endsection
