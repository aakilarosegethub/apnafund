# ApnaFund Project Documentation

## 1. Project Overview
- Project name: ApnaFund (Laravel-based crowdfunding platform)
- Purpose and problem statement: Provide a platform where creators can launch fundraising campaigns and backers can contribute with multiple payment methods, with admin oversight for approvals, payouts, and compliance.
- Target users: Campaign creators, donors/backers, and platform administrators.

## 2. Technology Stack
- Backend: Laravel 11 (PHP 8.2), Sanctum for API auth, Socialite for social login.
- Frontend: Blade templates with Vite build pipeline (resources `css/`, `js/`, and `resources/views/themes/*`).
- Database: MySQL/SQLite compatible schema (Laravel migrations + SQL patch files).
- Third-party services / APIs:
  - Payments: Authorize.Net, Stripe (JS/V3), Razorpay, Paystack, Checkout.com, PayPal SDK, Flutterwave, MercadoPago, Coinbase Commerce, CoinPayments, BTCPay, NowPayments, PerfectMoney, 2Checkout, JazzCash, MWallet, CardPayment.
  - Notifications/Comms: SendGrid, Mailjet, PHPMailer, Twilio, Vonage.
  - Identity & Social: Google OAuth, Facebook OAuth.
  - Other: Firebase (OTP), YouTube API, Google Maps API.
- Background jobs / cron jobs:
  - Queue configured for database driver (`jobs` table), but no scheduled tasks in `Console\Kernel`.
  - Custom Artisan commands: `campaigns:update-raised-amounts`, `admin:change-password` (run manually or via cron if desired).

## 3. System Architecture
- High-level flow:
  - Public web routes render marketing pages and campaign listings.
  - Authenticated users create/manage campaigns, rewards, and updates.
  - Donations are processed via gateway-specific controllers and IPN callbacks.
  - Admin panel oversees campaigns, users, payments, payouts, notifications, and settings.
- Component interaction:
  - Controllers orchestrate business logic and call Models/Services.
  - Models encapsulate core domain entities (Campaign, Deposit, Withdrawal, Reward).
  - Gateway modules process payment initialization and callbacks, updating deposits/transactions.
  - Notifications are sent via the `Notify` pipeline and `NotificationTemplate` records.
  - Admin and user views are separated under `resources/views/admin` and `resources/views/themes/*`.

## 4. User Roles & Permissions
- Admin:
  - Separate guard and model (`Admin`), manages platform settings, users, campaigns, donations, and payouts.
- Campaign Creator:
  - Standard `User` who creates campaigns; can manage campaign details, rewards, FAQs, updates, collaborators, and request withdrawals.
- Backer (Donor):
  - Any authenticated or guest user who contributes to a campaign via deposits.
- Collaborator:
  - Additional users linked to a campaign via `campaign_collaborators` to edit campaigns.

## 5. Core Features / Modules
- Authentication:
  - User login, registration, OTP login, password reset.
  - Social logins: Google and Facebook.
  - Admin auth with separate guard and password reset flows.
- Campaign management:
  - Create, edit, approve/reject, and view campaigns.
  - Campaign sections: basics, rewards, story, people, payment, boost, FAQs, updates.
  - Collaborators and campaign promotions (Meta/Facebook).
- Payments & transactions:
  - Donation creation, gateway selection, IPN callbacks, and transaction logging.
  - Supports manual and automated gateways.
  - Reward tracking tied to donations and transactions.
- Payouts:
  - Withdrawal methods and withdrawal requests.
  - Creator campaign payout summaries and actions (partial/full payouts).
- Notifications:
  - Email/SMS templates, admin notifications, user notifications.
  - Email logs and webhook logs for monitoring.
- Admin dashboard:
  - User management, campaign moderation, payment approvals, withdrawals, settings, language, SEO, homepage config, and social login config.

## 6. Payment Flow
- Step-by-step payment lifecycle:
  1. User selects campaign and submits donation details.
  2. `Gateway\PaymentController@depositInserts` validates inputs and creates a `deposits` record (status = initiated).
  3. System redirects to gateway confirmation (`depositConfirm`) which hands off to the gateway ProcessController.
  4. Gateway-specific IPN/callback updates the payment result.
  5. On success: `PaymentController::campaignDataUpdate` updates deposit status, campaign raised amount, donor/receiver transactions, reward claims, and notifications.
  6. On manual gateways: donation is set to pending and awaits admin approval.
- Success handling:
  - Deposit marked successful.
  - Campaign raised amount updated.
  - Transactions created for donor and campaign owner.
  - Admin notification and donor confirmation sent.
- Failure handling:
  - Gateway ProcessController returns error and redirect with toast.
  - No campaign balance update; deposit remains initiated/cancelled.
  - Admin may reject manual donations via admin dashboard.

## 7. Database Overview
- List of tables and purpose:
  - `admins`: Admin accounts and authentication.
  - `admin_password_resets`: Admin password reset tokens.
  - `admin_notifications`: Admin notification queue entries.
  - `users`: End-user accounts (campaign creators and donors).
  - `password_reset_tokens`: User password reset tokens.
  - `sessions`: User session storage.
  - `personal_access_tokens`: Sanctum API tokens.
  - `campaigns`: Core campaigns/fundraisers with status, target, media, and metadata.
  - `campaign_faqs`: FAQs attached to campaigns.
  - `campaign_updates`: Campaign updates/posts.
  - `campaign_collaborators`: User collaborators per campaign.
  - `campaign_promotions`: Meta/Facebook promotion jobs for campaigns.
  - `categories`: Campaign categories.
  - `sub_categories`: Subcategories for campaigns.
  - `header_categories`: Header/menu category mapping.
  - `comments`: Campaign and update comments (with rating fields).
  - `rewards`: Donation reward tiers.
  - `deposits`: Donation/payment records per campaign.
  - `transactions`: Ledger of balance changes for donors and creators.
  - `withdraw_methods`: Payout method configuration.
  - `withdrawals`: Withdrawal requests and payout tracking.
  - `payout_banks`: Bank accounts available for campaign payouts.
  - `creator_campaign_fee_settings`: Fee configuration for creator payouts.
  - `creator_campaign_payouts`: Payout summary for successful campaigns.
  - `creator_campaign_payout_actions`: Actions (partial/full payout) for payouts.
  - `gateways`: Payment gateway definitions.
  - `gateway_currencies`: Currency and fee configurations per gateway.
  - `currencies`: Currency master data and exchange rates.
  - `settings`: Platform configuration (email, SEO, home settings, etc.).
  - `plugins`: Optional feature toggles/integrations.
  - `notification_templates`: Email/SMS templates.
  - `email_logs`: Email send and delivery log records.
  - `data_logs`: Unified webhook/logging data store.
  - `forms`: Dynamic form definitions for gateways/withdrawals.
  - `languages`: Locale definitions and translations.
  - `subscribers`: Newsletter subscribers.
  - `contacts`: Contact form submissions.
  - `faqs`: Global FAQ entries.
  - `banners`: Home page banners.
  - `site_data`: CMS-like site content blocks.
  - `registration_steps`: Registration flow steps.
  - `registration_questions`: Questions for user registration flows.
  - `user_registration_responses`: Answers to registration questions.
  - `jobs`: Queue jobs.
  - `cache`: Cache store for Laravel cache driver.

- Important relationships:
  - `users` → `campaigns` (creator owns many campaigns).
  - `campaigns` → `deposits` (donations) → `transactions` (ledger entries).
  - `campaigns` → `rewards`, `campaign_faqs`, `campaign_updates`, `comments`.
  - `campaigns` → `creator_campaign_payouts` → `creator_campaign_payout_actions`.
  - `gateways` → `gateway_currencies` → `deposits`.
  - `withdrawals` → `withdraw_methods` and `users`.

## 8. Quick Answers (File References)
1. Main entry point:
   - `public/index.php` (primary Laravel front controller)
   - `index.php` (alternate entry depending on server config)

2. Files that handle routes:
   - Web routes: `routes/web.php`
   - Admin routes: `routes/admin.php`
   - Related: `routes/user.php`, `routes/api.php`, `routes/ipn.php`

3. Main folders and responsibilities:
   - `app/`: Application code (controllers, models, services, middleware)
   - `bootstrap/`: Framework bootstrap and app initialization
   - `config/`: Configuration files
   - `database/`: Migrations, seeders, factories
   - `public/`: Web root and public assets (includes `public/index.php`)
   - `resources/`: Blade views, frontend assets, localization
   - `routes/`: Route definitions (web, admin, api, user, ipn)
   - `storage/`: Logs, cache, compiled views, uploads
   - `tests/`: Automated tests
   - `vendor/`: Composer dependencies
   - `lang/` and `languages/`: Translation files
   - `assets/`: Additional static assets outside `public/`
   - `api/`: Standalone PHP endpoints (e.g., `api/home_api.php`)

4. Request lifecycle start and end:
   - Start: `public/index.php` → bootstraps app, captures request, resolves HTTP kernel.
   - End: Response sent and kernel terminated in `public/index.php` (`$kernel->terminate(...)`).
