#!/bin/bash

# ApnaCrowdfunding Development Server Launcher
# Quick serve script for Laravel project

echo "🚀 Starting ApnaCrowdfunding Development Server..."
echo "📁 Project Directory: $(pwd)"
echo "🌐 Server URL: http://0.0.0.0:8000"
echo "⏰ Started at: $(date)"
echo "=================================================="

# Check if artisan exists
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found."
    echo "Make sure you're in the Laravel project directory."
    exit 1
fi

echo "🔄 Executing: php artisan serve"
echo "=================================================="
echo ""

# Start the Laravel development server with XAMPP PHP
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000
