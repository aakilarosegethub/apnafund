# ApnaFund API Documentation

Complete list of all APIs - **Protected** and **Non-Protected** (Public)

**Base URL:** `{{APP_URL}}` (e.g. http://localhost/apnafund or http://192.168.1.104:8000)

---

## 🔓 Non-Protected (Public) APIs

### 1. Utility
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/csrf-token` | CSRF token for forms |
| POST | `/api/verify-email` | Email verification (API) |

### 2. Home & Fund
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/home_api.php` | Home data |
| GET/POST | `/api/home.php` | Home data |
| GET/POST | `/api/catwisefund.php` | Category wise funds |
| GET/POST | `/api/search_fund.php` | Search fund |
| GET/POST | `/api/fundidwise.php` | Fund by ID |

### 3. Categories & Pages
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/catlist.php` | Category list |
| GET/POST | `/api/charitylist.php` | Charity list |
| GET/POST | `/api/faq.php` | FAQ list |
| GET/POST | `/api/pagelist.php` | Page list |

### 4. Payment Gateways
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/paymentgateway.php` | Payment gateway list |
| GET | `/api/gateways` | Gateways list |
| GET/POST | `/api/payment/webview-url` | Payment webview URL |

### 5. Auth (Register/Login)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/reg_user.php` | Register user |
| GET/POST | `/api/user_login.php` | User login |
| GET/POST | `/api/forget_password.php` | Forget password |
| GET/POST | `/api/social_login.php` | Social login |

### 6. Auth (OTP & Verification)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/mobile_check.php` | Check mobile |
| GET/POST | `/api/verify_email_otp.php` | Verify email OTP |
| GET/POST | `/api/resend_mobile_otp.php` | Resend mobile OTP |
| GET/POST | `/api/verify_mobile_otp.php` | Verify mobile OTP |
| GET/POST | `/api/send_password_reset_otp.php` | Send password reset OTP |
| GET/POST | `/api/verify_password_reset_otp.php` | Verify password reset OTP |
| GET/POST | `/api/reset_password.php` | Reset password |

### 7. OTP
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/msg_otp.php` | MSG OTP |
| GET/POST | `/api/twilio_otp.php` | Twilio OTP |
| GET/POST | `/api/sms_type.php` | SMS type |

### 8. Campaigns API (REST)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/campaigns` | Get all campaigns (query: limit, offset, category, search) |
| GET | `/api/campaigns/featured` | Get featured campaigns |
| GET | `/api/campaigns/{slug}` | Get campaign by slug |
| GET | `/api/categories` | Get all categories |
| GET | `/api/subcategories/{categoryId}` | Get subcategories by category ID |

---

## 🔐 Protected APIs (Bearer Token Required)

**Auth Header:** `Authorization: Bearer {token}`

*Token milta hai `/api/user_login.php` se response mein.*

### 1. Fund Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/fundlist.php` | User's fund list |
| GET/POST | `/api/fundraise.php` | Create fundraise |
| GET/POST | `/api/fund_update.php` | Update fund |
| GET/POST | `/api/fund_cancle.php` | Cancel fund |
| GET/POST | `/api/fund_complete.php` | Complete fund |
| GET/POST | `/api/fund_delete.php` | Delete fund |
| GET/POST | `/api/edit_fund.php` | Edit fund |

### 2. User Profile
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/edit_profile.php` | Edit profile |
| GET/POST | `/api/pro_image.php` | Upload profile image |
| GET/POST | `/api/wallet_up.php` | Update wallet |
| GET/POST | `/api/getbalance.php` | Get balance |

### 3. Donate
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/donate_now.php` | Donate now |
| GET/POST | `/api/my_donate_fundlist.php` | My donated fund list |

### 4. Withdraw
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/request_withdraw.php` | Request withdrawal |
| GET/POST | `/api/payout_list.php` | Payout list |

### 5. Wallet & Activity
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/wallet_report.php` | Wallet report |
| GET/POST | `/api/activity.php` | Activity list |
| GET/POST | `/api/notification.php` | Notifications |

### 6. Account
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET/POST | `/api/acc_delete.php` | Delete account |

---

## 👑 Admin APIs

### Public
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/admin/login` | Admin login |

### Protected (Admin Bearer Token)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/settings/gemini` | Get Gemini settings |
| PUT | `/api/admin/settings/gemini` | Update Gemini settings |
| POST | `/api/admin/settings/gemini/test` | Test Gemini |
| POST | `/api/admin/settings/gemini/reset` | Reset Gemini |

---

## 💳 IPN Webhooks (Payment Callbacks)

*Yeh routes payment gateways call karte hain – normally direct use nahi hote.*

| Method | Endpoint |
|--------|----------|
| POST | `/ipn/authorize` |
| ANY | `/ipn/btc-pay` |
| ANY | `/ipn/checkout` |
| POST | `/ipn/coinbase-commerce` |
| POST | `/ipn/coinpayments` |
| GET | `/ipn/flutterwave/{trx}/{type}` |
| POST | `/ipn/mercado-pago` |
| POST | `/ipn/now-payments-checkout` |
| POST | `/ipn/payeer` |
| GET | `/ipn/paypal-sdk` |
| POST | `/ipn/paystack` |
| POST | `/ipn/perfect-money` |
| POST | `/ipn/razorpay` |
| POST | `/ipn/stripe-v3` |
| POST | `/ipn/2checkout` |
| POST | `/ipn/stripe-js` |
| POST | `/ipn/card-payment` |
| POST | `/ipn/mwallet` |
| POST | `/ipn/jazzcash-wallet` |
| POST | `/ipn/jazzcash-wallet/process` |

*Note: JazzCash ka alag IPN hai:* `ANY /jazzcash/ipn`

---

## Postman Collection

Import file: **`APNAFUND_API_COLLECTION.postman_collection.json`**

1. Postman kholo  
2. Import → Upload Files → `APNAFUND_API_COLLECTION.postman_collection.json` select karo  
3. Collection variables mein `base_url` set karo (e.g. `http://localhost/apnafund`)  
4. Protected APIs ke liye pehle Login karke `bearer_token` copy karo aur Collection variables mein paste karo  
