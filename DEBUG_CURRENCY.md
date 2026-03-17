# Live Currency Debug Guide

## Step 1: Debug Route

`.env` mein add karo:
```
DEBUG_CURRENCY_KEY=your_secret_123
```

Phir live site pe visit karo:
```
https://your-site.com/debug-currency?key=your_secret_123
```

**Response mein dikhega:**
- `ip_raw` – Server ko milne wala IP
- `ip_resolved` – Proxy headers se resolve kiya gaya IP
- `ip_likely_localhost` – Agar true hai = real visitor IP nahi mil rahi (sabse common issue)
- `tcur_env` – TCUR ka value (.env se)
- `config_app_currency` – Jo currency use ho rahi hai
- `session_currency` – IP se detect kiya (agar null = IP detection fail)
- `ip_currency_cache_exists` – Table maujood hai ya nahi
- `getIpGeoData_result` – Geo API se country
- `getOrFetchIpCurrencyData_result` – Final IP currency data
- `setting_cur_sym` – Setting se symbol (DB fallback)

---

## Step 2: Logs Check Karo

```
storage/logs/laravel.log
```

**Jo messages search karo:**

| Log Message | Matlab |
|-------------|--------|
| `IpCurrencyDebug: IP is localhost, skipping` | Real visitor IP nahi mil rahi – proxy/config fix karo |
| `IpCurrencyDebug: ip_currency_cache table missing` | Migration nahi chali – table banao |
| `IpCurrencyDebug: getIpGeoData returned null` | Geo API fail – IP sahi hai lekin country nahi mil raha |
| `IpCurrencyDebug: getIpGeoData all APIs failed` | Teen API fail – server ka outbound HTTP check karo |
| `DetectCurrencyByIP: getOrFetchIpCurrencyData returned null/empty` | IP detection fail, DB use ho raha hai |

---

## Common Fixes

### 1. IP localhost aa rahi hai (Cloudflare/Nginx ke peeche)
- `app/Http/Middleware/TrustProxies.php` mein `$proxies = '*'` set hai?
- Nginx config: `proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;` ho
- Cloudflare ke saath `CF-Connecting-IP` header milna chahiye

### 2. ip_currency_cache table missing
```sql
-- SQL chalao (earlier provided)
CREATE TABLE `ip_currency_cache` (...);
```

### 3. Geo API block / fail
- `allow_url_fopen = On`
- Firewall / host allow kare: ip-api.com, ipapi.co, ipinfo.io
- `curl https://ip-api.com/json/8.8.8.8` server se test karo

### 4. TCUR set hai .env mein
- TCUR set hone par IP detection bilkul nahi chalega
- IP detection ke liye `.env` se TCUR hatao ya comment karo
