<?php

namespace App\Providers;

use App\Models\User;
use App\Services\FirebaseService;
use App\Services\FirebaseServiceFallback;
use App\Models\Contact;
use App\Models\Deposit;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\SiteData;
use App\Models\Withdrawal;
use App\Constants\ManageStatus;
use App\Models\AdminNotification;
use App\Models\Comment;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Firebase: use fallback when Firestore/gRPC unavailable (e.g. missing ext-grpc)
        $this->app->singleton(FirebaseService::class, function () {
            try {
                return new FirebaseService();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Firebase init failed, using fallback: ' . $e->getMessage());
                return new FirebaseServiceFallback();
            }
        });
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        try {
            $setting                        = bs();
        } catch (\Exception $e) {
            $setting = null;
        }
        $activeTheme                    = $setting ? activeTheme() : 'themes.primary.';
        $shareToView['setting']         = $setting;
        $shareToView['activeTheme']     = $activeTheme;
        $whatsappChatbotNumber = '';
        try {
            $wd = SiteData::where('data_key', 'general.whatsapp_settings')->first();
            if ($wd && $wd->data_info) {
                $wi = is_array($wd->data_info) ? $wd->data_info : (array)$wd->data_info;
                $whatsappChatbotNumber = $wi['chatbot_number'] ?? '';
            }
        } catch (\Exception $e) {}
        $shareToView['whatsappChatbotNumber'] = $whatsappChatbotNumber;
        $shareToView['activeThemeTrue'] = $setting ? activeTheme(true) : 'assets/themes/primary/';
        $shareToView['emptyMessage']    = 'No data found';

        view()->share($shareToView);

        if ($setting) {
            view()->composer('admin.partials.topbar', function ($view) {
                $view->with([
                    'adminNotifications'     => AdminNotification::where('is_read', ManageStatus::NO)->with('user')->latest()->take(10)->get(),
                    'adminNotificationCount' => AdminNotification::where('is_read', ManageStatus::NO)->count(),
                ]);
            });

            view()->composer('admin.partials.sidebar', function ($view) {
                $view->with([
                    'bannedUsersCount'            => User::banned()->count(),
                    'emailUnconfirmedUsersCount'  => User::emailUnconfirmed()->count(),
                    'mobileUnconfirmedUsersCount' => User::mobileUnconfirmed()->count(),
                    'kycUnconfirmedUsersCount'    => User::kycUnconfirmed()->count(),
                    'kycPendingUsersCount'        => User::kycPending()->count(),
                    'pendingDonationsCount'       => Deposit::pending()->count(),
                    'pendingWithdrawalsCount'     => Withdrawal::pending()->count(),
                    'unansweredContactsCount'     => Contact::where('status', ManageStatus::NO)->count(),
                    'pendingCampaignCount'        => Campaign::pending()->count(),
                    'pendingCommentCount'         => Comment::pending()->count(),
                ]);
            });

            view()->composer('partials.seo', function ($view) {
                $seo = SiteData::where('data_key', 'seo.data')->first();

                $view->with([
                    'seo' => $seo ? $seo->data_info : $seo,
                ]);
            });

            view()->composer(['themes.green.layouts.green-home', $activeTheme . 'layouts.frontend'], function ($view) {
                $user = auth()->user();
                $inboxFirebaseConfig = null;
                $inboxTokenUrl = null;
                $inboxUserId = null;
                if ($user && config('firebase.client.api_key') && config('firebase.client.project_id')) {
                    $inboxFirebaseConfig = [
                        'apiKey' => config('firebase.client.api_key'),
                        'authDomain' => config('firebase.client.auth_domain'),
                        'projectId' => config('firebase.client.project_id'),
                        'storageBucket' => config('firebase.client.storage_bucket'),
                        'messagingSenderId' => config('firebase.client.messaging_sender_id'),
                        'appId' => config('firebase.client.app_id'),
                        'chatCollectionPrefix' => config('firebase.firestore.collection_prefix', 'apnacrowdfunding'),
                    ];
                    $inboxTokenUrl = route('user.inbox.firebase.token');
                    $inboxUserId = (string) $user->id;
                }
                $view->with([
                    'campCategories' => Category::active()->latest()->limit(3)->get(),
                    'inboxFirebaseConfig' => $inboxFirebaseConfig,
                    'inboxTokenUrl' => $inboxTokenUrl,
                    'inboxUserId' => $inboxUserId,
                ]);
            });

            // Footer composer for apnacrowdfunding theme
            view()->composer($activeTheme . 'partials.footer', function ($view) {
                $view->with([
                    'categories' => Category::active()->latest()->get(),
                ]);
            });

            // Dashboard layout composer
            view()->composer($activeTheme . 'layouts.dashboard', function ($view) {
                $user = auth()->user();
                $firebaseConfig = null;
                if ($user && config('firebase.client.api_key') && config('firebase.client.project_id')) {
                    $firebaseConfig = [
                        'apiKey' => config('firebase.client.api_key'),
                        'authDomain' => config('firebase.client.auth_domain'),
                        'projectId' => config('firebase.client.project_id'),
                        'storageBucket' => config('firebase.client.storage_bucket'),
                        'messagingSenderId' => config('firebase.client.messaging_sender_id'),
                        'appId' => config('firebase.client.app_id'),
                        'chatCollectionPrefix' => config('firebase.firestore.collection_prefix', 'apnacrowdfunding'),
                    ];
                }
                $view->with([
                    'dashboardParams' => [
                        'isHomePage' => request()->routeIs('home') || request()->path() === '/',
                        'userType' => 'dashboard',
                        'pageTitle' => 'Dashboard',
                        'customData' => 'Your custom data here',
                        'userInfo' => $user ? [
                            'name' => $user->name,
                            'email' => $user->email,
                            'id' => $user->id
                        ] : null
                    ],
                    'inboxFirebaseConfig' => $firebaseConfig,
                    'inboxTokenUrl' => $user ? route('user.inbox.firebase.token') : null,
                    'inboxUserId' => $user ? (string) $user->id : null,
                ]);
            });

            // Register custom validation rules
            Validator::extend('phone_by_country', function ($attribute, $value, $parameters, $validator) {
                $country = request('country');
                if (!$country) {
                    return false;
                }
                return validatePhoneByCountry($value, $country);
            }, 'The :attribute format is invalid for the selected country.');
        }

        if ($setting && $setting->enforce_ssl) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFour();
    }
}
