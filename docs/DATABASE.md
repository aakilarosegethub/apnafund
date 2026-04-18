# Database overview

This document summarizes **purpose** and **important columns** for core tables. The authoritative schema is the set of files in `database/migrations/`. Run `php artisan migrate` to apply them.

## Core crowdfunding

### `campaigns`

Stores creator projects: title/slug, description, media, location, goal and raised amounts, dates, approval status, featured flag, payout bank references, and optional `goal_reached_notified_at` (timestamp when a one-time “goal reached” notification was sent).

### `deposits`

Payment attempts and successful charges tied to `campaign_id`, gateway (`method_code`, `method_currency`), amounts, `trx`, donor fields (`email`, `full_name`, …), `status` (see `App\Constants\ManageStatus`), and optional `reward_id`.

### `users`

Backers and creators: profile fields, `password`, Sanctum API tokens, optional KYC / business fields, `balance` (wallet), verification timestamps.

### `categories` / `sub_categories`

Taxonomy for browsing campaigns (admin-managed).

### `rewards`

Per-campaign pledge tiers: minimum amount, quantity, images, etc.

### `comments`

Campaign and update comments; may link to `update_id` for update threads.

### `campaign_updates`

Backer-facing updates (`slug`, content, `is_published`).

### `campaign_faqs`

Per-campaign FAQ rows.

### `campaign_collaborators`

Users who may edit a campaign alongside the owner.

### `payout_banks`

Selectable payout methods for creators (used with campaign payment settings).

### `creator_campaign_payouts` / `creator_campaign_fee_settings` / `creator_campaign_payout_actions`

Admin ledger for successful campaigns: fees, withholdings, net payable, audit actions.

## Payments & gateways

### `gateways` / `gateway_currencies`

Active payment methods and supported currencies per gateway.

### `transactions` / `withdrawals`

Wallet movements and withdrawal requests (non-deposit flows).

## Site & admin

### `settings`

Global site configuration (currency symbols, email/SMS switches, etc.) — often accessed via `bs()` helper.

### `site_data`

Key/value (or JSON) content blocks for CMS-style pages (home sections, etc.).

### `admins` / `roles` / `permissions`

Staff accounts and RBAC (see migrations adding `role_id`, permission JSON, dashboard widgets).

### `admin_activity_logs`

Audit trail for admin actions (`ActivityLogger` service).

### `email_logs` / `notification_templates`

Outbound email logging and templated notifications.

### `webhook_logs` / `data_logs`

Inbound gateway/webhook diagnostics (`WebhookLoggerService`, `UnifiedWebhookLoggerService`).

### `ip_currency_cache`

Caches IP-to-currency hints to reduce repeated geo lookups (optional; middleware and helpers).

### `user_push_devices`

FCM tokens per user for push notifications.

## Laravel system tables

Standard Laravel tables may include `jobs`, `failed_jobs`, `cache`, `sessions`, `password_reset_tokens`, `personal_access_tokens`, etc., depending on which migrations ran.

## Migrations

Each migration file may include a short header comment describing the change. For **column-level** detail, open the migration or inspect the database after migrating.
