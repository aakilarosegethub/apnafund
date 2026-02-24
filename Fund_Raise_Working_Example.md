# Fund Raise API – Working Example

Base URL: `http://127.0.0.1:8000` (change if your server is different)

**Zaroori:** Pehle **Step 1** chala kar apne project ke gateways dekho. Unhi ka `id` ya `code` use karna hai — `"jazzcash"` tabhi kaam karega jab DB mein woh code ho.

---

## Step 1: Get available gateways (Pakistan) — pehle ye chalao

```bash
curl -s 'http://127.0.0.1:8000/api/gateways' \
  -H 'Accept: application/json' \
  -H 'X-Country: Pakistan'
```

Response mein `gateways` array aata hai. Har gateway ke andar:
- **id** — use as `"gateway_id": 10` (apna number daalo)
- **code** — use as `"gateway": "10"` ya jo code aaye (e.g. `"101"`, `"10"`)
- **currencies** — inme se koi ek use karo (e.g. `"currency": "PKR"`) aur `amount` us currency ke `min_amount`–`max_amount` ke beech hona chahiye.

---

## Step 2: Get payment webview URL (working example)

**Option A – Gateway by ID (sabse reliable)**  
Step 1 ki response se kisi bhi gateway ka **id** lo (e.g. `10`) aur neeche `gateway_id` mein wahi daalo:

```bash
curl -s -X POST 'http://127.0.0.1:8000/api/payment/webview-url' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "gateway_id": 10,
    "amount": 500,
    "campaign_slug": "giftify-an-online-shop-for-unique-customized-gifts-dispatched-with-heart",
    "full_name": "Donor Name",
    "email": "donor@example.com",
    "country": "Pakistan",
    "currency": "PKR",
    "phone": "03001234567"
  }'
```

**Option B – Gateway by code**  
Step 1 ki response mein jo **code** dikhe (e.g. `"10"` ya `"101"`), woh use karo (string format mein):

```bash
curl -s -X POST 'http://127.0.0.1:8000/api/payment/webview-url' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "gateway": "10",
    "amount": 500,
    "campaign_slug": "giftify-an-online-shop-for-unique-customized-gifts-dispatched-with-heart",
    "full_name": "Donor Name",
    "email": "donor@example.com",
    "country": "Pakistan",
    "currency": "PKR",
    "phone": "03001234567"
  }'
```

**Success response (200):**

```json
{
  "Result": "true",
  "ResponseCode": "200",
  "ResponseMsg": "Payment URL generated",
  "payment_url": "http://127.0.0.1:8000/deposit/confirm?trx=ABC123XYZ456",
  "trx": "ABC123XYZ456"
}
```

**Mobile use:** `payment_url` ko WebView mein open karo; wahi gateway (e.g. JazzCash) ka page dikhega.

---

## One-liner (replace `10` with your gateway id from Step 1)

```bash
curl -s -X POST 'http://127.0.0.1:8000/api/payment/webview-url' -H 'Content-Type: application/json' -H 'Accept: application/json' -d '{"gateway_id":10,"amount":500,"campaign_slug":"giftify-an-online-shop-for-unique-customized-gifts-dispatched-with-heart","full_name":"Donor Name","email":"donor@example.com","country":"Pakistan","currency":"PKR","phone":"03001234567"}'
```

---

## Agar error aaye

1. **"Campaign not found"**  
   `campaign_slug` sahi hona chahiye. Kisi approved campaign ka slug use karo (e.g. campaigns list se).

2. **"This gateway does not support currency PKR"**  
   Step 1 chala kar dekho kaun se gateways PKR support karte hain; usi ka `code` ya `id` use karo.

3. **"Gateway not available for country"**  
   Same gateway list se woh gateway choose karo jiske `currencies` mein Pakistan / PKR ho.

4. **Amount limit**  
   Step 1 ki response mein har currency ke `min_amount` aur `max_amount` hote hain; `amount` us range mein hona chahiye (e.g. JazzCash PKR: 50–100000).

---

## Summary

| Field          | Example value |
|----------------|---------------|
| gateway_id     | `10` — Step 1 se gateway ka **id** |
| gateway        | `"10"` — Step 1 ka **code** (use either gateway_id or gateway) |
| amount         | `500` (within gateway’s min/max) |
| campaign_slug  | Your real campaign slug |
| full_name      | Donor name |
| email          | Donor email |
| country        | `Pakistan` |
| currency       | `PKR` (must be supported by chosen gateway) |
| phone          | Optional |
