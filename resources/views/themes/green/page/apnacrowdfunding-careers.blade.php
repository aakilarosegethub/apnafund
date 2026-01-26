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
.career-banner{
    position:relative;
    min-height:420px;
    background-image:url('../images/careers-banner.jpg');
    background-size:cover;
    background-position:center;
    display:flex;
    align-items:center;
}

.career-banner-overlay{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,0.55);
}

.career-banner-content{
    position:relative;
    color:#fff;
    text-align:center;
}

.career-banner h1{
    font-size:42px;
    font-weight:700;
    margin-bottom:12px;
}

.career-banner p{
    font-size:18px;
    opacity:.9;
}

/* LISTS */
.career-role-list,
.career-perk-list{
    margin:20px 0;
    padding-left:20px;
}

.career-role-list li,
.career-perk-list li{
    margin-bottom:10px;
    font-size:16px;
}

/* HEADINGS */
section h2{
    font-weight:700;
    margin-bottom:15px;
}

section h4{
    margin-top:25px;
    font-weight:600;
}

/* CTA */
.career-cta{

}

.career-cta h2{

}
.new{
    margin-top: 125px;
}

</style>


<section class="career-banner">
    <div class="career-banner-overlay"></div>
    <div class="container career-banner-content new">
        <h1>{!! apnaCrowdfundingLink() !!} Careers</h1>
        <p>Build the future of creative funding.</p>
    </div>
</section>

<!-- INTRO -->
<section class="career-intro section-padding">
    <div class="container">
        <h2>Jobs/career</h2>
        <p>
            At {!! apnaCrowdfundingLink() !!}, we are giving power to creators, entrepreneurs, and community causes,
            all over the world. Everyone at {!! apnaCrowdfundingLink() !!} is helping real people to fulfill their dreams
            through crowdfunding.
        </p>
    </div>
</section>

<!-- WHY WORK WITH US -->
<section class="career-why section-padding bg-light">
    <div class="container">
        <h2>Why Work With Us?</h2>
        <p>
            We value everyone. We are a movement. We will make sure that your dreams are turned into
            reality through funds.
        </p>
    </div>
</section>

<!-- OPEN ROLES -->
<section class="career-roles section-padding">
    <div class="container">
        <h2>Open Roles</h2>
        <p>Be a part of our team. We need people in the following respective areas :</p>

        <ul class="career-role-list">
            <li><strong>Product & Engineering</strong> — build tools</li>
            <li><strong>Creative & Design</strong> — for creativity</li>
            <li><strong>Marketing & Growth</strong> — for the support of creators</li>
            <li><strong>Strategy & Operations</strong> — we want smooth operation of our platform</li>
        </ul>

        <p>
            Positions range from fully remote roles to regional roles supporting Pakistan-focused
            crowdfunding growth and more opportunities are always opening as we grow.
        </p>
    </div>
</section>

<!-- BENEFITS -->
<section class="career-benefits section-padding bg-light">
    <div class="container">
        <h2>Benefits & Perks</h2>

        <h4>Learn & Grow Continuously</h4>
        <p>Curiosity fuels creativity. Our main investment is in skills and development.</p>

        <h4>Culture of Community</h4>
        <p>
            Work with passionate teammates and join affinity groups, virtual meetups and events that bring people together.
            We celebrate diversity, inclusion and collaboration.
        </p>

        <h4>Work-Life Harmony</h4>
        <p>The remote work environment is flexible. No pressure at work.</p>

        <h4>Make a Social Impact Every Day</h4>
        <p>At {!! apnaCrowdfundingLink() !!}, the campaigns will be supported by your work.</p>

        <h4>Resources and Support for A Successful Campaign</h4>
        <p>
            You will provide us with tools, resources and guides so that campaigns can be backed successfully.
            It will be a huge contribution for the creators.
        </p>
    </div>
</section>

<!-- WHO WE ARE -->
<section class="career-about section-padding">
    <div class="container">
        <h2>Who We Are</h2>
        <p>
            {!! apnaCrowdfundingLink() !!} is normalizing funding so that hardworking people with ideas can launch
            their projects in the form of campaigns and bakers can support them.
        </p>
        <p>
            Crowdfunding should be accessible, transparent, and impactful. We are trying to build a team
            that can apply these characteristics.
        </p>
    </div>
</section>

<!-- PERKS -->
<section class="career-perks section-padding bg-light">
    <div class="container">
        <h2>Perks At a Glance</h2>
        <ul class="career-perk-list">
            <li>✔ Flexible remote work</li>
            <li>✔ Career development support</li>
            <li>✔ Community events and team socials</li>
            <li>✔ Health & wellbeing stipends</li>
        </ul>
    </div>
</section>

<!-- INCLUSIVE -->
<section class="career-inclusive section-padding">
    <div class="container">
        <h2>Inclusive by Design</h2>
        <p>
            There will be interviews and then hiring. There will be diversity on our platform.
            We will support all perspectives and we will also provide accomodations if requested.
        </p>
    </div>
</section>

<!-- CTA -->
<section class="career-cta section-padding text-center">
    <div class="container">
        <h2>Be Part of Something Bigger</h2>
        <p>
            If you want to empower creators, uplift communities, and reimagine how projects get funded,
            we want to hear from you.
        </p>
    </div>
</section>

@endsection
