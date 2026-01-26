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
.stats-box ul li{
    list-style:none;
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

    <!-- HERO / PRESSROOM -->
    <div class="press-hero mb-5">
        <h1>Pressroom</h1>
        <p>
            {!! apnaCrowdfundingLink() !!} is a crowdfunding platform that helps people around the world turn their ideas into reality. It can be a personal project, a social project, a start-up, or anything else. We will make it come true. Working on an {!! apnaCrowdfundingLink() !!} story? You can belong from any profession and upload your story on {!! apnaCrowdfundingLink() !!}.
        </p>
    </div>

    <!-- MEDIA INQUIRIES -->
    <div class="media-inquiries mb-5">
        <h4 class="fw-bold">Media Inquiries</h4>
        <p>Email: <a href="mailto:press@apnacrowdfunding.com">press@apnacrowdfunding.com</a></p>
        <p>You can take help from our support centre and chat with our team.</p>
    </div>

    <!-- STATS -->
    <div class="stats-box mb-5">
        <h4 class="fw-bold">{!! apnaCrowdfundingLink() !!} at a glance</h4>
        <ul>
            <li>Since our launch, {!! apnaCrowdfundingLink() !!} has helped thousands of individuals, families, creators, and entrepreneurs raise funds for causes that truly matter.</li>
            <li>Thousands of campaigns launched</li>
            <li>Millions raised for social and creative causes</li>
            <li>Transparent fundraising</li>
        </ul>
    </div>


    <!-- OUR STORY -->
    <div class="our-story mb-5">
        <h4 class="fw-bold">Our Story</h4>
        <p>
            {!! apnaCrowdfundingLink() !!} has a simple realization: People want to help but they need the right platform to do so. Countless families, creators, and innovators struggle to access timely financial support.
        </p>
    </div>

    <!-- LEADERSHIP & TEAM -->
    <div class="leadership-box mb-5">
        <h4 class="fw-bold">Leadership & Team</h4>
        <p>{!! apnaCrowdfundingLink() !!} is a team of people who believe that crowdfunding can change lives. "Crowdfunding is about compassion and hope."</p>
    </div>

    <!-- EXISTING FAQ (unchanged) -->
    <div class="mb-5">
        <h4 class="fw-bold mb-4">The basics</h4>

        <div class="faq-item">
            <div class="faq-question">
                <span>How does {!! apnaCrowdfundingLink() !!} work?</span>
                <span class="faq-icon">+</span>
            </div>
            <div class="faq-answer">
             Create a campaign, share your story and invite people to show their support. Funds go directly to the beneficiary with complete transparency.

            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Why do people raise funds?
</span>
                <span class="faq-icon">+</span>
            </div>
            <div class="faq-answer">
                Our campaigns are a representation of true human stories. A hope, urgency, creativity or change.

            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>What kind of causes are allowed?</span>
                <span class="faq-icon">+</span>
            </div>
            <div class="faq-answer">
                Medical emergencies, education, startups, creative projects, NGOs, social causes, community needs and more.

            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>How does {!! apnaCrowdfundingLink() !!} sustain itself?
</span>
                <span class="faq-icon">+</span>
            </div>
            <div class="faq-answer">
               We will take a small amount of fee to give security to our creators.

            </div>
        </div>
    </div>

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
