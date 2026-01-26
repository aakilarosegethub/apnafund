@php
    $activeTheme = activeTheme();
@endphp

@extends($activeTheme . 'layouts.green-home')

@section('content')

<style>
body {
    background: #f9fafb;
    font-family: 'Arial', sans-serif;
    color: #111827;
}

/* HERO */
.kc-rules-hero {
    background: linear-gradient(120deg, #0b1b4d, #1e3a8a);
    padding: 140px 20px 80px;
    text-align: center;
    color: #fff;
    position: relative;
}

.kc-rules-hero h1 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 15px;
}

.kc-rules-hero p {
    font-size: 20px;
    color: #d1d5db;
}

/* HERO IMAGE */
.kc-hero-img {
    max-width: 700px;
    width: 100%;
    margin: 30px auto 0;
    border-radius: 20px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    display: block;
}

/* CONTENT SECTIONS */
.kc-content {
    padding: 90px 20px;
}

.kc-rule {
    max-width: 950px;
    margin: 0 auto 60px;
    background: #ffffff;
    padding: 35px 40px;
    border-radius: 15px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.06);
    transition: transform 0.3s;
}

.kc-rule:hover {
    transform: translateY(-5px);
}

.kc-rule h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 12px;
    color: #1e3a8a;
}

.kc-rule p {
    font-size: 16px;
    color: #4b5563;
    line-height: 1.8;
    margin-bottom: 12px;
}

.kc-rule ul {
    padding-left: 20px;
    margin-bottom: 12px;
}

.kc-rule ul li {
    font-size: 15px;
    color: #4b5563;
    line-height: 1.7;
    margin-bottom: 8px;
}

.kc-check {
    color: #16a34a;
    font-weight: 700;
    margin-right: 8px;
}

/* SECTION IMAGES */
.kc-rule img {
    max-width: 100%;
    border-radius: 15px;
    margin-top: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* RESPONSIVE */
@media (max-width:768px) {
    .kc-rules-hero h1 { font-size: 32px; }
    .kc-rules-hero p { font-size: 16px; }
    .kc-content { padding: 60px 15px; }
    .kc-rule { padding: 25px 20px; }
}
</style>

<!-- HERO -->
<section class="kc-rules-hero">
    <h1>Our Rules</h1>
    <p>Fair, Transparent & Community-Driven Crowdfunding</p>
    <img class="kc-hero-img" src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=900&q=80" alt="Crowdfunding Concept">
</section>

<!-- CONTENT -->
<section class="kc-content">
    <div class="container">

        <!-- HISTORY -->
        <div class="kc-rule">
            <h3>Community-Driven Reward-Based Crowdfunding</h3>
            <p>
                ApnaCrowdfunding is on a mission to bring Reward-Based Crowdfunding to underserved countries around the world, starting with Pakistan. While crowdfunding is not a new idea globally, many underdeveloped countries are still unfamiliar with its true potential.
            </p>
            <p>
                Kickstarter, the world’s leading reward-based crowdfunding platform, demonstrated how powerful this model can be. Its journey began when Perry Chen struggled to fund a music and dance event. After failing to secure support from banks or lenders, he partnered with Yancey Strickler, and together they launched Kickstarter in 2009 — raising its first reward-based funds and reshaping how ideas get funded.
            </p>
            <img src="https://images.unsplash.com/photo-1565372913730-16c8c74c6b6b?auto=format&fit=crop&w=900&q=80" alt="Crowdfunding History">
            <p>
                Transparency and education are essential where crowdfunding is still new. Drawing inspiration from global platforms, <strong>ApnaCrowdfunding</strong> empowers people to bring ideas, goals, and dreams to life through a safe, ethical, and community-focused crowdfunding website.
            </p>
        </div>

        <!-- WHO CAN USE -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Who Can Use ApnaCrowdfunding</h3>
            <p>
                Anyone legally eligible to enter into agreements in their country and compliant with local laws may use ApnaCrowdfunding. Users under 18 may participate only with the consent of a legal guardian.
            </p>
        </div>

        <!-- WHAT YOU CAN RAISE -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>What You Can Raise Funds For</h3>
            <ul>
                <li>Personal and medical needs</li>
                <li>Creative projects and startups</li>
                <li>Educational support</li>
                <li>Social impact initiatives</li>
                <li>Community needs and purpose-driven goals</li>
            </ul>
            <p><strong>We do not allow:</strong></p>
            <ul>
                <li>Illegal activities or fraud</li>
                <li>Hate, discrimination or violence</li>
                <li>Gambling or speculative financial schemes</li>
                <li>Fundraising that violates laws, rights, or safety standards</li>
            </ul>
        </div>

        <!-- HONEST CAMPAIGNS -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Honest & Accurate Campaigns</h3>
            <p>
                All campaigns must provide truthful, accurate, and complete information. Funding goals, budgets, and use of funds must be clearly explained. Misleading backers or misusing funds may result in campaign removal or legal action.
            </p>
        </div>

        <!-- TRANSPARENCY -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Transparency With Backers</h3>
            <ul>
                <li>Clear funding goals</li>
                <li>Detailed use of funds</li>
                <li>Any risks or challenges involved</li>
            </ul>
        </div>

        <!-- REWARDS -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Rewards & Promises</h3>
            <p>
                Rewards must be realistic, clearly described, and deliverable. Fulfilling promises on time is essential to maintaining trust across all crowdfunding platforms.
            </p>
        </div>

        <!-- RESPECT -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Respectful Behavior</h3>
            <p>
                Harassment, hate speech, threats, or abusive behavior toward creators, backers, or our team is strictly prohibited. Respect is a core value of community-driven crowdfunding.
            </p>
        </div>

        <!-- FEES -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Platform Fees & Sustainability</h3>
            <p>
                ApnaCrowdfunding charges a small platform fee on successful campaigns. These fees support technology, security, and user support. All fees are clearly disclosed in advance.
            </p>
        </div>

        <!-- PRIVACY -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Privacy & Data Protection</h3>
            <p>
                Protecting user data is a top priority. Personal information is safeguarded under our privacy policy and is never shared without consent, unless required by law.
            </p>
        </div>

        <!-- REPORTING -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Reporting Violations</h3>
            <p>
                If you notice suspicious activity or rule violations, please report them immediately at <strong>support@apnacrowdfunding.com</strong>.
            </p>
        </div>

        <!-- CHANGES -->
        <div class="kc-rule">
            <h3><span class="kc-check">✔</span>Changes to These Rules</h3>
            <p>
                As the platform grows, rules may be updated to improve safety and compliance. Users are encouraged to review them regularly.
            </p>
        </div>

        <!-- PROMISE -->
        <div class="kc-rule">
            <h3>Our Promise</h3>
            <p>
                ApnaCrowdfunding exists to support real people with real stories. Our rules are designed to protect creators and backers while ensuring transparency, trust, and long-term community impact.
            </p>
            <p><strong>Let’s create more impact — the right way.</strong></p>
            <img src="https://images.unsplash.com/photo-1596496059757-3f1e2c4dcf69?auto=format&fit=crop&w=900&q=80" alt="Community Impact">
        </div>

    </div>
</section>

@endsection
