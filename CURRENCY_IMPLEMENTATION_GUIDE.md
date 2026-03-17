# Currency Exchange - Complete Implementation Guide

## Overview

- **Platform Currency**: Admin sets at `/admin/setting/basic` (site_cur). ALL amounts in DB (campaigns, deposits) stored in this currency.
- **Creator Input**: Creator enters goal in THEIR currency (PKR/INR based on country). System converts → Platform before save.
- **Contributor Payment**: Contributor pays in their currency. Gateway + system converts → Platform before save in deposits.
- **Frontend Display**: DB amounts (platform) → convert to visitor's currency (IP) → show with symbol.

---

## Already Implemented ✓

1. **getPlatformCurrency()** – helpers.php – returns raw DB site_cur  
2. **formatPlatformForDisplay($amount)** – helpers.php – converts platform → visitor currency, formats  
3. **CurrencyService::convertToPlatform($amount, $fromCurrency)** – for saving  
4. **CurrencyService::convertFromPlatform($amount, $toCurrency)** – for display  

---

## Manual Changes Required

### 1. Campaign New Form – Pass Creator Currency

**File:** `app/Http/Controllers/User/CampaignController.php`

**In `new()` method**, add before return:

```php
$currencyService = app(\App\Services\CurrencyService::class);
$creatorCurrency = $currencyService->detectCurrencyCode(auth()->user());
$creatorSymbol = \App\Services\CurrencyService::getSymbolForCode($creatorCurrency);

return view($this->activeTheme . 'user.campaign.new', compact('pageTitle', 'categories', 'creatorCurrency', 'creatorSymbol'));
```

### 2. Campaign Store – Convert Goal to Platform

**File:** `app/Http/Controllers/User/CampaignController.php`

**In `store()` method**, REPLACE the line:

```php
$goalAmount = request('goal_amount', 1000);
```

with:

```php
$goalAmountRaw = (float) request('goal_amount', 1000);
$currencyService = app(\App\Services\CurrencyService::class);
$inputCurrency = request('input_currency') ?? $currencyService->detectCurrencyCode(auth()->user());
$goalAmount = $currencyService->convertToPlatform($goalAmountRaw, $inputCurrency);
```

### 3. Campaign New View – Label + Hidden Input

**File:** `resources/views/themes/green/user/campaign/new.blade.php`

Replace:
```html
<label for="targetAmount" class="form-label">Target Amount ({{ $setting->cur_sym }})  </label>
<input type="number" name="goal_amount" class="form-control" id="targetAmount" placeholder="5000" min="1" step="0.01" required>
```

with:
```html
<label for="targetAmount" class="form-label">@lang('Target Amount') - @lang('Enter amount in') {{ $creatorCurrency ?? 'USD' }}</label>
<input type="hidden" name="input_currency" value="{{ $creatorCurrency ?? 'USD' }}">
<input type="number" name="goal_amount" class="form-control" id="targetAmount" placeholder="5000" min="1" step="0.01" required>
<small class="text-muted">@lang('Amount will be stored in platform currency') ({{ getPlatformCurrency() }})</small>
```

### 4. Admin Basic Setting – Platform Currency

- Ensure **Platform Currency** (site_cur) is set – e.g. `USD`, `PKR`, `INR`, `BDT`
- Admin sets this once. All DB amounts use this.

### 5. Frontend – Use formatPlatformForDisplay

Replace `$setting->cur_sym . showAmount($amount)` with `formatPlatformForDisplay($amount)` wherever campaign goal, raised, donation amounts are shown on public pages.

---

## Donation Flow (Contributor)

Payment gateways (Stripe, PayPal, etc.) typically charge in gateway currency. Your deposit flow should:

1. Get contributor's desired amount in their currency (from session/IP)
2. Convert to platform currency before creating deposit record
3. Gateway may charge in their currency – store `amount` in platform, optionally `original_amount` + `original_currency`

Check: `app/Http/Controllers/Gateway/PaymentController.php` and donation controllers for where deposit is created.

---

## Summary

| Step | Action |
|------|--------|
| Admin | Set Platform Currency (USD/PKR/INR) at Basic Settings |
| Creator | Sees "Enter amount in PKR" – enters 50000 – system converts to platform, saves |
| Contributor | Pays in local currency – gateway + system converts to platform for deposit |
| Visitor | Sees amounts in their currency (IP) via formatPlatformForDisplay |
