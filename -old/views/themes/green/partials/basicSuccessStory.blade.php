<div class="success-showcase__card">
    <div class="success-showcase__img">
        <a href="{{ route('stories.show', @$successElement->id) }}">
            <img src="{{ getImage('assets/images/site/success_story/thumb_' . @$successElement->data_info->image, '415x230') }}" alt="image">
        </a>
    </div>
    <div class="success-showcase__txt">
        <h3 class="success-showcase__title">
            <a href="{{ route('stories.show', @$successElement->id) }}">
                {{ __(strLimit(@$successElement->data_info->title, 25)) }}
            </a>
        </h3>
        <p class="success-showcase__desc">{{ __(strLimit(strip_tags(@$successElement->data_info->details), 100)) }}</p>
        <a href="{{ route('stories.show', @$successElement->id) }}" class="btn btn--sm btn--base">
            @lang('Read More')
        </a>
    </div>
</div>

@push('page-style')
    <style>
        .success-showcase__img {
            -webkit-mask-image: url("{{ asset($activeThemeTrue . 'images/campaign-image-shape.png') }}");
            background: url("{{ asset($activeThemeTrue . 'images/campaign-image-shape.png') }}");
            mask-image: url("{{ asset($activeThemeTrue . 'images/campaign-image-shape.png') }}");
        }
    </style>
@endpush
