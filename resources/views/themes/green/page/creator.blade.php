@php
    $activeTheme = activeTheme();
@endphp

@extends($activeTheme . 'layouts.green-home')

@section('content')
<style type="text/css">
    .creator-guide .hero {
    background: #f1fbf6;
    padding: 80px 0;
}

.hero-img {
    max-width: 600px;
    width: 100%;
    border-radius: 18px;
    margin-top: 30px;
}

.section {
    padding: 70px 0;
}

.light-bg {
    background: #fafafa;
}

.section-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 30px;
    text-align: center;
}

.checklist {
    list-style: none;
    padding-left: 0;
}

.checklist li {
    padding-left: 30px;
    position: relative;
    margin-bottom: 12px;
}

.checklist li::before {
    content: "✔";
    position: absolute;
    left: 0;
    color: #1c8e50;
}

.feature-box {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,.05);
    text-align: center;
}

.reward-card {
    background: #fff;
    padding: 20px;
    margin: 10px 0;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
}

.cta {
    background: #1c8e50;
    color: #fff;
}

.cta .btn {
    margin-top: 20px;
}

.support {
    margin-top: 15px;
    font-size: 14px;
}
/* HERO SECTION */
.apna-hero {
    position: relative;
    min-height: 420px;
    display: flex;
    align-items: center;
    background: linear-gradient(
        120deg,
        rgb(255, 0, 150),
        rgb(0, 204, 255),
        rgb(0, 255, 170)
    );
    overflow: hidden;
}

/* BLACK OVERLAY */
.apna-hero .hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
}

/* CENTER CONTENT BOX */
.hero-box {

    backdrop-filter: blur(6px);
        margin-top: 126px;
}

/* TEXT */
.hero-box h1 {
    font-size: 42px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 15px;
}

.hero-box p {
    font-size: 16px;
    color: #eaeaea;
    line-height: 1.7;
}

/* BUTTON */
.hero-box .btn {
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 600;
}
.lead {
        font-size: 20px;

}
.new p{
        font-size: 20px;

}
.new_2{
        font-size: 20px;

}
.new_3{
text-align: center;
font-size:20px;
}

.feature-box h4{
    margin-bottom:10px;
    font-weight:600;
}

.feature-box p{
    font-size:16px;
    color:#555;
}
.creator-guide p{
    font-size: 20px;
}


</style>

<div class="creator-guide">

    <!-- HERO SECTION -->
  <!-- TOP HERO BANNER -->
<section class="apna-hero">
    <div class="hero-overlay"></div>

    <div class="container">
        <div class="hero-box text-center">
            <h1>Creator’s Guide</h1>
            <p>
                Learn how to launch, grow, and fund your ideas on
                {!! apnaCrowdfundingLink() !!}.
            </p>
            <a href="#" class="btn btn-light mt-3">Start Your Journey</a>
        </div>
    </div>
</section>

    </section>

    <!-- INTRO -->
    <section class="section">
        <div class="container text-center">
            <p class="lead">
               This is your personal guide for learning how to create projects on   {!! apnaCrowdfundingLink() !!}. Whether a dream is becoming a reality or you are starting your own start-up, Creator's  Guide is built for online crowdfunding for creators. It gives you tools, tips and step by step resources to support your resources.

As one of the most trusted crowdfunding platforms in Pakistan, {!! apnaCrowdfundingLink() !!} gives creators everything they need to launch, grow and successfully deliver their projects—all in one place. 

            </p>
        </div>
    </section>

    <!-- WHAT IS CROWDFUNDING -->
    <section class="section light-bg">
        <div class="container">
            <h2 class="section-title">What Is Crowdfunding for Creators?</h2>
            <div class="row align-items-center">
                <div class="col-md-6 new">
                    <p>
                        Creators do not have to rely on investors or loans.
                        They can raise funds directly from people who believe in their dreams.
                    </p>
                    <p>
                        On {!! apnaCrowdfundingLink() !!}, crowdfunding becomes
                        <strong>simple, structured, and achievable</strong>.
                    </p>
                </div>
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- BEFORE YOU LAUNCH -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Before You Launch — Plan for Success</h2>

            <div class="row">
                       <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984" class="img-fluid rounded">
                </div>
                <div class="col-md-6 new_2">
                    <ul class="checklist">
                        <li>Define your project goals clearly</li>
                        <li>Set a realistic funding target</li>
                        <li>Create a timeline</li>
                        <li>Build an emotional story</li>
                        <li>Build trust</li>
                    </ul>
                </div>
         
            </div>
        </div>
    </section>


        <section class="section">
        <div class="container">
            <h2 class="section-title">Pre-Campaign Checklist</h2>

            <div class="row">
                <div class="col-md-6 new_2">
                    <ul class="checklist">
                        <li>Authenticate the idea
</li>
                        <li>Email list and social media presence is essential</li>
                        <li>Before launching the project, supporters are important</li>
                
                    </ul>
                </div>
        <div class="col-md-6">
    <img 
        src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=900&q=80"
        class="img-fluid rounded"
        alt="Pre Campaign Planning"
    >
</div>
        </div>
    </section>

    <!-- VISUALS & VIDEO TIPS -->
<!-- VISUALS & VIDEO TIPS -->





    <!-- STORY -->
    <section class="section light-bg">
        <div class="container new_3">
            <h2 class="section-title">Build a Campaign Story That Connects</h2>
            <p>A genuine story is very important. It will build trust of the masses.</p>

            <div class="row">
                <div class="col-md-4 feature-box">
                    <h4>Problem</h4>
                    <p>What problem is your project solving?</p>
                </div>
                <div class="col-md-4 feature-box">
                    <h4>Your Journey</h4>
                    <p>Your journey as a creator builds trust.</p>
                </div>
                <div class="col-md-4 feature-box">
                    <h4>Funds Usage</h4>
                    <p>Clear budget, risks, and milestones.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="section light-bg">
    <div class="container">
        <h2 class="section-title">Visuals & Video Tips</h2>
        <p class="text-center mb-5" style="font-size:18px;">
            Short, clean, high-quality images and videos are essential for successful projects.
            Explain your story simply and honestly.
        </p>

        <div class="row align-items-center">
            <!-- LEFT CONTENT -->
            <div class="col-md-6">
                <div class="feature-box mb-4">
                    <h4>📸 High-Quality Images</h4>
                    <p>
                        Use sharp, well-lit images that clearly represent your idea, product,
                        or mission. Clean visuals build instant trust.
                    </p>
                </div>

                <div class="feature-box mb-4">
                    <h4>🎥 Simple & Clear Video</h4>
                    <p>
                        Keep your video short (1–2 minutes). Explain who you are,
                        what you’re building, and why it matters.
                    </p>
                </div>

                <div class="feature-box">
                    <h4>🤝 Be Real & Authentic</h4>
                    <p>
                        Speak naturally. No heavy editing required.
                        Honest communication connects better with backers.
                    </p>
                </div>
            </div>

            <!-- RIGHT IMAGES -->
            <div class="col-md-6">
                <!-- MAIN IMAGE -->
                <img 
                    src="https://images.unsplash.com/photo-1605379399642-870262d3d051?auto=format&fit=crop&w=900&q=80"
                    class="img-fluid rounded shadow mb-3"
                    alt="Creating campaign video"
                >

                <!-- SMALL IMAGES ROW -->
                <div class="row">
                    <div class="col-6">
                        <img 
                            src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80"
                            class="img-fluid rounded shadow-sm"
                            alt="Team planning visuals"
                        >
                    </div>
                    <div class="col-6">
                        <img 
                            src="https://images.unsplash.com/photo-1492724441997-5dc865305da7?auto=format&fit=crop&w=600&q=80"
                            class="img-fluid rounded shadow-sm"
                            alt="Video shooting setup"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- REWARDS -->
    <section class="section">
        <div class="container new_3">
            <h2 class="section-title">Rewards & Backer Experience</h2>
            <p>People will join your journeys.
</p>

            <div class="row">
                <div class="col-md-3 reward-card">Early-bird offers</div>
                <div class="col-md-3 reward-card">Limited edition items</div>
                <div class="col-md-3 reward-card">Personalized perks</div>
                <div class="col-md-3 reward-card">Behind-the-scenes access</div>
            </div>
        </div>
    </section>

    <!-- PROMOTION -->
    <section class="section light-bg">
        <div class="container new_3">
            <h2 class="section-title">Promote Like a Pro</h2>
            <p class="text-center">
                Promotion keeps your campaign alive and moving.
            </p>

            <div class="row text-center">
                <div class="col-md-3">Email Marketing</div>
                <div class="col-md-3">WhatsApp Groups</div>
                <div class="col-md-3">Social Media</div>
                <div class="col-md-3">Live Updates</div>
            </div>
        </div>
    </section>

    <!-- TOOLKIT -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Toolkit from {!! apnaCrowdfundingLink() !!}</h2>

            <ul class="checklist">
                <li>Dashboard with real-time analytics</li>
                <li>Backer management</li>
                <li>Shipping & SKU collection tools</li>
                <li>Fulfillment planning support</li>
            </ul>
        </div>
    </section>

    <!-- SUPPORT -->


</div>

@endsection
