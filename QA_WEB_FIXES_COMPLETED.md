# QA Web Fixes — Completed / In Progress

Web-related QA points ki status. Mobile-only: `QA_MOBILE_BUGS_PENDING.md`.

**Legend:** Done | Partial | Pending | N/A (content/infra)

---

## Authentication & Registration

| Bug ID | Status | Fix summary |
|--------|--------|-------------|
| BUG-002 | Partial | Dashboard logo — theme/asset path review needed |
| BUG-003 | Done | Strict email validation (OTP + register flows) |
| BUG-004 | Done | Weak password blocklist + min rules on OTP signup |
| BUG-005 | Done | Specific validation messages (not generic "Validation Fail") |
| BUG-006 | Done | Name must contain letters |
| BUG-007 | Done | max-length on name/password fields (frontend + backend) |
| BUG-008 | Done | No false success on failed OTP; clearer error handling |
| BUG-009 | Done | `history.replaceState` after verify; authorized users skip OTP page |
| BUG-010 | Done | Remember me unchecked by default; session expire_on_close when not remembered |
| BUG-011 | Done | New Google/social users → session `requires_terms_accept` + terms page (no `tc` conflict with login) |
| BUG-012 | Done | Same as BUG-011 |
| BUG-033 | Partial | CAPTCHA on classic register; OTP flow uses existing captcha component |
| BUG-057–059, BUG-080 | Done | Privacy / Cookie / Terms links on signup (green theme) |
| BUG-061 | Done | Google OAuth terms gate (duplicate of 011/012) |

---

## Session & Security

| Bug ID | Status | Fix summary |
|--------|--------|-------------|
| BUG-013 | Done | `PreventSensitivePageCache` middleware on auth user routes |
| BUG-032 | Partial | Web `user/campaign/*` POST still uses CSRF; `api/*` exempt (mobile app safe) |
| BUG-036 | Pending | CNIC private URLs — not implemented (needs protected file route) |
| BUG-038 | Done | Payment `depositInserts` requires login |
| BUG-044–049 | Partial | JSON API responses + throttle on auth/donate (see code) |

---

## Campaign Creation & Editor

| Bug ID | Status | Fix summary |
|--------|--------|-------------|
| BUG-014 | Pending | `; project` header leak — locate in live theme |
| BUG-015 | Pending | Start project "Saving…" back-button state |
| BUG-016 | Pending | Who's eligible link |
| BUG-017 | Pending | Category data loss on back navigation |
| BUG-018, 027, 062 | Done | Draft preview links to `user.campaign.show` for owner |
| BUG-019 | Pending | Back to Location link |
| BUG-020 | Partial | Short description validation |
| BUG-021–024 | Partial | Basics validation / image / YouTube URL |
| BUG-025 | Pending | Add-ons tab duplicate UI |
| BUG-026 | Partial | Collaborator uses user search (not free-text email); QA email field may differ |
| BUG-035 | Pending | Browser back wipes form (needs draft persistence) |

---

## Payment

| Bug ID | Status | Fix summary |
|--------|--------|-------------|
| BUG-039–042 | Partial | Gateway min amounts — align UI tiers or document limits |
| BUG-040 | Done | Zero amount validation message on donate form |
| BUG-043 | Pending | JazzCash integration (third-party) |
| BUG-067 | Pending | Platform fee / tip calculation review |
| BUG-068 | Done | Hide "Back This Project" when campaign expired |
| BUG-089 | Done | Marketing checkbox unchecked by default (GDPR) |

---

## UI / Content / Navigation

| Bug ID | Status | Fix summary |
|--------|--------|-------------|
| BUG-028, 066 | Done | Hide Firestore debug banner from production inbox |
| BUG-029 | Pending | Inbox UX (reload, icons) |
| BUG-030 | Done | Logged-in contact name/email readonly + grey disabled styling |
| BUG-031 | Done | Comment form clears after success |
| BUG-034 | Done | Updates tab empty state |
| BUG-054 | Pending | Pagination >3 pages — verify `per_page_item` + load-more |
| BUG-055–056 | Done | Search uses `name` + `slug` |
| BUG-063 | Pending | Profile image rendering |
| BUG-064 | Pending | Category nav hover |
| BUG-069 | Pending | Self-backing prevention |
| BUG-070 | Done | Over-funded campaigns show actual % + label |
| BUG-071 | Done | Campaign page title branding |
| BUG-072–076 | Partial | Content typos (PnixFund, bakers, careers title, etc.) |
| BUG-077 | Done | Business resources loading fallback when no API |p
| BUG-078–083 | Partial | Footer/legal/dead links |
| BUG-084–085 | N/A | Marked Fixed in QA sheet |
| BUG-086–088 | Partial | Placeholder image, file size label, driver license optional |
| BUG-090–091 | N/A | Content team |

---

---

## Recheck log (2026-05-29)

- Restored `api/*` CSRF exempt — mobile/legacy API was breaking without it.
- Social terms: use session flag only for **new** accounts (`tc` is reused by login/2FA logic).
- OTP captcha: only when reCAPTCHA plugin active; token appended in signup AJAX.
- Verified: logout → `user.login.form`, preview → `user.campaign.show`, search slug+name OK.

*Last updated: 2026-05-29*
