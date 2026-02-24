# API Fund Contribution Flow (Simple)

Start: API request hits the backend.
End: wallet balance is updated (if refund/wallet use) and/or campaign total changes because a deposit is inserted.

1) **API request entry**
   - File path: `app/Http/Controllers/Api/DonateController.php`
   - Class name: `DonateController`
   - Method name: `donateNow(Request $request)`
   - What this method does: receives the API request and starts the donation flow.
   - Why this step exists: this is the single entry point for API fund contributions.
   - Balance changes: none.

2) **Validate required fields + authenticate user**
   - File path: `app/Http/Controllers/Api/DonateController.php`
   - Class name: `DonateController`
   - Method name: `donateNow(Request $request)`
   - What this method does: checks `fund_id`, `amt`, `tip`, `payment_method_id`, and verifies the user token.
   - Why this step exists: prevents unauthenticated or incomplete requests.
   - Balance changes: none.

3) **Normalize currency and convert to USD**
   - File path: `app/Http/Controllers/Api/DonateController.php`
   - Class name: `DonateController`
   - Method name: `donateNow(Request $request)`
   - What this method does: normalizes the currency and converts the amount to USD using `CurrencyService`.
   - Why this step exists: API donations must use a consistent base currency.
   - Balance changes: none.

4) **Check remaining campaign amount**
   - File path: `app/Http/Controllers/Api/DonateController.php`
   - Class name: `DonateController`
   - Method name: `donateNow(Request $request)`
   - What this method does: reads total deposits and campaign goal to calculate remaining amount.
   - Why this step exists: prevents over-funding the campaign.
   - Balance changes: none.

5) **If over-funded: refund to wallet**
   - File path: `app/Http/Controllers/Api/DonateController.php`
   - Class name: `DonateController`
   - Method name: `donateNow(Request $request)`
   - What this method does: when the remaining amount is too small, it adds the amount to the user’s wallet and writes a wallet report entry.
   - Why this step exists: API refunds go directly to the in-app wallet.
   - Balance changes: user `users.balance` increases by the refunded amount.

6) **If wallet is used: deduct wallet balance**
   - File path: `app/Http/Controllers/Api/DonateController.php`
   - Class name: `DonateController`
   - Method name: `donateNow(Request $request)`
   - What this method does: subtracts `wall_amt` from the user’s wallet and logs a wallet report entry.
   - Why this step exists: tracks wallet usage for the donation.
   - Balance changes: user `users.balance` decreases by `wall_amt`.

7) **Insert deposit record (successful API donation)**
   - File path: `app/Http/Controllers/Api/DonateController.php`
   - Class name: `DonateController`
   - Method name: `donateNow(Request $request)`
   - What this method does: inserts a row in `deposits` with `status = 1` (success).
   - Why this step exists: this is the authoritative record of a completed API donation.
   - Balance changes: campaign total increases indirectly because campaign totals are computed from `deposits` (no direct `raised_amount` update here).

End note:
- There is no direct `Campaign->raised_amount` update in this API flow.
- The campaign balance changes because a successful deposit is inserted.
