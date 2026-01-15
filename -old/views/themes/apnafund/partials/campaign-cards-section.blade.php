@php
    $campaignCards = [
        [
            'image' => 'assets/images/banner-1.jpg',
            'title' => 'Campaign Basics',
            'description' => 'Learn the fundamentals of creating a successful crowdfunding campaign',
            'link' => route('stories.show', 1)
        ],
        [
            'image' => 'assets/images/banner-2.jpg',
            'title' => 'Telling Your Story',
            'description' => 'Craft a compelling narrative that resonates with potential backers',
            'link' => route('stories.show', 2)
        ],
        [
            'image' => 'assets/images/banner-3.jpeg',
            'title' => '10 Ways To Level Up',
            'description' => 'Expert advice to maximize your campaign\'s potential and reach',
            'link' => route('stories.show', 3)
        ],
        [
            'image' => 'assets/images/banner-4.jpg',
            'title' => 'Funding Strategies',
            'description' => 'Effective approaches to reach your funding goals and beyond',
            'link' => route('stories.show', 4)
        ]
    ];
@endphp

<div class="row mb-4">
    <div class="col-12">
        <h2>@lang('Build a winning campaign')</h2>
    </div>
</div>

<div class="row mb-5">
    @foreach ($campaignCards as $card)
        <div class="col-md-3 mb-4">
            <div class="campaign-card">
                <div class="campaign-card-img"
                    style="background-image: url('{{ asset($card['image']) }}'); background-size: cover; background-position: center;">
                </div>
                <div class="campaign-card-body">
                    <h3 class="campaign-card-title">@lang($card['title'])</h3>
                    <p class="campaign-card-text">@lang($card['description'])</p>
                    <a href="{{ $card['link'] }}" class="campaign-card-link">@lang('Learn more') <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    @endforeach
</div> 