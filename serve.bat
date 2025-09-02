@echo off
title ApnaFund Development Server
echo ==========================================
echo    ApnaFund Development Server Launcher
echo ==========================================
echo.
echo ^🚀 Starting Laravel Development Server...
echo ^📁 Project Directory: %CD%
echo ^🌐 Server URL: http://localhost:8000
echo ^⏰ Started at: %date% %time%
echo.
echo ==========================================
echo.

REM Check if artisan exists
if not exist artisan (
    echo ❌ Error: artisan file not found.
    echo Make sure you're in the Laravel project directory.
    pause
    exit /b 1
)

echo ^🔄 Executing: php artisan serve
echo ==========================================
echo.

REM Start the Laravel development server with XAMPP PHP
C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000

pause
