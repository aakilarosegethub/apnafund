<?php

namespace App\Providers;

use App\Models\User;
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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
        $setting                        = bs();
        $activeTheme                    = $setting ? activeTheme() : 'themes.primary.';
        $shareToView['setting']         = $setting;
        $shareToView['activeTheme']     = $activeTheme;
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

            view()->composer($activeTheme . 'layouts.frontend', function ($view) {
                $view->with([
                    'campCategories' => Category::active()->latest()->limit(3)->get(),
                ]);
            });

            // Dashboard layout composer
            view()->composer($activeTheme . 'layouts.dashboard', function ($view) {
                $view->with([
                    'dashboardParams' => [
                        'isHomePage' => request()->routeIs('home') || request()->path() === '/',
                        'userType' => 'dashboard',
                        'pageTitle' => 'Dashboard',
                        'customData' => 'Your custom data here',
                        'userInfo' => auth()->user() ? [
                            'name' => auth()->user()->name,
                            'email' => auth()->user()->email,
                            'id' => auth()->user()->id
                        ] : null
                    ]
                ]);
            });
        }

        if ($setting && $setting->enforce_ssl) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFour();
    }
}
