@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="contact py-120">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="1500">
                <div class="col-lg-6">
                    <div class="section-heading text-center">
                        <h2 class="section-heading__title mx-auto">{{ __(@$contactContent->data_info->section_heading) }}</h2>
                        <p class="section-heading__desc">{{ __(@$contactContent->data_info->description) }}</p>
                    </div>
                </div>
            </div>
            <div class="row gy-5 justify-content-lg-around justify-content-center">
                <div class="col-12">
                    <div class="row g-4">
                        @foreach ($contactElements as $contact)
                            <div class="col-lg-4 col-sm-6">
                                <div class="custom--card contact__info__card" data-aos="fade-up" data-aos-duration="1500">
                                    <div class="card-body">
                                        <h3 class="contact__info__title card-subtitle mb-2">@php echo $contact->data_info->icon @endphp {{ __(@$contact->data_info->heading) }}:</h3>
                                        <p>{{ __(@$contact->data_info->data) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-header">
                            <h3 class="title">{{ __(@$contactContent->data_info->form_heading) }}</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" class="row g-3">
                                @csrf
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Your Full Name')</label>
                                    <input type="text" name="name" class="form--control" value="{{ old('name', @$user->fullname) }}" @readonly(@$user) required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Your Email')</label>
                                    <input type="email" name="email" class="form--control" value="{{ old('email', @$user->email) }}" @readonly(@$user) required>
                                </div>
                                <div class="col-12">
                                    <label class="form--label required">@lang('Subject')</label>
                                    <input type="text" name="subject" class="form--control" value="{{ old('subject') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form--label required">@lang('Message')</label>
                                    <textarea name="message" class="form--control" rows="10" required>{{ old('message') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn--base">{{ __(@$contactContent->data_info->form_button_name) }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact__map-card custom--card" data-aos="fade-up" data-aos-duration="1500">
                        <div class="card-body">
                            <div class="contact__map">
                                <iframe src="https://maps.google.com/maps?hl=en&amp;q={{ @$contactContent->data_info->latitude }},%20{{ @$contactContent->data_info->longitude }}+({{ @$setting->site_name }})&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed" loading="lazy" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
