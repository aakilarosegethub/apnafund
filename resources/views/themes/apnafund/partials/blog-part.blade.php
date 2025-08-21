@php
    $successContent = getSiteData('success_story.content', true);
    $successElements = getSiteData('success_story.element', false, 4, true);
@endphp

<!-- Latest Success Stories Section -->
<div class="success-stories-section">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="section-title">{{ __(@$successContent->data_info->section_heading ?? 'Latest Success Stories') }}</h2>
            <p class="section-subtitle">{{ __(@$successContent->data_info->description ?? 'Discover inspiring campaigns that achieved their funding goals') }}</p>
        </div>
    </div>

    <div class="row">
        @forelse ($successElements as $successElement)
            <div class="col-lg-3 col-md-6 mb-4">
                @include($activeTheme . 'partials.success-story-card')
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">@lang('No success stories available at the moment.')</p>
            </div>
        @endforelse
    </div>

    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('stories') }}" class="btn-theme">@lang('View All Success Stories')</a>
        </div>
    </div>
</div>

<style>
    /* Success Stories Section Styles */
    .success-stories-section {
        margin: 60px 0;
        padding: 40px 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        text-align: center;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #666;
        text-align: center;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 576px) {
        .success-stories-section {
            margin: 40px 0;
            padding: 30px 0;
        }

        .section-title {
            font-size: 1.6rem;
        }
    }
</style> 