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
        Route::get('donations', 'campaignDonations')->name('donations');
        Route::get('donations/top', 'campaignTopDonations')->name('donations.top');
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

    // Help & Sitemap
    Route::get('help', 'help')->name('help');
    Route::get('sitemap', 'sitemap')->name('sitemap');

    // Policy Details
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    // Update user country in session
    Route::post('/update-user-country', [App\Http\Controllers\WebsiteController::class, 'updateUserCountry'])->name('update.user.country');

// Test route for IP detection
Route::get('/test-ip-detection', function() {
    $ip = request()->ip();
    $ipCountry = getUserCountryByIP();
    $detectedCountry = detectUserCountry();
    
    return response()->json([
        'user_ip' => $ip,
        'ip_country' => $ipCountry,
        'detected_country' => $detectedCountry,
        'session_country' => session('user_country'),
        'headers' => [
            'HTTP_CF_CONNECTING_IP' => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? null,
            'HTTP_X_FORWARDED_FOR' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
            'HTTP_X_REAL_IP' => $_SERVER['HTTP_X_REAL_IP'] ?? null,
            'HTTP_CLIENT_IP' => $_SERVER['HTTP_CLIENT_IP'] ?? null,
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]
    ]);
});

// YouTube OAuth Callback (Public route)
Route::get('/youtube/callback', function(\Illuminate\Http\Request $request) {
    try {
        $youtubeService = new \App\Services\YouTubeUploadService();
        $accessToken = $youtubeService->handleCallback($request->get('code'));
        
        // You can store tokens in database or session here
        // For now, we'll redirect to admin with success message
        
        return redirect('/admin/youtube')->with('success', 'YouTube authorization successful!');
        
    } catch (\Exception $e) {
        return redirect('/admin/youtube')->with('error', 'YouTube authorization failed: ' . $e->getMessage());
    }
})->name('youtube.callback');
});
