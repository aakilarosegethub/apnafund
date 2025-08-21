<?php

use Illuminate\Support\Facades\Route;
use App\Models\SiteData;
use App\Models\Campaign;
use App\Models\Category;

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

Route::controller('WebsiteController')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('home-new', 'homeNew')->name('home.new');
    Route::get('volunteers', 'volunteers')->name('volunteers');
    Route::get('about', 'aboutUs')->name('about.us');
    Route::get('faq', 'faq')->name('faq');
    Route::get('campaigns', 'campaigns')->name('campaign');

    // Campaign 
    Route::prefix('campaign/{slug}')->name('campaign.')->group(function () {
        Route::get('/', 'campaignShow')->name('show');
        Route::get('/contribute', 'campaignDonate')->name('donate');
        Route::get('/rewards', 'RewardController@show')->name('rewards');
        Route::post('comment', 'storeCampaignComment')->name('comment');
        Route::get('fetch-comment', 'fetchCampaignComment')->name('comment.fetch');
    });

    Route::get('upcoming-campaigns', 'upcomingCampaigns')->name('upcoming');
    Route::get('upcoming-campaign/{slug}', 'upcomingCampaignShow')->name('upcoming.show');

    // Success Stories
    Route::get('success-stories', 'stories')->name('stories');
    Route::get('success-story/{id}', 'storyShow')->name('stories.show');

    // Business Resources
    Route::get('business-resources', 'businessResources')->name('business.resources');

    // Subscriber
    Route::post('subscriber/store', 'subscriberStore')->name('subscriber.store');;

    // Contact
    Route::get('contact', 'contact')->name('contact');
    Route::post('contact', 'contactStore');

    // Cookie
    Route::get('cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    // Language
    Route::get('change/{lang?}', 'changeLanguage')->name('lang');

    // Policy Details
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');
});
