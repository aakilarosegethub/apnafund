@php
    $volunteerContent        = getSiteData('volunteer.content', true);
    $volunteerElements       = getSiteData('volunteer.element', false, 4, true);
@endphp

<div class="volunteer pt-60 pb-120">
    <div class="container">
        <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
            <div class="col-lg-6">
                <div class="section-heading text-center">
                    <h2 class="section-heading__title mx-auto">{{ __(@$volunteerContent->data_info->section_heading) }}</h2>
                    <p class="section-heading__desc">{{ __(@$volunteerContent->data_info->description) }}</p>
                </div>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse ($volunteerElements as $volunteer)
                @include($activeTheme . 'partials.basicVolunteer')
            @empty
                <p class="text-center" data-aos="fade-up" data-aos-duration="1500">{{ __($emptyMessage) }}</p>
            @endforelse
        </div>

    </div>
</div>