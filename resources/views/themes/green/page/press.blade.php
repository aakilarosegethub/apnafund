@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')

<style>
.press-hero{
    text-align:center;
    padding:100px 20px 60px;
}
.press-hero h1{
    font-size:42px;
    font-weight:700;
    margin-top: 50px;
}
.press-hero p{
    max-width:650px;
    margin:15px auto 0;
    color:#555;
}

.stats-box{
    background:#a89cf6;
    color:#fff;
    padding:60px 30px;
    text-align:center;
    margin-bottom:80px;
}
.stats-box h2{
    font-size:28px;
    font-weight:700;
}
.stats-box p{
    max-width:700px;
    margin:15px auto;
}
.stats-box .btn{
    border:1px solid #fff;
    color:#fff;
}

.faq-box{
    border:1px solid #ddd;
    padding:16px 20px;
    margin-bottom:10px;
    cursor:pointer;
}

.quote-box{
    background:#2f55ff;
    color:#fff;
    padding:70px 30px;
    text-align:center;
    margin:80px 0;
}
.quote-box p{
    max-width:700px;
    margin:0 auto;
    font-size:18px;
}

.profile-img{
    width:100%;
    height:320px;
    object-fit:cover;
}

.profile-section{
    padding:80px 0;
}

.highlight-box{
    background:#ff7a59;
    color:#fff;
    padding:70px 30px;
    text-align:center;
    margin:80px 0;
}
.highlight-box p{
    max-width:700px;
    margin:0 auto;
}
.faq-item{
    border-bottom:1px solid #e5e7eb;
    padding:18px 0;
}
.faq-question{
    display:flex;
    justify-content:space-between;
    align-items:center;
    cursor:pointer;
    font-weight:600;
    font-size:16px;
}
.faq-icon{
    font-size:20px;
    transition:0.3s;
}
.faq-answer{
    display:none;
    padding-top:12px;
    color:#555;
    font-size:15px;
    line-height:1.6;
}
.faq-item.active .faq-answer{
    display:block;
}
.faq-item.active .faq-icon{
    transform:rotate(45deg);
}

</style>

<div class="container">

    <!-- HERO -->
    <div class="press-hero">
        <h1>Pressroom</h1>
        <p>
            ApnaCrowdfunding is a people-powered crowdfunding platform built to help dreamers, doers and changemakers across the world and beyond turn ideas into reality. From personal causes and social impact initiatives to startups, creative projects, and community needs we help stories find support and ideas find life.
        </p>
    </div>

    <!-- STATS -->
    <div class="stats-box">
        <h2>Our mission is to make fundraising accessible, transparent, and human.
</h2>
       
        <a href="#" class="btn mt-3">View company stats</a>
    </div>

    <!-- FAQ -->
<div class="mb-5">
    <h4 class="fw-bold mb-4">The basics</h4>

    <div class="faq-item">
        <div class="faq-question">
            <span>What is ApnaCrowdfunding?</span>
            <span class="faq-icon">+</span>
        </div>
        <div class="faq-answer">
            ApnaCrowdfunding is a platform that helps creators bring ideas to life
            with the support of a community that believes in them.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>How does funding work?</span>
            <span class="faq-icon">+</span>
        </div>
        <div class="faq-answer">
            Creators set a goal and timeline. Supporters pledge money to help
            reach that goal. If the goal is met, the project moves forward.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Who can launch a project?</span>
            <span class="faq-icon">+</span>
        </div>
        <div class="faq-answer">
            Anyone with a creative or meaningful idea can launch a project,
            as long as it follows our platform guidelines.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Where is ApnaCrowdfunding available?</span>
            <span class="faq-icon">+</span>
        </div>
        <div class="faq-answer">
            ApnaCrowdfunding is accessible online and supports creators
            and backers from multiple regions.
        </div>
    </div>
</div>

</div>

<!-- QUOTE -->
<div class="quote-box">
    <p>
        “Kickstarter gives creative people the freedom to build ambitious ideas
        with the support of a passionate global community.”
    </p>
</div>

<div class="container">

    <!-- PROFILE 1 -->
    <div class="row profile-section align-items-center">
        <div class="col-md-5">
            <img src="https://images.unsplash.com/photo-1527980965255-d3b416303d12" class="profile-img" alt="">
        </div>
        <div class="col-md-7">
            <h5 class="fw-bold">Creator Story</h5>
            <p>
                A musician shares how Kickstarter helped turn an independent album into a global success.
            </p>
        </div>
    </div>

    <!-- TEAM -->
    <div class="row profile-section">
        <div class="col-md-4">
            <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61" class="profile-img" alt="">
        </div>
        <div class="col-md-4">
            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e" class="profile-img" alt="">
        </div>
        <div class="col-md-4">
            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2" class="profile-img" alt="">
        </div>
    </div>

</div>

<!-- HIGHLIGHT -->
<div class="highlight-box">
    <p>
        “Kickstarter is the world’s largest platform for funding creative work,
        helping artists, designers, and innovators bring their ideas to life.”
    </p>
</div>

<div class="container mb-5">

    <h4 class="fw-bold mb-3">Highlights</h4>
    <p>
        Kickstarter continues to support creative independence by enabling
        artists and innovators to work directly with their audiences.
    </p>

</div>
<script>
document.querySelectorAll('.faq-question').forEach(item=>{
    item.addEventListener('click',()=>{
        const parent = item.parentElement;
        parent.classList.toggle('active');
    });
});
</script>


@endsection
