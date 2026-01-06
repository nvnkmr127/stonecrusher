#!/bin/bash

# Stone Crusher ERP - cPanel Deployment Script
# This script automates the common deployment steps for a Laravel application on cPanel.

set -e

echo "🚀 Starting Deployment..."

# 1. Check for .env file
if [ ! -f .env ]; then
    echo "⚠️  .env file not found. Copying .env.example..."
    cp .env.example .env
    echo "❗ Please update your .env file with production settings (DB, URL, etc.) before proceeding if you haven't already."
fi

# 2. Install/Update Dependencies
echo "📦 Installing PHP dependencies..."
# Trying to detect the best PHP version or fallback to default 'php'
if command -v php82 &> /dev/null; then
    PHP_BIN="php82"
elif command -v /usr/local/bin/ea-php83 &> /dev/null; then
    PHP_BIN="/usr/local/bin/ea-php83"
elif command -v /usr/local/bin/ea-php82 &> /dev/null; then
    PHP_BIN="/usr/local/bin/ea-php82"
else
    PHP_BIN="php"
fi

$PHP_BIN /usr/local/bin/composer install --optimize-autoloader --no-dev

# 3. Generate App Key if missing
if ! grep -q "APP_KEY=base64" .env; then
    echo "🔑 Generating Application Key..."
    $PHP_BIN artisan key:generate
fi

# 4. Database Setup (SQLite)
if [ ! -f database/database.sqlite ]; then
    echo "🗄️  Creating SQLite database..."
    touch database/database.sqlite
fi

echo "🔄 Running migrations..."
$PHP_BIN artisan migrate --force

echo "🌱 Seeding initial data..."
$PHP_BIN artisan db:seed --class=RoleAndPermissionSeeder --force

# 5. Production Optimizations
echo "⚡ Optimizing for production..."
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan storage:link

# 6. Final Permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment Complete!"
echo "🌐 Your application is ready. Ensure your Document Root points to the 'public' folder."
