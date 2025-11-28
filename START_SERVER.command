#!/bin/bash

# Change to project directory
cd "$(dirname "$0")"

# Clear any existing server on port 8000
lsof -ti:8000 | xargs kill -9 2>/dev/null

echo "🚀 Starting ApnaFund Server..."
echo "📁 Directory: $(pwd)"
echo "🌐 Server will run on: http://0.0.0.0:8000"
echo "=========================================="
echo ""

# Start the server
/Applications/XAMPP/xamppfiles/bin/php artisan serve --host=0.0.0.0 --port=8000

