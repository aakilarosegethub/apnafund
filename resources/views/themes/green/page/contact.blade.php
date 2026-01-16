@php
    $activeTheme = activeTheme();
    $activeThemeTrue = activeTheme();
@endphp
@extends($activeTheme . 'layouts.green-home')

@section('content')
<style>
.contact-section {
    padding: 80px 0;
    background: #f9fafb;
}
.contact-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
    transition: .25s;
    height: 100%;
}
.contact-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px rgba(0,0,0,.1);
}
.contact-info-card {
    padding: 30px;
    text-align: center;
}
.contact-info-card h3 {
    font-weight: 600;
    color: var(--green);
    font-size: 1.1rem;
    margin-bottom: 15px;
}
.contact-info-card p {
    color: #6b7280;
    margin: 0;
    font-size: .95rem;
}
.contact-form-card {
    padding: 30px;
}
.contact-form-card h3 {
    font-weight: 700;
    color: #111827;
    margin-bottom: 25px;
    font-size: 1.5rem;
}
.form-control {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: .95rem;
    transition: all .2s;
}
.form-control:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    outline: none;
}
.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
    font-size: .9rem;
}
.btn-success {
    background: var(--green);
    border: none;
    border-radius: 8px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all .2s;
}
.btn-success:hover {
    background: #15803d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}
.contact-map-card {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
    height: 100%;
}
.contact-map iframe {
    width: 100%;
    height: 400px;
    border: none;
    display: block;
}
.section-heading {
    margin-bottom: 50px;
}
.section-heading h2 {
    font-weight: 700;
    color: #111827;
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    margin-bottom: 15px;
}
.section-heading p {
    color: #6b7280;
    font-size: 1.05rem;
    max-width: 600px;
    margin: 0 auto;
}
@media(max-width: 768px) {
    .contact-section {
        padding: 60px 0;
    }
    .contact-map iframe {
        height: 300px;
    }
}
</style>

<section class="contact-section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="section-heading text-center">
                    <h2>{{ __(@$contactContent->data_info['section_heading'] ?? 'Contact Us') }}</h2>
                    <p>{{ __(@$contactContent->data_info['description'] ?? 'Get in touch with us. We\'d love to hear from you.') }}</p>
                </div>
            </div>
        </div>
        
        @if($contactElements && count($contactElements) > 0)
        <div class="row g-4 mb-5">
            @foreach ($contactElements as $contact)
                <div class="col-lg-4 col-md-6">
                    <div class="contact-card contact-info-card">
                        <h3>@php echo @$contact->data_info['icon'] ?? '' @endphp {{ __(@$contact->data_info['heading'] ?? '') }}</h3>
                        <p>{{ __(@$contact->data_info['data'] ?? '') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="contact-card contact-form-card">
                    <h3>{{ __(@$contactContent->data_info['form_heading'] ?? 'Send us a Message') }}</h3>
                    <form action="{{ route('contact') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-sm-6">
                            <label class="form-label">@lang('Your Full Name') <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', @$user->fullname) }}" @readonly(@$user) required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">@lang('Your Email') <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', @$user->email) }}" @readonly(@$user) required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Subject') <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Message') <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="6" required>{{ old('message') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">{{ __(@$contactContent->data_info['form_button_name'] ?? 'Send Message') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-map-card">
                    <div class="contact-map">
                        <iframe src="https://maps.google.com/maps?hl=en&amp;q={{ @$contactContent->data_info['latitude'] ?? '0' }},%20{{ @$contactContent->data_info['longitude'] ?? '0' }}+({{ @$setting->site_name ?? 'ApnaCrowdfunding' }})&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed" loading="lazy" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
