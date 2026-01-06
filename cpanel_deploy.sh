#!/bin/bash
set -e

# Stone Crusher ERP - cPanel Deployment Script
# Optimized for shared hosting environments

# --- Configuration ---
BUILD_ASSETS=false # We built assets locally, so skip this on server

log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

log "� Starting cPanel Deployment..."

# 1. Detect PHP
log "🔍 Detecting PHP..."
if command -v php83 &> /dev/null; then PHP_BIN="php83"
elif command -v php82 &> /dev/null; then PHP_BIN="php82"
elif command -v /usr/local/bin/ea-php83 &> /dev/null; then PHP_BIN="/usr/local/bin/ea-php83"
elif command -v /usr/local/bin/ea-php82 &> /dev/null; then PHP_BIN="/usr/local/bin/ea-php82"
else PHP_BIN="php"; fi

log "🐘 Using PHP: $($PHP_BIN -v | head -n 1)"

# 2. Update Code from Git (Force Sync)
if [ -d ".git" ]; then
    # Detect default branch (main or master)
    BRANCH=$(git remote show origin | grep 'HEAD branch' | cut -d' ' -f5)
    [ -z "$BRANCH" ] && BRANCH="main"
    
    log "📥 Fetching latest code from Git (Branch: $BRANCH)..."
    git fetch origin "$BRANCH"
    
    log "🧹 Cleaning up local changes and syncing with origin/$BRANCH..."
    # This ensures the server version exactly matches the repo
    git reset --hard "origin/$BRANCH"
    
    log "✨ Git sync complete. Current version: $(git log -1 --format='%h - %s')"
else
    log "ℹ️  Not a git repository. Skipping git pull."
    log "⚠️  To enable auto-pull, clone the repo instead of uploading zip: git clone <url> ."
fi

# 2.5 Force Clear Route Cache
# This helps if previous deployments left a stale cache
log "🗑️  Clearing old route cache..."
$PHP_BIN artisan route:clear || true

# 3. --- CRITICAL: Clear Bootstrap Cache ---
# This fixes "Class not found" errors for dev packages like Laravel\Pail
log "🧹 Clearing bootstrap discovery cache..."
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes.php

# 3. Detect/Setup Composer
log "📦 Setting up Composer..."
COMPOSER_BIN=""
if command -v composer &> /dev/null && composer --version &> /dev/null; then
    COMPOSER_BIN="composer"
elif [ -f composer.phar ] && $PHP_BIN composer.phar --version &> /dev/null; then
    COMPOSER_BIN="$PHP_BIN composer.phar"
else
    log "📥 Downloading local composer.phar..."
    $PHP_BIN -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    $PHP_BIN composer-setup.php --quiet
    $PHP_BIN -r "unlink('composer-setup.php');"
    COMPOSER_BIN="$PHP_BIN composer.phar"
fi

# 4. Install Dependencies
log "🚀 Installing dependencies..."
$COMPOSER_BIN install --optimize-autoloader --no-dev --no-scripts --ignore-platform-reqs

# 5. Application Setup
log "🏗️  Configuring application..."

# Detect Laravel Version for Maintenance Mode
# Using a simpler grep for compatibility
IS_L11=$($PHP_BIN artisan --version | grep "11." || true)
if [ -n "$IS_L11" ]; then
    DOWN_CMD="app:down"
    UP_CMD="app:up"
else
    DOWN_CMD="down"
    UP_CMD="up"
fi

$PHP_BIN artisan $DOWN_CMD || log "Application is already down."

if [ ! -f .env ]; then
    log "📄 Creating .env from template..."
    cp .env.example .env
fi

if ! grep -q "APP_KEY=base64" .env; then
    log "🔑 Generating Application Key..."
    $PHP_BIN artisan key:generate
fi

# 6. Database
if [ ! -f database/database.sqlite ]; then
    log "�️  Creating SQLite database..."
    touch database/database.sqlite
fi

log "🔄 Running migrations..."
$PHP_BIN artisan migrate --force

log "🌱 Seeding roles/permissions..."
$PHP_BIN artisan db:seed --class=RoleAndPermissionSeeder --force

# 7. Optimization
log "⚡ Optimizing application..."
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan storage:link --force

# 8. Permissions & Cleanup
log "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

log "🔓 Exiting maintenance mode..."
$PHP_BIN artisan $UP_CMD

log "✅ Deployment Complete!"
