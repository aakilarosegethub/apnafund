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

/* HERO IMAGE */
.hero-img img{
    width:100%;
    height:600px;
    object-fit:cover;
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

/* SECTIONS */
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

.btn-outline{
    border:1px solid #222;
    background:none;
    padding:8px 18px;
    font-size:14px;
}
.new img{
    height: 1050px;
    width: 100%;
    object-fit: cover;
}
</style>


<!-- HERO IMAGE -->
<div class="hero-img">
    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f" alt="">
</div>

<!-- MISSION -->
<div class="blue-box">
   Bringing Big Ideas to Life, Together.

</div>

<!-- INTRO -->
<div class="container section">
    <p>
       ApnaCrowdfunding exists to turn ideas into action. Inspired by global reward-based crowdfunding platforms like Kickstarter, we’ve built a space designed for our own communities — where creators, entrepreneurs, changemakers and dreamers connect with people who believe in them.
Whether it’s a startup idea, a social cause, a creative project, or a personal mission, ApnaCrowdfunding helps you find supporters who want to see your idea succeed. Because powerful ideas shouldn’t stay stuck in notebooks — they deserve a chance to grow, be seen and be supported.

    </p>
</div>

<!-- COMMUNITY IMAGE -->
<div class="container-fluid p-0 new">
    <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846" class="w-100" alt="">
</div>

<!-- QUOTE -->
<div class="peach-box">
   ApnaCrowdfunding connects your vision with people who believe in you and want to see you succeed.
</div>

<!-- OUR COMMUNITY -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Powered by Community
</h2>
        </div>
        <div class="col-md-7">
            <p>Every successful campaign starts close to home, friends, family, early believers. From there, Momentum builds. Support grows. Stories spread.
That’s the power of community.
Our growing network of backers spans cities, countries and cultures, united by a simple idea: when we support each other, incredible things can happen.
“Crowdfunding isn’t just about money, it's about people standing behind an idea.”

            </p>
        </div>
    </div>
</div>

<!-- OUR MISSION -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Our mission.</h2>
        </div>
        <div class="col-md-7">
            <p>
                Our mission is to help bring meaningful projects to life.
We believe creativity, innovation and social progress are essential to a thriving society. Everyone deserves access to opportunity, not just those with privilege, connections, or capital.
ApnaCrowdfunding is built to:
Empower independent creators


Support innovation at every level


Encourage social impact and equality


Give communities a voice in shaping the future


We’re here to put power where it belongs in the hands of people with ideas and purpose.

            </p>
       
        </div>
    </div>
</div>

<!-- STATS -->
<div class="pink-box">
    <h3>Transparency – Honest storytelling and clear expectations
</h3>

</div>

<!-- OUR TEAM -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Our team.</h2>
        </div>
        <div class="col-md-7">
            <p>
                We’re a team of builders, designers, writers, technologists and dreamers who believe in the power of collective action.
Many of us are creators ourselves. We’ve launched ideas, supported causes and backed campaigns we believe in. We know how vulnerable it feels to put your idea out into the world and how powerful it feels when people say “yes.”
That’s why we’re here.


            </p>
       
        </div>
    </div>
</div>

<!-- OUR HISTORY -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Our Journey
</h2>
        </div>
        <div class="col-md-7">
            <p>
               ApnaCrowdfunding was created with a simple belief:
Great ideas shouldn’t fail because of lack of access.
Since our beginning, we’ve focused on building a platform that’s easy to use, fair and creator-friendly that grows alongside its community.
And this is just the start.

            </p>
        </div>
    </div>
</div>

<!-- STORY -->
<div class="deep-blue-box">
 
    <br><br>
 
</div>

<!-- PROJECT UPDATES -->


<!-- CREATIVE INDEPENDENT -->


<!-- PRESS -->


<!-- JUMP IN -->
<div class="container section">
    <div class="row">
        <div class="col-md-5">
            <h2>Jump in.</h2>
        </div>
        <div class="col-md-7">
            <p>
                Ready to support ideas you believe in?
Browse live campaigns and become part of something meaningful.
Have an idea you’re ready to share?
Start your campaign and invite the world to join you.
ApnaCrowdfunding Your idea. Our community. Shared success.

            </p>
            <button class="btn-otline mt-3">Discover projects</button>
        </div>
    </div>
</div>


@endsection
