# Deployment Guide

## Production Environment Setup

### Server Requirements

- PHP 8.4+ with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD (for PDF)
- Composer 2.x
- Node.js 18+ and npm (build step only)
- MySQL 8.0+
- Web server: Apache or Nginx

### Step 1: Clone and Install

```bash
git clone <repository-url> /var/www/comm-calc
cd /var/www/comm-calc

composer install --no-dev --optimize-autoloader
npm install && npm run build
```

### Step 2: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for production:

```env
APP_NAME="Bayside Pavers"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sales.baysidepavers.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=comm-calc
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
BCRYPT_ROUNDS=12
```

### Step 3: Database

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Step 4: Storage and Permissions

```bash
php artisan storage:link

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 5: Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
```

### Step 6: Web Server Config

**Apache** (`.htaccess` included in `public/`):
- Point your virtual host document root to `/var/www/comm-calc/public`
- Ensure `mod_rewrite` is enabled

**Nginx**:
```nginx
server {
    listen 80;
    server_name sales.baysidepavers.com;
    root /var/www/comm-calc/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Updating

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Backups

- **Database**: Schedule `mysqldump comm-calc > backup.sql` daily
- **Uploads**: Back up `storage/app/public/branding/` directory
- **Environment**: Keep `.env` backed up securely (never in git)

## Monitoring

- Check `storage/logs/laravel.log` for application errors
- Monitor disk space for `storage/` directory
- Review Audit Logs in the admin panel for suspicious activity
