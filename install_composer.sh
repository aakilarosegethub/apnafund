#!/bin/bash

echo "🔄 Installing/Updating Composer Dependencies..."
echo ""

cd /Applications/XAMPP/xamppfiles/htdocs/apnafund

# Try to find composer
if command -v composer &> /dev/null; then
    COMPOSER_CMD="composer"
elif [ -f "/usr/local/bin/composer" ]; then
    COMPOSER_CMD="/usr/local/bin/composer"
elif [ -f "/usr/bin/composer" ]; then
    COMPOSER_CMD="/usr/bin/composer"
elif [ -f "composer.phar" ]; then
    COMPOSER_CMD="php composer.phar"
else
    echo "❌ Composer not found!"
    echo "Please install Composer first: https://getcomposer.org/download/"
    exit 1
fi

echo "Using: $COMPOSER_CMD"
echo ""

# Install/Update dependencies
echo "Running: $COMPOSER_CMD install --no-interaction"
$COMPOSER_CMD install --no-interaction

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Composer dependencies installed successfully!"
    echo ""
    echo "Now run:"
    echo "  /Applications/XAMPP/xamppfiles/bin/php artisan config:clear"
    echo "  /Applications/XAMPP/xamppfiles/bin/php artisan cache:clear"
    echo ""
    echo "Then try: http://localhost/apnafund/"
else
    echo ""
    echo "❌ Error installing dependencies!"
    exit 1
fi

