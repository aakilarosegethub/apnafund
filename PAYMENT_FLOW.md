# Payment Webview API Flow (Specific Request)

This document explains how this exact request is processed:

```bash
curl --location 'http://localhost:8000/api/payment/webview-url' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
  "gateway_id": "1001",
  "amount": "100",
  "campaign_id": "129",
  "full_name": "Demo User",
  "email": "raheelshehzad188@gmail.com",
  "country": "Pakistan",
  "phone": "3001234567"
}'
Fn```

---

## 1) Route and Controller

- Route: `POST /api/payment/webview-url`
- Handler: `App\Http\Controllers\Api\PaymentController@webviewUrl`

---

## 2) Input Validation

Backend validates:

- `gateway_id` or `gateway` required
- `amount` required, numeric, `> 0`
- `campaign_id` required integer
- `full_name` required
- `email` required valid email

If any field is invalid, response is:

```json
{
  "Result": "false",
  "ResponseCode": "400",
  "ResponseMsg": "..."
}
```

---

## 3) Campaign Validation

- Campaign is loaded using `campaign_id`
- Must be approved and not expired

If campaign not found/expired, request fails with `404/400`.

---

## 4) Country -> Currency Detection

For this request:

- `country = Pakistan`
- Local currency resolves to `PKR`

Important:

- Client-sent `currency` is not required in this flow.
- Currency is derived from request country (or IP fallback if country missing).

---

## 5) Gateway Currency Row Selection

Backend loads active gateway currency row for gateway code/id `1001`:

Priority:

1. direct currency match (`PKR`, or `"0"` alias for PKR)
2. `input_currency_rates` mapping match
3. first active row fallback

If no active row found:

```json
{
  "Result": "false",
  "ResponseCode": "400",
  "ResponseMsg": "Invalid gateway or gateway not available for this currency"
}
```

---

## 6) Amount Conversion (Core Logic)

Input amount is treated as **local/donor currency** amount:

- Input: `100 PKR`
- Converted to platform/system currency (USD) for DB accounting:
  - Example runtime conversion: `100 PKR -> 0.357828 USD`

So:

- `deposits.amount` = platform amount (USD)

---

## 7) Gateway Limit Check

Min/max are validated in **gateway currency context** (not raw USD field).

If out of range:

```json
{
  "Result": "false",
  "ResponseCode": "400",
  "ResponseMsg": "Amount must be between X and Y PKR"
}
```

---

## 8) Charges and Final Payable

After platform amount is set:

- `charge = fixed_charge + (amount * percent_charge / 100)`
- `payable = amount + charge`  (platform side)
- `final_amount = payable * gateway_rate`  (gateway payable side)

Saved fields summary:

- `deposits.amount` -> platform/system currency amount (USD)
- `deposits.method_currency` -> gateway currency (e.g. PKR)
- `deposits.charge` -> calculated charge
- `deposits.rate` -> gateway rate
- `deposits.final_amount` -> amount to pay via selected gateway
- `deposits.trx` -> generated transaction reference

---

## 9) Manual vs Automated Gateway Response

Gateway decision:

- If gateway code `>= 1000` -> manual flow
- Else -> automated flow

For your `gateway_id: 1001` (manual):

- `payment_url = null`
- `trx` returned
- `payment_guide` returned (instructions + amounts + form fields)

Typical manual success response:

```json
{
  "Result": "true",
  "ResponseCode": "200",
  "ResponseMsg": "Payment guide generated",
  "payment_url": null,
  "trx": "XXXX",
  "payment_guide": {
    "gateway_type": "manual",
    "amount": 3.4579,
    "final_amount": 3.4579,
    "currency": "PKR",
    "platform_amount": 0.3578,
    "platform_currency": "USD"
  }
}
```

---

## 10) Next Step After This API

For manual gateway, app should call:

- `POST /api/payment/manual-proof`

Required:

- `trx`
- `payment_proof` (jpeg/jpg/png/pdf/webp, max 5MB)
- optional `note`

Then transaction moves to pending approval for admin review.
