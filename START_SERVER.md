# Server Start Guide

## Issue: Connection Refused Error

Agar aapko `ERR_CONNECTION_REFUSED` error aa raha hai, to yeh steps follow karein:

### Step 1: XAMPP Start Karein

1. **XAMPP Control Panel** kholen
2. **MySQL** ko **Start** karein
3. **Apache** (agar needed ho) ko **Start** karein

Ya terminal se:

```bash
# XAMPP directory mein jao
cd /Applications/XAMPP/xamppfiles

# MySQL start karein
./xampp startmysql

# Apache start karein (agar needed)
./xampp startapache
```

### Step 2: Laravel Server Start Karein

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund

# Server start karein
php artisan serve --host=192.168.1.34 --port=8000
```

Ya existing script use karein:

```bash
./run_server
```

### Step 3: Database Connection Check Karein

`.env` file mein database settings check karein:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

### Quick Fix Commands:

```bash
# MySQL status check
/Applications/XAMPP/xamppfiles/bin/mysql.server status

# MySQL start (agar permission issue ho to sudo use karein)
sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start

# Laravel server start
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund
php artisan serve --host=192.168.1.34 --port=8000
```

### Common Issues:

1. **MySQL PID file exists but server not running:**
   ```bash
   rm -f /Applications/XAMPP/xamppfiles/var/mysql/*.pid
   /Applications/XAMPP/xamppfiles/xampp startmysql
   ```

2. **Port 8000 already in use:**
   ```bash
   lsof -i :8000
   # Process kill karein agar needed
   kill -9 <PID>
   ```

3. **Database connection refused:**
   - XAMPP Control Panel se MySQL start karein
   - `.env` file mein database credentials check karein