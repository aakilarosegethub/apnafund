<?php

use Illuminate\Support\Facades\Route;
use App\Models\SiteData;
use App\Models\Campaign;
use App\Models\Category;

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('csrf.token');

// API routes for email verification (no CSRF required)
Route::post('/api/verify-email', 'App\Http\Controllers\User\AuthorizationController@emailVerificationApi')->name('api.verify.email');

Route::controller('WebsiteController')->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('home-new', 'homeNew')->name('home.new');
    Route::get('volunteers', 'volunteers')->name('volunteers');
    Route::get('about', 'aboutUs')->name('about.us');
    Route::get('faq', 'faq')->name('faq');
    Route::get('creators', 'creators')->name('creators');
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

    // Start Project
    Route::get('start-project', 'startProject')->name('start.project');
    Route::post('start-project/save-categories', 'saveProjectCategories')->name('start.project.save.categories');
    Route::get('start-project/location', 'projectLocation')->name('start.project.location');
    Route::post('start-project/save-location', 'saveProjectLocation')->name('start.project.save.location');
    Route::get('start-project/terms', 'projectTerms')->name('start.project.terms');
    Route::post('start-project/create-campaign', 'createCampaignFromSession')->name('start.project.create.campaign');

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
    
    // Report Fundraiser
    Route::get('report-fundraiser', 'reportFundraiser')->name('report.fundraiser');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    // Update user country in session
    Route::post('/update-user-country', [App\Http\Controllers\WebsiteController::class, 'updateUserCountry'])->name('update.user.country');
});

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
Route::any('/jazzcash/ipn', [App\Http\Controllers\Gateway\JazzCash\IpnController::class, 'handle'])->name('jazzcash.ipn');

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
            
            $formattedCampaigns = $campaigns->map(function($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->name,
                    'description' => $campaign->description,
                    'short_description' => strLimit($campaign->description, 150),
                    'image_url' => getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')),
                    'url' => route('campaign.show', $campaign->slug),
                    'product_url' => route('campaign.show', $campaign->slug),
                    'permalink' => route('campaign.show', $campaign->slug),
                    'category' => $campaign->category->name ?? null,
                    'category_id' => $campaign->category_id,
                    'user' => $campaign->user->username ?? null,
                    'user_id' => $campaign->user_id,
                    'goal_amount' => $campaign->goal_amount,
                    'raised_amount' => $campaign->raised_amount,
                    'progress_percentage' => $campaign->goal_amount > 0 ? round(($campaign->raised_amount / $campaign->goal_amount) * 100, 2) : 0,
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
    
    // Get single campaign/product by slug
    Route::get('/campaigns/{slug}', function($slug) {
        try {
            $campaign = Campaign::with(['category', 'user', 'rewards'])
                ->where('slug', $slug)
                ->approve()
                ->first();
            
            if (!$campaign) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaign not found',
                    'data' => null
                ], 404);
            }
            
            $formattedCampaign = [
                'id' => $campaign->id,
                'title' => $campaign->name,
                'description' => $campaign->description,
                'short_description' => strLimit($campaign->description, 150),
                'image_url' => getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')),
                'url' => route('campaign.show', $campaign->slug),
                'product_url' => route('campaign.show', $campaign->slug),
                'permalink' => route('campaign.show', $campaign->slug),
                'category' => $campaign->category->name ?? null,
                'category_id' => $campaign->category_id,
                'user' => $campaign->user->username ?? null,
                'user_id' => $campaign->user_id,
                'goal_amount' => $campaign->goal_amount,
                'raised_amount' => $campaign->raised_amount,
                'progress_percentage' => $campaign->goal_amount > 0 ? round(($campaign->raised_amount / $campaign->goal_amount) * 100, 2) : 0,
                'status' => $campaign->status,
                'featured' => $campaign->featured,
                'created_at' => $campaign->created_at->toISOString(),
                'updated_at' => $campaign->updated_at->toISOString(),
                'end_date' => $campaign->end_date ? $campaign->end_date->toISOString() : null,
                'rewards' => $campaign->rewards->map(function($reward) {
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
                })
            ];
            
            return response()->json([
                'success' => true,
                'data' => $formattedCampaign
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching campaign: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    });
    
    // Get featured campaigns/products
    Route::get('/campaigns/featured', function(\Illuminate\Http\Request $request) {
        try {
            $limit = $request->get('limit', 5);
            
            $campaigns = Campaign::with(['category', 'user'])
                ->approve()
                ->featured()
                ->latest()
                ->limit($limit)
                ->get();
            
            $formattedCampaigns = $campaigns->map(function($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->name,
                    'description' => $campaign->description,
                    'short_description' => strLimit($campaign->description, 150),
                    'image_url' => getImage(getFilePath('campaign') . '/' . $campaign->image, getFileSize('campaign')),
                    'url' => route('campaign.show', $campaign->slug),
                    'product_url' => route('campaign.show', $campaign->slug),
                    'permalink' => route('campaign.show', $campaign->slug),
                    'category' => $campaign->category->name ?? null,
                    'category_id' => $campaign->category_id,
                    'user' => $campaign->user->username ?? null,
                    'user_id' => $campaign->user_id,
                    'goal_amount' => $campaign->goal_amount,
                    'raised_amount' => $campaign->raised_amount,
                    'progress_percentage' => $campaign->goal_amount > 0 ? round(($campaign->raised_amount / $campaign->goal_amount) * 100, 2) : 0,
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
