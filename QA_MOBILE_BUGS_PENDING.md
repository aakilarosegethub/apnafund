# QA Bugs — Mobile App Only (Web team scope nahi)

Yeh points **native mobile app / Flutter** se related hain. Inhe web codebase mein fix nahi kiya gaya.

| Bug ID | Title | Severity | Notes |
|--------|--------|----------|--------|
| **BUG-001** | Campaign detail routing — HTTP 403 on Android staging app | Critical | Mobile Frontend API Gateway; `/campaign/{slug}` API auth/CORS/mobile token issue |
| **BUG-050** | Email verification screen — `BOTTOM OVERFLOWED BY 108 PIXELS` with keyboard | High | Flutter layout on OTP screen; mobile viewport only |

---

## Related API bugs (mobile + Postman — backend fix alag track)

Mobile app in API endpoints use karti hai. Web fixes `QA_WEB_FIXES_COMPLETED.md` mein track hongi jahan Laravel backend change hui ho:

| Bug ID | Title | Backend note |
|--------|--------|----------------|
| BUG-044 | `POST /api/verify-email` returns HTML instead of JSON | API middleware / `Accept: application/json` |
| BUG-045 | Registration API skips reCAPTCHA | API validation |
| BUG-046 | No rate limit on login API | Throttle middleware |
| BUG-047 | No rate limit on donate API | Throttle middleware |
| BUG-048 | 405 returns HTML error page | Exception handler JSON |
| BUG-049 | Malformed JSON exposes stack traces | Exception handler |

---

## Performance (infrastructure — not single code fix)

| Bug ID | Title |
|--------|--------|
| BUG-051 | Homepage 100% errors under 300 users (JMeter) |
| BUG-052 | Campaign detail 100% errors under load |
| BUG-053 | 30-min soak test — 20s+ latency, homepage failures |

---

*Source: ApnaCrowdFunding Bug Report CSV — May 2026*
