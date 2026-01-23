@php
    $activeTheme = activeTheme();
@endphp

@extends($activeTheme . 'layouts.green-home')

@section('meta')
    <title>ApnaCrowdfunding Rules & Guidelines | Transparent Reward-Based Crowdfunding</title>
    <meta name="description" content="Read ApnaCrowdfunding rules and guidelines for creators and backers. Learn about eligibility, transparency, rewards, platform fees, and ethical reward-based crowdfunding practices.">
@endsection

@section('content')

<style>
body{background:#ffffff;}
.kc-rules-hero{background:#0b1b4d;padding:120px 20px;text-align:center;position:relative;overflow:hidden;}
.kc-rules-hero h1{color:#fff;font-size:42px;font-weight:500;margin-bottom:10px;}
.kc-rules-hero p{color:#d1d5db;font-size:16px;}
.kc-hero-center{max-width:820px;margin:0 auto;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;margin-top:110px;}
.kc-content{padding:90px 15px;}
.kc-intro{max-width:920px;margin:0 auto 70px;font-size:16px;line-height:1.9;color:#374151;}
.kc-rule{max-width:920px;margin:0 auto 55px;}
.kc-rule h2{font-size:20px;font-weight:600;color:#111827;margin-bottom:12px;}
.kc-rule h3{font-size:17px;font-weight:600;color:#111827;margin-bottom:10px;}
.kc-rule p{font-size:15px;color:#4b5563;line-height:1.85;margin-bottom:12px;}
.kc-rule ul{padding-left:20px;}
.kc-rule ul li{font-size:14.5px;color:#4b5563;line-height:1.8;margin-bottom:10px;}
.kc-check{color:#16a34a;font-weight:700;margin-right:8px;}
@media(max-width:768px){
    .kc-rules-hero h1{font-size:30px;}
    .kc-content{padding:60px 15px;}
}
</style>

<!-- HERO -->
<section class="kc-rules-hero">
    <div class="kc-hero-center">
        <h1>Our Rules – Fair, Transparent & Community-Driven Crowdfunding</h1>
        <p>Clear guidelines for creators and backers on ApnaCrowdfunding</p>
    </div>
</section>

<!-- CONTENT -->
<section class="kc-content">
<div class="container">

<!-- INTRO -->
<div class="kc-intro">
    <h2>Community-Driven Reward-Based Crowdfunding</h2>
    <p>
        ApnaCrowdfunding is a <strong>reward-based crowdfunding platform</strong> built to empower people to bring ideas,
        causes and goals to life ethically. These rules ensure transparency, trust and fairness for everyone using our platform.
    </p>
    <p>
        Inspired by global platforms like Kickstarter, we aim to educate and support underserved regions, starting with Pakistan,
        through responsible and community-focused crowdfunding.
    </p>
    <p>
        Learn how our platform works by visiting
        <a href="{{ url('/how-it-works') }}">How Crowdfunding Works</a>.
    </p>
</div>

<!-- RULES -->

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Who Can Use ApnaCrowdfunding</h2>
    <p>
        You must be legally eligible to enter into agreements in your country and comply with local laws.
        Users under 18 may only participate with guardian consent.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>What You Can Raise Funds For</h2>
    <h3>Eligible Crowdfunding Categories</h3>
    <ul>
        <li>Personal and medical needs</li>
        <li>Creative projects and startups</li>
        <li>Educational support</li>
        <li>Community and social impact initiatives</li>
    </ul>
    <p>
        Start your campaign responsibly using our
        <a href="{{ url('/start-campaign') }}">campaign creation guide</a>.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Honest & Accurate Campaigns</h2>
    <p>
        All campaigns must provide truthful information regarding goals, budgets and fund usage.
        Misrepresentation or misuse of funds may result in campaign removal or legal action.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Transparency With Backers</h2>
    <h3>Required Campaign Disclosures</h3>
    <ul>
        <li>Clear funding goals</li>
        <li>Detailed fund usage explanation</li>
        <li>Potential risks or challenges</li>
    </ul>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Rewards & Promises</h2>
    <p>
        Rewards must be realistic and delivered as promised.
        This builds long-term trust within the crowdfunding community.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Respectful Behavior</h2>
    <p>
        Harassment, hate speech or abusive behavior toward creators, backers or our team is strictly prohibited.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Platform Fees & Sustainability</h2>
    <p>
        ApnaCrowdfunding charges a small fee on successful campaigns to maintain security, technology and user support.
        View full details on our <a href="{{ url('/fees') }}">platform fees page</a>.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Privacy & Data Protection</h2>
    <p>
        User data is protected under our
        <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
        and is never shared without consent unless required by law.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Reporting Violations</h2>
    <p>
        Report suspicious activity or rule violations via our
        <a href="{{ url('/contact') }}">support page</a>.
    </p>
</div>

<div class="kc-rule">
    <h2><span class="kc-check">✔</span>Changes to Crowdfunding Rules</h2>
    <p>
        These rules may evolve as the platform grows. Users are encouraged to review them regularly.
    </p>
</div>

<div class="kc-rule">
    <h2>Our Promise to Creators & Backers</h2>
    <p>
        ApnaCrowdfunding exists to support real people with real stories.
        We are committed to ethical, transparent and community-driven crowdfunding.
    </p>
    <p>
        Explore active campaigns on
        <a href="{{ url('/explore') }}">ApnaCrowdfunding</a>.
    </p>
</div>

</div>
</section>

@endsection
