@php
    $activeTheme = activeTheme();
@endphp

@extends($activeTheme . 'layouts.green-home')

@section('content')

<!-- TOP BANNER -->
<style>
    /* GENERAL */
.section-padding{
    padding:70px 0;
}

.bg-light{
    background:#f8f9fb;
}

/* TOP BANNER */
.story-banner{
    position:relative;
    min-height:420px;
    background-image:url('../images/story-banner.jpg');
    background-size:cover;
    background-position:center;
    display:flex;
    align-items:center;
}

.story-banner-overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.55);
}

.story-banner-content{
    position:relative;
    color:#fff;
    text-align:center;
}

.story-banner h1{
    font-size:42px;
    font-weight:700;
    margin-bottom:12px;
}

.story-banner p{
    font-size:18px;
    opacity:.9;
}

/* HEADINGS */
section h2{
    font-weight:700;
    margin-bottom:15px;
}

section h3{
    margin-top:25px;
    font-weight:600;
    margin-bottom:12px;
}

section p{
    line-height:1.8;
    margin-bottom:15px;
}

.new{
    margin-top: 125px;
}

</style>


<section class="story-banner">
    <div class="story-banner-overlay"></div>
    <div class="container story-banner-content new">
        <h1>Our Story</h1>
        <p>How {!! apnaCrowdfundingLink() !!} came to be.</p>
    </div>
</section>

<!-- OUR BEGINNING -->
<section class="story-beginning section-padding">
    <div class="container">
        <h2>Our Beginning</h2>
        <p>
            {!! apnaCrowdfundingLink() !!} was born from a simple yet powerful idea: everyone deserves a chance to bring their dreams to life. 
            We saw talented creators, passionate entrepreneurs, and dedicated community leaders struggling to find the resources 
            they needed to make their visions a reality.
        </p>
        <p>
            In a world where traditional funding sources often feel out of reach, we wanted to create a platform that democratizes 
            opportunity—where a great idea matters more than connections or wealth.
        </p>
    </div>
</section>

<!-- OUR MISSION -->
<section class="story-mission section-padding bg-light">
    <div class="container">
        <h2>Our Mission</h2>
        <p>
            At {!! apnaCrowdfundingLink() !!}, our mission is to empower creators, entrepreneurs, and changemakers to turn their ideas into 
            reality through the power of community-backed funding. We believe that great ideas come from everywhere, and everyone 
            should have access to the resources they need to succeed.
        </p>
        <p>
            We're building more than just a platform—we're building a movement. A movement where communities come together to 
            support what matters most to them. Where backers become partners in success. Where dreams transform into tangible impact.
        </p>
    </div>
</section>

<!-- OUR VALUES -->
<section class="story-values section-padding">
    <div class="container">
        <h2>What We Stand For</h2>
        
        <h3>Accessibility</h3>
        <p>
            Crowdfunding should be accessible to everyone, regardless of background, location, or experience. We've designed 
            our platform to be intuitive, supportive, and welcoming to first-time campaign creators and experienced fundraisers alike.
        </p>

        <h3>Transparency</h3>
        <p>
            Trust is the foundation of every successful campaign. We require complete transparency from campaign creators about 
            their goals, timelines, and how funds will be used. Backers deserve to know exactly where their money is going.
        </p>

        <h3>Community</h3>
        <p>
            We believe in the power of community. Every successful campaign on our platform represents a community coming together 
            to support something meaningful. We foster connections between creators and backers that last long after campaigns end.
        </p>

        <h3>Impact</h3>
        <p>
            Whether it's launching a business, creating art, or supporting a cause, every campaign on {!! apnaCrowdfundingLink() !!} has the 
            potential to create positive change. We're proud to be part of thousands of success stories.
        </p>
    </div>
</section>

<!-- OUR COMMITMENT -->
<section class="story-commitment section-padding bg-light">
    <div class="container">
        <h2>Our Commitment to You</h2>
        <p>
            Whether you're a creator with a vision or a backer looking to support meaningful projects, we're committed to 
            providing you with the tools, resources, and support you need to succeed. We're constantly improving our platform, 
            listening to feedback, and adapting to better serve our community.
        </p>
        <p>
            We believe in empowering those who empower others. Together, we're not just funding projects—we're building futures, 
            creating opportunities, and making dreams possible.
        </p>
    </div>
</section>

<!-- CTA -->
<section class="story-cta section-padding text-center">
    <div class="container">
        <h2>Be Part of Our Story</h2>
        <p>
            Join thousands of creators and backers who are already making their mark on {!! apnaCrowdfundingLink() !!}.
        </p>
    </div>
</section>

@endsection
