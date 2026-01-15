@php
    $partnerElements = getSiteData('partner.element', false, null, true)
@endphp

<div class="payment-gateway pt-60 pb-120">
    <div class="container">
        <div class="payment-gateway__slider" data-aos="fade-up" data-aos-duration="1500">
            @foreach ($partnerElements as $partner)
                <div class="payment-gateway__card">
                    <img src="{{ getImage('assets/images/site/partner/' . @$partner->data_info->image, '200x45') }}" alt="image">
                </div>
            @endforeach
        </div>
    </div>
</div>
