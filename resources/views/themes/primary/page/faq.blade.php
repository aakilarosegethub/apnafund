@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="faq py-120">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$faqContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$faqContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="accordion custom--accordion" id="accordionExample">
                <div class="row g-4 align-items-center justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        @foreach ($faqElements as $faq)
                            <div class="accordion-item" data-aos="fade-up" data-aos-duration="1500">
                                <h2 class="accordion-header">
                                    <button class="accordion-button @if (!$loop->first) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="{{ '#collapse_' . $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ 'collapse_' . $loop->iteration }}">
                                        {{ __(@$faq->data_info->question) }}
                                    </button>
                                </h2>
                                <div id="{{ 'collapse_' . $loop->iteration }}" class="accordion-collapse collapse @if ($loop->first) show @endif" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @php echo @$faq->data_info->answer @endphp
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include($activeTheme . 'sections.volunteer')
    @include($activeTheme . 'sections.donor')
    @include($activeTheme . 'sections.partner')
@endsection
