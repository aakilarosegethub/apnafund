@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')

<style>
/* RESET */
body{
    background:#ffffff;
}

/* HERO */
.kc-rules-hero{
    background:#0b1b4d;
    padding:120px 20px;
    text-align:center;
    position:relative;
    overflow:hidden;
}

.kc-rules-hero h1{
    color:#fff;
    font-size:42px;
    font-weight:500;
    margin-bottom:10px;
}

.kc-rules-hero p{
    color:#d1d5db;
    font-size:16px;
}
.kc-hero-center{
    max-width:820px;
    margin:0 auto;
    text-align:center;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
        margin-top: 110px;
}

/* CONFETTI SHAPES */
.kc-shape{
    position:absolute;
    opacity:0.9;
}
.circle{width:14px;height:14px;border-radius:50%;background:#22c55e;}
.square{width:14px;height:14px;background:#3b82f6;}
.triangle{
    width:0;height:0;
    border-left:7px solid transparent;
    border-right:7px solid transparent;
    border-bottom:14px solid #facc15;
}

/* CONTENT */
.kc-content{
    padding:90px 15px;
}

.kc-intro{
    max-width:920px;
    margin:0 auto 70px;
    font-size:16px;
    line-height:1.9;
    color:#374151;
}

/* RULE ITEM */
.kc-rule{
    max-width:920px;
    margin:0 auto 55px;
}

.kc-rule h3{
    font-size:18px;
    font-weight:600;
    color:#111827;
    margin-bottom:12px;
}

.kc-rule p{
    font-size:15px;
    color:#4b5563;
    line-height:1.85;
    margin-bottom:12px;
}

.kc-rule ul{
    padding-left:20px;
}

.kc-rule ul li{
    font-size:14.5px;
    color:#4b5563;
    line-height:1.8;
    margin-bottom:10px;
}

/* CHECK ICON */
.kc-check{
    color:#16a34a;
    font-weight:700;
    margin-right:8px;
}

/* CTA BOXES */
.kc-cta{
    background:#f9fafb;
    padding:70px 15px;
}

.kc-cta-box{
    background:#fff;
    border:1px solid #e5e7eb;
    padding:40px 30px;
    text-align:center;
    height:100%;
}

.kc-cta-box h4{
    font-size:16px;
    font-weight:600;
    margin-bottom:12px;
}

.kc-cta-box a{
    display:inline-block;
    margin-top:12px;
    padding:10px 18px;
    background:#16a34a;
    color:#fff;
    font-size:14px;
    border-radius:6px;
    text-decoration:none;
}

/* RESPONSIVE */
@media(max-width:768px){
    .kc-rules-hero h1{font-size:30px;}
    .kc-content{padding:60px 15px;}
}
</style>

<!-- HERO -->
<section class="kc-rules-hero">
    <div class="kc-hero-center">
        <h1>Our Rules</h1>
        <p>Fair, Transparent & Community-Driven
</p>
    </div>

    <!-- shapes -->
  
</section>
<!-- CONTENT -->
<section class="kc-content">
    <div class="container">

        <div class="kc-intro">
        At ApnaCrowdfunding, we believe in empowering people to bring ideas, causes, and dreams to life. To keep our platform safe, trustworthy and fair for everyone, we follow simple but firm rules that protect both creators and backers.
These rules help us build a community where crowdfunding works the way it should with integrity, transparency and respect.

        </div>

        <!-- RULE 1 -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Who Can Use ApnaCrowdfunding</h3>
            <p>
               You must be eligible to enter into a legal agreement in your country and comply with all local laws. If you are under 18, you must use the platform with a legal guardian’s consent.
            </p>
        </div>

        <!-- RULE 2 -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>What You Can Raise Funds For
</h3>
            <p>
                ApnaCrowdfunding supports a wide range of meaningful causes, including:

            </p>
            <ul>
                <li>Personal and medical needs</li>
                <li>Creative projects and startups</li>
                <li>Education support</li>
                <li>Community needs and non-profit goals</li>
            </ul>
        </div>

        <!-- RULE 3 -->
      

        <!-- RULE 4 -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Honest & Accurate Campaigns</h3>
            <p>
                Creators must tell the truth. Your story, goals, budget and how funds will be used must be clear, accurate, and complete. Misleading backers or misusing funds violates our rules and may result in campaign removal or legal action.
We monitor campaigns to maintain trust and fairness  but responsibility starts with you.

            </p>
        </div>

        <!-- RULE 5 -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Transparency With Backers
</h3>
            <p>
          Campaigns must include:
            </p>
            <ul>
                <li>Clear funding goals</li>
                <li>Detailed explanation of how funds will be spent
</li>
<li>Risks or challenges your project might face
</li>
            </ul>
        </div>
           <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Rewards & Promises</h3>
            <p>If you offer rewards, make sure they are deliverable and described clearly. Fulfilling your promises on time isn’t just good practice, it’s essential for community trust.

            </p>
        </div>

         <div class="kc-rule">
            <h3><span class="kc-check">✔</span> Respectful Behavior</h3>
            <p>Our community thrives on respect. Hate speech, harassment, threats or abusive behavior  whether toward backers, creators or our team  is strictly prohibited.

            </p>
        </div>

             <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Platform Fees & Sustainability</h3>
            <p>To keep ApnaCrowdfunding running securely and smoothly, we charge a small platform fee on successful campaigns. This helps us maintain technology, support, and safety for everyone.
We clearly disclose these fees before you launch so you always know what to expect.


            </p>
        </div>




    </div>
</section>

<!-- CTA -->


@endsection
