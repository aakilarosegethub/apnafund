# PayPal API Keys – Request Document

**Project:** Apna Crowdfunding  
**Date:** March 14, 2026

---

## Purpose

PayPal payment integration is failing due to invalid credentials. We need the correct **Client ID** and **Client Secret** from the PayPal Business Account holder.

---

## What We Need

| Key | Description |
|-----|-------------|
| **Client ID** | From PayPal Developer Dashboard |
| **Client Secret** | From the same app |

---

## How to Get Them

1. Go to **[developer.paypal.com/dashboard](https://developer.paypal.com/dashboard/)**
2. Log in with the **PayPal Business Account**
3. Open **Apps & Credentials**
4. Use **Sandbox** tab for testing, **Live** tab for real payments
5. Create an app (or use existing) → Copy **Client ID** and **Secret**

---

## What to Send Us

- **Client ID**  
- **Client Secret**  
- **Environment:** Sandbox or Live

Share via secure channel (not plain email).

---

---

## SDK Fix Applied (March 14, 2026)

**Problem:** API in `paypal.php` works (Sandbox), but system SDK was failing with "Client Authentication failed".

**Root cause:**
1. **Environment mismatch:** SDK always used Production API (`api.paypal.com`). Sandbox credentials only work with Sandbox API (`api.sandbox.paypal.com`).
2. **Credentials:** SDK reads from Admin Panel (DB), not from `.env`. Old/invalid credentials were stored.

**Fix in code:** ProcessController now uses `SandboxEnvironment` when **Sandbox Mode** is enabled in the gateway settings.

**What you need to do:**
1. Go to **Admin Panel** → **Payment Gateway** → **PayPal** (PaypalSdk)
2. Enter **Client ID** and **Client Secret** (same as in `paypal.php`)
3. Enable **Sandbox Mode** = Yes (for testing)
4. Save

**Note:** If your `paypal.php` secret has a prefix like `"key is  "`, remove it – PayPal expects only the secret string.

---

*Apna Crowdfunding – Dev Team*
