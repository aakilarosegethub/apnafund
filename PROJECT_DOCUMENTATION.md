# ApnaCrowdfunding - Complete Project Documentation

**Version:** 1.0  
**Last Updated:** January 2025  
**Framework:** Laravel 11.x  
**PHP Version:** 8.2+

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Technical Stack](#2-technical-stack)
3. [System Architecture](#3-system-architecture)
4. [User Roles & Permissions](#4-user-roles--permissions)
5. [Core Features](#5-core-features)
6. [Application Workflow](#6-application-workflow)
7. [Admin Panel Modules](#7-admin-panel-modules)
8. [Database Overview](#8-database-overview)
9. [APIs & Integrations](#9-apis--integrations)
10. [Installation & Setup Guide](#10-installation--setup-guide)
11. [Security Considerations](#11-security-considerations)
12. [Known Limitations](#12-known-limitations)
13. [Future Enhancements](#13-future-enhancements)

---

## 1. Project Overview

### 1.1 Purpose of the Project

ApnaCrowdfunding is a comprehensive crowdfunding platform that enables individuals and businesses to create fundraising campaigns, receive donations from supporters, and manage the entire fundraising lifecycle. The platform facilitates secure payment processing, campaign management, and administrative oversight.

### 1.2 Business Problem It Solves

The platform addresses several key challenges:

- **Fundraising Accessibility**: Provides an easy-to-use platform for individuals and businesses to launch fundraising campaigns without technical expertise
- **Payment Processing**: Integrates multiple payment gateways to support global transactions and various payment methods
- **Campaign Management**: Offers tools for campaign creators to manage their fundraising efforts, track progress, and engage with donors
- **Trust & Verification**: Implements KYC (Know Your Customer) verification and admin approval workflows to ensure platform integrity
- **Multi-currency Support**: Supports various currencies and payment methods to cater to a global audience

### 1.3 Target Users

The platform serves three primary user groups:

1. **Campaign Creators**: Individuals or businesses seeking to raise funds for projects, causes, or business ventures
2. **Donors**: Supporters who contribute funds to campaigns, either as registered users or anonymous donors
3. **Administrators**: Platform managers who oversee campaigns, verify users, process payments, and maintain system integrity

---

## 2. Technical Stack

### 2.1 Backend Technologies and Framework

- **Framework**: Laravel 11.x
- **PHP Version**: 8.2 or higher
- **Architecture**: MVC (Model-View-Controller)
- **ORM**: Eloquent ORM
- **Authentication**: Laravel's built-in authentication with custom guards for users and admins

### 2.2 Frontend Technologies

- **Templating Engine**: Blade (Laravel's templating system)
- **CSS Framework**: Custom CSS with responsive design
- **JavaScript**: Vanilla JavaScript and modern ES6+
- **Rich Text Editor**: CKEditor (with license key support)
- **Asset Management**: Vite for modern asset bundling

### 2.3 Database

- **Primary Database**: SQLite (default) or MySQL/MariaDB
- **Database Support**: 
  - SQLite
  - MySQL/MariaDB
  - PostgreSQL
  - SQL Server
- **Migrations**: Laravel database migrations for schema management
- **Query Builder**: Eloquent ORM with query scopes

### 2.4 Server / Hosting Environment

- **Web Server**: Apache (via XAMPP) or Nginx
- **PHP Requirements**: PHP 8.2+ with required extensions
- **Session Driver**: Database-based sessions
- **Cache Driver**: Database cache (configurable to Redis/Memcached)
- **Queue Driver**: Database queues (configurable to Redis/Beanstalkd)

### 2.5 PHP / Framework Version

- **PHP**: 8.2+
- **Laravel**: 11.0
- **Composer**: Latest stable version
- **Node.js**: Required for asset compilation (Vite)

### 2.6 Key Dependencies

- **Payment Gateways**: Multiple payment gateway SDKs (Stripe, Razorpay, PayPal, etc.)
- **Image Processing**: Intervention Image (v2.7)
- **Firebase**: Firebase PHP SDK for OTP authentication
- **Social Login**: Laravel Socialite for Facebook and Google OAuth
- **Email Services**: PHPMailer, SendGrid, Mailjet support
- **SMS Services**: Twilio, Vonage (Nexmo) support
- **YouTube Integration**: Google API Client for YouTube uploads

---

## 3. System Architecture

### 3.1 High-Level Architecture Explanation

The application follows a traditional three-tier architecture:

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│  (Blade Views, JavaScript, CSS)        │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│         Application Layer                │
│  (Controllers, Middleware, Services)    │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│         Data Layer                       │
│  (Models, Database, File Storage)       │
└─────────────────────────────────────────┘
```

**Key Components:**

1. **Routes**: Define application endpoints (web, API, admin, user, IPN)
2. **Controllers**: Handle business logic and request processing
3. **Models**: Represent database entities with relationships
4. **Middleware**: Handle authentication, authorization, and request filtering
5. **Services**: Encapsulate complex business logic (Firebase, YouTube, etc.)
6. **Views**: Blade templates for rendering HTML
7. **Storage**: File system for images, documents, and media

### 3.2 Request-Response Flow

1. **User Request**: HTTP request arrives at the web server
2. **Route Matching**: Laravel router matches the request to a route
3. **Middleware Execution**: 
   - Authentication checks
   - CSRF protection
   - Authorization verification
   - KYC status validation (for withdrawals)
4. **Controller Processing**: 
   - Validates input
   - Processes business logic
   - Interacts with models
   - Calls services if needed
5. **Database Interaction**: Eloquent ORM queries the database
6. **Response Generation**: 
   - Renders Blade view
   - Returns JSON (for API endpoints)
   - Redirects with messages
7. **Response Delivery**: HTTP response sent to client

### 3.3 Role of Backend, Frontend, and Database

**Backend (Laravel):**
- Handles all business logic
- Manages authentication and authorization
- Processes payments and webhooks
- Validates and sanitizes user input
- Generates dynamic content
- Manages file uploads and storage

**Frontend (Blade Templates):**
- Renders user interface
- Displays dynamic content from backend
- Handles form submissions
- Provides user feedback (toasts, notifications)
- Implements responsive design

**Database:**
- Stores all application data (users, campaigns, payments, etc.)
- Maintains relationships between entities
- Supports transactions for data integrity
- Enables efficient querying through indexes

---

## 4. User Roles & Permissions

### 4.1 List of User Roles

The platform has two primary user roles:

1. **Regular Users** (Campaign Creators & Donors)
2. **Administrators** (Platform Managers)

### 4.2 Detailed Permissions for Each Role

#### 4.2.1 Regular Users

**Authentication & Profile:**
- Register new account (email/password or social login)
- Login/Logout
- OTP-based phone authentication (Firebase)
- Email verification
- Mobile/phone verification
- Password reset
- Profile management
- Two-factor authentication (2FA) setup

**Campaign Management:**
- Create new campaigns
- Edit own campaigns (pending/approved status)
- View own campaigns (all statuses)
- Delete own campaigns
- Upload campaign images and gallery
- Add/remove campaign rewards
- Manage campaign sections (basics, story, rewards, people, payment, boost)

**Donations:**
- Make donations to campaigns (anonymous or registered)
- View donation history
- View received donations (for campaign creators)
- Download donation receipts

**Withdrawals:**
- Request withdrawals (requires KYC verification)
- View withdrawal history
- Select withdrawal methods

**Account Features:**
- Submit KYC documents
- View KYC status
- View transaction history
- Manage account settings
- Enable/disable 2FA

**Restrictions:**
- Cannot access admin panel
- Cannot approve/reject campaigns
- Cannot manage other users
- Cannot access system settings
- Withdrawals require KYC approval

#### 4.2.2 Administrators

**Dashboard:**
- View system statistics
- Monitor platform activity
- Access quick actions

**User Management:**
- View all users (active, banned, pending KYC)
- View user details
- Approve/reject KYC submissions
- Ban/unban users
- Update user balances (manual adjustments)
- Login as user (impersonation)
- Send emails to users (individual or bulk)
- Delete users (individual or bulk)
- View email unconfirmed users
- View mobile unconfirmed users

**Campaign Management:**
- View all campaigns (pending, approved, rejected, running, upcoming, expired)
- Approve/reject campaigns
- Mark campaigns as featured
- View campaign details
- Update campaign status
- Manage campaign categories and subcategories

**Payment Management:**
- View all donations (pending, completed, cancelled)
- Approve/reject manual payment deposits
- View payment gateway configurations
- Manage automated payment gateways
- Manage manual payment methods
- Configure gateway currencies and charges

**Withdrawal Management:**
- View all withdrawal requests (pending, completed, cancelled)
- Approve/reject withdrawal requests
- Manage withdrawal methods
- Configure withdrawal charges

**Content Management:**
- Manage campaign categories
- Manage subcategories
- Approve/reject campaign comments
- Manage FAQ entries
- Manage site content and sections
- Configure homepage settings

**System Configuration:**
- Basic settings (site name, logo, favicon)
- Email configuration (SMTP, SendGrid, Mailjet)
- SMS configuration (Twilio, Vonage)
- Notification templates
- Language management
- Plugin management
- SEO settings
- Maintenance mode
- Cookie policy settings

**Reports & Analytics:**
- View transaction reports
- View email logs
- View webhook logs
- Export data
- View statistics

**Integrations:**
- Configure YouTube integration
- Manage social login settings (Facebook, Google)
- Configure Firebase for OTP
- Manage payment gateway settings

**Restrictions:**
- Cannot delete system-critical data
- Cannot modify core system files (through UI)
- All actions are logged for audit purposes

---

## 5. Core Features

### 5.1 Authentication & Authorization

**User Authentication:**
- Email/password registration and login
- Social login (Facebook, Google OAuth)
- OTP-based phone authentication via Firebase
- Email verification
- Phone/mobile verification
- Password reset via email
- Two-factor authentication (2FA) using Google Authenticator
- Remember me functionality
- Session management

**Admin Authentication:**
- Separate admin login system
- Admin password reset
- Admin session management

**Authorization:**
- Role-based access control
- Middleware-based route protection
- KYC status checks for withdrawals
- Email/mobile verification requirements
- Campaign ownership verification

### 5.2 Campaign Creation and Management

**Campaign Creation Process:**
1. User selects categories
2. Sets campaign location
3. Accepts terms and conditions
4. Creates campaign with:
   - Basic information (name, description, goal amount)
   - Campaign image and gallery
   - Video support (upload or YouTube URL)
   - Start and end dates
   - Preferred donation amounts
   - Location information

**Campaign Sections:**
- **Basics**: Name, description, category, goal amount
- **Story**: Detailed campaign story with rich text editor
- **Rewards**: Create reward tiers with minimum amounts
- **People**: Team members and campaign organizers
- **Payment**: Payment information and bank details
- **Boost**: Additional promotion options

**Campaign Statuses:**
- **Pending**: Awaiting admin approval
- **Approved**: Active and visible to public
- **Rejected**: Not approved by admin

**Campaign Lifecycle:**
- **Upcoming**: Start date in the future
- **Running**: Currently active (between start and end dates)
- **Expired**: End date has passed

**Campaign Features:**
- Featured campaigns (admin-controlled)
- Campaign comments (with admin moderation)
- Donation tracking
- Progress percentage calculation
- Donor count tracking
- Gallery management
- Reward management

### 5.3 Payment Processing

**Payment Flow:**
1. User selects campaign and donation amount
2. Chooses payment gateway
3. Redirects to payment gateway
4. Completes payment
5. Gateway sends IPN (Instant Payment Notification)
6. System verifies and processes payment
7. Updates campaign raised amount
8. Sends confirmation emails

**Payment Gateways Supported:**
- **Stripe** (Stripe.js and Stripe V3)
- **PayPal** (PayPal SDK)
- **Razorpay**
- **Authorize.Net**
- **Flutterwave**
- **Paystack**
- **Mercado Pago**
- **Coinbase Commerce**
- **CoinPayments**
- **BTCPay Server**
- **Perfect Money**
- **Payeer**
- **2Checkout**
- **Checkout.com**
- **NowPayments**
- **Card Payment Gateway** (v1.1)
- **JazzCash** (Mobile Wallet)
- **JazzCash Wallet**
- **MWallet**
- **Custom Gateway**

**Payment Features:**
- Multiple currency support per gateway
- Fixed and percentage charges
- Minimum and maximum amount limits
- Country-based gateway restrictions
- Sandbox/test mode support
- Manual payment method support
- Payment status tracking
- Anonymous donation option
- Donor information capture

**Payment Statuses:**
- **Initiate**: Payment initiated but not completed
- **Success**: Payment completed successfully
- **Pending**: Payment pending approval (manual methods)
- **Cancel**: Payment cancelled or failed

### 5.4 Admin Approval Flow

**Campaign Approval:**
1. User creates campaign → Status: Pending
2. Admin reviews campaign details
3. Admin can approve or reject
4. If approved → Status: Approved (visible to public)
5. If rejected → Status: Rejected (user notified)

**KYC Approval:**
1. User submits KYC documents
2. Status: Pending
3. Admin reviews documents
4. Admin approves or rejects
5. If approved → User can request withdrawals
6. If rejected → User must resubmit

**Comment Approval:**
1. User posts comment on campaign
2. Status: Pending
3. Admin reviews comment
4. Admin approves or rejects
5. Approved comments appear on campaign page

**Manual Payment Approval:**
1. User submits manual payment proof
2. Admin reviews payment details
3. Admin approves or rejects
4. If approved → Campaign raised amount updated

**Withdrawal Approval:**
1. User requests withdrawal (requires KYC)
2. Admin reviews withdrawal request
3. Admin approves or rejects
4. If approved → Funds transferred to user's account

### 5.5 Notifications & Emails

**Email Notifications:**
- Welcome email (on registration)
- Email verification
- Password reset
- Campaign approval/rejection
- KYC approval/rejection
- Payment confirmation
- Withdrawal approval/rejection
- Admin notifications
- Custom email templates

**Email Services Supported:**
- SMTP (standard email servers)
- SendGrid
- Mailjet
- PHPMailer

**SMS Notifications:**
- OTP codes (via Firebase)
- SMS via Twilio
- SMS via Vonage (Nexmo)

**Notification Templates:**
- Editable email templates
- Editable SMS templates
- Variable substitution support
- Universal notification settings

**Email Logging:**
- All emails are logged
- View email history
- Resend failed emails
- Email statistics

**Webhook Logging:**
- Payment gateway webhooks logged
- Retry failed webhooks
- Webhook statistics
- Gateway-specific filtering

---

## 6. Application Workflow

### 6.1 Step-by-Step User Journey

#### 6.1.1 Campaign Creator Journey

**Registration:**
1. User visits registration page
2. Chooses registration type (personal or business)
3. Fills registration form (email, password, name, etc.)
4. Optionally uses social login (Facebook/Google)
5. Receives email verification link
6. Verifies email address
7. Optionally verifies phone via OTP
8. Account activated

**Campaign Creation:**
1. User logs in
2. Navigates to "Start Project" or "Create Campaign"
3. Selects campaign categories
4. Sets campaign location
5. Accepts terms and conditions
6. Creates campaign with:
   - Basic information
   - Campaign story
   - Images and gallery
   - Video (optional)
   - Goal amount and duration
   - Rewards (optional)
7. Submits campaign for approval
8. Campaign status: Pending

**Campaign Management:**
1. User views campaign dashboard
2. Sees campaign status (pending/approved/rejected)
3. Can edit campaign (if pending)
4. Monitors donations received
5. Views donor list
6. Manages campaign rewards
7. Updates campaign information

**Withdrawal Process:**
1. User completes KYC verification
2. Submits KYC documents
3. Waits for admin approval
4. Once KYC approved, can request withdrawal
5. Selects withdrawal method
6. Enters withdrawal amount
7. Submits withdrawal request
8. Admin reviews and approves
9. Funds transferred to user

#### 6.1.2 Donor Journey

**Browsing Campaigns:**
1. Visitor browses homepage
2. Views featured campaigns
3. Filters campaigns by category
4. Searches for specific campaigns
5. Views campaign details

**Making a Donation:**
1. Clicks "Donate" or "Contribute" on campaign
2. Selects donation amount (or enters custom)
3. Chooses to donate as registered user or anonymously
4. If anonymous: Enters name, email, phone (optional)
5. If registered: Logs in (if not already)
6. Selects payment gateway
7. Redirected to payment gateway
8. Completes payment
9. Redirected back to success page
10. Receives confirmation email

**Registered Donor Features:**
1. Views donation history
2. Downloads receipts
3. Tracks donations to multiple campaigns
4. Receives updates on supported campaigns

### 6.2 Admin-Side Workflow

**Daily Operations:**
1. Admin logs into admin panel
2. Views dashboard with key metrics
3. Reviews pending campaigns
4. Approves/rejects campaigns
5. Reviews pending KYC submissions
6. Approves/rejects KYC
7. Reviews pending manual payments
8. Approves/rejects payments
9. Reviews withdrawal requests
10. Processes withdrawals
11. Monitors system health

**User Management:**
1. Views user list
2. Filters by status (active, banned, KYC pending)
3. Reviews user details
4. Manages user accounts
5. Sends communications to users

**Content Management:**
1. Manages categories and subcategories
2. Moderates campaign comments
3. Updates site content
4. Manages FAQ entries

**System Configuration:**
1. Updates site settings
2. Configures payment gateways
3. Manages email/SMS settings
4. Updates notification templates
5. Manages languages
6. Configures plugins

**Reports & Analytics:**
1. Views transaction reports
2. Analyzes email logs
3. Monitors webhook status
4. Exports data for analysis

---

## 7. Admin Panel Modules

### 7.1 Dashboard

**Features:**
- System statistics overview
- Recent activities
- Pending items count (campaigns, KYC, payments, withdrawals)
- Quick action buttons
- Revenue metrics
- User statistics
- Campaign statistics

### 7.2 User Management

**User Lists:**
- All users
- Active users
- Banned users
- KYC pending users
- KYC unconfirmed users
- Email unconfirmed users
- Mobile unconfirmed users

**User Operations:**
- View user details
- Edit user information
- Approve/reject KYC
- Ban/unban users
- Update user balance
- Login as user
- Send email to user
- Send bulk emails
- Delete users (individual or bulk)
- Test welcome emails

### 7.3 Campaign Management

**Campaign Lists:**
- All campaigns
- Pending campaigns
- Approved campaigns
- Rejected campaigns
- Running campaigns
- Upcoming campaigns
- Expired campaigns

**Campaign Operations:**
- View campaign details
- Approve/reject campaigns
- Mark as featured
- Update campaign status
- View campaign donations
- View campaign comments

**Category Management:**
- Create/edit categories
- Manage subcategories
- Activate/deactivate categories
- Set category order

**Comment Management:**
- View all comments
- Approve/reject comments
- Delete comments

### 7.4 Payments & Withdrawals

**Donation Management:**
- View all donations
- Pending donations (manual)
- Completed donations
- Cancelled donations
- Approve/reject manual payments
- Filter by gateway, status, date

**Payment Gateway Management:**
- Automated gateways configuration
- Manual payment methods
- Gateway currency settings
- Charge configuration
- Country restrictions
- Enable/disable gateways

**Withdrawal Management:**
- View all withdrawals
- Pending withdrawals
- Completed withdrawals
- Cancelled withdrawals
- Approve/reject withdrawals
- Withdrawal method configuration
- Charge settings

### 7.5 Reports & Settings

**Reports:**
- Transaction reports
- Email logs with statistics
- Webhook logs with statistics
- Export functionality
- Filter by date, gateway, status

**System Settings:**
- Basic settings (site name, logo, favicon, cover image)
- Email configuration
- SMS configuration
- Notification templates
- Language management
- SEO settings
- Cookie policy
- Maintenance mode
- Cache management

**Content Settings:**
- Homepage sections management
- Site content management
- Theme selection
- Custom code (CSS/JavaScript)

**Integration Settings:**
- YouTube integration
- Social login (Facebook, Google)
- Firebase configuration
- Payment gateway settings

**Plugin Management:**
- Enable/disable plugins
- Configure plugin settings

---

## 8. Database Overview

### 8.1 Major Tables

**User Management:**
- `users`: User accounts, profiles, KYC data, verification status
- `admins`: Administrator accounts
- `admin_password_resets`: Admin password reset tokens
- `password_resets`: User password reset tokens

**Campaign Management:**
- `campaigns`: Campaign details, goals, status, dates
- `categories`: Campaign categories
- `sub_categories`: Subcategories for campaigns
- `comments`: Campaign comments
- `rewards`: Campaign reward tiers
- `galleries`: Campaign gallery images

**Payment Management:**
- `deposits`: Donation/payment records
- `withdrawals`: Withdrawal requests
- `withdraw_methods`: Withdrawal method configurations
- `transactions`: Transaction history
- `gateways`: Payment gateway configurations
- `gateway_currencies`: Gateway currency settings

**Content Management:**
- `site_data`: Site content and sections
- `contacts`: Contact form submissions
- `subscribers`: Newsletter subscribers
- `forms`: Dynamic form configurations (KYC, etc.)

**System Configuration:**
- `settings`: System-wide settings
- `plugins`: Plugin configurations
- `languages`: Language configurations
- `notification_templates`: Email/SMS templates

**Logging & Tracking:**
- `email_logs`: Email sending history
- `webhook_logs`: Payment gateway webhook logs
- `data_logs`: General data logging
- `admin_notifications`: Admin notification records

**Registration:**
- `registration_steps`: Multi-step registration configuration
- `registration_questions`: Registration form questions
- `user_registration_responses`: User registration responses

### 8.2 Relationships Between Tables

**User Relationships:**
- `users` → `campaigns` (One-to-Many)
- `users` → `deposits` (One-to-Many)
- `users` → `withdrawals` (One-to-Many)
- `users` → `transactions` (One-to-Many)
- `users` → `comments` (One-to-Many)

**Campaign Relationships:**
- `campaigns` → `users` (Many-to-One)
- `campaigns` → `categories` (Many-to-One)
- `campaigns` → `deposits` (One-to-Many)
- `campaigns` → `comments` (One-to-Many)
- `campaigns` → `rewards` (One-to-Many)
- `campaigns` → `galleries` (One-to-Many)

**Category Relationships:**
- `categories` → `campaigns` (One-to-Many)
- `categories` → `sub_categories` (One-to-Many)

**Payment Relationships:**
- `deposits` → `users` (Many-to-One)
- `deposits` → `campaigns` (Many-to-One)
- `deposits` → `gateways` (Many-to-One via method_code)
- `withdrawals` → `users` (Many-to-One)
- `withdrawals` → `withdraw_methods` (Many-to-One)

**Gateway Relationships:**
- `gateways` → `gateway_currencies` (One-to-Many)
- `gateway_currencies` → `deposits` (One-to-Many via method_code)

### 8.3 Data Flow Explanation

**Campaign Creation Flow:**
1. User creates campaign → `campaigns` table
2. Campaign linked to user via `user_id`
3. Campaign linked to category via `category_id`
4. Status set to "pending"
5. Admin approves → Status updated to "approved"

**Donation Flow:**
1. User makes donation → `deposits` table created
2. Deposit linked to user (if registered) via `user_id`
3. Deposit linked to campaign via `campaign_id`
4. Deposit linked to gateway via `method_code`
5. Payment gateway processes payment
6. IPN received → Deposit status updated
7. Campaign `raised_amount` updated
8. Transaction record created in `transactions` table

**Withdrawal Flow:**
1. User requests withdrawal → `withdrawals` table created
2. Withdrawal linked to user via `user_id`
3. Withdrawal linked to method via `method_id`
4. Status set to "pending"
5. Admin approves → Status updated to "success"
6. User balance updated
7. Transaction record created

**KYC Flow:**
1. User submits KYC → `users.kyc_data` (JSON field) updated
2. `users.kc` status set to "pending"
3. Admin reviews → Status updated to "verified" or "unverified"

**Comment Flow:**
1. User posts comment → `comments` table created
2. Comment linked to campaign via `campaign_id`
3. Comment linked to user via `user_id`
4. Status set to "pending"
5. Admin approves → Status updated to "approved"
6. Comment visible on campaign page

---

## 9. APIs & Integrations

### 9.1 Payment Gateways

**Automated Payment Gateways:**
- **Stripe**: Credit/debit card payments (Stripe.js and Stripe V3)
- **PayPal**: PayPal account and card payments
- **Razorpay**: Indian payment gateway
- **Authorize.Net**: Credit card processing
- **Flutterwave**: African payment gateway
- **Paystack**: African payment gateway
- **Mercado Pago**: Latin American payments
- **Coinbase Commerce**: Cryptocurrency payments
- **CoinPayments**: Cryptocurrency payments
- **BTCPay Server**: Bitcoin payments
- **Perfect Money**: Digital currency
- **Payeer**: Digital currency
- **2Checkout**: Global payments
- **Checkout.com**: Payment processing
- **NowPayments**: Cryptocurrency payments
- **Card Payment Gateway**: Generic card payment (v1.1)
- **JazzCash**: Pakistan mobile wallet
- **JazzCash Wallet**: Pakistan wallet payments
- **MWallet**: Mobile wallet payments
- **Custom Gateway**: Configurable custom gateway

**Manual Payment Methods:**
- Bank transfer
- Cash deposit
- Other manual methods (admin-configurable)

**Gateway Features:**
- Multiple currency support
- Country-based restrictions
- Sandbox/test mode
- Fixed and percentage charges
- Minimum/maximum amount limits
- IPN/webhook support
- Secure hash verification

### 9.2 Third-Party APIs

**Firebase Integration:**
- Phone number authentication
- OTP (One-Time Password) generation and verification
- Configurable timeout and attempt limits
- Used for phone verification during registration/login

**Google APIs:**
- **Google OAuth**: Social login authentication
- **Google Maps API**: Location services (if configured)
- **YouTube Data API**: Video upload and management

**Facebook Integration:**
- Facebook OAuth for social login
- User profile data retrieval

**Email Services:**
- **SendGrid**: Transactional email service
- **Mailjet**: Email delivery service
- **SMTP**: Standard email server support
- **PHPMailer**: Email library

**SMS Services:**
- **Twilio**: SMS messaging service
- **Vonage (Nexmo)**: SMS messaging service
- **Firebase**: OTP via SMS

### 9.3 Webhooks and Background Processes

**Payment Gateway Webhooks (IPN):**
- All payment gateways send IPN to `/ipn/{gateway-name}` routes
- Webhooks are logged in `webhook_logs` table
- Automatic retry for failed webhooks
- Webhook verification and security checks
- Status updates based on webhook data

**Webhook Flow:**
1. Payment gateway sends webhook to application
2. Webhook received and logged
3. Webhook data verified (signature, hash, etc.)
4. Payment status updated
5. Campaign raised amount updated
6. User notified via email
7. Transaction record created

**Background Processes:**
- Email queue processing (database queue)
- SMS queue processing
- Webhook retry mechanism
- Scheduled tasks (via Laravel scheduler)

**Webhook Logging Features:**
- Complete webhook request logging
- Response logging
- Retry functionality
- Statistics and analytics
- Filter by gateway, status, date
- Export functionality

---

## 10. Installation & Setup Guide

### 10.1 Server Requirements

**Minimum Requirements:**
- PHP 8.2 or higher
- Composer (latest version)
- Node.js 16+ and npm
- Web server (Apache or Nginx)
- Database (SQLite, MySQL 5.7+, MariaDB 10.3+, PostgreSQL 10+, or SQL Server)

**PHP Extensions Required:**
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD or Imagick (for image processing)
- cURL
- Zip

**Recommended:**
- 2GB+ RAM
- 10GB+ disk space
- SSL certificate (for production)

### 10.2 Environment Setup

**Step 1: Clone/Download Project**
```bash
cd /path/to/your/project
```

**Step 2: Install PHP Dependencies**
```bash
composer install
```

**Step 3: Install Node Dependencies**
```bash
npm install
```

**Step 4: Environment Configuration**
```bash
# Copy environment template
cp env_template.txt .env

# Generate application key
php artisan key:generate
```

**Step 5: Configure .env File**
Edit `.env` file with your settings:
- Database configuration
- Mail settings
- Payment gateway credentials
- Firebase credentials
- Social login credentials
- Application URL
- Timezone
- Locale

**Step 6: Database Setup**

For SQLite:
```bash
touch database/database.sqlite
```

For MySQL/MariaDB:
```bash
# Create database
mysql -u root -p
CREATE DATABASE apnacrowdfunding;
exit;
```

**Step 7: Run Migrations**
```bash
php artisan migrate
```

**Step 8: Seed Database (Optional)**
```bash
php artisan db:seed
```

**Step 9: Build Assets**

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

**Step 10: Set Permissions**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 10.3 Database Setup

**Option 1: SQLite (Default)**
- No additional setup required
- Database file: `database/database.sqlite`
- Suitable for development and small deployments

**Option 2: MySQL/MariaDB**
1. Create database
2. Update `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=apnacrowdfunding
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```
3. Run migrations

**Option 3: PostgreSQL**
1. Create database
2. Update `.env` with PostgreSQL credentials
3. Run migrations

### 10.4 Deployment Steps

**Production Deployment:**

1. **Server Preparation:**
   - Install PHP 8.2+, Composer, Node.js
   - Configure web server (Apache/Nginx)
   - Set up SSL certificate
   - Configure database server

2. **Application Deployment:**
   ```bash
   # Clone repository
   git clone <repository-url>
   cd apnacrowdfunding
   
   # Install dependencies
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   
   # Configure environment
   cp env_template.txt .env
   php artisan key:generate
   # Edit .env with production values
   
   # Database setup
   php artisan migrate --force
   
   # Optimize application
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Web Server Configuration:**
   - Point document root to `public/` directory
   - Configure URL rewriting
   - Set up SSL
   - Configure PHP-FPM (if using Nginx)

4. **Cron Job Setup:**
   ```bash
   # Add to crontab
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

5. **Queue Worker (if using queues):**
   ```bash
   php artisan queue:work --daemon
   ```

6. **File Permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

### 10.5 Post-Installation Configuration

**Admin Account:**
- Create admin account via database or seeder
- Or use admin registration (if enabled)

**Payment Gateway Setup:**
1. Access admin panel
2. Navigate to Payment Gateways
3. Configure each gateway with credentials
4. Set up gateway currencies
5. Test in sandbox mode

**Email Configuration:**
1. Navigate to Email Settings
2. Configure SMTP or email service
3. Test email sending
4. Configure email templates

**Basic Settings:**
1. Set site name, logo, favicon
2. Configure timezone and locale
3. Set up homepage content
4. Configure SEO settings

---

## 11. Security Considerations

### 11.1 Authentication Security

**Password Security:**
- Passwords hashed using bcrypt (12 rounds)
- Password reset tokens with expiration
- Rate limiting on login attempts
- Strong password requirements (configurable)

**Session Security:**
- Database-based sessions
- Session encryption (configurable)
- CSRF protection on all forms
- Secure session cookies

**Two-Factor Authentication:**
- Google Authenticator support
- Time-based OTP (TOTP)
- Backup codes support

**Social Login Security:**
- OAuth 2.0 implementation
- Secure token storage
- Profile data validation

### 11.2 Payment Security

**Payment Gateway Security:**
- Secure hash verification for all payments
- IPN/webhook signature validation
- SSL/TLS encryption for payment pages
- PCI DSS compliance (via gateway providers)

**Payment Data:**
- Sensitive payment data not stored locally
- Payment information encrypted in transit
- Secure payment redirects
- Payment status verification

**Manual Payment Security:**
- Admin approval required
- Payment proof verification
- Audit trail for all payments

### 11.3 Data Protection Practices

**User Data Protection:**
- KYC data stored securely
- Personal information encrypted
- GDPR compliance considerations
- Data access controls

**File Upload Security:**
- File type validation
- File size limits
- Virus scanning (if configured)
- Secure file storage

**Database Security:**
- Prepared statements (via Eloquent)
- SQL injection prevention
- Database access controls
- Regular backups recommended

**API Security:**
- Rate limiting on API endpoints
- API authentication (if implemented)
- Input validation and sanitization
- Output encoding

**General Security:**
- XSS (Cross-Site Scripting) protection
- CSRF token validation
- Secure headers configuration
- Error message sanitization
- Logging and monitoring

---

## 12. Known Limitations

### 12.1 Current System Constraints

**Scalability:**
- Database queue processing may be slow for high volumes
- Consider Redis for queues in production
- File storage may need CDN for high traffic
- Consider database optimization for large datasets

**Payment Processing:**
- Manual payment approval required (not instant)
- Some gateways may have regional restrictions
- Currency conversion handled by gateways
- Refund processing may require manual intervention

**Campaign Features:**
- Campaign editing limited after approval
- No automatic campaign extension
- Limited analytics and reporting features
- No built-in social media sharing automation

**User Features:**
- KYC verification requires admin approval (not automated)
- Limited user communication tools
- No built-in messaging system between users
- Limited notification preferences

**Technical Limitations:**
- SQLite may not be suitable for high-traffic production
- No built-in load balancing
- Limited caching mechanisms
- No built-in CDN integration

**Integration Limitations:**
- YouTube integration requires manual OAuth setup
- Firebase OTP requires proper configuration
- Some payment gateways may have setup complexity
- Limited third-party analytics integration

---

## 13. Future Enhancements

### 13.1 Scalability Improvements

**Infrastructure:**
- Implement Redis for caching and queues
- Add CDN integration for static assets
- Implement database read replicas
- Add load balancing support
- Implement horizontal scaling

**Performance:**
- Database query optimization
- Implement full-page caching
- Add image optimization and lazy loading
- Implement API response caching
- Add database indexing optimization

**Monitoring:**
- Implement application performance monitoring (APM)
- Add error tracking and alerting
- Implement log aggregation
- Add real-time analytics dashboard

### 13.2 Feature Expansion Ideas

**Campaign Features:**
- Recurring donation support
- Campaign milestones and updates
- Social media integration and sharing
- Campaign analytics dashboard
- A/B testing for campaign pages
- Campaign templates
- Multi-language campaign support

**User Features:**
- In-app messaging system
- User profiles and portfolios
- Follower/following system
- User activity feeds
- Advanced notification preferences
- User badges and achievements

**Payment Features:**
- Automated refund processing
- Payment plans and installments
- Cryptocurrency wallet integration
- Mobile money expansion
- Payment splitting (multiple recipients)
- Escrow services

**Admin Features:**
- Advanced analytics and reporting
- Automated KYC verification (AI/ML)
- Bulk operations improvements
- Advanced user segmentation
- Automated campaign moderation
- Revenue forecasting

**Communication:**
- Email marketing integration
- SMS marketing campaigns
- Push notifications
- In-app notifications
- Newsletter management
- Automated email sequences

**Integration Enhancements:**
- More payment gateway integrations
- Accounting software integration (QuickBooks, Xero)
- CRM integration
- Marketing automation tools
- Social media management
- Analytics platforms (Google Analytics, etc.)

**Mobile:**
- Native mobile app (iOS/Android)
- Progressive Web App (PWA)
- Mobile-optimized admin panel
- Mobile payment optimization

**Internationalization:**
- Multi-language support expansion
- Regional payment method support
- Currency conversion features
- Tax calculation per region
- Compliance features (GDPR, etc.)

---

## Appendix A: File Structure Overview

```
ApnaCrowdfunding/
├── app/
│   ├── Console/          # Artisan commands
│   ├── Constants/        # Application constants
│   ├── Exceptions/       # Exception handlers
│   ├── Helpers/          # Helper functions
│   ├── Http/
│   │   ├── Controllers/  # Application controllers
│   │   └── Middleware/   # HTTP middleware
│   ├── Lib/              # Library classes
│   ├── Models/           # Eloquent models
│   ├── Notifications/    # Notification classes
│   ├── Notify/           # Notification services
│   ├── Providers/        # Service providers
│   ├── Services/         # Business logic services
│   └── Traits/           # Reusable traits
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Web root directory
├── resources/
│   ├── views/            # Blade templates
│   ├── css/              # CSS files
│   └── js/               # JavaScript files
├── routes/               # Route definitions
├── storage/              # Storage directory
└── vendor/                # Composer dependencies
```

---

## Appendix B: Key Configuration Files

- `.env`: Environment configuration
- `config/app.php`: Application configuration
- `config/database.php`: Database configuration
- `config/mail.php`: Email configuration
- `config/auth.php`: Authentication configuration
- `config/session.php`: Session configuration
- `config/cache.php`: Cache configuration

---

## Appendix C: Important Routes

**Public Routes:**
- `/`: Homepage
- `/campaigns`: Campaign listing
- `/campaign/{slug}`: Campaign details
- `/start-project`: Campaign creation

**User Routes:**
- `/user/login`: User login
- `/user/register`: User registration
- `/user/dashboard`: User dashboard
- `/user/campaign`: Campaign management

**Admin Routes:**
- `/admin`: Admin login
- `/admin/dashboard`: Admin dashboard
- `/admin/campaigns`: Campaign management
- `/admin/users`: User management

**API Routes:**
- `/api/campaigns`: Campaign API
- `/api/categories`: Category API

**IPN Routes:**
- `/ipn/{gateway}`: Payment gateway webhooks

---

## Document Revision History

| Version | Date | Changes | Author |
|---------|------|---------|--------|
| 1.0 | January 2025 | Initial comprehensive documentation | Technical Documentation Team |

---

**End of Documentation**

For technical support or questions, please refer to the project repository or contact the development team.

