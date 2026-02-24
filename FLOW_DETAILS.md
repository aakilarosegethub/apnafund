# Detailed Flow Explanations (Simple English)

This document explains the main flows in the codebase in depth. Each flow lists
the exact file paths and function names, why each step exists, and what must NOT
be changed.

## Fund Contribution Flow

### A) Web contribution (standard gateway)

**Entry routes**
- `routes/user.php`
  - `Route::prefix('deposit')...` maps to `App\Http\Controllers\Gateway\PaymentController`.
  - Entry point: `depositInserts($slug)` → `user.deposit.insert`.
  - Confirmation: `depositConfirm()` → `user.deposit.confirm`.
  - Success: `success()` → `user.deposit.success`.
- `routes/ipn.php`
  - Each gateway IPN points to a gateway-specific `ProcessController@ipn` method.

**Core controller**
- `app/Http/Controllers/Gateway/PaymentController.php`
  - `depositInserts($slug)`
  - `depositConfirm()`
  - `campaignDataUpdate($deposit, $isManual = null)`
  - `manualDepositConfirm()`
  - `manualDepositUpdate()`
  - `success()`

**Step-by-step (why each step exists)**
1) **Input validation and campaign lookup**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `depositInserts($slug)`
   - Validates amount, donor identity, gateway, and currency.
   - Why: stops invalid payments early, ensures correct gateway/currency pair.
   - It loads `Campaign::where('slug', $slug)->approve()->firstOrFail()`.
   - Why: only approved campaigns can accept contributions.
   - It checks `Campaign::isExpired()` to stop donations to expired campaigns.

2) **Gateway selection by country**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `depositInserts($slug)`
   - `GatewayCurrency::whereHas('method', ...)` chooses a valid gateway for the
     donor’s country and requested currency.
   - Why: prevents using a gateway that is disabled or invalid for that country.

3) **Currency normalization and conversion**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `depositInserts($slug)`
   - Uses `App\Services\CurrencyService` to normalize code, create currency, and
     convert the original amount to USD.
   - Why: the system stores USD equivalents and uses rates consistently.

4) **Reward validation (optional)**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `depositInserts($slug)`
   - Checks reward availability and minimum amount.
   - Why: ensures reward inventory is not oversold and donor meets rules.

5) **Charge and payable calculation**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `depositInserts($slug)`
   - `charge`, `payable`, `final_amount` computed from gateway config.
   - Why: charge and gateway rate must be consistent with gateway configuration.

6) **Deposit record creation**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `depositInserts($slug)`
   - Creates a `Deposit` model with campaign, donor, receiver, currency, rates,
     and transaction id `getTrx()`.
   - Why: this is the authoritative payment record and ties the flow together.
   - `session()->put('Track', $deposit->trx)` stores tracking key for later steps.

7) **Gateway processing**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `depositConfirm()`
   - Loads `Deposit::with('gateway')->where('trx', $track)->initiate()`.
   - For automated gateways, it calls the gateway’s `ProcessController::process`.
   - Why: gateway-specific workflows (redirects, tokens, session IDs) live in
     their own controller.

8) **Gateway IPN callback**
   - Files:
     - `routes/ipn.php` (routes)
     - `app/Http/Controllers/Gateway/*/ProcessController.php` (IPN handlers)
   - Each IPN handler loads the `Deposit` and then calls:
     - `PaymentController::campaignDataUpdate($deposit)`
   - Why: the IPN is the trusted signal that a payment succeeded.

9) **Finalized data updates**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `campaignDataUpdate($deposit, $isManual = null)`
   - Sets `Deposit->status = ManageStatus::PAYMENT_SUCCESS`.
   - Updates `Campaign->raised_amount`.
   - Updates reward `claimed_count` when a reward is used.
   - Adds funds to campaign owner’s `User->balance`.
   - Inserts **two** `Transaction` rows:
     - donor: `trx_type = '-'`, `remark = 'donation_given'`
     - receiver: `trx_type = '+'`, `remark = 'donation_received'`
   - Sends admin notification and donor notification.
   - Why: this is the single source of truth for finalizing money movement.

10) **User success view**
    - File: `app/Http/Controllers/Gateway/PaymentController.php`
    - Function: `success()`
    - Uses the `Track` session key to show success after payment completes.
    - Why: ensures only successful payments display the success message.

**What must NOT be changed (web flow)**
- Do not change `ManageStatus::PAYMENT_*` values in `app/Constants/ManageStatus.php`.
- Do not remove `getTrx()` usage or the `session()->put('Track')` tracking key.
- Do not skip `campaign->isExpired()` or `campaign->approve()` checks; they
  block invalid donations.
- Do not change the `Deposit` fields `amount`, `original_amount`,
  `original_currency`, `usd_amount`, and `exchange_rate`; downstream analytics
  and payout logic depend on them.
- Do not remove or rename `Transaction->remark` values `donation_given` and
  `donation_received`; admin reward tracking uses these.
- Do not remove the reward availability and minimum amount checks; they keep
  reward inventory consistent.

### B) Manual Fund Contribution (manual payment method)

Start: user selects a manual payment method.
End: admin approves or rejects the contribution.

1) **Show manual confirmation details**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `manualDepositConfirm()`
   - What this method does: loads the initiated `Deposit` from session track,
     verifies it is a manual method using `method_code > 999`, and shows the
     manual payment instructions/form.
   - Why this step exists: manual methods require extra instructions and a
     custom form before submitting proof.
   - Status changes: no change (deposit remains `ManageStatus::PAYMENT_INITIATE`).

2) **Submit manual payment form**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `manualDepositUpdate()`
   - What this method does: validates the manual form fields and saves the
     user’s proof/details.
   - Why this step exists: admin needs structured evidence to verify the payment.
   - Status changes: sets `Deposit->status = ManageStatus::PAYMENT_PENDING`.

3) **Admin approves or rejects**
   - File path: `app/Http/Controllers/Admin/DepositController.php`
   - Class name: `DepositController`
   - Method name: `approve($id)` / `reject($id)`
   - What this method does: `approve($id)` finalizes the donation and calls
     `PaymentController::campaignDataUpdate($deposit, true)`; `reject($id)` cancels
     the deposit and notifies the donor.
   - Why this step exists: manual payments must be verified by an admin.
   - Status changes:
     - Approve: `Deposit->status = ManageStatus::PAYMENT_SUCCESS`.
     - Reject: `Deposit->status = ManageStatus::PAYMENT_CANCEL`.

**What must NOT be changed (manual flow)**
- Do not change the `method_code > 999` manual gateway check.
- Do not finalize manual deposits outside `Admin/DepositController::approve`.
- Do not skip `FormProcessor` validation; form schema is admin-configured.

### C) Mobile/API contribution

**Entry point**
- File: `app/Http/Controllers/Api/DonateController.php`
  - Function: `donateNow(Request $request)`

**Step-by-step**
1) **Request validation and user authentication**
   - Ensures `fund_id`, `amt`, `tip`, and `payment_method_id` exist.
   - Uses `getUserId($request)` to ensure token is valid.
   - Why: API flow must be protected and explicit.

2) **Currency conversion**
   - Uses `CurrencyService` to normalize and convert `amt` to USD.
   - Why: API deposits must be consistent with web deposits.

3) **Campaign remaining amount check**
   - Queries `deposits` and `campaigns` to compute remaining amount.
   - If over-funding, the donation is refused or refunded to wallet.
   - Why: prevents campaign overfunding and keeps totals aligned.

4) **Wallet usage**
   - If `wall_amt` is used, deducts from `users.balance`.
   - Records a wallet report entry.
   - Why: tracks internal wallet usage for API flows.

5) **Deposit insert**
   - Inserts directly into `deposits` with status `1` (success).
   - Why: API flow assumes the payment is already completed.

**What must NOT be changed (API flow)**
- Do not remove the campaign remaining amount check; it prevents overfunding.
- Do not change `status = 1` behavior in API inserts without revisiting the
  payment trust model.
- Do not remove `CurrencyService` conversion; it keeps totals consistent with
  web contribution flows.

## Withdrawal & Payout Flow

### A) User withdrawal request

**Entry routes**
- `routes/user.php`
  - `Route::controller('WithdrawController')->prefix('withdraw')`.

**Core controller**
- File: `app/Http/Controllers/User/WithdrawController.php`
  - Functions: `methods()`, `store()`, `preview()`, `submit()`

**Step-by-step**
1) **Method selection**
   - `methods()` loads `WithdrawMethod::active()`.
   - Why: only active withdrawal methods can be used.

2) **Request creation**
   - `store()` validates `method_id` and `amount`.
   - Checks min/max limits and user balance.
   - Calculates charge and final amount.
   - Saves `Withdrawal` with `trx = getTrx()` and stores `session()->put('wtrx')`.
   - Why: creates a pending withdrawal record tied to a unique transaction id.

3) **Preview**
   - `preview()` loads `Withdrawal::initiate()` by session trx.
   - Why: confirms details before final submission.

4) **Final submission**
   - `submit()` validates method form data (bank account, etc.).
   - Optionally checks 2FA (`verifyG2fa()`).
   - Sets `Withdrawal->status = ManageStatus::PAYMENT_PENDING`.
   - Deducts user balance (`$user->balance -= $withdraw->amount`).
   - Creates a `Transaction` with `trx_type = '-'` and `remark = 'withdraw'`.
   - Notifies admin and user.
   - Why: money is reserved immediately; admin later approves or cancels.

**What must NOT be changed (user withdrawal)**
- Do not change the sequence: validate → save → set pending → deduct balance →
  create transaction. That order preserves correct ledger behavior.
- Do not change the `trx` storage or the `session()->put('wtrx')` dependency.
- Do not remove 2FA verification for users with `ts` enabled.

### B) Admin approve/cancel withdrawal

**Core controller**
- File: `app/Http/Controllers/Admin/WithdrawController.php`
  - Functions: `approve()`, `cancel()`

**Step-by-step**
1) **Approve**
   - `approve()` loads `Withdrawal::pending()`.
   - Sets status to `PAYMENT_SUCCESS`.
   - Sends `WITHDRAW_APPROVE` notification.
   - Why: approval confirms payout to the user; balance was already reserved.

2) **Cancel**
   - `cancel()` loads pending withdrawal.
   - Sets status `PAYMENT_CANCEL`.
   - Adds amount back to user balance.
   - Creates a **refund** `Transaction` with `trx_type = '+'` and
     `remark = 'withdraw_reject'`.
   - Sends `WITHDRAW_REJECT` notification.
   - Why: cancellation restores balance and creates audit trail.

**What must NOT be changed (admin withdrawal)**
- Do not refund balance in `approve()`; funds were already reserved in submit.
- Do not remove the refund transaction creation in `cancel()`; it is essential
  for audit and user history.

### C) Creator campaign payout (admin-managed)

**Core controllers and services**
- `app/Http/Controllers/Admin/CreatorPayoutController.php`
  - `index()`, `show()`, `partialPayout()`, `fullPayout()`,
    `markFulfillmentComplete()`
- `app/Services/CreatorCampaignPayoutService.php`
  - `ensurePayoutRecord()`, `isCampaignSuccessful()`, `calculateAmounts()`
- Models:
  - `app/Models/CreatorCampaignPayout.php`
  - `app/Models/CreatorCampaignPayoutAction.php`

**Step-by-step**
1) **Detect eligible campaigns**
   - `CreatorPayoutController::index()` builds a list of approved campaigns
     that are either past end date or have deposits >= goal amount.
   - Why: payouts only for successful, approved campaigns.

2) **Create payout records**
   - `CreatorCampaignPayoutService::ensurePayoutRecord()` creates a payout
     record if not already created.
   - Why: a persistent snapshot of fees and amounts is needed for audit.

3) **Fee calculation**
   - `calculateAmounts()` computes platform fee, marketing fee, chargeback
     withholding, and fulfillment withholding.
   - Why: payout net amount is derived from these settings and must be stable.

4) **Partial/Full payout**
   - `partialPayout()` and `fullPayout()` update `total_paid_amount` and
     `payout_status`, and log actions.
   - Why: admin needs to document incremental releases.

5) **Fulfillment release**
   - `markFulfillmentComplete()` releases withheld fulfillment amount.
   - Why: withheld funds are released only after fulfillment confirmation.

**What must NOT be changed (creator payout)**
- Do not change `isCampaignSuccessful()` rules without reviewing payout policy.
- Do not change fee calculations without a full accounting review.
- Do not remove `CreatorCampaignPayoutAction` logging; it is the audit trail.

## Transaction & Balance Logic

### Core data structures
- `app/Models/Transaction.php`
  - Links to `User`, `Deposit`, `Reward`.
- `app/Models/Deposit.php`
  - Uses `ManageStatus::PAYMENT_*` for state tracking.
- `app/Models/Withdrawal.php`
  - Uses `ManageStatus::PAYMENT_*` for state tracking.
- `app/Constants/ManageStatus.php`
  - Contains numeric constants used across all flows.

### Where balances change
1) **Contribution success (receiver balance increase)**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `campaignDataUpdate()`
   - Adds `deposit->amount` to campaign owner balance.
   - Why: the receiver’s wallet reflects collected donations.

2) **Contribution transaction records**
   - File: `app/Http/Controllers/Gateway/PaymentController.php`
   - Function: `campaignDataUpdate()`
   - Creates **donor** and **receiver** transactions with same `trx`.
   - Why: donor/receiver need mirrored ledger entries for a single payment.

3) **Withdrawal submit (balance decrease)**
   - File: `app/Http/Controllers/User/WithdrawController.php`
   - Function: `submit()`
   - Deducts `Withdrawal->amount` from user balance.
   - Creates `Transaction` with `remark = 'withdraw'`.
   - Why: reserve funds as soon as the user submits a withdrawal.

4) **Withdrawal cancel (balance refund)**
   - File: `app/Http/Controllers/Admin/WithdrawController.php`
   - Function: `cancel()`
   - Refunds balance and creates `Transaction` with `remark = 'withdraw_reject'`.
   - Why: reversal must be explicit and visible in ledger.

5) **Admin manual balance adjustment**
   - File: `app/Http/Controllers/Admin/UserController.php`
   - Function: `balanceUpdate($id)`
   - Adds or subtracts balance and creates a `Transaction`.
   - Why: admin operations must be recorded and visible to the user.

6) **API wallet usage**
   - File: `app/Http/Controllers/Api/DonateController.php`
   - Function: `donateNow()`
   - Deducts `users.balance` when `wall_amt` is used.
   - Why: API flow supports wallet usage for donations.

### What must NOT be changed (transaction/balance)
- Do not change `trx_type` semantics (`+` for credit, `-` for debit).
- Do not rename transaction remarks without updating admin filters and UI.
- Do not bypass `post_balance` updates; it is used for audit consistency.
- Do not change `ManageStatus::PAYMENT_*` constants; they are relied upon by
  model scopes and filtering.
- Do not change `Campaign::raisedAmount()` fallback logic; it ensures the UI
  always shows the correct total even if `raised_amount` is not set.

## Campaign Approval Lifecycle

### User creation (web)
- File: `app/Http/Controllers/User/CampaignController.php`
  - Function: `store()`
  - Sets `Campaign->status = ManageStatus::CAMPAIGN_PENDING`.
  - Sends admin notification and email to admins.
  - Why: campaigns require admin review before being public.

### User update (web)
- File: `app/Http/Controllers/User/CampaignController.php`
  - Function: `update($id)`
  - Validates fields and updates campaign data.
  - Does **not** auto-approve; status remains unchanged unless admin updates.
  - Why: updates should not bypass the approval lifecycle.

### Admin approval / rejection
- File: `app/Http/Controllers/Admin/CampaignController.php`
  - Function: `updateStatus($id, $type)`
  - Sets `status` to `CAMPAIGN_APPROVED` or `CAMPAIGN_REJECTED`.
  - Sends notification to the campaign owner (`CAMPAIGN_APPROVE` or
    `CAMPAIGN_REJECT`).
  - Why: this is the official approval step.

### Approval state accessors and scopes
- File: `app/Models/Campaign.php`
  - Scopes: `scopePending()`, `scopeApprove()`, `scopeReject()`.
  - Badge accessors: `approvalStatusBadge()`, `campaignStatusBadge()`.
  - Why: centralized status logic for UI and queries.

### API campaign lifecycle mapping
- File: `app/Http/Controllers/Api/FundController.php`
  - Function: `fundRaise()`
  - Maps incoming API status strings to numeric statuses:
    - `Pending` → `2`
    - `Approved` → `1`
    - `Cancelled/Rejected` → `0`
  - Why: API integrates legacy status values into the new campaigns table.

### What must NOT be changed (approval lifecycle)
- Do not change the status numeric mapping in `ManageStatus` or API mapping.
- Do not bypass admin approval in `Admin/CampaignController::updateStatus()`.
- Do not remove `Campaign::scopeApprove()` use in public listings or donation
  entry points; it is the core guard for public campaigns.
- Do not change `Campaign::isExpired()` logic without updating campaign
  success and payout checks.

## Fund Contribution Flow (Simple, End-to-End)

Start: user clicks the "Contribute" button on a campaign.
End: campaign balance (`Campaign->raised_amount`) is updated.

1) **Route to deposit insert**
   - File path: `routes/user.php`
   - Class name: (route definition)
   - Method name: `user.deposit.insert` → `PaymentController::depositInserts($slug)`
   - What this method does: maps the "Contribute" action to the deposit insert handler.
   - Why this step exists: it is the entry point that wires the button click to backend logic.

2) **Validate input + load campaign**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `depositInserts($slug)`
   - What this method does: validates amount, donor info, gateway, and currency; loads the
     approved campaign by slug and blocks expired campaigns.
   - Why this step exists: it stops invalid or expired donations before creating any records.

3) **Pick a valid gateway**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `depositInserts($slug)`
   - What this method does: selects a valid `GatewayCurrency` for the donor’s country
     and requested currency.
   - Why this step exists: only enabled and compatible gateways can be used.

4) **Normalize currency + convert to USD**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `depositInserts($slug)`
   - What this method does: uses `CurrencyService` to normalize the currency and
     convert the amount to USD.
   - Why this step exists: the system stores USD equivalents for consistent reporting.

5) **Reward checks (if selected)**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `depositInserts($slug)`
   - What this method does: checks reward availability and minimum amount rules.
   - Why this step exists: prevents overselling rewards and enforces minimums.

6) **Calculate charges and payable**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `depositInserts($slug)`
   - What this method does: calculates gateway charges, payable amount, and final amount.
   - Why this step exists: donor pays the correct amount based on gateway settings.

7) **Create the deposit record**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `depositInserts($slug)`
   - What this method does: creates a `Deposit` row with campaign, donor, receiver,
     currency, rates, and a unique `trx`; stores `Track` in session.
   - Why this step exists: creates the authoritative payment record and tracking key.

8) **Start gateway processing**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `depositConfirm()`
   - What this method does: loads the tracked `Deposit` and calls the gateway
     `ProcessController::process()` for redirects or tokens.
   - Why this step exists: gateways require their own processing flow.

9) **IPN callback from gateway**
   - File path: `routes/ipn.php`
   - Class name: (route definition)
   - Method name: gateway `ProcessController::ipn`
   - What this method does: receives the gateway’s success callback and loads
     the related `Deposit`.
   - Why this step exists: the gateway callback is the trusted signal of payment success.

10) **Finalize donation and update campaign balance**
   - File path: `app/Http/Controllers/Gateway/PaymentController.php`
   - Class name: `PaymentController`
   - Method name: `campaignDataUpdate($deposit, $isManual = null)`
   - What this method does: marks the deposit as successful, updates the campaign’s
     `raised_amount`, updates reward counts, updates campaign owner balance, and creates
     donor/receiver transactions.
   - Why this step exists: it is the single place that finalizes money movement and
     updates the campaign balance.
