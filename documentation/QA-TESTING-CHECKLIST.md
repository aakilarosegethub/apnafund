# ApnaFund – Complete QA Testing Checklist

> **Purpose:** Non-technical QA testers can use this checklist to verify every implemented functionality in the system.  
> **Last Updated:** March 9, 2026  
> **Scope:** All controllers, routes, models, middleware, services, and frontend flows.

---

## Module: Beta / Landing

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | View beta landing page | First-time visitors see beta landing before home | `routes/web.php` (beta.page) | Guest |
| 2 | Accept beta and proceed | User accepts beta → cookie set → redirect to home | `routes/web.php` (beta.accept) | Guest |

---

## Module: Authentication (User)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | User login (form) | Display login form | `User\Auth\LoginController@loginForm` | Guest |
| 2 | User login (submit) | Authenticate and log in user | `User\Auth\LoginController@login` | Guest |
| 3 | User logout | End user session | `User\Auth\LoginController@logout` | User |
| 4 | User registration (form) | Display registration form | `User\Auth\RegisterController@registerBusinessForm` | Guest |
| 5 | User registration (submit) | Create new user account | `User\Auth\RegisterController@register` | Guest |
| 6 | Business registration | Business-specific registration flow | `User\Auth\RegisterController@registerBusiness` | Guest |
| 7 | Check user exists | Validate username/email before registration | `User\Auth\RegisterController@checkUser` | Guest |
| 8 | OTP login form | Display OTP-based login form | `User\Auth\OTPController@showOTPForm` | Guest |
| 9 | Send OTP | Send OTP to phone for login | `User\Auth\OTPController@sendOTP` | Guest |
| 10 | Verify OTP | Verify OTP and log in | `User\Auth\OTPController@verifyOTP` | Guest |
| 11 | Resend OTP | Resend OTP code | `User\Auth\OTPController@resendOTP` | Guest |
| 12 | Check phone number | Validate phone before OTP | `User\Auth\OTPController@checkPhoneNumber` | Guest |
| 13 | Facebook login | Redirect to Facebook OAuth | `User\Auth\SocialLoginController@redirectToFacebook` | Guest |
| 14 | Facebook callback | Handle Facebook OAuth callback | `User\Auth\SocialLoginController@handleFacebookCallback` | Guest |
| 15 | Google login | Redirect to Google OAuth | `User\Auth\SocialLoginController@redirectToGoogle` | Guest |
| 16 | Google callback | Handle Google OAuth callback | `User\Auth\SocialLoginController@handleGoogleCallback` | Guest |
| 17 | Forgot password (form) | Request password reset form | `User\Auth\ForgotPasswordController@requestForm` | Guest |
| 18 | Forgot password (send) | Send reset code to email | `User\Auth\ForgotPasswordController@sendResetCode` | Guest |
| 19 | Code verification form | Enter reset code received via email | `User\Auth\ForgotPasswordController@verificationForm` | Guest |
| 20 | Code verification | Verify reset code | `User\Auth\ForgotPasswordController@verificationCode` | Guest |
| 21 | Reset password (form) | Form to set new password | `User\Auth\ResetPasswordController@resetForm` | Guest |
| 22 | Reset password (submit) | Save new password | `User\Auth\ResetPasswordController@resetPassword` | Guest |

---

## Module: User Authorization (Post-Login)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Authorization form | Show authorization status (email/mobile/2FA) | `User\AuthorizationController@authorizeForm` | User |
| 2 | Resend verify code | Resend email/mobile verification code | `User\AuthorizationController@sendVerifyCode` | User |
| 3 | Verify email | Submit email verification code | `User\AuthorizationController@emailVerification` | User |
| 4 | Verify mobile | Submit mobile verification code | `User\AuthorizationController@mobileVerification` | User |
| 5 | Verify 2FA | Submit Google 2FA code | `User\AuthorizationController@g2faVerification` | User |
| 6 | Email verification (API) | API endpoint for email verify (no CSRF) | `User\AuthorizationController@emailVerificationApi` | User |

---

## Module: User Dashboard & Profile

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | User dashboard | Main dashboard/home after login | `User\UserController@home` | User |
| 2 | KYC data | Fetch KYC form data | `User\UserController@kycData` | User |
| 3 | KYC form | Display KYC submission form | `User\UserController@kycForm` | User |
| 4 | KYC submit | Submit KYC documents | `User\UserController@kycSubmit` | User |
| 5 | Profile view | View own profile | `User\UserController@profile` | User |
| 6 | Profile update | Update profile details | `User\UserController@profileUpdate` | User |
| 7 | Change password (form) | Form to change password | `User\UserController@password` | User |
| 8 | Change password (submit) | Update password | `User\UserController@passwordChange` | User |
| 9 | Two-factor form | Show 2FA enable/disable form | `User\UserController@show2faForm` | User |
| 10 | Enable 2FA | Enable two-factor authentication | `User\UserController@enable2fa` | User |
| 11 | Disable 2FA | Disable two-factor authentication | `User\UserController@disable2fa` | User |
| 12 | Donation history | List donations made by user | `User\UserController@donationHistory` | User |
| 13 | Donations received | List donations received for campaigns | `User\UserController@donationReceived` | User |
| 14 | Rewards tracking | Track reward fulfillment for received donations | `User\UserController@rewardsTracking` | User |
| 15 | Reward fulfill | Mark reward as fulfilled for a donation | `User\UserController@fulfillReward` | User |
| 16 | Transactions | View transaction history | `User\UserController@transactions` | User |
| 17 | File download | Download files (e.g. receipts) | `User\UserController@fileDownload` | User |

---

## Module: Campaigns (User/Creator)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Campaign index | List all user campaigns | `User\CampaignController@index` | User |
| 2 | Approved campaigns | List approved campaigns | `User\CampaignController@approved` | User |
| 3 | Pending campaigns | List pending campaigns | `User\CampaignController@pending` | User |
| 4 | Rejected campaigns | List rejected campaigns | `User\CampaignController@rejected` | User |
| 5 | Create campaign (form) | New campaign creation form | `User\CampaignController@new` | User |
| 6 | Store campaign | Save new campaign | `User\CampaignController@store` | User |
| 7 | Edit campaign | Edit campaign main page | `User\CampaignController@edit` | User |
| 8 | Edit basics | Edit basics section | `User\CampaignController@editSection` (basics) | User |
| 9 | Edit reward | Edit rewards section | `User\CampaignController@editSection` (reward) | User |
| 10 | Edit story | Edit story section | `User\CampaignController@editSection` (story) | User |
| 11 | Edit people | Edit team/people section | `User\CampaignController@editSection` (people) | User |
| 12 | Edit payment | Edit payment settings | `User\CampaignController@editSection` (payment) | User |
| 13 | Update payment | Save payment settings | `User\CampaignController@updatePayment` | User |
| 14 | Edit boost | Edit promotion section | `User\CampaignController@editSection` (boost) | User |
| 15 | Edit FAQ | Edit FAQ section | `User\CampaignController@editSection` (faq) | User |
| 16 | Edit updates | Edit campaign updates section | `User\CampaignController@editSection` (updates) | User |
| 17 | Pay registration fee (form) | Pay campaign registration fee | `User\CampaignController@payRegistrationFee` | User |
| 18 | Submit registration fee | Process registration fee payment | `User\CampaignController@submitRegistrationFee` | User |
| 19 | Add collaborator | Add collaborator to campaign | `User\CampaignController@addCollaborator` | User |
| 20 | Remove collaborator | Remove collaborator | `User\CampaignController@removeCollaborator` | User |
| 21 | Search collaborators | Search users for collaboration | `User\CampaignController@searchUsers` | User |
| 22 | Remove image | Remove campaign image | `User\CampaignController@removeImage` | User |
| 23 | Store FAQ | Add FAQ item | `User\CampaignController@storeFaq` | User |
| 24 | Update FAQ | Update FAQ item | `User\CampaignController@updateFaq` | User |
| 25 | Delete FAQ | Delete FAQ item | `User\CampaignController@deleteFaq` | User |
| 26 | Get FAQ | Get FAQ for edit | `User\CampaignController@getFaq` | User |
| 27 | Store update | Add campaign update | `User\CampaignController@storeUpdate` | User |
| 28 | Update update | Edit campaign update | `User\CampaignController@updateUpdate` | User |
| 29 | Delete update | Delete campaign update | `User\CampaignController@deleteUpdate` | User |
| 30 | Get update | Get update for edit | `User\CampaignController@getUpdate` | User |
| 31 | Upload image | Upload campaign image | `User\CampaignController@uploadImage` | User |
| 32 | Upload story media | Upload story section media | `User\CampaignController@uploadStoryMedia` | User |
| 33 | Upload external image | Upload image from URL | `User\CampaignController@uploadExternalImage` | User |
| 34 | Upload campaign image | Upload main campaign image | `User\CampaignController@uploadCampaignImage` | User |
| 35 | Upload campaign video | Upload campaign video | `User\CampaignController@uploadCampaignVideo` | User |
| 36 | Update campaign | Save campaign changes | `User\CampaignController@update` | User |
| 37 | Campaign details | View campaign details | `User\CampaignController@show` | User |
| 38 | Delete campaign | Delete campaign | `User\CampaignController@destroy` | User |
| 39 | Gallery upload | Upload gallery image | `User\CampaignController@galleryUpload` | User |
| 40 | Gallery remove | Remove gallery image | `User\CampaignController@galleryRemove` | User |
| 41 | Delete all gallery | Remove all gallery images | `User\CampaignController@deleteAllGallery` | User |

---

## Module: Campaign Promotion (Meta/Facebook Boost)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Promote campaign | Start Meta/Facebook promotion | `User\CampaignPromotionController@promoteCampaign` | User |
| 2 | Pause promotion | Pause active promotion | `User\CampaignPromotionController@pausePromotion` | User |
| 3 | Promotion status | Get promotion status | `User\CampaignPromotionController@getPromotionStatus` | User |

---

## Module: Rewards (User/Creator)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Rewards index | List rewards for campaign | `User\RewardController@index` | User |
| 2 | Create reward (form) | Add new reward form | `User\RewardController@create` | User |
| 3 | Store reward | Save new reward | `User\RewardController@store` | User |
| 4 | Edit reward (form) | Edit reward form | `User\RewardController@edit` | User |
| 5 | Update reward | Save reward changes | `User\RewardController@update` | User |
| 6 | Delete reward | Delete reward | `User\RewardController@destroy` | User |
| 7 | Toggle reward status | Activate/deactivate reward | `User\RewardController@toggleStatus` | User |

---

## Module: Withdrawals (User)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Withdraw methods | View available withdraw methods | `User\WithdrawController@methods` | User (KYC verified) |
| 2 | Withdraw store | Submit withdrawal request | `User\WithdrawController@store` | User |
| 3 | Withdraw preview | Preview withdrawal before submit | `User\WithdrawController@preview` | User |
| 4 | Withdraw submit | Confirm and submit withdrawal | `User\WithdrawController@submit` | User |
| 5 | Withdraw index | View withdrawal history | `User\WithdrawController@index` | User |

---

## Module: Deposit / Donation (User/Guest)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Insert deposit | Start donation, create deposit record | `Gateway\PaymentController@depositInserts` | User/Guest |
| 2 | Deposit confirm | Payment confirmation page (gateway redirect) | `Gateway\PaymentController@depositConfirm` | User/Guest |
| 3 | Deposit success | Success page after successful payment | `Gateway\PaymentController@success` | User/Guest |
| 4 | Payment error | Error page on payment failure/cancel | `Gateway\PaymentController@paymentError` | User/Guest |
| 5 | Manual deposit confirm | Manual payment instructions | `Gateway\PaymentController@manualDepositConfirm` | User/Guest |
| 6 | Manual deposit update | Submit manual payment proof | `Gateway\PaymentController@manualDepositUpdate` | User/Guest |

---

## Module: Chat / Inbox

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Inbox | View messages/inbox | `User\ChatController@inbox` | User |
| 2 | Firebase token | Get Firebase token for chat | `User\ChatController@getFirebaseToken` | User |
| 3 | Unread count | Get unread message count | `User\ChatController@unreadCount` | User |
| 4 | Creator names | Get creator names for chat | `User\ChatController@getCreatorNames` | User |

---

## Module: Public Website (Guest)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Home | Main homepage | `WebsiteController@home` | Guest |
| 2 | Home (new) | Alternative home layout | `WebsiteController@homeNew` | Guest |
| 3 | About us | About page | `WebsiteController@aboutUs` | Guest |
| 4 | FAQ | Frequently asked questions | `WebsiteController@faq` | Guest |
| 5 | Creators | Creators/creators list | `WebsiteController@creators` | Guest |
| 6 | Campaigns list | Browse all campaigns | `WebsiteController@campaigns` | Guest |
| 7 | Campaigns by category | Campaigns filtered by category | `WebsiteController@campaignCategory` | Guest |
| 8 | Campaign show | View single campaign | `WebsiteController@campaignShow` | Guest |
| 9 | Campaign donate | Donate to campaign (contribute page) | `WebsiteController@campaignDonate` | Guest |
| 10 | Campaign rewards (public) | Public rewards page for campaign | `RewardController@show` | Guest |
| 11 | Post comment | Add comment on campaign | `WebsiteController@storeCampaignComment` | Guest |
| 12 | Fetch comments | Load campaign comments | `WebsiteController@fetchCampaignComment` | Guest |
| 13 | Campaign donations | List campaign donations | `WebsiteController@campaignDonations` | Guest |
| 14 | Top donations | Top donors for campaign | `WebsiteController@campaignTopDonations` | Guest |
| 15 | Campaign updates | List campaign updates | `WebsiteController@campaignUpdates` | Guest |
| 16 | Campaign update show | View single update | `WebsiteController@campaignUpdateShow` | Guest |
| 17 | Update comment | Comment on campaign update | `WebsiteController@storeUpdateComment` | Guest |
| 18 | Upcoming campaigns | List upcoming campaigns | `WebsiteController@upcomingCampaigns` | Guest |
| 19 | Upcoming campaign show | View upcoming campaign | `WebsiteController@upcomingCampaignShow` | Guest |
| 20 | Success stories | Success stories list | `WebsiteController@stories` | Guest |
| 21 | Story show | View single story | `WebsiteController@storyShow` | Guest |
| 22 | Creator hub / Business resources | Creator resources page | `WebsiteController@businessResources` | Guest |
| 23 | Start project | Multi-step campaign creation wizard | `WebsiteController@startProject` | Guest |
| 24 | Save project categories | Save categories in start flow | `WebsiteController@saveProjectCategories` | Guest |
| 25 | Project location | Select location step | `WebsiteController@projectLocation` | Guest |
| 26 | Save project location | Save location | `WebsiteController@saveProjectLocation` | Guest |
| 27 | Project terms | Accept terms step | `WebsiteController@projectTerms` | Guest |
| 28 | Create campaign from session | Create campaign after wizard | `WebsiteController@createCampaignFromSession` | Guest |
| 29 | Subscribe | Newsletter/store subscriber | `WebsiteController@subscriberStore` | Guest |
| 30 | Contact form | Contact us page | `WebsiteController@contact` | Guest |
| 31 | Contact submit | Submit contact form | `WebsiteController@contactStore` | Guest |
| 32 | Cookie accept | Accept cookie policy | `WebsiteController@cookieAccept` | Guest |
| 33 | Cookie policy | View cookie policy | `WebsiteController@cookiePolicy` | Guest |
| 34 | Change language | Switch site language | `WebsiteController@changeLanguage` | Guest |
| 35 | Help | Help page | `WebsiteController@help` | Guest |
| 36 | Sitemap | Sitemap page | `WebsiteController@sitemap` | Guest |
| 37 | Creator profile (public) | Public creator profile by username | `WebsiteController@creatorProfile` | Guest |
| 38 | Order success | Thank you page after donation | `WebsiteController@orderSuccess` | Guest |
| 39 | Policy pages | View policy by slug/id | `WebsiteController@policyPages` | Guest |
| 40 | Report fundraiser | Report fundraiser page | `WebsiteController@reportFundraiser` | Guest |
| 41 | Placeholder image | Generate placeholder image | `WebsiteController@placeholderImage` | Guest |
| 42 | Update user country | Update country in session (e.g. geo) | `WebsiteController@updateUserCountry` | Guest |
| 43 | Dynamic page by slug | Dynamic CMS page | `WebsiteController@pageBySlug` | Guest |
| 44 | Dynamic pages | Catch-all dynamic page | `WebsiteController@dynamicPages` | Guest |
| 45 | Editor | Rich text editor preview | `WebsiteController@editor` | Guest |

---

## Module: Admin Authentication

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Admin login (form) | Admin login form | `Admin\Auth\LoginController@loginForm` | Guest |
| 2 | Admin login (submit) | Authenticate admin | `Admin\Auth\LoginController@login` | Guest |
| 3 | Admin logout | End admin session | `Admin\Auth\LoginController@logout` | Admin |
| 4 | Admin forgot password | Request password reset | `Admin\Auth\ForgotPasswordController@requestForm` | Guest |
| 5 | Admin send reset code | Send reset code | `Admin\Auth\ForgotPasswordController@sendResetCode` | Guest |
| 6 | Admin code verification | Verify reset code | `Admin\Auth\ForgotPasswordController@verificationForm`, `verificationCode` | Guest |
| 7 | Admin reset form | Reset password form | `Admin\Auth\ResetPasswordController@resetForm` | Guest |
| 8 | Admin reset password | Save new password | `Admin\Auth\ResetPasswordController@resetPassword` | Guest |

---

## Module: Admin Dashboard & Profile

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Admin dashboard | Main admin dashboard | `Admin\AdminController@dashboard` | Admin |
| 2 | Admin profile | View admin profile | `Admin\AdminController@profile` | Admin |
| 3 | Admin profile update | Update admin profile | `Admin\AdminController@profileUpdate` | Admin |
| 4 | Admin password change | Change admin password | `Admin\AdminController@passwordChange` | Admin |
| 5 | Notifications all | List all notifications | `Admin\AdminController@notificationAll` | Admin |
| 6 | Notification read | Mark notification as read | `Admin\AdminController@notificationRead` | Admin |
| 7 | Read all notifications | Mark all as read | `Admin\AdminController@notificationReadAll` | Admin |
| 8 | Remove notification | Remove single notification | `Admin\AdminController@notificationRemove` | Admin |
| 9 | Remove all notifications | Remove all notifications | `Admin\AdminController@notificationRemoveAll` | Admin |
| 10 | Transaction index | View transactions | `Admin\AdminController@transaction` | Admin |
| 11 | File download | Download files | `Admin\AdminController@fileDownload` | Admin |
| 12 | Upload file | CKEditor file upload | `Admin\AdminController@uploadFile` | Admin |
| 13 | Upload external image | CKEditor external image | `Admin\AdminController@uploadExternalImage` | Admin |

---

## Module: Admin – Banners

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Banners index | List banners | `Admin\BannerController@index` | Admin |
| 2 | Create banner | Create banner form | `Admin\BannerController@create` | Admin |
| 3 | Store banner | Save new banner | `Admin\BannerController@store` | Admin |
| 4 | Show banner | View banner details | `Admin\BannerController@show` | Admin |
| 5 | Edit banner | Edit banner form | `Admin\BannerController@edit` | Admin |
| 6 | Update banner | Save banner changes | `Admin\BannerController@update` | Admin |
| 7 | Destroy banner | Delete banner | `Admin\BannerController@destroy` | Admin |

---

## Module: Admin – Blog / DSA Posts

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Blog index | List blog posts | `Admin\DsaPostController@index` | Admin |
| 2 | Create post | Create blog form | `Admin\DsaPostController@create` | Admin |
| 3 | Store post | Save new post | `Admin\DsaPostController@store` | Admin |
| 4 | Edit post | Edit post form | `Admin\DsaPostController@edit` | Admin |
| 5 | Update post | Save post changes | `Admin\DsaPostController@update` | Admin |
| 6 | Destroy post | Delete post | `Admin\DsaPostController@destroy` | Admin |

---

## Module: Admin – Roles (RBAC)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Roles index | List roles | `Admin\RoleController@index` | Admin |
| 2 | Create role | Create role form | `Admin\RoleController@create` | Admin |
| 3 | Store role | Save new role | `Admin\RoleController@store` | Admin |
| 4 | Edit role | Edit role form | `Admin\RoleController@edit` | Admin |
| 5 | Update role | Save role changes | `Admin\RoleController@update` | Admin |
| 6 | Destroy role | Delete role | `Admin\RoleController@destroy` | Admin |

---

## Module: Admin – Sub-Admins

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Admin users index | List sub-admins | `Admin\AdminUserController@index` | Admin |
| 2 | Create admin | Create sub-admin form | `Admin\AdminUserController@create` | Admin |
| 3 | Store admin | Save new sub-admin | `Admin\AdminUserController@store` | Admin |
| 4 | Edit admin | Edit sub-admin form | `Admin\AdminUserController@edit` | Admin |
| 5 | Update admin | Save sub-admin changes | `Admin\AdminUserController@update` | Admin |
| 6 | Destroy admin | Delete sub-admin | `Admin\AdminUserController@destroy` | Admin |
| 7 | Admin status | Toggle sub-admin status | `Admin\AdminUserController@status` | Admin |

---

## Module: Admin – Categories

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Categories index | List categories | `Admin\CategoryController@index` | Admin |
| 2 | Store category | Create/update category | `Admin\CategoryController@store` | Admin |
| 3 | Category status | Toggle category status | `Admin\CategoryController@status` | Admin |
| 4 | Subcategories index | List subcategories | `Admin\SubCategoryController@index` | Admin |
| 5 | Subcategory store | Create/update subcategory | `Admin\SubCategoryController@store` | Admin |
| 6 | Subcategory status | Toggle subcategory status | `Admin\SubCategoryController@status` | Admin |
| 7 | Subcategory delete | Delete subcategory | `Admin\SubCategoryController@delete` | Admin |
| 8 | Header categories | Manage header categories | `Admin\HeaderCategoryController` | Admin |
| 9 | Footer categories | Manage footer categories | `Admin\FooterCategoryController` | Admin |

---

## Module: Admin – Payout Banks

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Payout banks index | List payout banks | `Admin\PayoutBankController@index` | Admin |
| 2 | Store bank | Create/update bank | `Admin\PayoutBankController@store` | Admin |
| 3 | Bank status | Toggle bank status | `Admin\PayoutBankController@status` | Admin |
| 4 | Bank delete | Delete bank | `Admin\PayoutBankController@delete` | Admin |

---

## Module: Admin – Campaigns

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Campaigns index | List all campaigns | `Admin\CampaignController@index` | Admin |
| 2 | Pending campaigns | List pending campaigns | `Admin\CampaignController@pending` | Admin |
| 3 | Approved campaigns | List approved campaigns | `Admin\CampaignController@approved` | Admin |
| 4 | Rejected campaigns | List rejected campaigns | `Admin\CampaignController@rejected` | Admin |
| 5 | Running campaigns | List running campaigns | `Admin\CampaignController@running` | Admin |
| 6 | Upcoming campaigns | List upcoming campaigns | `Admin\CampaignController@upcoming` | Admin |
| 7 | Expired campaigns | List expired campaigns | `Admin\CampaignController@expired` | Admin |
| 8 | Campaign details | View campaign details | `Admin\CampaignController@details` | Admin |
| 9 | Edit campaign | Edit campaign form | `Admin\CampaignController@edit` | Admin |
| 10 | Upload campaign image | Upload campaign image | `Admin\CampaignController@uploadCampaignImage` | Admin |
| 11 | Update campaign | Save campaign changes | `Admin\CampaignController@update` | Admin |
| 12 | Fix images | Fix campaign images | `Admin\CampaignController@fixAllImages` | Admin |
| 13 | Status update | Approve/reject/update status | `Admin\CampaignController@updateStatus` | Admin |
| 14 | Featured update | Toggle featured | `Admin\CampaignController@updateFeatured` | Admin |

---

## Module: Admin – Registration Steps

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Steps index | List registration steps | `Admin\RegistrationStepController@index` | Admin |
| 2 | Create step | Create step form | `Admin\RegistrationStepController@create` | Admin |
| 3 | Store step | Save new step | `Admin\RegistrationStepController@store` | Admin |
| 4 | Edit step | Edit step form | `Admin\RegistrationStepController@edit` | Admin |
| 5 | Update step | Save step changes | `Admin\RegistrationStepController@update` | Admin |
| 6 | Destroy step | Delete step | `Admin\RegistrationStepController@destroy` | Admin |
| 7 | Toggle step status | Enable/disable step | `Admin\RegistrationStepController@toggleStatus` | Admin |
| 8 | Reorder steps | Change step order | `Admin\RegistrationStepController@reorderSteps` | Admin |
| 9 | Add question | Add question to step | `Admin\RegistrationStepController@addQuestion` | Admin |
| 10 | Update question | Update question | `Admin\RegistrationStepController@updateQuestion` | Admin |
| 11 | Delete question | Delete question | `Admin\RegistrationStepController@deleteQuestion` | Admin |
| 12 | Toggle question status | Enable/disable question | `Admin\RegistrationStepController@toggleQuestionStatus` | Admin |
| 13 | Reorder questions | Change question order | `Admin\RegistrationStepController@reorderQuestions` | Admin |

---

## Module: Admin – Comments

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Comments index | List campaign comments | `Admin\CommentController@index` | Admin |
| 2 | Approve comment | Approve comment | `Admin\CommentController@approve` | Admin |
| 3 | Delete comment | Delete comment | `Admin\CommentController@destroy` | Admin |

---

## Module: Admin – User Management

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Users index | List all users | `Admin\UserController@index` | Admin |
| 2 | Active users | List active users | `Admin\UserController@active` | Admin |
| 3 | Banned users | List banned users | `Admin\UserController@banned` | Admin |
| 4 | KYC pending | List KYC pending users | `Admin\UserController@kycPending` | Admin |
| 5 | KYC unconfirmed | List KYC unconfirmed | `Admin\UserController@kycUnConfirmed` | Admin |
| 6 | Email unconfirmed | List email unconfirmed | `Admin\UserController@emailUnConfirmed` | Admin |
| 7 | Mobile unconfirmed | List mobile unconfirmed | `Admin\UserController@mobileUnConfirmed` | Admin |
| 8 | KYC approve | Approve user KYC | `Admin\UserController@kycApprove` | Admin |
| 9 | KYC cancel | Reject/cancel KYC | `Admin\UserController@kycCancel` | Admin |
| 10 | User details | View user details | `Admin\UserController@details` | Admin |
| 11 | User update | Update user | `Admin\UserController@update` | Admin |
| 12 | Password change | Change user password | `Admin\UserController@changePassword` | Admin |
| 13 | Login as user | Impersonate user | `Admin\UserController@login` | Admin |
| 14 | Balance update | Add/subtract user balance | `Admin\UserController@balanceUpdate` | Admin |
| 15 | User status | Ban/unban user | `Admin\UserController@status` | Admin |
| 16 | Send email (form) | Send email to user form | `Admin\UserController@sendEmail` | Admin |
| 17 | Send email (post) | Send email to user | `Admin\UserController@sendEmailToUser` | Admin |
| 18 | Bulk email (form) | Bulk email form | `Admin\UserController@sendBulkEmail` | Admin |
| 19 | Bulk email (post) | Send bulk email | `Admin\UserController@sendBulkEmailToUsers` | Admin |
| 20 | Delete all users (form) | Delete all users confirmation | `Admin\UserController@deleteAllUsers` | Admin |
| 21 | Confirm delete all | Execute delete all users | `Admin\UserController@confirmDeleteAllUsers` | Admin |
| 22 | Delete selected users | Delete selected users | `Admin\UserController@deleteSelectedUsers` | Admin |
| 23 | Test welcome email | Test welcome email for user | `Admin\UserController@testWelcomeEmail` | Admin |
| 24 | Test email last user | Test email to last registered user | `Admin\UserController@testEmailToLastUser` | Admin |
| 25 | Send welcome recent | Send welcome to recent users | `Admin\UserController@sendWelcomeToRecentUsers` | Admin |
| 26 | Welcome template editor | Edit welcome email template | `Admin\UserController@welcomeTemplateEditor` | Admin |
| 27 | Welcome template update | Save welcome template | `Admin\UserController@welcomeTemplateUpdate` | Admin |

---

## Module: Admin – Gateways (Payment)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Automated gateway index | List automated gateways | `Admin\AutomatedGatewayController@index` | Admin |
| 2 | Edit automated gateway | Edit gateway config | `Admin\AutomatedGatewayController@edit` | Admin |
| 3 | Update automated gateway | Save gateway config | `Admin\AutomatedGatewayController@update` | Admin |
| 4 | Remove gateway | Remove gateway | `Admin\AutomatedGatewayController@remove` | Admin |
| 5 | Gateway status | Toggle gateway status | `Admin\AutomatedGatewayController@status` | Admin |
| 6 | Manual gateway index | List manual gateways | `Admin\ManualGatewayController@index` | Admin |
| 7 | New manual gateway | Create manual gateway form | `Admin\ManualGatewayController@new` | Admin |
| 8 | Store manual gateway | Save manual gateway | `Admin\ManualGatewayController@store` | Admin |
| 9 | Edit manual gateway | Edit manual gateway | `Admin\ManualGatewayController@edit` | Admin |
| 10 | Manual gateway status | Toggle manual gateway status | `Admin\ManualGatewayController@status` | Admin |

---

## Module: Admin – Creator Payouts

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Creator payout settings | Edit payout fee settings | `Admin\CreatorPayoutSettingController@edit` | Admin |
| 2 | Update payout settings | Save payout settings | `Admin\CreatorPayoutSettingController@update` | Admin |
| 3 | Creator payouts index | List creator payouts | `Admin\CreatorPayoutController@index` | Admin |
| 4 | Creator payout show | View payout details | `Admin\CreatorPayoutController@show` | Admin |
| 5 | Partial payout | Process partial payout | `Admin\CreatorPayoutController@partialPayout` | Admin |
| 6 | Full payout | Process full payout | `Admin\CreatorPayoutController@fullPayout` | Admin |
| 7 | Mark fulfillment complete | Mark reward fulfillment complete | `Admin\CreatorPayoutController@markFulfillmentComplete` | Admin |

---

## Module: Admin – Donations

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Donations index | List all donations | `Admin\DepositController@index` | Admin |
| 2 | Pending donations | List pending (manual) donations | `Admin\DepositController@pending` | Admin |
| 3 | Done donations | List completed donations | `Admin\DepositController@done` | Admin |
| 4 | Cancelled donations | List cancelled donations | `Admin\DepositController@cancelled` | Admin |
| 5 | Approve donation | Approve manual donation | `Admin\DepositController@approve` | Admin |
| 6 | Reject donation | Reject manual donation | `Admin\DepositController@reject` | Admin |
| 7 | Rewards tracking | View rewards fulfillment tracking | `Admin\DepositController@rewardsTracking` | Admin |

---

## Module: Admin – Withdrawals

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Withdraw method index | List withdraw methods | `Admin\WithdrawMethodController@index` | Admin |
| 2 | New method | Create withdraw method form | `Admin\WithdrawMethodController@new` | Admin |
| 3 | Store method | Save withdraw method | `Admin\WithdrawMethodController@store` | Admin |
| 4 | Edit method | Edit withdraw method | `Admin\WithdrawMethodController@edit` | Admin |
| 5 | Method status | Toggle method status | `Admin\WithdrawMethodController@status` | Admin |
| 6 | Withdraw index | List withdrawals | `Admin\WithdrawController@index` | Admin |
| 7 | Pending withdrawals | List pending | `Admin\WithdrawController@pending` | Admin |
| 8 | Done withdrawals | List completed | `Admin\WithdrawController@done` | Admin |
| 9 | Cancelled withdrawals | List cancelled | `Admin\WithdrawController@cancelled` | Admin |
| 10 | Approve withdrawal | Approve withdrawal | `Admin\WithdrawController@approve` | Admin |
| 11 | Cancel withdrawal | Cancel withdrawal | `Admin\WithdrawController@cancel` | Admin |

---

## Module: Admin – Subscribers & Contacts

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Subscribers index | List subscribers | `Admin\ContactController@subscriberIndex` | Admin |
| 2 | Remove subscriber | Remove subscriber | `Admin\ContactController@subscriberRemove` | Admin |
| 3 | Send subscriber email | Email subscribers | `Admin\ContactController@sendEmailSubscriber` | Admin |
| 4 | Contacts index | List contact messages | `Admin\ContactController@contactIndex` | Admin |
| 5 | Remove contact | Remove contact message | `Admin\ContactController@contactRemove` | Admin |
| 6 | Contact status | Update contact status | `Admin\ContactController@contactStatus` | Admin |

---

## Module: Admin – Settings

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Basic setting | Site basic settings | `Admin\SettingController@basic` | Admin |
| 2 | Basic update | Save basic settings | `Admin\SettingController@basicUpdate` | Admin |
| 3 | System update | Save system settings | `Admin\SettingController@systemUpdate` | Admin |
| 4 | Logo/favicon update | Update logo and favicon | `Admin\SettingController@logoFaviconUpdate` | Admin |
| 5 | Cover setting | Cover image settings | `Admin\SettingController@cover`, `coverUpdate` | Admin |
| 6 | Plugin setting | Plugin configuration | `Admin\SettingController@plugin` | Admin |
| 7 | Plugin update | Update plugin config | `Admin\SettingController@pluginUpdate` | Admin |
| 8 | Plugin status | Toggle plugin status | `Admin\SettingController@pluginStatus` | Admin |
| 9 | SEO setting | SEO configuration | `Admin\SettingController@seo` | Admin |
| 10 | KYC setting | KYC form configuration | `Admin\SettingController@kyc`, `kycUpdate` | Admin |
| 11 | Home setting | Homepage settings | `Admin\SettingController@home`, `homeUpdate` | Admin |
| 12 | Cookie setting | Cookie policy settings | `Admin\SettingController@cookie`, `cookieUpdate` | Admin |
| 13 | Maintenance | Maintenance mode | `Admin\SettingController@maintenance`, `maintenanceUpdate` | Admin |
| 14 | Cache clear | Clear application cache | `Admin\SettingController@cacheClear` | Admin |

---

## Module: Admin – Gemini AI

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Gemini index | Gemini AI settings page | `Admin\GeminiSettingController@index` | Admin |
| 2 | Gemini update | Save Gemini settings | `Admin\GeminiSettingController@update` | Admin |
| 3 | Gemini test | Test Gemini API | `Admin\GeminiSettingController@test` | Admin |
| 4 | Gemini reset | Reset Gemini config | `Admin\GeminiSettingController@reset` | Admin |

---

## Module: Admin – Notifications (Email/SMS)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Welcome template | Welcome email template | `Admin\NotificationController@welcome` | Admin |
| 2 | Welcome update | Save welcome template | `Admin\NotificationController@welcomeUpdate` | Admin |
| 3 | Universal template | Universal template | `Admin\NotificationController@universal` | Admin |
| 4 | Universal update | Save universal | `Admin\NotificationController@universalUpdate` | Admin |
| 5 | Templates list | List notification templates | `Admin\NotificationController@templates` | Admin |
| 6 | Template edit | Edit template | `Admin\NotificationController@templateEdit` | Admin |
| 7 | Template update | Save template | `Admin\NotificationController@templateUpdate` | Admin |
| 8 | Email setting | Email configuration | `Admin\NotificationController@email` | Admin |
| 9 | Email update | Save email config | `Admin\NotificationController@emailUpdate` | Admin |
| 10 | Test email | Send test email | `Admin\NotificationController@testEmail` | Admin |
| 11 | SMS setting | SMS configuration | `Admin\NotificationController@sms` | Admin |
| 12 | SMS update | Save SMS config | `Admin\NotificationController@smsUpdate` | Admin |
| 13 | Test SMS | Send test SMS | `Admin\NotificationController@testSMS` | Admin |

---

## Module: Admin – Language

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Language index | List languages | `Admin\LanguageController@index` | Admin |
| 2 | Keywords | Manage language keywords | `Admin\LanguageController@keywords` | Admin |
| 3 | Store language | Create/update language | `Admin\LanguageController@store` | Admin |
| 4 | Language status | Toggle language status | `Admin\LanguageController@status` | Admin |
| 5 | Delete language | Delete language | `Admin\LanguageController@delete` | Admin |
| 6 | Translate keyword | Translate keyword | `Admin\LanguageController@translateKeyword` | Admin |
| 7 | Import language | Import language file | `Admin\LanguageController@languageImport` | Admin |
| 8 | Store key | Add language key | `Admin\LanguageController@languageKeyStore` | Admin |
| 9 | Update key | Update language key | `Admin\LanguageController@languageKeyUpdate` | Admin |
| 10 | Delete key | Delete language key | `Admin\LanguageController@languageKeyDelete` | Admin |

---

## Module: Admin – Site / Frontend

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Themes | Manage site themes | `Admin\SiteController@themes` | Admin |
| 2 | Make active theme | Activate theme | `Admin\SiteController@makeActive` | Admin |
| 3 | Sections | Manage home sections | `Admin\SiteController@sections` | Admin |
| 4 | Section content | Update section content | `Admin\SiteController@content` | Admin |
| 5 | Element | Manage section element | `Admin\SiteController@element` | Admin |
| 6 | Remove element | Remove element | `Admin\SiteController@remove` | Admin |

---

## Module: Admin – Home Page

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Homepage index | Homepage management | `Admin\HomePageController@index` | Admin |
| 2 | Update hero | Update hero section | `Admin\HomePageController@updateHero` | Admin |
| 3 | Update info banner | Update info banner | `Admin\HomePageController@updateInfoBanner` | Admin |
| 4 | Update featured projects | Update featured campaigns | `Admin\HomePageController@updateFeaturedProjects` | Admin |
| 5 | Update trending campaign | Update trending campaign | `Admin\HomePageController@updateTrendingCampaign` | Admin |

---

## Module: Admin – Custom Code

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Custom code index | Custom CSS/JS settings | `Admin\CustomCodeController@index` | Admin |
| 2 | Custom code update | Save custom code | `Admin\CustomCodeController@update` | Admin |

---

## Module: Admin – YouTube

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | YouTube index | YouTube integration page | `Admin\YouTubeController@index` | Admin |
| 2 | YouTube auth | Start YouTube OAuth | `Admin\YouTubeController@auth` | Admin |
| 3 | YouTube callback | OAuth callback (in web.php) | `routes/web.php` (youtube.callback) | Guest |
| 4 | Test upload | Test YouTube upload | `Admin\YouTubeController@testUpload` | Admin |

---

## Module: Admin – Report Fundraiser

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Report fundraiser index | Manage report fundraiser page | `Admin\ReportFundraiserController@index` | Admin |
| 2 | Report fundraiser update | Update report settings | `Admin\ReportFundraiserController@update` | Admin |

---

## Module: Admin – Store Management

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Store dashboard | Store management dashboard | `Admin\StoreManagementController@index` | Admin |
| 2 | Run cron | Trigger cron manually | `Admin\StoreManagementController@runCron` | Admin |
| 3 | Sync status | Get sync status | `Admin\StoreManagementController@getSyncStatus` | Admin |

---

## Module: Admin – Email Logs

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Email logs index | List email logs | `Admin\EmailLogController@index` | Admin |
| 2 | Email logs stats | Email statistics | `Admin\EmailLogController@stats` | Admin |
| 3 | Email log show | View single log | `Admin\EmailLogController@show` | Admin |
| 4 | Email log preview | Preview email content | `Admin\EmailLogController@preview` | Admin |
| 5 | Resend email | Resend failed email | `Admin\EmailLogController@resend` | Admin |
| 6 | Destroy email log | Delete email log | `Admin\EmailLogController@destroy` | Admin |

---

## Module: Admin – Webhook Logs

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Webhook logs index | List webhook logs | `Admin\WebhookLogController@index` | Admin |
| 2 | Statistics | Webhook statistics | `Admin\WebhookLogController@statistics` | Admin |
| 3 | Export | Export webhook logs | `Admin\WebhookLogController@export` | Admin |
| 4 | By gateway | Filter by gateway | `Admin\WebhookLogController@byGateway` | Admin |
| 5 | By status | Filter by status | `Admin\WebhookLogController@byStatus` | Admin |
| 6 | Cleanup | Cleanup old logs | `Admin\WebhookLogController@cleanup` | Admin |
| 7 | Webhook log show | View single log | `Admin\WebhookLogController@show` | Admin |
| 8 | Retry | Retry failed webhook | `Admin\WebhookLogController@retry` | Admin |

---

## Module: Admin – Social Login Settings

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Social login index | Social login config page | `Admin\SocialLoginSettingController@index` | Admin |
| 2 | Social login update | Save social login config | `Admin\SocialLoginSettingController@update` | Admin |
| 3 | Test configuration | Test social login | `Admin\SocialLoginSettingController@testConfiguration` | Admin |

---

## Module: Admin – Firebase OTP

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Firebase OTP index | Firebase OTP settings | `Admin\FirebaseOTPSettingController@index` | Admin |
| 2 | Firebase OTP update | Save Firebase OTP config | `Admin\FirebaseOTPSettingController@update` | Admin |

---

## Module: Admin – Activity Logs

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Activity logs index | List activity logs | `Admin\ActivityLogController@index` | Admin |
| 2 | Activity log show | View log details | `Admin\ActivityLogController@show` | Admin |

---

## Module: Payment Gateways (IPN / Callbacks)

| # | Gateway | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Authorize.net | IPN callback | `Gateway\Authorize\ProcessController@ipn` | System |
| 2 | BTCPay | IPN callback | `Gateway\BTCPay\ProcessController@ipn` | System |
| 3 | Checkout | IPN callback | `Gateway\Checkout\ProcessController@ipn` | System |
| 4 | Coinbase Commerce | IPN callback | `Gateway\CoinbaseCommerce\ProcessController@ipn` | System |
| 5 | Coinpayments | IPN callback | `Gateway\Coinpayments\ProcessController@ipn` | System |
| 6 | Flutterwave | IPN callback | `Gateway\Flutterwave\ProcessController@ipn` | System |
| 7 | MercadoPago | IPN callback | `Gateway\MercadoPago\ProcessController@ipn` | System |
| 8 | NowPayments Checkout | IPN callback | `Gateway\NowPaymentsCheckout\ProcessController@ipn` | System |
| 9 | Payeer | IPN callback | `Gateway\Payeer\ProcessController@ipn` | System |
| 10 | PayPal SDK | IPN callback | `Gateway\PaypalSdk\ProcessController@ipn` | System |
| 11 | Paystack | IPN callback | `Gateway\Paystack\ProcessController@ipn` | System |
| 12 | Perfect Money | IPN callback | `Gateway\PerfectMoney\ProcessController@ipn` | System |
| 13 | Razorpay | IPN callback | `Gateway\Razorpay\ProcessController@ipn` | System |
| 14 | Stripe V3 | IPN callback | `Gateway\StripeV3\ProcessController@ipn` | System |
| 15 | 2Checkout | IPN callback | `Gateway\TwoCheckout\ProcessController@ipn` | System |
| 16 | Stripe JS | IPN callback | `Gateway\StripeJs\ProcessController@ipn` | System |
| 17 | Card Payment | IPN callback | `Gateway\CardPayment\ProcessController@ipn` | System |
| 18 | MWallet | IPN callback | `Gateway\MWallet\ProcessController@ipn` | System |
| 19 | JazzCash Wallet | IPN callback | `Gateway\JazzCashWallet\ProcessController@ipn` | System |
| 20 | JazzCash IPN | JazzCash IPN handler | `Gateway\JazzCash\IpnController@handle` | System |

---

## Module: Mobile / API (Public)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Home API | Home data for mobile | `Api\HomeController@index` | Guest |
| 2 | Category-wise fund | Campaigns by category | `Api\FundController@categoryWiseFund` | Guest |
| 3 | Search fund | Search campaigns | `Api\FundController@searchFund` | Guest |
| 4 | Fund by ID | Get campaign by ID | `Api\FundController@fundById` | Guest |
| 5 | Category list | List categories | `Api\CategoryController@categoryList` | Guest |
| 6 | Charity list | List charities | `Api\CategoryController@charityList` | Guest |
| 7 | FAQ list | List FAQs | `Api\FaqController@faqList` | Guest |
| 8 | Page list | List pages | `Api\PageController@pageList` | Guest |
| 9 | Payment gateway list | List payment gateways | `Api\PaymentController@paymentGatewayList` | Guest |
| 10 | Gateways | Get gateways | `Api\PaymentController@gateways` | Guest |
| 11 | Webview URL | Get payment webview URL | `Api\PaymentController@webviewUrl` | Guest |
| 12 | Register user | User registration | `Api\AuthController@register` | Guest |
| 13 | User login | User login | `Api\AuthController@login` | Guest |
| 14 | Forget password | Request password reset | `Api\AuthController@forgetPassword` | Guest |
| 15 | Social login | Social login | `Api\AuthController@socialLogin` | Guest |
| 16 | Check mobile | Check mobile exists | `Api\AuthController@checkMobile` | Guest |
| 17 | Verify email OTP | Verify email OTP | `Api\AuthController@verifyEmailOTP` | Guest |
| 18 | Resend mobile OTP | Resend mobile OTP | `Api\AuthController@resendMobileOTP` | Guest |
| 19 | Verify mobile OTP | Verify mobile OTP | `Api\AuthController@verifyMobileOTP` | Guest |
| 20 | Send password reset OTP | Send password reset OTP | `Api\AuthController@sendPasswordResetOTP` | Guest |
| 21 | Verify password reset OTP | Verify password reset OTP | `Api\AuthController@verifyPasswordResetOTP` | Guest |
| 22 | Reset password | Reset password | `Api\AuthController@resetPassword` | Guest |
| 23 | MSG OTP | Send OTP (MSG provider) | `Api\OTPController@msgOTP` | Guest |
| 24 | Twilio OTP | Send OTP (Twilio) | `Api\OTPController@twilioOTP` | Guest |
| 25 | SMS type | Get SMS type | `Api\OTPController@smsType` | Guest |

---

## Module: Mobile / API (Protected – Auth Required)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Fund list | List user campaigns | `Api\FundController@fundList` | User (token) |
| 2 | Fund raise | Create campaign | `Api\FundController@fundRaise` | User (token) |
| 3 | Fund update | Add campaign update | `Api\FundUpdateController@fundUpdate` | User (token) |
| 4 | Cancel fund | Cancel campaign | `Api\FundUpdateController@cancelFund` | User (token) |
| 5 | Complete fund | Mark campaign complete | `Api\FundUpdateController@completeFund` | User (token) |
| 6 | Delete fund | Delete campaign | `Api\FundUpdateController@deleteFund` | User (token) |
| 7 | Edit fund | Edit campaign | `Api\FundUpdateController@editFund` | User (token) |
| 8 | Edit profile | Update user profile | `Api\UserController@editProfile` | User (token) |
| 9 | Upload profile image | Upload avatar | `Api\UserController@uploadProfileImage` | User (token) |
| 10 | Update wallet | Update wallet | `Api\UserController@updateWallet` | User (token) |
| 11 | Get balance | Get wallet balance | `Api\UserController@getBalance` | User (token) |
| 12 | Donate now | Make donation | `Api\DonateController@donateNow` | User (token) |
| 13 | My donate fund list | List donations made | `Api\DonateController@myDonateFundList` | User (token) |
| 14 | Request withdraw | Request withdrawal | `Api\WithdrawController@requestWithdraw` | User (token) |
| 15 | Payout list | List payouts | `Api\WithdrawController@payoutList` | User (token) |
| 16 | Wallet report | Wallet report | `Api\WalletController@walletReport` | User (token) |
| 17 | Activity list | User activity | `Api\ActivityController@activityList` | User (token) |
| 18 | Notification | User notifications | `Api\HomeController@notification` | User (token) |
| 19 | Delete account | Delete user account | `Api\AccountController@deleteAccount` | User (token) |

---

## Module: Mobile / API (Admin)

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | Admin login | Admin API login | `Api\Admin\AdminAuthController@login` | Guest |
| 2 | Gemini settings (get) | Get Gemini config | `Api\Admin\GeminiSettingsController@index` | Admin (token) |
| 3 | Gemini settings (put) | Update Gemini config | `Api\Admin\GeminiSettingsController@update` | Admin (token) |
| 4 | Gemini test | Test Gemini | `Api\Admin\GeminiSettingsController@test` | Admin (token) |
| 5 | Gemini reset | Reset Gemini | `Api\Admin\GeminiSettingsController@reset` | Admin (token) |

---

## Module: REST API – Campaigns & Categories

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | GET /api/campaigns | List campaigns (paginated) | `routes/web.php` | Guest |
| 2 | GET /api/campaigns/{slug} | Get single campaign | `routes/web.php` | Guest |
| 3 | GET /api/campaigns/featured | Get featured campaigns | `routes/web.php` | Guest |
| 4 | GET /api/categories | List categories | `routes/web.php` | Guest |
| 5 | GET /api/subcategories/{id} | Get subcategories | `routes/web.php` | Guest |

---

## Module: Utility Routes

| # | Feature | Description | Related Files | Role |
|---|---------|-------------|---------------|------|
| 1 | CSRF token | Refresh CSRF token | `routes/web.php` (csrf.token) | Guest |
| 2 | Maintenance mode | Maintenance page | `WebsiteController@maintenance` | Guest |
| 3 | Test IP detection | Debug IP/country detection | `routes/web.php` | Dev |
| 4 | Test logging | Test logging functionality | `routes/web.php` | Dev |
| 5 | Password reset page | Legacy change.htm | `routes/web.php` | Guest |

---

## Middleware Reference

| Middleware | Purpose |
|------------|---------|
| `auth` | Require authenticated user |
| `guest` | Require unauthenticated user |
| `admin` | Require admin user |
| `admin.guest` | Require unauthenticated admin |
| `admin.permission` | RBAC – check route permissions |
| `authorize.status` | User must have completed authorization (email/mobile) |
| `kyc.status` | User must have completed KYC for withdrawals |
| `register.status` | Registration allowed (can be disabled) |
| `maintenance` | Maintenance mode |
| `beta.gate` | Beta landing page gate |

---

## Quick Summary by Role

- **Guest:** 100+ public website, auth, API public endpoints  
- **User:** 80+ dashboard, campaigns, rewards, donations, withdraw, profile, chat  
- **Admin:** 150+ admin panel features (RBAC applies per route)  
- **System:** 20 payment gateway IPN callbacks  

---

*Use this document as a QA test plan. For each row, verify: the feature loads, validation works, success/error paths, and permission checks where applicable.*
