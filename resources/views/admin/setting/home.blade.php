@extends('admin.layouts.master')

@section('master')
    <div class="col-12">
        <div class="custom--card">
            <div class="card-header">
                <h3 class="title">@lang('Home Page Settings')</h3>
            </div>
            <div class="card-body">
                <form class="row g-lg-4 g-3" action="{{ route('admin.home.setting.update') }}" method="POST">
                    @csrf
                    
                    <!-- Hero Section -->
                    <div class="col-12">
                        <h5 class="mb-3">@lang('Hero Section')</h5>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Hero Title Line 1')</label>
                        <input type="text" class="form--control" name="home_hero_title_1" value="{{ $setting->home_hero_title_1 ?? 'Funding By The People' }}" placeholder="@lang('Funding By The People')">
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Hero Title Line 2')</label>
                        <input type="text" class="form--control" name="home_hero_title_2" value="{{ $setting->home_hero_title_2 ?? 'Funding For The People' }}" placeholder="@lang('Funding For The People')">
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Hero Subtitle')</label>
                        <textarea class="form--control" name="home_hero_subtitle" rows="3" placeholder="@lang('Together, we empower small businesses— From young dreamers, bold visionaries and those who want to improve their societies.')">{{ $setting->home_hero_subtitle ?? 'Together, we empower small businesses— From young dreamers, bold visionaries and those who want to improve their societies.' }}</textarea>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Business Button Text')</label>
                        <input type="text" class="form--control" name="home_business_button_text" value="{{ $setting->home_business_button_text ?? 'For Business' }}" placeholder="@lang('For Business')">
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Personal Button Text')</label>
                        <input type="text" class="form--control" name="home_personal_button_text" value="{{ $setting->home_personal_button_text ?? 'For Myself' }}" placeholder="@lang('For Myself')">
                    </div>

                    <!-- Resource Section -->
                    <div class="col-12">
                        <h5 class="mb-3 mt-4">@lang('Resource Section')</h5>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Resource Title')</label>
                        <input type="text" class="form--control" name="home_resource_title" value="{{ $setting->home_resource_title ?? 'Creator Resource Hub' }}" placeholder="@lang('Creator Resource Hub')">
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Resource Subtitle')</label>
                        <input type="text" class="form--control" name="home_resource_subtitle" value="{{ $setting->home_resource_subtitle ?? 'It\'s your turn. Learn how to get started.' }}" placeholder="@lang('It\'s your turn. Learn how to get started.')">
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Resource Description')</label>
                        <textarea class="form--control" name="home_resource_description" rows="3" placeholder="@lang('Everything you need to get your project off the ground, reach the right people, build a marketing campaign, and see it through.')">{{ $setting->home_resource_description ?? 'Everything you need to get your project off the ground, reach the right people, build a marketing campaign, and see it through.' }}</textarea>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Resource Button Text')</label>
                        <input type="text" class="form--control" name="home_resource_button_text" value="{{ $setting->home_resource_button_text ?? 'Let\'s go' }}" placeholder="@lang('Let\'s go')">
                    </div>

                    <!-- Steps Section -->
                    <div class="col-12">
                        <h5 class="mb-3 mt-4">@lang('Steps Section')</h5>
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Steps Title')</label>
                        <input type="text" class="form--control" name="home_steps_title" value="{{ $setting->home_steps_title ?? 'Create with confidence' }}" placeholder="@lang('Create with confidence')">
                    </div>
                    
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label">@lang('Step 1 Title')</label>
                        <input type="text" class="form--control" name="home_step_1_title" value="{{ $setting->home_step_1_title ?? '23 million+ backers on Kickstarter' }}" placeholder="@lang('23 million+ backers on Kickstarter')">
                    </div>
                    <div class="col-lg-8 col-sm-6">
                        <label class="form--label">@lang('Step 1 Description')</label>
                        <textarea class="form--control" name="home_step_1_description" rows="3" placeholder="@lang('Connect with passionate, engaged backers who share your values and interests.')">{{ $setting->home_step_1_description ?? 'Connect with passionate, engaged backers who share your values and interests. You\'ll find your community here — people who get your vision and are excited to support it.' }}</textarea>
                    </div>
                    
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label">@lang('Step 2 Title')</label>
                        <input type="text" class="form--control" name="home_step_2_title" value="{{ $setting->home_step_2_title ?? 'Creative independence' }}" placeholder="@lang('Creative independence')">
                    </div>
                    <div class="col-lg-8 col-sm-6">
                        <label class="form--label">@lang('Step 2 Description')</label>
                        <textarea class="form--control" name="home_step_2_description" rows="3" placeholder="@lang('Create on your own terms.')">{{ $setting->home_step_2_description ?? 'Create on your own terms. You\'ll always have complete creative control and ownership of every project you launch on Kickstarter.' }}</textarea>
                    </div>
                    
                    <div class="col-lg-4 col-sm-6">
                        <label class="form--label">@lang('Step 3 Title')</label>
                        <input type="text" class="form--control" name="home_step_3_title" value="{{ $setting->home_step_3_title ?? 'Empowering platform' }}" placeholder="@lang('Empowering platform')">
                    </div>
                    <div class="col-lg-8 col-sm-6">
                        <label class="form--label">@lang('Step 3 Description')</label>
                        <textarea class="form--control" name="home_step_3_description" rows="3" placeholder="@lang('We\'re here to help you succeed.')">{{ $setting->home_step_3_description ?? 'We\'re here to help you succeed. Optimize your results with tools, tips, and support throughout the life of your project and beyond.' }}</textarea>
                    </div>

                    <!-- Success Stories Section -->
                    <div class="col-12">
                        <h5 class="mb-3 mt-4">@lang('Success Stories Section')</h5>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Stories Title')</label>
                        <input type="text" class="form--control" name="home_stories_title" value="{{ $setting->home_stories_title ?? 'Creators are the stars of Apna Fund' }}" placeholder="@lang('Creators are the stars of Apna Fund')">
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Stories Subtitle')</label>
                        <textarea class="form--control" name="home_stories_subtitle" rows="2" placeholder="@lang('We\'re the world\'s leading funding and launch platform because of the stellar ideas that come to life here.')">{{ $setting->home_stories_subtitle ?? 'We\'re the world\'s leading funding and launch platform because of the stellar ideas that come to life here.' }}</textarea>
                    </div>

                    <!-- FAQ Section -->
                    <div class="col-12">
                        <h5 class="mb-3 mt-4">@lang('FAQ Section')</h5>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('FAQ Title')</label>
                        <input type="text" class="form--control" name="home_faq_title" value="{{ $setting->home_faq_title ?? 'Frequently Asked Questions' }}" placeholder="@lang('Frequently Asked Questions')">
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('FAQ Subtitle')</label>
                        <textarea class="form--control" name="home_faq_subtitle" rows="2" placeholder="@lang('Everything you need to know about Apna Fund and crowdfunding')">{{ $setting->home_faq_subtitle ?? 'Everything you need to know about Apna Fund and crowdfunding' }}</textarea>
                    </div>

                    <!-- Community Section -->
                    <div class="col-12">
                        <h5 class="mb-3 mt-4">@lang('Community Section')</h5>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Community Title')</label>
                        <input type="text" class="form--control" name="home_community_title" value="{{ $setting->home_community_title ?? 'Join the Creator Community' }}" placeholder="@lang('Join the Creator Community')">
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <label class="form--label">@lang('Community Button Text')</label>
                        <input type="text" class="form--control" name="home_community_button_text" value="{{ $setting->home_community_button_text ?? 'Sign me up' }}" placeholder="@lang('Sign me up')">
                    </div>
                    <div class="col-12">
                        <label class="form--label">@lang('Community Description')</label>
                        <textarea class="form--control" name="home_community_description" rows="3" placeholder="@lang('Be the first to know about product updates and enjoy monthly inspiration, guides & best practices, webinars and opportunities to connect.')">{{ $setting->home_community_description ?? 'Be the first to know about product updates and enjoy monthly inspiration, guides & best practices, webinars and opportunities to connect.' }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn--base px-4">@lang('Submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 