<div class="col-xl-3 col-lg-4 col-md-5 col-sm-6" data-aos="fade-up" data-aos-duration="1500">
    <div class="volunteer__card">
        <div class="volunteer__img">
            <img src="{{ getImage('assets/images/site/volunteer/' . @$volunteer->data_info->volunteer_image, '305x350') }}" alt="image">
        </div>
        <div class="volunteer__txt">
            <h3 class="volunteer__name">{{ __(@$volunteer->data_info->name) }}</h3>
            <ul>
                <li>
                    <span>@lang('Participate'):</span>
                    {{ @$volunteer->data_info->participated }} @lang('Campaigns')
                </li>
                <li>
                    <span>@lang('From'):</span>
                    {{ __(@$volunteer->data_info->from) }}
                </li>
                <li>
                    <span>@lang('Social'):</span>
                    <div class="social">
                        <a href="{{ @$volunteer->data_info->facebook }}" target="_blank">
                            <i class="ti ti-brand-facebook"></i>
                        </a>
                        <a href="{{ @$volunteer->data_info->twitter }}" target="_blank">
                            <i class="ti ti-brand-x"></i>
                        </a>
                        <a href="{{ @$volunteer->data_info->instagram }}" target="_blank">
                            <i class="ti ti-brand-instagram"></i>
                        </a>
                        <a href="{{ @$volunteer->data_info->linkedin }}" target="_blank">
                            <i class="ti ti-brand-linkedin"></i>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
