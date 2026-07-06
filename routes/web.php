<?php

/**
 * Application routes (web + JSON API).
 *
 * **Note:** Mobile/legacy APIs live here under `Route::prefix('api')`, not in `routes/api.php`.
 * Grouping below: beta/cron/debug → public site (`WebsiteController`) → redirects → user/admin → gateways → `/api` blocks.
 * See inline comments throughout for HTTP methods, middleware, and endpoint purpose.
 */

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\OTPController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\FundController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FundUpdateController;
use App\Http\Controllers\Api\CampaignManageApiController;
use App\Http\Controllers\CampaignVerificationDocumentController;
use App\Http\Controllers\Api\CampaignCollaboratorApiController;
use App\Http\Controllers\Api\CampaignPaymentApiController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DonateController;
use App\Http\Controllers\Api\WithdrawController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\CurrencyInfoController;
use App\Http\Controllers\Api\AllowedLocationCountriesController;
use App\Http\Controllers\Api\UserNotificationApiController;
use App\Models\Campaign;
use Illuminate\Support\Facades\Cookie;
Route::get('/clear-cache', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');

    return response()->json([
        'success' => true,
        'message' => 'All caches cleared successfully.'
    ]);
});
// Beta landing page logic
Route::get('/beta', function (\Illuminate\Http\Request $request) {
    // If cookie already exists, go to normal home
    if (Cookie::get('apnafund_beta_seen')) {
        return redirect()->route('home');
    }

    return view('themes.green.page.beta');
})->name('beta.page');

Route::post('/beta/start', function (\Illuminate\Http\Request $request) {
    // Set cookie for 1 day (60 minutes * 24 hours)
    $cookie = Cookie::make('apnafund_beta_seen', true, 60 * 24);

    return redirect()->route('home')->withCookie($cookie);
})->name('beta.accept');

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

// Debug currency detection (live: ?key=YOUR_SECRET from .env DEBUG_CURRENCY_KEY)
Route::get('/debug-currency', function () {
    $key = request('key');
    if ($key !== env('DEBUG_CURRENCY_KEY') && !config('app.debug')) {
        return response()->json(['error' => 'Forbidden'], 403);
    }
    $ip = request()->ip();
    $ipHeaders = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    $resolvedIp = $ip;
    foreach ($ipHeaders as $h) {
        if (!empty($_SERVER[$h])) {
            $resolvedIp = trim(explode(',', (string)($_SERVER[$h] ?? ''))[0]);
            if ($resolvedIp && !in_array($resolvedIp, ['127.0.0.1', '::1', 'localhost'])) break;
        }
    }
    $tcur = config('app.currency');
    $hasTable = \Illuminate\Support\Facades\Schema::hasTable('ip_currency_cache');
    $dbRow = $hasTable ? \Illuminate\Support\Facades\DB::table('ip_currency_cache')->where('ip', $resolvedIp)->first() : null;
    $geo = function_exists('getIpGeoData') ? getIpGeoData($resolvedIp) : null;
    $data = function_exists('getOrFetchIpCurrencyData') ? getOrFetchIpCurrencyData($resolvedIp) : null;
    $setting = \App\Models\Setting::first();
    return response()->json([
        'ip_raw' => $ip,
        'ip_resolved' => $resolvedIp,
        'ip_likely_localhost' => in_array($resolvedIp, ['127.0.0.1', '::1', 'localhost']),
        'tcur_env' => env('TCUR'),
        'config_app_currency' => $tcur,
        'session_currency' => session('user_detected_currency'),
        'session_symbol' => session('user_detected_symbol'),
        'session_country' => session('user_detected_country'),
        'ip_currency_cache_exists' => $hasTable,
        'db_row_for_ip' => $dbRow ? ['currency_code' => $dbRow->currency_code, 'country_name' => $dbRow->country_name, 'refreshed_at' => $dbRow->refreshed_at] : null,
        'getIpGeoData_result' => $geo,
        'getOrFetchIpCurrencyData_result' => $data,
        'setting_cur_sym' => $setting?->cur_sym ?? null,
        'setting_site_cur' => $setting?->site_cur ?? null,
    ], 200, ['Content-Type' => 'application/json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
})->name('debug.currency');

// Cron: sync currency rates (public GET; protect with server firewall / scheduler if needed)
Route::get('/cron/currencies-sync', [\App\Http\Controllers\Admin\CurrencyController::class, 'syncRatesPublic'])->name('cron.currencies-sync');

// API routes for email verification (no CSRF required)
Route::post('/api/verify-email', 'App\Http\Controllers\User\AuthorizationController@emailVerificationApi')
    ->middleware('throttle:20,1')
    ->name('api.verify.email');

// Social signup — terms acceptance (routes/user.php is not always writable in deploy environments)
Route::middleware([
    'web',
    'maintenance',
    \App\Http\Middleware\PreventSensitivePageCache::class,
])->prefix('user')->name('user.')->group(function () {
    Route::get('terms/accept', [\App\Http\Controllers\User\Auth\SocialLoginController::class, 'showTermsAcceptForm'])->name('terms.accept.form');
    Route::post('terms/accept', [\App\Http\Controllers\User\Auth\SocialLoginController::class, 'acceptTerms'])->name('terms.accept');
});

// Campaign CNIC / verification documents — login + owner/collaborator check in controller
Route::middleware(['web', 'maintenance', 'auth'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('cnic/{id}', [CampaignVerificationDocumentController::class, 'showByDocumentId'])
            ->where('id', '[A-Za-z0-9._-]+')
            ->name('verification.document');
    });

// Admin CNIC document serve (routes/admin.php is not always writable in deploy environments)
Route::middleware(['web', 'admin', 'admin.permission'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('cnic/{id}', [CampaignVerificationDocumentController::class, 'adminShowByDocumentId'])
            ->where('id', '[A-Za-z0-9._-]+')
            ->name('verification.document');
    });

// Redirect /user/campaign/new to /start-project
Route::get('/user/campaign/new', function () {
    return redirect()->route('start.project');
})->name('user.campaign.new.redirect');

// Password Reset Page
Route::get('/change.htm', function () {
    return response()->file(public_path('change.htm'));
})->name('password.reset.page');

// All public website routes that should respect the beta page
Route::middleware(\App\Http\Middleware\BetaGate::class)->group(function () {
Route::controller('WebsiteController')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('home-new', 'homeNew')->name('home.new');
    Route::get('about-us', 'aboutUs')->name('about.us');
    Route::get('about', function () {
        return redirect('/about-us');
    })->name('about.redirect');
    Route::get('faq', 'faq')->name('faq');
    Route::get('creators', 'creators')->name('creators');
    Route::get('campaigns/load-more', 'loadMoreCampaigns')->name('campaign.load-more');
    Route::get('campaigns', 'campaigns')->name('campaign');
    Route::get('campaigns/category/{slug}', 'campaignCategory')->name('campaign.category');

    // Campaign 
    Route::prefix('campaign/{slug}')->name('campaign.')->group(function () {
        Route::get('/', 'campaignShow')->name('show');
        Route::get('/contribute', 'campaignDonate')->middleware('auth')->name('donate');
        Route::get('/rewards', 'RewardController@show')->name('rewards');
        Route::post('comment', 'storeCampaignComment')->name('comment');
        Route::get('fetch-comment', 'fetchCampaignComment')->name('comment.fetch');
        Route::get('donations', 'campaignDonations')->name('donations');
        Route::get('donations/top', 'campaignTopDonations')->name('donations.top');
        // Campaign Updates
        Route::get('updates', 'campaignUpdates')->name('updates');
        Route::get('updates/{updateSlug}', 'campaignUpdateShow')->name('update.show');
        Route::post('updates/{updateSlug}/comment', 'storeUpdateComment')->name('update.comment');
    });

    Route::get('upcoming-campaigns', 'upcomingCampaigns')->name('upcoming');
    Route::get('upcoming-campaign/{slug}', 'upcomingCampaignShow')->name('upcoming.show');

    // Success Stories
    Route::get('stories', 'stories')->name('stories');
    Route::get('stories/{slug}', 'storyShow')->name('stories.show');

    // Business Resources / Creator Hub
    Route::get('creator-hub', 'businessResources')->name('creator.hub');
    Route::get('business-resources', 'businessResources')->name('business.resources');
    // Redirect creator-guide to business-resources
    Route::get('creator-guide', function () {
        return redirect('/business-resources', 301);
    })->name('creator.guide.redirect');

    // Start Project
    Route::get('start-project', 'startProject')->name('start.project');
    Route::post('start-project/save-categories', 'saveProjectCategories')->name('start.project.save.categories');
    Route::get('start-project/location', 'projectLocation')->name('start.project.location');
    Route::post('start-project/save-location', 'saveProjectLocation')->name('start.project.save.location');
    Route::get('start-project/terms', 'projectTerms')->name('start.project.terms');
    Route::post('start-project/create-campaign', 'createCampaignFromSession')->name('start.project.create.campaign');

    // Subscriber
    Route::post('subscriber/store', 'subscriberStore')->name('subscriber.store');;

    // Payments not available for visitor currency/region (contribute flow redirect)
    Route::get('payments-unavailable', 'paymentsUnavailableInRegion')->name('payments.unavailable.region');

    // Contact
        // New pretty slug: /contact-us
        Route::get('contact-us', 'contact')->name('contact');
        Route::post('contact-us', 'contactStore');

    // Cookie
    Route::get('cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    // Language
    Route::get('change/{lang?}', 'changeLanguage')->name('lang');

    // Help & Sitemap
    Route::get('help', 'help')->name('help');
    Route::get('sitemap', 'sitemap')->name('sitemap');

    // Editor
    Route::get('editor', 'editor')->name('editor');

    // Public User/Creator Profile
    Route::get('creator/{username}', 'creatorProfile')->name('creator.profile');

    // Order/Donation Thank You
    Route::get('order-success/{id}', 'orderSuccess')->name('order.success');

    // Policy Details
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');
    
    // Report Fundraiser
    Route::get('report-fundraiser', 'reportFundraiser')->name('report.fundraiser');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    // Update user country in session
    Route::post('/update-user-country', [App\Http\Controllers\WebsiteController::class, 'updateUserCountry'])->name('update.user.country');
    Route::post('/update-user-currency', [App\Http\Controllers\WebsiteController::class, 'updateUserCurrency'])->name('update.user.currency');
});

// Redirect /page/about to /about-us
Route::get('page/about', function () {
    return redirect('/about-us');
})->name('page.about.redirect');

// Redirect /page/forwardfunds to /forwardfunds
Route::get('page/forwardfunds', function () {
    return redirect('/forwardfunds');
})->name('page.forwardfunds.redirect');

// Redirect /page/press to /press
Route::get('page/press', function () {
    return redirect('/press');
})->name('page.press.redirect');

    // Redirect /page/rules to /apnacrowdfunding-rules
Route::get('page/rules', function () {
        return redirect('/apnacrowdfunding-rules');
})->name('page.rules.redirect');

// Redirect /page/charter to /our-mission
Route::get('page/charter', function () {
    return redirect('/our-mission');
})->name('page.charter.redirect');

// Redirect /page/story to /our-story for clarity
Route::get('our-story', function(\Illuminate\Http\Request $request) {
    $controller = app(\App\Http\Controllers\WebsiteController::class);
    return $controller->pageBySlug('story');
})->name('our.story');

// Dynamic Page by Slug (outside controller group to avoid binding issues)
Route::get('page/{slug}', [\App\Http\Controllers\WebsiteController::class, 'pageBySlug'])->name('page.show');

// Specific routes for pages with view files (before catch-all dynamic route)
    // New pretty URL for rules page
Route::get('our-rules', function(\Illuminate\Http\Request $request) {
    $controller = app(\App\Http\Controllers\WebsiteController::class);
        // Still load the same page record by its internal slug
    return $controller->pageBySlug('apnacrowdfunding-rules');
})->name('rules');

    // Backward compatible redirect from old URL
Route::get('apnacrowdfunding-rules', function () {
    return redirect('/our-rules');
    })->name('apnacrowdfunding-rules.redirect');

// Careers page route
Route::get('apnacrowdfunding-careers', function(\Illuminate\Http\Request $request) {
    $controller = app(\App\Http\Controllers\WebsiteController::class);
    return $controller->pageBySlug('apnacrowdfunding-careers');
})->name('careers');

Route::get('forwardfunds', function(\Illuminate\Http\Request $request) {
    $controller = app(\App\Http\Controllers\WebsiteController::class);
    return $controller->pageBySlug('forwardfunds');
})->name('forwardfunds');
Route::get('press', function(\Illuminate\Http\Request $request) {
    $controller = app(\App\Http\Controllers\WebsiteController::class);
    return $controller->pageBySlug('press');
})->name('press');
Route::get('our-mission', function(\Illuminate\Http\Request $request) {
    $controller = app(\App\Http\Controllers\WebsiteController::class);
    return $controller->pageBySlug('charter');
})->name('our.mission');
Route::get('charter', function () {
    return redirect('/our-mission');
})->name('charter.redirect');

// Dynamic Pages - Must be at the end to avoid route conflicts
Route::get('{slug}', [App\Http\Controllers\WebsiteController::class, 'dynamicPages'])
    ->name('dynamic.pages')
    ->where('slug', '[a-z0-9-]+');
});

// Logged-in payments hub (explicit URI so /user/payments always resolves; same name as before for route() / menus)
Route::middleware(['auth', 'authorize.status'])->group(function () {
    Route::get('user/payments', [\App\Http\Controllers\User\UserController::class, 'payments'])->name('user.payments');
});

// Backward compatible redirect from old /contact to /contact-us
Route::get('contact', function () {
    return redirect('/contact-us');
})->name('contact.redirect');

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

// JazzCash IPN Callback - Logs all incoming data
Route::any('/jazzcash/ipn', [App\Http\Controllers\Gateway\jazzcashwallet\ProcessController::class, 'ipn'])->name('jazzcash.ipn');

// Test route to demonstrate logging functionality
Route::any('/test-logging', function(\Illuminate\Http\Request $request) {
    try {
        // Create a simple log entry without database
        $logData = [
            'timestamp' => now()->toDateTimeString(),
            'endpoint' => 'test-logging',
            'method' => $request->method(),
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'raw_input' => $request->getContent(),
            'url' => $request->fullUrl()
        ];
        
        // Log to file for testing
        \Log::info('Data Logging Test', $logData);
        
        return response()->json([
            'message' => 'Data logged successfully',
            'logged_data' => $logData,
            'note' => 'Check storage/logs/laravel.log for the logged data'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Logging failed: ' . $e->getMessage()
        ], 500);
    }
})->name('test.logging');

// Mobile App API Routes (moved from routes/api.php)
Route::prefix('api')->middleware('throttle:60,1')->group(function () {

    // Public APIs (No authentication required)
    Route::match(['get', 'post'], '/home_api.php', [HomeController::class, 'index']);
    Route::match(['get', 'post'], '/home.php', [HomeController::class, 'index']);
    Route::match(['get', 'post'], '/catwisefund.php', [FundController::class, 'categoryWiseFund']);
    Route::match(['get', 'post'], '/search_fund.php', [FundController::class, 'searchFund']);
    Route::match(['get', 'post'], '/fundidwise.php', [FundController::class, 'fundById']);
    Route::match(['get', 'post'], '/currency_info.php', [CurrencyInfoController::class, 'index']);
    /** Admin Basic → allowed project-location countries (country_id = 1-based index in master list) */
    Route::match(['get', 'post'], '/allowed_location_countries.php', [AllowedLocationCountriesController::class, 'allowedList']);
    /** Same currency fields as currency_info.php, scoped by country_id from allowed_location_countries.php */
    Route::match(['get', 'post'], '/currency_info_by_country.php', [AllowedLocationCountriesController::class, 'currencyByCountry']);
    Route::match(['get', 'post'], '/catlist.php', [CategoryController::class, 'categoryList']);
    Route::match(['get', 'post'], '/charitylist.php', [CategoryController::class, 'charityList']);
    Route::match(['get', 'post'], '/faq.php', [FaqController::class, 'faqList']);
    /** Per-campaign FAQ: list/get public; create/update/delete still require Bearer token in controller */
    Route::match(['get', 'post'], '/campaign_faq.php', [CampaignManageApiController::class, 'faqs']);
    /** Backer updates list/get (op=list|get): GET without token for approved campaigns; POST mutations stay behind sanctum */
    Route::get('/campaign_post_updates.php', [CampaignManageApiController::class, 'postUpdatesPublicRead']);
    /** Campaign comments create: guest(name/email) or user(token/user_id). */
    Route::match(['get', 'post'], '/campaign_comment.php', [CampaignManageApiController::class, 'storeCampaignCommentApi']);
    /** Campaign update comments list (public): slug/campaign_id + update_id. */
    Route::match(['get', 'post'], '/campaign_update_comments.php', [CampaignManageApiController::class, 'listUpdateCommentsApi']);
    Route::match(['get', 'post'], '/pagelist.php', [PageController::class, 'pageList']);
    Route::match(['get', 'post'], '/paymentgateway.php', [PaymentController::class, 'paymentGatewayList']);
    Route::get('/gateways', [PaymentController::class, 'gateways']);
    Route::match(['get', 'post'], '/payment/webview-url', [PaymentController::class, 'webviewUrl']);
    Route::post('/payment/manual-proof', [PaymentController::class, 'manualProof']);

    // Auth APIs (Public - No token required for login/register)
    Route::match(['get', 'post'], '/reg_user.php', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::match(['get', 'post'], '/user_login.php', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::match(['get', 'post'], '/forget_password.php', [AuthController::class, 'forgetPassword']);
    Route::match(['get', 'post'], '/social_login.php', [AuthController::class, 'socialLogin']);
    Route::match(['get', 'post'], '/mobile_check.php', [AuthController::class, 'checkMobile']);
    Route::match(['get', 'post'], '/verify_email_otp.php', [AuthController::class, 'verifyEmailOTP']);
    Route::match(['get', 'post'], '/resend_mobile_otp.php', [AuthController::class, 'resendMobileOTP']);
    Route::match(['get', 'post'], '/verify_mobile_otp.php', [AuthController::class, 'verifyMobileOTP']);
    Route::match(['get', 'post'], '/send_password_reset_otp.php', [AuthController::class, 'sendPasswordResetOTP']);
    Route::match(['get', 'post'], '/verify_password_reset_otp.php', [AuthController::class, 'verifyPasswordResetOTP']);
    Route::match(['get', 'post'], '/reset_password.php', [AuthController::class, 'resetPassword']);

    // OTP APIs (Public)
    Route::match(['get', 'post'], '/msg_otp.php', [OTPController::class, 'msgOTP']);
    Route::match(['get', 'post'], '/twilio_otp.php', [OTPController::class, 'twilioOTP']);
    Route::match(['get', 'post'], '/sms_type.php', [OTPController::class, 'smsType']);

    // Protected APIs (Require authentication via Bearer token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('cnic/{id}', [CampaignVerificationDocumentController::class, 'showByDocumentId'])
            ->where('id', '[A-Za-z0-9._-]+')
            ->name('api.verification.document');

        // Fund APIs
        Route::match(['get', 'post'], '/fundlist.php', [FundController::class, 'fundList']);
        Route::match(['get', 'post'], '/fundraise.php', [FundController::class, 'fundRaise']);

        // Campaign story, rewards, FAQ, backer updates (op=...; campaign_id|fund_id|slug)
        Route::match(['get', 'post'], '/campaign_story.php', [CampaignManageApiController::class, 'story']);
        Route::match(['get', 'post'], '/campaign_rewards.php', [CampaignManageApiController::class, 'rewards']);
        Route::post('/campaign_post_updates.php', [CampaignManageApiController::class, 'postUpdates']);
        /** Comment on published update — Bearer only; no CSRF (mobile vs web /updates/{id}/comment) */
        Route::post('/campaign_update_comment.php', [CampaignManageApiController::class, 'storeUpdateCommentApi']);
        /** People / collaborators — search users, list, add, remove (Bearer; web: user/campaign/collaborators/*) */
        Route::match(['get', 'post'], '/campaign_collaborators.php', [CampaignCollaboratorApiController::class, 'collaborators']);
        /** Payout banks list + save account (web: user/campaign/edit/{slug}/payment) */
        Route::match(['get', 'post'], '/campaign_payment.php', [CampaignPaymentApiController::class, 'payment']);
        Route::get('/campaign_required_documents.php', [CampaignManageApiController::class, 'requiredDocuments']);
        Route::post('/campaign_required_documents_submit.php', [CampaignManageApiController::class, 'submitRequiredDocuments']);
        // Fund Update APIs
        Route::match(['get', 'post'], '/fund_update.php', [FundUpdateController::class, 'fundUpdate']);
        Route::match(['get', 'post'], '/fund_cancle.php', [FundUpdateController::class, 'cancelFund']);
        Route::match(['get', 'post'], '/fund_complete.php', [FundUpdateController::class, 'completeFund']);
        Route::match(['get', 'post'], '/fund_delete.php', [FundUpdateController::class, 'deleteFund']);
        Route::match(['get', 'post'], '/edit_fund.php', [FundUpdateController::class, 'editFund']);

        // User APIs
        Route::match(['get', 'post'], '/edit_profile.php', [UserController::class, 'editProfile']);
        Route::match(['get', 'post'], '/pro_image.php', [UserController::class, 'uploadProfileImage']);
        Route::match(['get', 'post'], '/wallet_up.php', [UserController::class, 'updateWallet']);
        Route::match(['get', 'post'], '/getbalance.php', [UserController::class, 'getBalance']);

        // Donate APIs
        Route::match(['get', 'post'], '/donate_now.php', [DonateController::class, 'donateNow'])->middleware('throttle:30,1');
        Route::match(['get', 'post'], '/my_donate_fundlist.php', [DonateController::class, 'myDonateFundList']);
        Route::match(['get', 'post'], '/user_payment_list.php', [PaymentController::class, 'userPaymentList']);
        Route::match(['get', 'post'], '/donation_list.php', [PaymentController::class, 'donationList']);
        Route::post('/user_payment_proof_submit.php', [PaymentController::class, 'userPaymentProofSubmit']);
        Route::match(['get', 'post'], '/user_contributions_proof_list.php', [PaymentController::class, 'contributionsProofList']);

        // Withdraw APIs
        Route::match(['get', 'post'], '/request_withdraw.php', [WithdrawController::class, 'requestWithdraw']);
        Route::match(['get', 'post'], '/payout_list.php', [WithdrawController::class, 'payoutList']);

        // Wallet APIs
        Route::match(['get', 'post'], '/wallet_report.php', [WalletController::class, 'walletReport']);

        // Activity APIs
        Route::match(['get', 'post'], '/activity.php', [ActivityController::class, 'activityList']);

        // Notification APIs
        Route::match(['get', 'post'], '/notification.php', [HomeController::class, 'notification']);
        /** In-app notifications (user_notifications); legacy notification.php uses tbl_notification */
        Route::match(['get', 'post'], '/user_notifications.php', [UserNotificationApiController::class, 'list']);

        // Account APIs
        Route::match(['get', 'post'], '/acc_delete.php', [AccountController::class, 'deleteAccount']);
    });

    // Admin API (Bearer token auth)
    Route::post('/admin/login', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'admin.api'])->prefix('admin/settings')->group(function () {
        Route::get('/gemini', [\App\Http\Controllers\Api\Admin\GeminiSettingsController::class, 'index']);
        Route::put('/gemini', [\App\Http\Controllers\Api\Admin\GeminiSettingsController::class, 'update']);
        Route::post('/gemini/test', [\App\Http\Controllers\Api\Admin\GeminiSettingsController::class, 'test']);
        Route::post('/gemini/reset', [\App\Http\Controllers\Api\Admin\GeminiSettingsController::class, 'reset']);
    });
});

// API Routes for Products/Campaigns
Route::prefix('api')->group(function () {
    // Get all campaigns/products
    Route::get('/campaigns', function(\Illuminate\Http\Request $request) {
        try {
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $category = $request->get('category');
            $search = $request->get('search');
            
            $campaigns = Campaign::with(['category', 'user'])
                ->approve()
                ->when($search, function($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                               ->orWhere('description', 'like', "%{$search}%");
                })
                ->when($category, function($query, $category) {
                    return $query->whereHas('category', function($q) use ($category) {
                        $q->where('name', 'like', "%{$category}%")
                          ->orWhere('id', $category);
                    });
                })
                ->latest()
                ->limit($limit)
                ->offset($offset)
                ->get();
            
            $setting = bs();
            $currencyCode = $setting ? (string) $setting->site_cur : 'USD';
            $currencySymbol = $setting ? (string) $setting->cur_sym : '$';

            $formattedCampaigns = $campaigns->map(function($campaign) {
                $raised = (float) $campaign->raised_amount;
                $goal = (float) $campaign->goal_amount;

                return [
                    'id' => $campaign->id,
                    'title' => $campaign->name,
                    'short_description' => $campaign->short_description ?? strLimit($campaign->description ?? '', 150),
                    'image_url' => getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')),
                    'url' => route('campaign.show', $campaign->slug),
                    'product_url' => route('campaign.show', $campaign->slug),
                    'permalink' => route('campaign.show', $campaign->slug),
                    'category' => $campaign->category->name ?? null,
                    'category_id' => $campaign->category_id,
                    'user' => $campaign->user->username ?? null,
                    'user_id' => $campaign->user_id,
                    'goal_amount' => $goal,
                    'raised_amount' => $raised,
                    'progress_percentage' => $goal > 0 ? round(($raised / $goal) * 100, 2) : 0,
                    'donors_count' => (int) $campaign->donors_count,
                    'status' => $campaign->status,
                    'featured' => $campaign->featured,
                    'created_at' => $campaign->created_at->toISOString(),
                    'updated_at' => $campaign->updated_at->toISOString(),
                    'end_date' => $campaign->end_date ? $campaign->end_date->toISOString() : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedCampaigns,
                'meta' => [
                    'currency' => $currencyCode,
                    'currency_symbol' => $currencySymbol,
                ],
                'total' => $campaigns->count(),
                'limit' => $limit,
                'offset' => $offset
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching campaigns: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    });

    // Must be before /campaigns/{slug} so "featured" is not treated as a slug
    Route::get('/campaigns/featured', function(\Illuminate\Http\Request $request) {
        try {
            $limit = $request->get('limit', 5);

            $campaigns = Campaign::with(['category', 'user'])
                ->approve()
                ->featured()
                ->latest()
                ->limit($limit)
                ->get();

            $setting = bs();
            $currencyCode = $setting ? (string) $setting->site_cur : 'USD';
            $currencySymbol = $setting ? (string) $setting->cur_sym : '$';

            $formattedCampaigns = $campaigns->map(function($campaign) {
                $raised = (float) $campaign->raised_amount;
                $goal = (float) $campaign->goal_amount;

                return [
                    'id' => $campaign->id,
                    'title' => $campaign->name,
                    'short_description' => $campaign->short_description ?? strLimit($campaign->description, 150),
                    'image_url' => getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')),
                    'url' => route('campaign.show', $campaign->slug),
                    'product_url' => route('campaign.show', $campaign->slug),
                    'permalink' => route('campaign.show', $campaign->slug),
                    'category' => $campaign->category->name ?? null,
                    'category_id' => $campaign->category_id,
                    'user' => $campaign->user->username ?? null,
                    'user_id' => $campaign->user_id,
                    'goal_amount' => $goal,
                    'raised_amount' => $raised,
                    'progress_percentage' => $goal > 0 ? round(($raised / $goal) * 100, 2) : 0,
                    'donors_count' => (int) $campaign->donors_count,
                    'status' => $campaign->status,
                    'featured' => $campaign->featured,
                    'created_at' => $campaign->created_at->toISOString(),
                    'updated_at' => $campaign->updated_at->toISOString(),
                    'end_date' => $campaign->end_date ? $campaign->end_date->toISOString() : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedCampaigns,
                'meta' => [
                    'currency' => $currencyCode,
                    'currency_symbol' => $currencySymbol,
                ],
                'total' => $campaigns->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching featured campaigns: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    });
    
    // Get single campaign by slug or numeric id (e.g. /api/campaigns/my-slug or /api/campaigns/150)
    Route::get('/campaigns/{slugOrId}', function ($slugOrId) {
        try {
            $with = [
                'category',
                'user',
                'rewards',
                'faqs' => function ($query) {
                    $query->orderBy('order')->orderBy('id');
                },
                'updates' => function ($query) {
                    $query->with(['user', 'comments.user']);
                },
                'comments' => function ($query) {
                    $query->whereNull('update_id')->with('user');
                },
                'deposits.user',
            ];

            $param = (string) $slugOrId;
            $query = Campaign::with($with)
                ->whereIn('status', [
                    \App\Constants\ManageStatus::CAMPAIGN_APPROVED,
                    \App\Constants\ManageStatus::CAMPAIGN_PENDING,
                ]);

            if ($param !== '' && ctype_digit($param)) {
                $campaign = $query->where('id', (int) $param)->first();
            } else {
                $campaign = $query->where('slug', $param)->first();
            }

            if (!$campaign) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaign not found',
                    'data' => null,
                ], 404);
            }

            $setting = bs();
            $raised = (float) $campaign->raised_amount;
            $goal = (float) $campaign->goal_amount;

            $creatorUser = $campaign->user;
            $creator = null;
            if ($creatorUser) {
                $fullName = trim(($creatorUser->firstname ?? '') . ' ' . ($creatorUser->lastname ?? ''));
                if ($fullName === '') {
                    $fullName = (string) ($creatorUser->username ?? '');
                }
                $img = $creatorUser->image ?? $creatorUser->avatar ?? null;
                $creator = [
                    'id' => (int) $creatorUser->id,
                    'name' => $fullName,
                    'username' => (string) ($creatorUser->username ?? ''),
                    'email' => (string) ($creatorUser->email ?? ''),
                    'mobile' => (string) ($creatorUser->mobile ?? ''),
                    'whatsapp' => (string) ($creatorUser->whatsapp ?? $creatorUser->mobile ?? ''),
                    'country_code' => (string) ($creatorUser->country_code ?? ''),
                    'country_name' => (string) ($creatorUser->country_name ?? ''),
                    'profile_image_url' => $img
                        ? getImage(getFilePath('userProfile') . '/' . $img, getFileSize('userProfile'))
                        : null,
                ];
            }

            $formatApiComment = static function ($comment): array {
                $displayName = $comment->user
                    ? (string) ($comment->user->fullname ?? $comment->user->username ?? '')
                    : (string) ($comment->name ?? '');

                return [
                    'id' => (int) $comment->id,
                    'title' => $comment->title,
                    'text' => (string) ($comment->comment ?? ''),
                    'rating' => $comment->rating,
                    'created_at' => $comment->created_at?->toISOString(),
                    'display_name' => $displayName,
                    'member' => $comment->user ? [
                        'id' => (int) $comment->user->id,
                        'username' => (string) ($comment->user->username ?? ''),
                    ] : null,
                ];
            };

            $campaignComments = $campaign->comments;

            $formattedCampaign = [
                'id' => $campaign->id,
                'slug' => $campaign->slug,
                'title' => $campaign->name,
                'description' => $campaign->description,
                'short_description' => $campaign->short_description ?? strLimit($campaign->description, 150),
                'image_url' => getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')),
                'permalink' => route('campaign.show', $campaign->slug),
                'category' => $campaign->category->name ?? null,
                'category_id' => $campaign->category_id,
                'creator' => $creator,
                'goal_amount' => $goal,
                'raised_amount' => $raised,
                'progress_percentage' => $goal > 0 ? round(($raised / $goal) * 100, 2) : 0,
                'donors_count' => (int) $campaign->donors_count,
                'status' => $campaign->status,
                'featured' => $campaign->featured,
                'created_at' => $campaign->created_at->toISOString(),
                'updated_at' => $campaign->updated_at->toISOString(),
                'end_date' => $campaign->end_date ? $campaign->end_date->toISOString() : null,
                'comments_total' => $campaignComments->count(),
                'rewards' => $campaign->rewards->map(function ($reward) {
                    return [
                        'id' => $reward->id,
                        'title' => $reward->title,
                        'description' => $reward->description,
                        'minimum_amount' => $reward->minimum_amount,
                        'quantity' => $reward->quantity,
                        'claimed_count' => $reward->claimed_count,
                        'image_url' => $reward->image_url,
                        'is_active' => $reward->is_active,
                    ];
                })->values(),
                'faqs' => $campaign->faqs->map(function ($faq) {
                    return [
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                        'order' => (int) ($faq->order ?? 0),
                    ];
                })->values(),
                'updates' => $campaign->updates->map(function ($update) use ($formatApiComment) {
                    $author = null;
                    if ($update->user) {
                        $author = [
                            'id' => (int) $update->user->id,
                            'name' => (string) ($update->user->fullname ?? $update->user->username ?? ''),
                            'username' => (string) ($update->user->username ?? ''),
                        ];
                    }

                    return [
                        'id' => $update->id,
                        'title' => $update->title,
                        'content' => $update->content,
                        'slug' => $update->slug,
                        'image_url' => $update->image ? getImage(getFilePath('campaign') . '/' . $update->image, getFileSize('campaign')) : null,
                        'is_published' => (bool) $update->is_published,
                        'author' => $author,
                        'comments' => $update->comments->map(fn ($c) => $formatApiComment($c))->values(),
                        'comments_count' => $update->comments->count(),
                        'created_at' => $update->created_at?->toISOString(),
                        'updated_at' => $update->updated_at?->toISOString(),
                    ];
                })->values(),
                'funds' => $campaign->deposits
                    ->where('status', \App\Constants\ManageStatus::PAYMENT_SUCCESS)
                    ->values()
                    ->map(function ($deposit) {
                        return [
                            'id' => $deposit->id,
                            'amount' => (float) $deposit->amount,
                            'currency' => $deposit->method_currency,
                            'donor_name' => $deposit->full_name ?? $deposit->name ?? $deposit->user->fullname ?? $deposit->user->username ?? null,
                            'donor_email' => $deposit->email ?? $deposit->user->email ?? null,
                            'trx' => $deposit->trx,
                            'created_at' => $deposit->created_at?->toISOString(),
                        ];
                    }),
                'comments' => $campaignComments->map(fn ($c) => $formatApiComment($c))->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedCampaign,
                'meta' => [
                    'currency' => $setting ? (string) $setting->site_cur : 'USD',
                    'currency_symbol' => $setting ? (string) $setting->cur_sym : '$',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching campaign: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    });

    // Get all categories
    Route::get('/categories', function() {
        try {
            $categories = Category::active()->orderBy('name')->get(['id', 'name', 'slug']);
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    });

    // Get subcategories by category ID
    Route::get('/subcategories/{categoryId}', function($categoryId) {
        try {
            $subcategories = \App\Models\Admins\SubCategory::where('category_id', $categoryId)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'category_id']);
            
            return response()->json([
                'success' => true,
                'data' => $subcategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subcategories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    });

    // API route for subcategories (with /api prefix)
    Route::get('/api/subcategories/{categoryId}', function($categoryId) {
        try {
            $subcategories = \App\Models\Admins\SubCategory::where('category_id', $categoryId)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'category_id']);
            
            return response()->json([
                'success' => true,
                'data' => $subcategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching subcategories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    });
});
