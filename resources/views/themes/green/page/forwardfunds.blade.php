@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')

<style>
.forward-hero{
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}
.forward-hero img{
    width:100%;
    height:420px;
    object-fit:cover;
}
.forward-box{
    position:absolute;
    left:40px;
    bottom:40px;
    background:#fff;
    padding:24px 28px;
    max-width:360px;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
}
.forward-box h2{
    font-size:28px;
    font-weight:700;
    margin-bottom:10px;
}
.forward-section{
    padding:70px 0;
}
.forward-card{
    border-left:4px solid #1aa34a;
    padding-left:20px;
}
.forward-banner{
    background:#0b3b2e;
    color:#fff;
    border-radius:8px;
    overflow:hidden;
}
.forward-banner img{
    width:100%;
    height:180px;
    object-fit:cover;
}
.new_2 ul{
    margin-left:-14px;
}
.new_1{
        position: relative;
    left: 70px;
}

</style>

<div class="container">

    <!-- HERO -->
    <div class="forward-hero mb-5">
        <img src="https://images.unsplash.com/photo-1526045612212-70caf35c14df" alt="">
        <div class="forward-box">
            <h2>Forward Funds</h2>
            <p class="mb-0">
               Powering Ideas That Build a Better Tomorrow
            </p>
        </div>
    </div>

    <!-- INTRO TEXT -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <p>
                At ApnaCrowdfunding, we believe that powerful ideas deserve powerful backing.
Forward Funders are foundations, nonprofits, brands, and mission-driven organizations that actively support crowdfunding campaigns aligned with their vision for a more creative, inclusive, and equitable world.
These funders don’t just donate, they stand behind ideas, publicly commit to causes they believe in, and back projects through direct pledges that help turn dreams into reality.

            </p>
        </div>
    </div>

    <!-- FOR CREATORS / ORGANIZATIONS -->
             <div class="col-md-6 mb-4">
            <h5 class="fw-bold">What Is a Forward Funder?
</h5>
            <p>Believes in the power of creativity and community


Supports projects aligned with its social, cultural, or environmental mission


Makes public commitments to back meaningful campaigns


Funds projects just like any supporter  through transparent, single pledges.



            </p>
        </div>
    <div class="row mb-5 new_2">
        <div class="col-md-6 mb-4">
            <h5 class="fw-bold">For Creators</h5>
            <p>
                Your Vision, Backed by Purpose
                <br>
                Every project launched on ApnaCrowdfunding that follows our platform guidelines is eligible for Forward Funding.

            </p>
            <br></br>
            <ul>
                <li>Nominate their own projects for specific Forward Funds
</li>
               <li>Get discovered by organizations actively looking to support impactful ideas

</li>
               <li>Benefit from curated exposure to aligned funders
</li>
            </ul>

        </div>



        <div class="col-md-6 mb-4 new_1">
            <h5 class="fw-bold">For Organizations</h5>
            <p>Turn Your Mission Into Measurable Impact
Forward Funders at ApnaCrowdfunding are united by a shared belief:
 Creative ideas drive social change.

            </p>
        </div>
    </div>

    <!-- FUNDER CARD -->
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <div class="forward-banner">
                <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d" alt="">
            </div>
        </div>
        <div class="col-md-6">
            <div class="forward-card">
                <h5 class="fw-bold"> Why Forward Funding Matters
</h5>
                <p>
                   Encourages bold and underrepresented voices


Strengthens community-driven innovation


Turns institutional support into real-world outcomes


Makes crowdfunding more equitable and inclusive.

                </p>
             
            </div>
        </div>
    </div>

</div>

@endsection
