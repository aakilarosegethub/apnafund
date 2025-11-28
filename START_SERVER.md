# Server Start Karne Ke Liye

## Quick Start Command:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000
```

## Ya Phir Script Use Karo:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund
chmod +x serve.sh
./serve.sh
```

## Server URLs:
- http://localhost:8000
- http://0.0.0.0:8000
- http://127.0.0.1:8000

## Background Mein Run Karne Ke Liye:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/apnafund
nohup /Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000 > server.log 2>&1 &
```

## Server Stop Karne Ke Liye:

```bash
pkill -f "artisan serve"
```

