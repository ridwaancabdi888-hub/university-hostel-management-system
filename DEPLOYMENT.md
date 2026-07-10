# Deployment Guide

This guide covers deploying the University Hostel Management System to a production server, either directly or via Docker.

## Server Requirements

- PHP 8.2+ with extensions: `pdo_mysql`, `mbstring`, `gd` (for PDF generation), `zip` (for Excel export), `bcmath`, `xml`, `curl`, `fileinfo`
- MySQL 8.0+ (or MariaDB 10.6+)
- Composer 2.x
- Node.js 20+ / npm (build-time only — not needed at runtime)
- A web server (nginx or Apache) in front of PHP-FPM
- `mysqldump` on `PATH` (required by the backup system)

## Production `.env` Checklist

Copy `.env.example` to `.env` and set:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

# Required once you're serving over HTTPS — without this, session/CSRF
# cookies are not marked Secure.
SESSION_SECURE_COOKIE=true

MAIL_MAILER=smtp
MAIL_HOST=...
# ...mail credentials...

BACKUP_NOTIFICATION_EMAIL=ops@your-domain.example
# Optional: encrypts backup archives at rest.
BACKUP_ARCHIVE_PASSWORD=
```

Never commit a real `.env` file — it's already excluded via `.gitignore`, and the backup system separately excludes it from backup archives (see `config/backup.php`).

## Build & Deploy Steps

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan key:generate --force   # first deploy only
php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Re-run `config:cache`/`route:cache`/`view:cache` after every deploy (or as part of your deploy script) — stale caches are a common source of "it worked locally" bugs.

## Queue Worker & Scheduler

The app uses `QUEUE_CONNECTION=database` by default — no queued jobs run today (notifications are sent synchronously), but the scheduler must run for backups to fire. Add a single cron entry:

```cron
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

This drives the three scheduled commands registered in `routes/console.php`:

- `backup:clean` — daily at 01:00
- `backup:run` — daily at 01:30
- `backup:monitor` — daily at 02:00

If you later add queued jobs, run a persistent worker under a process supervisor (systemd or Supervisor), e.g. `php artisan queue:work --tries=3`.

## Web Server

### nginx (recommended)

```nginx
server {
    listen 80;
    server_name your-domain.example;
    root /path/to/app/public;

    add_header X-Frame-Options "SAMEORIGIN";
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

Put this behind a TLS-terminating reverse proxy (or add a `listen 443 ssl` block with a certificate from Let's Encrypt) and set `SESSION_SECURE_COOKIE=true` once HTTPS is live.

### File Permissions

`storage/` and `bootstrap/cache/` must be writable by the web server user:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

The provided `docker-compose.yml` runs `app` (PHP-FPM), `nginx`, and `mysql`. It intentionally does not include Redis — the app's cache/session/queue already run on the `database` driver, which is sufficient at this app's scale; a commented-out `redis` service is included as a documented upgrade path if you outgrow it. For a production deployment, run the scheduler (`schedule:run`) via cron on the host, or add a lightweight cron container that execs into the `app` container.

## Backup & Restore

**Manual backup:**

```bash
php artisan backup:run              # full backup (database + files)
php artisan backup:run --only-db    # database only — useful before a risky migration
php artisan backup:list             # show existing backups and their health
```

Backups land on the `local` disk under `storage/app/private/{APP_NAME}/`. For off-server durability, add `s3` to `config/backup.php`'s `destination.disks` and configure the existing `AWS_*` environment variables.

**Restore** (Spatie's backup package intentionally does not ship a restore command — it's a deliberate one-way tool, so restoring is a manual, considered action):

1. Download/locate the backup archive and extract it: `unzip 2026-01-01-00-00-00.zip -d restore/`
2. Restore the database: `mysql -u <user> -p <database> < restore/db-dumps/mysql-<connection>.sql` (or `gunzip -c ... | mysql ...` if gzip-compressed)
3. Restore any needed files from the archive into place (e.g. `storage/app/public` uploads).
4. Run `php artisan migrate --force` in case the backup predates a schema change you still need to apply.
