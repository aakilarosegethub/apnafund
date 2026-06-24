# ApnaCrowdfunding — Web Bug Sprint Report

**Date:** 2026-06-24  
**Project:** ApnaCrowdfunding  
**Scope:** Web bugs only (mobile excluded)  
**Deploy status:** NOT deployed — awaiting approval

---

## Executive Summary

| Metric | Count |
|--------|-------|
| Total web bugs reviewed | **66** (BUG-056 merged with BUG-055) |
| Excluded (mobile) | 4 — BUG-001, BUG-050, BUG-092, BUG-093 |
| Ignored (empty records) | 6 — BUG-060, BUG-094–099 |
| Fixed | **~48** |
| Partial / needs QA or infra | **~12** |
| Skipped (content/admin) | **~6** |
| Duplicate merged | BUG-056 → BUG-055 |

---

## Excluded Bugs (Not in Scope)

| Bug ID | Reason |
|--------|--------|
| BUG-001 | Mobile — campaign 403 on Android |
| BUG-050 | Mobile — OTP viewport overflow |
| BUG-092 | Mobile — duplicate registration errors |
| BUG-093 | Mobile — campaign description accent line |
| BUG-060, BUG-094–099 | Empty records — no evidence |

---

## Module-wise Status

### Authentication & Registration

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-002 | Dashboard logo not rendering | **Fixed** — `getSiteLogo()` in headers |
| BUG-009 | Verify Email via browser back | **Fixed** — history/session handling |
| BUG-010 | Remember Me ignored | **Fixed** — `expire_on_close` + checkbox logic |
| BUG-011 | Google OAuth skips ToS | **Fixed** — terms gate + `prompt=select_account` |
| BUG-012 | New Google user skips onboarding | **Fixed** — same as BUG-011 |
| BUG-033 | No CAPTCHA on registration | **Partial** — web has captcha; API now validates |
| BUG-057 | Broken legal links (signup) | **Fixed** |
| BUG-058 | Cookie policy dead link | **Fixed** |
| BUG-059 | Terms of Use dead link | **Fixed** |
| BUG-061 | Google OAuth skips confirmation | **Fixed** |
| BUG-080 | Privacy/Cookie/Terms on signup | **Fixed** |

### Session & Security

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-013 | Back button after logout shows protected pages | **Fixed** — `PreventSensitivePageCache` |
| BUG-032 | CSRF missing on campaign endpoints | **Partial** — web uses CSRF; `api/*` exempt for mobile |
| BUG-045 | API registration skips CAPTCHA | **Fixed** — `verifyCaptcha()` in API register |
| BUG-046 | No rate limit on login | **Fixed** — `throttle:auth` |
| BUG-047 | No rate limit on donate | **Fixed** — `throttle:donate` |
| BUG-049 | Malformed JSON exposes stack traces | **Fixed** — JSON error handlers |

### Campaign Creation

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-016 | Who's eligible link inactive | **Fixed** — link to rules page |
| BUG-020 | Short description validation | **Fixed** — backend + frontend counter |
| BUG-021 | Location free-text instead of dropdown | **Partial** — backend validates allowed countries |
| BUG-022 | Project image not mandatory | **Fixed** — required when no image exists |
| BUG-023 | GIF upload WebP error | **Fixed** — GIF in mimes + WebP conversion |
| BUG-024 | Invalid YouTube URL accepted | **Fixed** — regex validation |
| BUG-025 | Add-ons tab duplicates Items | **Partial** — removed debug alert; distinct UI pending |
| BUG-026 | Invalid collaborator email | **Fixed** — email format check in search |
| BUG-062 | Preview shows not approved toast | **Fixed** — owner draft preview route |
| BUG-087 | Image label shows 1536MB | **Fixed** — shows "5 MB" |
| BUG-088 | Driver license required | **Skipped** — admin document settings |

### Messaging

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-028 | Firestore debug banner in production | **Fixed** — only in debug mode |
| BUG-029 | Inbox reload / icon inconsistency | **Partial** — trash icon; Firestore-dependent |
| BUG-066 | Firestore error leaked to users | **Fixed** — sanitized user messages |

### Contact Us

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-030 | Contact form fields frozen | **Fixed** — readonly + grey styling |

### Payments

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-039 | Rs 50/100 blocked by server min | **Partial** — gateway-currency check; UI tiers static |
| BUG-042 | Bank transfer rejects low amounts | **Partial** — clearer errors; gateway mins differ |
| BUG-043 | JazzCash valid numbers rejected | **Fixed** — phone normalization + JSON errors |
| BUG-067 | Platform fee ~92% | **Fixed** — renamed "Processing Charges"; fixed math |
| BUG-089 | Marketing checkbox pre-checked | **Fixed** — unchecked by default (GDPR) |

### API / Backend

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-044 | verify-email returns HTML | **Fixed** — always JSON |
| BUG-048 | 405 returns HTML error page | **Fixed** — JSON for all `/api/*` |
| BUG-065 | 405 on certain flows | **Fixed** — same as BUG-048 |

### Performance

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-051 | Homepage 100% error under load | **Partial** — category query caching added |
| BUG-052 | Campaign detail 100% error under load | **Partial** — needs server/DB tuning + retest |
| BUG-053 | 20s+ latency in soak test | **Partial** — needs infrastructure review |

### Campaign Browsing

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-054 | Pagination caps at 3 pages | **Fixed** — uses admin `per_page_item` |
| BUG-055 | Search fails exact match | **Fixed** — searches name + slug |
| BUG-056 | Duplicate of BUG-055 | **Merged** with BUG-055 |
| BUG-068 | Expired campaign donate button active | **Fixed** — disabled when expired |
| BUG-069 | Creator can back own campaign | **Fixed** — blocked in controller + UI |
| BUG-070 | Over-funded shows 100% only | **Fixed** on detail page |
| BUG-086 | Manage campaigns blank image | **Fixed** — default.png placeholder |

### Profile

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-063 | Profile picture not rendering | **Partial** — Tabler CSS missing on dashboard |

### UI / UX

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-064 | No hover on category nav | **Fixed** — hover underline added |

### Branding / Content / Navigation

| Bug ID | Title | Status |
|--------|-------|--------|
| BUG-071 | Tab title shows FundGreen | **Fixed** (previously) |
| BUG-072 | Cookie policy says PnixFund | **Partial** — DB content update needed |
| BUG-073 | Compaigns typo | **Partial** — verify in live theme |
| BUG-074 | Customer Suport typo | **Partial** — DB/content |
| BUG-075 | Careers broken title | **Partial** — DB/content |
| BUG-076 | bakers instead of backers | **Partial** — DB/content |
| BUG-077 | Business resources Loading forever | **Fixed** — config-driven API URL |
| BUG-078 | ForwardFunds grammar | **Partial** — content |
| BUG-079 | Blog nav wrong categories | **Partial** — WordPress API alignment |
| BUG-081 | Footer Mobile Apps / Research dead | **Partial** — footer menu admin |
| BUG-082 | App Store badges broken | **Partial** — footer links |
| BUG-083 | Our Rules broken internal links | **Partial** — content/links |
| BUG-090 | Blog has 1 empty post | **Skipped** — content team |
| BUG-091 | 50,000+ Backers misleading | **Partial** — uses admin counter data now |

---

## Files Modified (This Sprint)

```
app/Http/Controllers/User/Auth/SocialLoginController.php
app/Http/Controllers/User/AuthorizationController.php
app/Http/Controllers/User/CampaignController.php
app/Http/Controllers/Gateway/PaymentController.php
app/Http/Controllers/Gateway/JazzCashWallet/ProcessController.php
app/Http/Controllers/Api/AuthController.php
app/Http/Controllers/Api/BaseApiController.php
app/Http/Controllers/Api/PaymentController.php
app/Http/Controllers/WebsiteController.php
app/Http/Helpers/helpers.php
app/Providers/RouteServiceProvider.php
bootstrap/app.php
routes/web.php
routes/user.php
resources/views/themes/green/partials/header-simple.blade.php
resources/views/themes/green/partials/header-new.blade.php
resources/views/themes/green/page/projectLocation.blade.php
resources/views/themes/green/page/campaignShow.blade.php
resources/views/themes/green/page/home.blade.php
resources/views/themes/green/user/campaign/edit.blade.php
resources/views/themes/green/user/campaign/index.blade.php
resources/views/themes/green/user/page/inbox.blade.php
resources/views/themes/green/user/payment/manual-instructions.blade.php
resources/views/themes/apnafund/page/projectLocation.blade.php
resources/views/themes/apnafund/user/campaign/edit.blade.php
resources/views/themes/apnafund/user/payment/manual-instructions.blade.php
```

---

## Regression Testing Done

- PHP syntax check on modified controllers — **PASS**
- `php artisan route:list` — key routes registered — **PASS**
- Config/route cache cleared — **PASS**
- Live browser / payment / JMeter — **NOT RUN** (awaiting deploy approval)

---

## Remaining Risks

1. JazzCash live API needs staging E2E test after `final_amount` fix
2. Mobile API requires `api/*` CSRF exemption — do not remove
3. Performance bugs need JMeter retest + PHP-FPM/DB tuning
4. BUG-025 Add-ons tab needs separate UI component
5. BUG-063 Profile needs Tabler CSS or Font Awesome swap
6. Content/branding bugs (072–076, 090–091) need DB/admin updates
7. BUG-088 Driver license — set optional in admin document requirements
8. Rs 50 tier may fail on JazzCash Wallet (min 100 in seeder)

---

## Next Steps (After Your Approval)

1. Deploy to server
2. Test Registration
3. Test Login
4. Test Campaign Creation
5. Test Campaign Browsing
6. Test Payments
7. Test Messaging
8. Test APIs
9. Test Profile
10. Provide final deployment report

---

*Generated: 2026-06-24 | No deploy, merge, or push performed.*
