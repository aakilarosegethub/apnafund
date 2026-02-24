# Major Processes (Code-Based, In-Depth)

This document lists the major processes found in the current codebase. Each process includes exact file paths, class names, method names, step-by-step execution, why each step exists, and what must NOT be changed (and why). Everything here is based only on the code.

---

## 1) Front Routing + Beta Gate + Public Entry Points

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/routes/web.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Middleware/BetaGate.php`

### Classes + Methods
- `App\Http\Middleware\BetaGate::handle()`
- `App\Http\Controllers\WebsiteController` (methods listed in section 2)
- Anonymous route closures in `routes/web.php`

### Step-by-step execution order
1. A public request hits a route in `routes/web.php`.
2. For the main public site group, the route is wrapped by the `BetaGate` middleware (`Route::middleware(\App\Http\Middleware\BetaGate::class)->group(...)`).
3. `BetaGate::handle()` checks `BETA_GATE_ENABLED` from `.env` and the `apnafund_beta_seen` cookie.
4. If beta gate is enabled and the cookie is not present, any route other than `/beta` and `/beta/start` is redirected to `beta.page`.
5. `/beta` shows the beta landing page. `/beta/start` sets a cookie and redirects to the normal home route.
6. Other public routes continue to their target controller action or closure.

### Why each step exists
- The beta gate prevents uninvited users from accessing the public site while still allowing the beta page to show.
- The cookie is a lightweight “once accepted” switch; it avoids forcing users through beta on every request.
- Route-specific closures exist for simple redirects and static responses without controller overhead.

### What must NOT be changed and why
- Do NOT remove `BetaGate` from the public route group: the whole “beta landing” workflow depends on it.
- Do NOT change the cookie name `apnafund_beta_seen` without updating every place that reads it, or users will be stuck in loops.
- Do NOT move `/beta` or `/beta/start` inside guarded routes that would redirect back into the beta page, or you create a redirect loop.

---

## 2) Public Website Pages + Campaign Browsing + Dynamic Pages

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/WebsiteController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Campaign.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Category.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Deposit.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Comment.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/SiteData.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/routes/web.php`

### Classes + Methods
- `App\Http\Controllers\WebsiteController::home()`
- `App\Http\Controllers\WebsiteController::homeNew()`
- `App\Http\Controllers\WebsiteController::aboutUs()`
- `App\Http\Controllers\WebsiteController::faq()`
- `App\Http\Controllers\WebsiteController::creators()`
- `App\Http\Controllers\WebsiteController::campaigns()`
- `App\Http\Controllers\WebsiteController::campaignCategory()`
- `App\Http\Controllers\WebsiteController::campaignShow()`
- `App\Http\Controllers\WebsiteController::campaignDonate()`
- `App\Http\Controllers\WebsiteController::campaignDonations()`
- `App\Http\Controllers\WebsiteController::campaignTopDonations()`
- `App\Http\Controllers\WebsiteController::campaignUpdates()`
- `App\Http\Controllers\WebsiteController::campaignUpdateShow()`
- `App\Http\Controllers\WebsiteController::storeUpdateComment()`
- `App\Http\Controllers\WebsiteController::dynamicPages()`
- `App\Http\Controllers\WebsiteController::pageBySlug()`

### Step-by-step execution order (example: public campaign listing)
1. Public user visits `/campaigns`.
2. `routes/web.php` routes to `WebsiteController::campaigns()`.
3. `campaigns()` builds category list with campaign counts, then builds a campaign query.
4. The query uses filters based on `category`, `name`, and `date_range`, plus `commonQuery()` and `approve()` scope.
5. Results are paginated and rendered in `themes.<active>.page.campaign`.

### Step-by-step execution order (example: public campaign details)
1. Public user visits `/campaign/{slug}`.
2. Route calls `WebsiteController::campaignShow()`.
3. The campaign is loaded with rewards and deposits using `Campaign::approve()` and `firstOrFail()`.
4. Approved comments are loaded and limited; comment count is calculated.
5. Related campaigns are fetched by category.
6. SEO data is built from the campaign and image file path.
7. Country list is loaded from `resource_path('views/partials/country.json')`.
8. Active gateway currencies are fetched for the campaign page.
9. Recent donations are pulled from `Deposit::done()`.
10. FAQs are loaded for the campaign.
11. View is rendered with all prepared data.

### Why each step exists
- Category filters and `approve()` scopes ensure only public-approved campaigns are listed.
- SEO data is built dynamically from campaign content for social sharing.
- Gateways are loaded to power the “contribute” flow from the campaign page.
- Comments are filtered by approval to prevent unmoderated content from displaying.
- Country detection is used later to filter gateways based on locale.

### What must NOT be changed and why
- Do NOT remove `approve()` scopes from public queries; it would expose pending or rejected campaigns.
- Do NOT remove SEO data construction without replacing it elsewhere; social previews depend on it.
- Do NOT remove gateway loading from `campaignShow()` or `campaignDonate()`; contribution pages depend on it.
- Do NOT change the `campaign/{slug}` route to accept IDs without matching the controller logic; the controller expects slug lookups.

---

## 3) Start Project Wizard (Public → User Campaign Draft)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/WebsiteController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Campaign.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Constants/ManageStatus.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/routes/web.php`

### Classes + Methods
- `App\Http\Controllers\WebsiteController::startProject()`
- `App\Http\Controllers\WebsiteController::saveProjectCategories()`
- `App\Http\Controllers\WebsiteController::projectLocation()`
- `App\Http\Controllers\WebsiteController::saveProjectLocation()`
- `App\Http\Controllers\WebsiteController::projectTerms()`
- `App\Http\Controllers\WebsiteController::createCampaignFromSession()`

### Step-by-step execution order
1. User hits `/start-project`.
2. `startProject()` checks authentication; unauthenticated users are redirected to login.
3. Categories (and subcategories if the table exists) are loaded; the form renders.
4. `saveProjectCategories()` validates `category_id` + `subcategory_id`, then stores them in session.
5. User hits `/start-project/location`, which loads allowed countries via `getAllowedCountries()`.
6. `saveProjectLocation()` stores `project_country` in session.
7. User hits `/start-project/terms`; validation ensures session has all required data.
8. `createCampaignFromSession()` builds a new `Campaign` with placeholder defaults, sets status to `ManageStatus::CAMPAIGN_PENDING`, saves it, clears session.
9. Admin notification is created, and notification emails are sent to all admins.
10. User is redirected to edit the campaign.

### Why each step exists
- Session storage lets a multi-step wizard progress without committing a full campaign form.
- Campaign is created as a draft (pending) so admins review before public listing.
- Admin notifications and emails ensure moderators see new submissions.

### What must NOT be changed and why
- Do NOT remove the session keys `project_category_id`, `project_subcategory_id`, `project_country`; the wizard depends on them.
- Do NOT change `ManageStatus::CAMPAIGN_PENDING` here; approval workflow is tied to this status.
- Do NOT remove the admin notification creation; admin review relies on it.

---

## 4) User Campaign Creation + Editing + Media Uploads

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/CampaignController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Campaign.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Gallery.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/CampaignFaq.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/CampaignUpdate.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/CampaignCollaborator.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Services/CurrencyService.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Services/YouTubeUploadService.php`

### Classes + Methods (major)
- `CampaignController::index()`, `approved()`, `pending()`, `rejected()`
- `CampaignController::new()`
- `CampaignController::galleryUpload()`, `galleryRemove()`, `deleteAllGallery()`
- `CampaignController::uploadCampaignImage()`, `uploadCampaignVideo()`
- `CampaignController::store()`
- `CampaignController::editSection()`
- `CampaignController::update()`
- `CampaignController::removeImage()`
- `CampaignController::destroy()`
- `CampaignController::uploadImage()` (editor)
- `CampaignController::uploadStoryMedia()`
- `CampaignController::uploadExternalImage()`
- `CampaignController::storeFaq()`, `updateFaq()`, `deleteFaq()`, `getFaq()`
- `CampaignController::storeUpdate()`, `updateUpdate()`, `deleteUpdate()`, `getUpdate()`
- `CampaignController::updatePayment()`
- `CampaignController::addCollaborator()`, `removeCollaborator()`, `searchUsers()`

### Step-by-step execution order (create)
1. User opens “new campaign” form → `CampaignController::new()`.
2. `removePreviousGallery()` clears stale dropzone uploads.
3. User uploads gallery images → `galleryUpload()` stores `Gallery` records and files.
4. User submits form → `store()`.
5. `store()` validates inputs (name uniqueness, category, dates, sizes).
6. `CurrencyService` detects creator currency, converts goal into USD, stores `goal_amount_usd` plus original.
7. Main image (optional) is uploaded and stored. Gallery list is collected from dropzone + direct file inputs.
8. Video is either uploaded locally or sent to YouTube (if `auto_upload_youtube` is enabled and configured).
9. Campaign is saved with status `CAMPAIGN_PENDING`.
10. Gallery `Gallery` table records are deleted (they are now in campaign). 
11. Admin notification and admin email broadcasts are sent.
12. User is redirected to the edit page.

### Step-by-step execution order (edit)
1. User visits campaign edit route → `editSection()`.
2. `editSection()` determines the tab (basics/story/reward/faq/updates/payment/people/etc).
3. Data is loaded based on tab: rewards, FAQs, updates, collaborators, payout banks.
4. Update form submits to `update()`.
5. `update()` validates based on section (story vs basics).
6. On basics update: category, dates, goal, currency conversion, media updates, and document uploads are applied.
7. On story update: only description changes.
8. Save, then redirect to appropriate tab.

### Why each step exists
- Draft gallery uploads prevent heavy uploads from failing at final form submit.
- Currency conversion keeps financials in a normalized USD base.
- Section-based validation allows partial updates on complex forms.
- Image/video uploads are separated to improve UX and avoid timeouts.
- Admin notifications keep moderation informed of new campaigns.

### What must NOT be changed and why
- Do NOT remove `CurrencyService` conversion or fields like `goal_amount_usd`; payout and comparisons rely on normalized amounts.
- Do NOT bypass `canBeEditedBy()` checks; collaborator permissions enforce access control.
- Do NOT remove status `CAMPAIGN_PENDING` on creation; approval flow depends on it.
- Do NOT remove cleanup of `Gallery` temporary uploads; it prevents orphan files.

---

## 5) Campaign Approval Lifecycle (Creator → Admin → Public)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/CampaignController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/CampaignController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Campaign.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Constants/ManageStatus.php`

### Classes + Methods
- `User\CampaignController::store()` (creates pending)
- `Admin\CampaignController` (approval/rejection methods; see file)
- `Campaign` model scopes `pending()`, `approve()`, `reject()`

### Step-by-step execution order
1. Creator submits a campaign → `CampaignController::store()` sets `status = CAMPAIGN_PENDING`.
2. Admin views campaign lists in `Admin\CampaignController`.
3. Admin approves or rejects the campaign (specific method names are in the controller).
4. Approved campaigns are available to public queries using `Campaign::approve()` scope.

### Why each step exists
- Pending status ensures content is moderated before it goes live.
- Approval scopes keep the public site clean and compliant.

### What must NOT be changed and why
- Do NOT remove or alter the `CAMPAIGN_PENDING`, `CAMPAIGN_APPROVED`, `CAMPAIGN_REJECTED` constants: the full platform uses these values.
- Do NOT remove `approve()` scopes from public listings.

---

## 6) Contribution / Donation Flow (Web)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Gateway/PaymentController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Api/DonateController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Api/FundController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Deposit.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Transaction.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Helpers/helpers.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/routes/ipn.php`

### Classes + Methods (core web payment)
- `Gateway\PaymentController::deposit()`
- `Gateway\PaymentController::depositInsert()`
- `Gateway\PaymentController::confirmDeposit()`
- `Gateway\PaymentController::manualDepositConfirm()`
- `Gateway\PaymentController::appPayment()`

### Step-by-step execution order (web deposit path)
1. User selects a campaign and submits a contribution form.
2. `Gateway\PaymentController::deposit()` prepares the deposit session, validates amount + gateway.
3. `depositInsert()` creates a `Deposit` row with status pending and captures user/campaign/gateway details.
4. The user is redirected to the gateway or shown a manual gateway form.
5. For automated gateways, `confirmDeposit()` finalizes via gateway callbacks and updates deposit status.
6. For manual gateways, `manualDepositConfirm()` stores user-submitted proof and awaits admin approval.
7. Upon success, a `Transaction` is created and campaign amounts are updated (via helper logic).

### Why each step exists
- The `Deposit` record is a durable audit record for money movement.
- Separation between automated and manual gateway flows is required by provider behavior.
- Transactions are the ledger entries for user balance and reporting.

### What must NOT be changed and why
- Do NOT skip `Deposit` creation; other flows (transactions, admin views, campaign totals) depend on it.
- Do NOT change status values used in `Deposit::done()` / `pending()` scopes; reporting relies on them.
- Do NOT remove helper-based balance updates (see transaction flow); it will break user balances and campaign totals.

---

## 7) Transaction + Balance Logic (Ledger)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Transaction.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/User.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Helpers/helpers.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/UserController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/AdminController.php`

### Classes + Methods
- `Transaction` model (scopes and relationships)
- `User` model (balance fields)
- Helper functions in `helpers.php` (e.g., balance adjustments)
- `UserController::transactions()`
- `AdminController::transaction()`

### Step-by-step execution order (withdraw creates ledger entry)
1. User submits a withdraw request → `User\WithdrawController::submit()`.
2. The user balance is reduced immediately.
3. A `Transaction` row is inserted with `trx_type = '-'`, `remark = 'withdraw'`, and `post_balance`.
4. Admin later approves or cancels the withdrawal (see withdrawal flow).

### Why each step exists
- Transactions are the authoritative ledger; balances are derived by applying transactions.
- The `remark` field categorizes transactions for reports and filtering.

### What must NOT be changed and why
- Do NOT stop creating `Transaction` rows when money moves; admin reports and user transaction pages depend on them.
- Do NOT change `remark` strings without updating filters; `UserController::transactions()` filters by `remark`.

---

## 8) Withdrawal + Payout (User → Admin)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/WithdrawController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/WithdrawController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Withdrawal.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/WithdrawMethod.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Transaction.php`

### Classes + Methods (user flow)
- `User\WithdrawController::methods()`
- `User\WithdrawController::store()`
- `User\WithdrawController::preview()`
- `User\WithdrawController::submit()`

### Step-by-step execution order (user withdrawal)
1. User views methods → `methods()` loads active `WithdrawMethod` rows.
2. User enters amount → `store()` validates min/max, checks balance, creates `Withdrawal` with `status=initiated` and `trx`.
3. `preview()` loads `Withdrawal` from session.
4. `submit()` validates method form fields, checks 2FA, marks `Withdrawal` as `PAYMENT_PENDING`.
5. User balance is reduced, and a `Transaction` row is created (remark `withdraw`).
6. Admin notification is created; user receives `WITHDRAW_REQUEST` notification.

### Step-by-step execution order (admin decision)
1. Admin views pending withdrawals → `Admin\WithdrawController::pending()`.
2. Admin approves → `approve()` sets `status = PAYMENT_SUCCESS` and sends `WITHDRAW_APPROVE`.
3. Admin cancels → `cancel()` sets `status = PAYMENT_CANCEL`, refunds user balance, creates a `Transaction` with `remark = withdraw_reject`, and sends `WITHDRAW_REJECT`.

### Why each step exists
- The withdrawal request is created before funds are deducted to allow preview and validation.
- Balance is reduced when the request is submitted to prevent double-spending.
- Admin approval provides manual risk control for payouts.

### What must NOT be changed and why
- Do NOT move balance deduction to the admin approval step; the system expects funds to be reserved at request time.
- Do NOT remove refund logic in `cancel()`; it is the only code path returning funds.
- Do NOT remove `withdraw->trx` usage; it links to transaction history.

---

## 9) Creator Campaign Payouts (Success → Fee Split → Admin Actions)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/CreatorPayoutController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/CreatorPayoutSettingController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Services/CreatorCampaignPayoutService.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/CreatorCampaignPayout.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/CreatorCampaignPayoutAction.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/CreatorCampaignFeeSetting.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Deposit.php`

### Classes + Methods
- `CreatorCampaignPayoutService::getSettings()`
- `CreatorCampaignPayoutService::ensurePayoutRecord()`
- `CreatorCampaignPayoutService::getTotalRaised()`
- `CreatorCampaignPayoutService::isCampaignSuccessful()`
- `CreatorCampaignPayoutService::calculateAmounts()`
- `Admin\CreatorPayoutController::index()`
- `Admin\CreatorPayoutController::show()`
- `Admin\CreatorPayoutController::partialPayout()`
- `Admin\CreatorPayoutController::fullPayout()`
- `Admin\CreatorPayoutController::markFulfillmentComplete()`
- `Admin\CreatorPayoutSettingController::edit()`
- `Admin\CreatorPayoutSettingController::update()`

### Step-by-step execution order
1. Admin visits Creator Payout dashboard → `CreatorPayoutController::index()`.
2. It loads fee settings through `CreatorCampaignPayoutService::getSettings()` (creates defaults if missing).
3. It queries campaigns with `approve()` and success rules (ended or goal met by deposits).
4. For campaigns without a payout record, `ensurePayoutRecord()` creates a `CreatorCampaignPayout` row, calculating fees and withheld amounts.
5. Admin can record partial payout (`partialPayout()`), full payout (`fullPayout()`), or mark fulfillment completion (`markFulfillmentComplete()`).
6. Each action logs a `CreatorCampaignPayoutAction` record.

### Why each step exists
- Payout records are created only when campaigns are “successful,” enforcing platform rules.
- Fee settings allow platform, marketing, and withholding calculations to be consistent.
- Action logs provide an audit trail of payout decisions.

### What must NOT be changed and why
- Do NOT remove `ensurePayoutRecord()`; payouts rely on its calculated amounts.
- Do NOT change success criteria in `isCampaignSuccessful()` without reviewing policy; it defines who gets paid.
- Do NOT remove payout action logging; it is required for audit and tracking.

---

## 10) User Authentication + Verification (Web)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/Auth/LoginController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/Auth/RegisterController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/AuthorizationController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/User.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Constants/ManageStatus.php`

### Classes + Methods
- `LoginController::loginForm()`, `login()`, `logout()`
- `RegisterController::registerForm()`, `register()`, `create()`
- `RegisterController::registerBusinessForm()`, `registerBusiness()`, `createBusinessUser()`
- `AuthorizationController::authorizeForm()`
- `AuthorizationController::sendVerifyCode()`
- `AuthorizationController::emailVerification()`
- `AuthorizationController::emailVerificationApi()`
- `AuthorizationController::mobileVerification()`
- `AuthorizationController::g2faVerification()`

### Step-by-step execution order (registration)
1. User opens register form → `registerForm()` loads countries and settings.
2. `register()` validates fields, checks captcha, ensures username format, prevents duplicate mobile.
3. `create()` builds `User` with `ec/sc/kc` flags based on settings.
4. If email verification is required, a verification code is generated and sent via `notify()`.
5. User is logged in and redirected.

### Step-by-step execution order (login)
1. `login()` validates credentials and captcha.
2. Throttling is applied using the AuthenticatesUsers trait.
3. Attempt login and redirect to dashboard.

### Why each step exists
- Captcha throttles abuse; unique checks prevent duplicate identities.
- `ec/sc/kc` flags are part of verification and KYC enforcement.
- AuthorizationController ensures users pass verification gates before normal usage.

### What must NOT be changed and why
- Do NOT remove captcha checks without alternative protection.
- Do NOT change how `ec` / `sc` / `kc` are set unless you also update verification flows.
- Do NOT remove verification code send logic; otherwise email confirmation never works.

---

## 11) API Authentication + OTP (Mobile App)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Api/AuthController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/routes/web.php` (mobile API routes)

### Classes + Methods
- `Api\AuthController::register()`
- `Api\AuthController::login()`
- `Api\AuthController::forgetPassword()`
- `Api\AuthController::sendPasswordResetOTP()`
- `Api\AuthController::verifyPasswordResetOTP()`
- `Api\AuthController::resetPassword()`
- `Api\AuthController::verifyEmailOTP()`
- `Api\AuthController::resendMobileOTP()`
- `Api\AuthController::verifyMobileOTP()`

### Step-by-step execution order (API registration)
1. `register()` validates inputs, generates a username, checks users table.
2. It verifies uniqueness of mobile/email.
3. Creates a user row, sets `ec` based on settings.
4. Sends email OTP if required.
5. Returns a Sanctum token and user data.

### Why each step exists
- The API uses direct SQL helper methods for backward compatibility.
- OTP workflows are required for mobile verification and password reset.

### What must NOT be changed and why
- Do NOT remove the token creation; mobile clients depend on it.
- Do NOT change `ver_code` handling without updating OTP verification methods.

---

## 12) KYC (User) + KYC Settings (Admin)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/User/UserController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/SettingController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Middleware/KycCheck.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Form.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Lib/FormProcessor.php`

### Classes + Methods
- `UserController::kycForm()`, `kycSubmit()`, `kycData()`
- `SettingController::kyc()`, `kycUpdate()`
- `KycCheck::handle()`

### Step-by-step execution order
1. User opens KYC form → `kycForm()` checks status and shows `Form` definition.
2. User submits → `kycSubmit()` validates with `FormProcessor::valueValidation()`, stores `kyc_data`, sets `kc = PENDING`.
3. Middleware `KycCheck::handle()` blocks restricted actions when `kc` is `UNVERIFIED` or `PENDING`.
4. Admin updates KYC form fields using `SettingController::kycUpdate()`.

### Why each step exists
- KYC form is dynamic for compliance updates.
- Middleware ensures unverified users cannot access restricted flows.

### What must NOT be changed and why
- Do NOT bypass `KycCheck` on routes that require compliance.
- Do NOT remove `FormProcessor` usage; it keeps dynamic KYC fields consistent.

---

## 13) Notifications (Email/SMS Templates + Sending + Logs)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Notify/Notify.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Notify/Email.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Notify/Sms.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/NotificationController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/EmailLogController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Services/EmailLoggingService.php`

### Classes + Methods
- `Notify::send()`
- `Email::send()` and provider methods (`sendPhpMail`, `sendSmtpMail`, `sendSendGridMail`, `sendMailjetMail`)
- `Sms::send()`
- `NotificationController::templates()`, `templateEdit()`, `templateUpdate()`
- `NotificationController::emailUpdate()`, `smsUpdate()`, `testEmail()`, `testSMS()`
- `EmailLogController::index()`, `show()`, `preview()`, `resend()`

### Step-by-step execution order (sending)
1. `notify()` helper builds a `Notify` instance and calls `Notify::send()`.
2. `Notify::send()` resolves required delivery methods (email/sms).
3. Each method class (Email/Sms) constructs a message from templates and settings.
4. Email is sent using the configured provider; success/failure is logged by `EmailLoggingService`.
5. SMS is sent through the configured gateway.

### Why each step exists
- Separate provider logic supports multiple email/SMS vendors.
- Template system ensures consistent message formatting.
- Logging provides auditability and debugging.

### What must NOT be changed and why
- Do NOT remove logging in `Email::send()`; admin email log pages depend on it.
- Do NOT change template keys (e.g., `EVER_CODE`, `WITHDRAW_REQUEST`) without updating all calls.

---

## 14) CMS / Site Content / SEO / Themes

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/SiteController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/HomePageController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/CustomCodeController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/SiteData.php`

### Classes + Methods
- `SiteController::themes()` / `makeActive()`
- `SiteController::sections()` / `content()` / `element()` / `remove()`
- `SiteController::saveSeoData()`
- `HomePageController::index()` / `updateHero()` / `updateInfoBanner()` / `updateFeaturedProjects()` / `updateTrendingCampaign()`
- `CustomCodeController::index()` / `update()`

### Step-by-step execution order (content update)
1. Admin opens a section → `SiteController::sections()` loads site.json definitions and `SiteData`.
2. Admin submits content → `SiteController::content()` validates fields based on section rules.
3. Images are uploaded/updated; URLs can override local images.
4. `SiteData::data_info` is updated and saved.
5. Optional SEO data is stored in `SiteData` under `*.seo`.

### Why each step exists
- `SiteData` acts as a DB-backed CMS for theme sections.
- Validation rules differ by section to allow flexible content.
- Image handling supports both upload and external URLs.

### What must NOT be changed and why
- Do NOT change `data_key` patterns (`section.content`, `section.element`, `section.seo`); the CMS depends on them.
- Do NOT remove slug handling for dynamic pages; public routing uses it.
- Do NOT remove `SiteController::saveSeoData()` logic; SEO settings are stored there.

---

## 15) Admin Configuration (Settings, Gateways, Withdraw Methods, Categories)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/SettingController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/AutomatedGatewayController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/ManualGatewayController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/WithdrawMethodController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/CategoryController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/SubCategoryController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/HeaderCategoryController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/PayoutBankController.php`

### Step-by-step execution order (gateway configuration)
1. Admin opens automated gateway list → `AutomatedGatewayController::index()`.
2. Admin edits a gateway → `edit()` loads `Gateway` and `GatewayCurrency` definitions.
3. Admin submits updates → `update()` validates global and per-currency parameters.
4. Gateway parameters are stored as JSON; currencies are regenerated.

### Why each step exists
- Payment gateways require per-currency configuration.
- Withdraw methods need validation because they define limits and charges.
- Category and subcategory ordering controls public navigation and filters.

### What must NOT be changed and why
- Do NOT change gateway parameter JSON schema; existing gateways rely on it.
- Do NOT remove min/max validation for withdraw methods; it prevents invalid payouts.
- Do NOT remove category status checks; public campaign listing relies on active categories only.

---

## 16) Webhook / Data Logging

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Services/UnifiedWebhookLoggerService.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Services/WebhookLoggerService.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/WebhookLogController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/DataLog.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/WebhookLog.php`

### Classes + Methods
- `UnifiedWebhookLoggerService::logIncomingWebhook()`
- `UnifiedWebhookLoggerService::updateWebhookStatus()`
- `WebhookLoggerService::logWebhook()`
- `WebhookLogController::index()` / `show()` / `retry()` / `cleanup()` / `export()`

### Step-by-step execution order
1. A webhook hits the system (e.g., payment IPN routes).
2. `UnifiedWebhookLoggerService::logIncomingWebhook()` records both `DataLog` and `WebhookLog` entries.
3. As processing finishes, `updateWebhookStatus()` marks success/failure with timing details.
4. Admin can review logs and retry failed hooks via `WebhookLogController`.

### Why each step exists
- Dual logging provides generic request logs and webhook-specific details.
- Retrying is essential for resilience when external APIs fail.

### What must NOT be changed and why
- Do NOT remove `DataLog::logRequest` calls; admin dashboard relies on them.
- Do NOT drop `transaction_id` extraction; it links webhook logs to payments.

---

## 17) Admin Authentication + Password Reset

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/Auth/LoginController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/Auth/ForgotPasswordController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/Auth/ResetPasswordController.php`

### Classes + Methods
- `Admin\Auth\LoginController::login()` / `logout()`
- `Admin\Auth\ForgotPasswordController::sendResetCode()`
- `Admin\Auth\ResetPasswordController::resetPassword()`

### Step-by-step execution order
1. Admin logs in via `LoginController::login()` with captcha validation and throttling.
2. Forgot password sends a verification code to the admin’s email.
3. Reset password validates code and updates `Admin` password.

### Why each step exists
- Captcha and throttling protect admin authentication.
- Verification code resets prevent unauthorized password changes.

### What must NOT be changed and why
- Do NOT remove captcha checks from admin login and reset flows.
- Do NOT remove the verification code storage; reset logic depends on it.

---

## 18) Language + Localization Keys

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/LanguageController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/resources/lang/*.json`

### Classes + Methods
- `LanguageController::index()`, `store()`, `status()`, `delete()`
- `LanguageController::translateKeyword()`
- `LanguageController::languageImport()`
- `LanguageController::languageKeyStore()`, `languageKeyUpdate()`, `languageKeyDelete()`

### Step-by-step execution order
1. Admin manages languages in DB.
2. JSON translation files are created or updated in `resources/lang`.
3. Keys are extracted from views and SiteData content.

### Why each step exists
- JSON-based translations allow fast localization without code changes.

### What must NOT be changed and why
- Do NOT delete the default language file; fallback translation depends on it.
- Do NOT break the JSON file structure; the translator expects key/value pairs.

---

## 19) Miscellaneous Admin Features (Banners, Contact, Report Fundraiser, Social Login)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/BannerController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/ContactController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/ReportFundraiserController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/SocialLoginSettingController.php`

### Classes + Methods
- `BannerController::index()`, `store()`, `update()`, `destroy()`
- `ContactController::subscriberIndex()`, `sendEmailSubscriber()`, `contactIndex()`
- `ReportFundraiserController::index()` / `update()`
- `SocialLoginSettingController::index()` / `update()` / `testConfiguration()`

### Step-by-step execution order
- Banners: CRUD operations upload images and store path in DB.
- Contact: Admin views contacts/subscribers and can send bulk email.
- Report fundraiser: Admin configures the report page content and enable status.
- Social login: Admin updates settings and `.env` with provider credentials.

### Why each step exists
- These are CMS/admin tools to manage public site elements and integrations.

### What must NOT be changed and why
- Do NOT remove `.env` updates in social login settings; OAuth providers need those keys.
- Do NOT remove contact subscriber email sending; marketing workflows depend on it.

---

## 20) Mobile API Wallet, Fund Update, Withdraw

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Api/WalletController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Api/FundUpdateController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Api/WithdrawController.php`

### Classes + Methods
- `Api\WalletController::walletReport()`
- `Api\FundUpdateController::fundUpdate()`
- `Api\FundUpdateController::cancelFund()`
- `Api\FundUpdateController::completeFund()`
- `Api\FundUpdateController::editFund()`
- `Api\WithdrawController::requestWithdraw()`
- `Api\WithdrawController::payoutList()`

### Step-by-step execution order (wallet report)
1. API client calls `/wallet_report.php`.
2. Controller validates user auth, then reads wallet and wallet_report table.
3. It builds a list of credit/debit entries and returns wallet balance.

### Why each step exists
- These endpoints support legacy mobile app API contracts.

### What must NOT be changed and why
- Do NOT change response field names (`ResponseCode`, `Result`, etc.); mobile app expects them.
- Do NOT remove auth checks; these endpoints handle wallet/withdrawal data.

---

## 21) Admin Dashboard + Reporting

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/AdminController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Deposit.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Withdrawal.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Transaction.php`

### Classes + Methods
- `AdminController::dashboard()`
- `AdminController::transaction()`

### Step-by-step execution order
1. Admin opens dashboard.
2. `dashboard()` computes counts for users, campaigns, deposits, withdrawals.
3. Monthly charts are generated with grouped queries.

### Why each step exists
- It provides operational metrics and financial summaries to admins.

### What must NOT be changed and why
- Do NOT remove status-based filters; they define correct dashboard metrics.

---

## 22) YouTube Integration (Admin)

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/YouTubeController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Services/YouTubeUploadService.php`

### Classes + Methods
- `YouTubeController::auth()`
- `YouTubeController::callback()`
- `YouTubeController::testUpload()`

### Step-by-step execution order
1. Admin clicks “Authorize YouTube” → redirects to OAuth URL.
2. Callback receives `code`, which is exchanged for tokens.
3. Tokens are stored in `.env` by `updateEnvTokens()`.

### Why each step exists
- YouTube uploads require OAuth tokens; storing them in `.env` is the chosen approach.

### What must NOT be changed and why
- Do NOT remove `.env` update behavior without replacing token storage.

---

## 23) Store Management / Sync Dashboard

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/StoreManagementController.php`

### Classes + Methods
- `StoreManagementController::index()`
- `StoreManagementController::runCron()`
- `StoreManagementController::getSyncStatus()`

### Step-by-step execution order
1. Admin visits store dashboard → `index()` builds sync stats.
2. Admin triggers cron → `runCron()` simulates sync and returns a JSON response.
3. `getSyncStatus()` returns sync stats for the UI.

### Why each step exists
- It provides UI hooks for product sync operations (currently mocked).

### What must NOT be changed and why
- Do NOT change JSON response structure if front-end expects it.

---

## 24) Public Contact + Subscribers

### Files
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/WebsiteController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Http/Controllers/Admin/ContactController.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Contact.php`
- `/Applications/XAMPP/xamppfiles/htdocs/apnafund/app/Models/Subscriber.php`

### Classes + Methods
- `WebsiteController::contact()` / `contactStore()`
- `WebsiteController::subscriberStore()`
- `Admin\ContactController::subscriberIndex()` / `contactIndex()`

### Step-by-step execution order
1. Public user submits contact form → `contactStore()` validates and saves `Contact`.
2. Admin views contacts and can delete or mark status.
3. Users subscribe via `subscriberStore()`; admin can email subscribers.

### Why each step exists
- Contact and subscriber storage is needed for support and marketing.

### What must NOT be changed and why
- Do NOT remove duplicate contact checks; it prevents spamming admins.

---

End of file.
