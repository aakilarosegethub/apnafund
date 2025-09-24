# ApnaFund Application - Complete Serving Guide

## Overview
This guide provides step-by-step instructions to serve your ApnaFund Laravel application. Follow these instructions every time you want to start the application.

## Prerequisites
- XAMPP installed and running
- PHP 8.2+ (included with XAMPP)
- Composer installed
- Node.js and npm installed

## Quick Start (One-Command Solution)

### Option 1: Using the existing serve script
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund
chmod +x serve
./serve
```

### Option 2: Direct Laravel serve command
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000
```

## Complete Setup Process

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service (if using MySQL database)

### Step 2: Navigate to Project Directory
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund
```

### Step 3: Install Dependencies (if not already installed)
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 4: Environment Setup
1. Copy environment file (if .env doesn't exist):
```bash
cp .env.example .env
```

2. Generate application key:
```bash
/Applications/XAMPP/xamppfiles/bin/php artisan key:generate
```

3. Create database (if using SQLite):
```bash
touch database/database.sqlite
```

### Step 5: Database Migration
```bash
/Applications/XAMPP/xamppfiles/bin/php artisan migrate
```

### Step 6: Build Assets (for production)
```bash
npm run build
```

### Step 7: Start the Application
```bash
# Using the serve script
./serve

# OR using direct command
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000
```

## Access Points

### Main Application
- **URL**: http://localhost:8000
- **URL**: http://0.0.0.0:8000 (accessible from other devices on network)

### Admin Panel
- **URL**: http://localhost:8000/admin
- **URL**: http://0.0.0.0:8000/admin

### Public Assets
- **URL**: http://localhost:8000/public/apnafund/
- **URL**: http://0.0.0.0:8000/public/apnafund/

## Development vs Production

### Development Mode
```bash
# Start with auto-reload
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000

# In another terminal, watch for asset changes
npm run dev
```

### Production Mode
```bash
# Build assets first
npm run build

# Start server
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000
```

## Troubleshooting

### Common Issues

1. **Port 8000 already in use**
   ```bash
   # Use different port
   /Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8001
   ```

2. **Permission denied on serve script**
   ```bash
   chmod +x serve
   ```

3. **Composer dependencies missing**
   ```bash
   composer install --no-dev
   ```

4. **Node modules missing**
   ```bash
   npm install
   ```

5. **Database connection issues**
   - Check if MySQL is running in XAMPP
   - Verify database credentials in .env file
   - Run migrations: `php artisan migrate`

### Clear Cache (if needed)
```bash
/Applications/XAMPP/xamppfiles/bin/php artisan cache:clear
/Applications/XAMPP/xamppfiles/bin/php artisan config:clear
/Applications/XAMPP/xamppfiles/bin/php artisan route:clear
/Applications/XAMPP/xamppfiles/bin/php artisan view:clear
```

## File Structure Reference

```
apnafund/
├── serve                 # Main serve script
├── serve.sh             # Alternative serve script
├── serve.bat            # Windows serve script
├── serve.php            # PHP serve script
├── artisan              # Laravel artisan command
├── composer.json        # PHP dependencies
├── package.json         # Node.js dependencies
├── .env                 # Environment configuration
├── public/              # Web root
│   ├── index.php        # Entry point
│   └── apnafund/        # Public assets
├── app/                 # Application code
├── resources/           # Views, assets
├── routes/              # Route definitions
└── database/            # Database files
```

## Quick Commands Summary

```bash
# Navigate to project
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund

# Quick serve (recommended)
./serve

# Alternative serve methods
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000
php artisan serve
npm run dev  # For asset watching
npm run build  # For production build
```

## Notes
- The application uses Laravel 11 with PHP 8.2+
- SQLite database is configured by default
- Assets are built with Vite
- The application includes payment gateways (Stripe, Razorpay, etc.)
- Firebase integration is available for OTP
- YouTube integration is configured

## Support
If you encounter any issues, check:
1. XAMPP services are running
2. All dependencies are installed
3. Database is properly configured
4. Environment variables are set correctly
5. File permissions are correct

---
**Last Updated**: $(date)
**Application**: ApnaFund Laravel Application
**Version**: Laravel 11.x 
