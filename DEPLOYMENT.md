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

### **CRITICAL: PHP Version Mismatch Error**
If you see errors like `Root composer.json requires php ^8.2 but your php version (7.4.33)...`, your server is using an old PHP version by default.

**Solution:**
1.  **Check available versions**: Run `ls /usr/bin/php*` or `which php8.2`.
2.  **Use the correct executable**: Use the full path to php 8.2+ for composer commands.
    ```bash
    # Example using specific PHP version
    /usr/bin/php8.2 /usr/local/bin/composer install
    ```
3.  **Change default version**:
    - **cPanel**: Go to "MultiPHP Manager" and set the domain to PHP 8.2 or 8.3.
    - **Ubuntu/Debian**: `sudo update-alternatives --set php /usr/bin/php8.2`
    - **Alias**: Add `alias php='/usr/bin/php8.2'` to your shell profile.

### **CRITICAL: Missing PHP Extensions (ext-fileinfo)**
If you see errors like `requires ext-fileinfo * -> it is missing from your system`, your PHP installation is missing required extensions.

**Solution:**
1.  **cPanel / Shared Hosting**:
    - Go to **"Select PHP Version"** (or MultiPHP Manager).
    - Ensure your version is set to 8.2 or higher.
    - Click on the **"Extensions"** tab.
    - Check/Enable the `fileinfo` box.
    - Also ensure `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, and `ctype` are enabled.

2.  **VPS / Ubuntu**:
    ```bash
    sudo apt-get install php8.2-fileinfo
    ```

3.  **Temporary Workaround (Not Recommended for Production)**:
    If you cannot enable it immediately, you can try ignoring the requirement during install:
    ```bash
    composer install --optimize-autoloader --no-dev --ignore-platform-req=ext-fileinfo
    ```

### **Permission Issues (403 Forbidden / "User does not have right roles")**

If you can login but cannot see the sidebar or access pages:

1.  **Clear Permission Cache**:
    ```bash
    php artisan permission:cache-reset
    ```

2.  **Verify User Roles**:
    Run `php artisan tinker` and check if the user has the role:
    ```php
    $user = App\Models\User::where('email', 'admin@example.com')->first();
    $user->getRoleNames(); // Should output ["admin"]
    ```

3.  **Re-run Seeder**:
    If the role is missing, re-run the seeder:
    ```bash
    php artisan db:seed --class=RoleAndPermissionSeeder --force
    ```

4.  **Manually Assign Role (Emergency)**:
    If the seeder doesn't work, assign it manually in tinker:
    ```php
    $user = App\Models\User::where('email', 'your_email@example.com')->first();
    $user->assignRole('admin');
    exit
    ```

### **Wrong Dashboard URL (403 Forbidden)**

**Critical Note:** This system has separate dashboards for Admins and Users.
- **Admins** must use: `/admin/dashboard`
  - Requires `admin` role.
  - If you go to `/dashboard` as an admin, you **will** get a 403 error (unless you also have the 'user' role).
- **Standard Users** must use: `/dashboard`
  - Requires `user` role.

**Diagnose with "Role Checker" Script**:
Run this in `php artisan tinker` to see exactly what roles your user has:
```php
$u = App\Models\User::where('email', 'admin@example.com')->first();
echo "Roles: " . implode(', ', $u->getRoleNames()->toArray()) . "\n";
echo "Permissions: " . implode(', ', $u->getAllPermissions()->pluck('name')->toArray()) . "\n";
```

- **500 Server Error**: Check `storage/logs/laravel.log` and verify permissions.
- **Assets 404**: Ensure `npm run build` was run and the `public/build` directory exists.
- **Database Error**: Ensure the database file (SQLite) or connection (MySQL) is correct and migrations ran.

## 9. Automated Deployment via cPanel

This project includes a script (`cpanel_deploy.sh`) and a webhook (`public/deploy.php`) to automate updates from GitHub.

### Setup Instructions

1.  **Configure Deployment Key**:
    - Add a secret key to your `.env` file (on the server):
      ```ini
      DEPLOY_KEY="your-secret-deploy-key-here"
      ```

2.  **Setup GitHub Webhook**:
    - Go to your GitHub Repository -> **Settings** -> **Webhooks**.
    - Click **Add webhook**.
    - **Payload URL**: `https://your-domain.com/deploy.php?key=your-secret-deploy-key-here`
    - **Content type**: `application/json` (or just leave default, the script doesn't parse the body).
    - **Events**: Just the `push` event.
    - Click **Add webhook**.

3.  **Manual Trigger**:
    - You can also trigger a deployment manually by visiting the URL in your browser:
      `https://your-domain.com/deploy.php?key=your-secret-deploy-key-here`

### How it Works
- The `public/deploy.php` script receives the request and verifies the `key`.
- It executes `cpanel_deploy.sh` which:
    - Pulls the latest code from GitHub.
    - Installs dependencies (`composer install`).
    - Runs migrations (`php artisan migrate`).
    - Clears caches.
    - Maintenance mode is handled automatically.

