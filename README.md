# ApnaCrowdFunding

Laravel 11 application for running a **rewards-based crowdfunding** platform: creators launch campaigns, backers contribute through multiple payment gateways, and admins manage content, payouts, and settings. The project ships with a **theme-based public site**, an **admin panel**, and **mobile-oriented JSON APIs** (legacy `.php` endpoints) registered under the `/api` prefix in `routes/web.php`.

## Requirements

- PHP **8.2+** with common extensions (`mbstring`, `openssl`, `pdo_mysql`, `json`, `curl`, `fileinfo`, `gd` or `imagick` as needed)
- Composer 2.x
- MySQL 8 (or compatible) — or adjust `DB_*` in `.env`
- Node/npm **optional** (only if you build front-end assets from source)

## Installation

1. **Clone** the repository and enter the project directory.

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Environment file**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Edit `.env` and set at minimum:

   - `APP_URL` — your public base URL  
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — database credentials  
   - Mail, storage, and any payment gateway keys you enable  

   Optional: `DEBUG_CURRENCY_KEY` for the `/debug-currency` diagnostic route (see `routes/web.php`).

4. **Database**

   ```bash
   php artisan migrate
   ```

   Seeders (if you use demo data or templates):

   ```bash
   php artisan db:seed
   ```

5. **Storage link** (if the app serves user uploads from `storage/app/public`)

   ```bash
   php artisan storage:link
   ```

6. **Run the application**

   ```bash
   php artisan serve
   ```

   Or use the Composer script (raises upload limits for local dev):

   ```bash
   composer run serve
   ```

   The app will be available at `http://127.0.0.1:8000` (or the host/port you configure).

## Configuration notes

- **Queue**: Default in `.env.example` is `database`. For production, configure a real queue worker (`php artisan queue:work`) if you dispatch jobs.
- **Sanctum**: Mobile/API routes use `auth:sanctum` where noted in `routes/web.php`; ensure `SANCTUM_STATEFUL_DOMAINS` and SPA cookie settings match your deployment if you use session-based SPA auth.
- **Firebase / FCM**: Optional; see `.env.example` for `FIREBASE_*` variables and `config/firebase.php` if present.
- **Currency / IP**: Visitor currency detection uses helpers and optional `ip_currency_cache` — see `App\Http\Middleware\DetectCurrencyByIP` and `docs/DATABASE.md`.

## Folder structure (high level)

| Path | Purpose |
|------|--------|
| `app/Http/Controllers` | Web, admin, API, and payment gateway controllers |
| `app/Http/Middleware` | HTTP middleware (auth, beta gate, currency, admin RBAC, etc.) |
| `app/Models` | Eloquent models (`Campaign`, `User`, `Deposit`, …) |
| `app/Services` | Domain services (payouts, currency, notifications, webhooks, FCM, …) |
| `app/Http/Helpers/helpers.php` | Globally loaded helper functions (settings, uploads, notifications) |
| `config/` | Laravel and package configuration |
| `database/migrations` | Schema history (see `docs/DATABASE.md` for a conceptual overview) |
| `database/seeders` | Seed data (e.g. notification templates) |
| `resources/views` | Blade views organized by **theme** (e.g. `themes/green`, `themes/primary`) |
| `public/` | Web root (assets, uploads, `index.php`) |
| `routes/web.php` | **Primary route file** — public site, user area, admin, and `/api` JSON endpoints |
| `routes/api.php` | Unused placeholder (APIs live in `web.php`) |
| `docs/` | Project documentation (e.g. database overview) |

## Key features / modules

- **Campaigns**: Create, approve, feature, and display campaigns; goals, rewards, FAQs, updates, comments, collaborators.
- **Payments**: Many gateways under `app/Http/Controllers/Gateway/*` with a shared `Gateway\PaymentController` for deposit initiation and manual proof upload.
- **Creator payouts**: Admin workflow for campaign success, fees, and withholdings (`CreatorCampaignPayoutService`, related models).
- **Admin**: Settings, categories, users, campaigns, RBAC (`roles` / `permissions`), activity logs, email logs.
- **Mobile / legacy API**: JSON responses with legacy field names (`ResponseCode`, `fundlist`, …) via `Api\*` controllers extending `BaseApiController`.
- **Notifications**: Email templates, optional FCM push, goal-reached emails (`CampaignGoalReachedNotificationService`).

## API routing

REST-style routes under `Route::prefix('api')` in `routes/web.php` include both:

- **Legacy-style** `.php` endpoints (e.g. `home_api.php`, `fundlist.php`) for mobile clients.
- **Modern JSON** routes such as `GET /api/campaigns` and `GET /api/campaigns/{slugOrId}`.

See inline comments in `routes/web.php` for authentication (`auth:sanctum`) and purpose of each group.

## Validation and form requests

The codebase does **not** use dedicated `FormRequest` classes in `app/Http/Requests`; validation is typically applied in controllers with `$this->validate()` or `Validator::make()`. When extending behavior, prefer extracting rules into FormRequest classes following Laravel conventions.

## Jobs

There are **no** custom queued job classes under `app/Jobs` in this repository; queue configuration is still relevant if you add jobs later.

## Testing

```bash
php artisan test
```

## Documentation in code

PHPDoc blocks describe controllers, models, services, and middleware where maintained. For **database tables and important columns**, see:

- `docs/DATABASE.md`

## License

Project `composer.json` declares **MIT** for the Laravel skeleton; verify third-party assets and themes for their respective licenses.
