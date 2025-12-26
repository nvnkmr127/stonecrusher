# Deployment Guide - Stone Crusher ERP

This guide provides step-by-step instructions for deploying the Stone Crusher ERP application to a production server.

## 1. Server Requirements

Ensure your server meets the following requirements:
- **PHP**: >= 8.2
- **Database**: SQLite (default) or MySQL/PostgreSQL
- **Web Server**: Nginx or Apache
- **Composer**: Latest version
- **Node.js**: >= 18.x (for building assets)

## 2. Initial Setup

### Clone Repository
```bash
git clone <repository_url> stonecrusher-erp
cd stonecrusher-erp
```

### Install Dependencies
```bash
# Install PHP dependencies optimized for production
composer install --optimize-autoloader --no-dev

# Install JavaScript dependencies
npm install
```

## 3. Configuration

### Environment Setup
Copy the example environment file:
```bash
cp .env.example .env
```

Edit the `.env` file with your production settings:
```ini
APP_NAME="Stone Crusher ERP"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (Default is SQLite, change for MySQL)
DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=stonecrusher
# DB_USERNAME=forge
# DB_PASSWORD=secret

# Optional: Google Maps API for distance calculation
GOOGLE_MAPS_API_KEY=your_api_key_here
```

### Application Key
Generate a unique application key:
```bash
php artisan key:generate
```

## 4. Database Setup

### Create SQLite Database (if using SQLite)
```bash
touch database/database.sqlite
```

### Run Migrations
Run the database migrations to create the schema:
```bash
php artisan migrate --force
```

### Seed Default Data (Required)
Seed the database with initial roles, permissions, and admin user:
```bash
php artisan db:seed --class=RoleAndPermissionSeeder --force
# Optional: Seed other master data if needed
# php artisan db:seed --force
```
> **Note**: The default admin login is `admin@example.com` / `password`. Change this immediately after login!

## 5. Build Assets

Compile the CSS and JavaScript assets for production:
```bash
npm run build
```

## 6. Permissions

Ensure the web server has write access to storage and cache directories:
```bash
# Assuming standard Linux permissions (www-data group)
chown -R :www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## 7. Web Server Configuration (Nginx Example)

Create a configuration block for your site:

```nginx
server {
    listen 80;
    server_name example.com;
    root /var/www/stonecrusher-erp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 8. Optimization (Production Only)

Once deployed, run these commands to optimize performance:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Troubleshooting

- **500 Server Error**: Check `storage/logs/laravel.log` and verify permissions.
- **Assets 404**: Ensure `npm run build` was run and the `public/build` directory exists.
- **Database Error**: Ensure the database file (SQLite) or connection (MySQL) is correct and migrations ran.
