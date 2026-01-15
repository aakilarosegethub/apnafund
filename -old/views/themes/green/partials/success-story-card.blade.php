<div class="campaign-card">
    <div class="campaign-card-img"
        style="background-image: url('{{ getImage('assets/images/site/success_story/thumb_' . @$successElement->data_info->image, '415x230') }}'); background-size: cover; background-position: center;">
    </div>
    <div class="campaign-card-body">
        <h3 class="campaign-card-title">{{ __(strLimit(@$successElement->data_info->title, 30)) }}</h3>
        <p class="campaign-card-text">{{ __(strLimit(strip_tags(@$successElement->data_info->details), 80)) }}</p>
        <a href="{{ route('stories.show', @$successElement->id) }}" class="campaign-card-link">@lang('Read More') <i class="fas fa-arrow-right"></i></a>
    </div>
</div> 