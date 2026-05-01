# TouchBase — Hostinger Deployment Guide

## Step 1: Fresh Laravel skeleton

On your local machine (where PHP + Composer are installed):

```bash
composer create-project laravel/laravel touchbase-temp
```

Copy everything from that skeleton **except** the files below into your TouchBase folder:
- `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `tests/`
- `artisan`, `composer.json`, `composer.lock`, `package.json`, `.gitignore`

Then **overwrite** with the custom files from this repo.

## Step 2: Install dependencies

```bash
composer install --no-dev --optimize-autoloader
```

## Step 3: Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` — set your Hostinger MySQL credentials.

## Step 4: Run migrations

```bash
php artisan migrate
```

## Step 5: Upload to Hostinger

Upload everything **except** `node_modules` and `.git` to Hostinger via:
- File Manager (zip upload), or
- FTP (FileZilla), or
- Git pull (if SSH access available)

Point your domain's **Document Root** to the `public/` subfolder inside your uploaded directory.

## Step 6: Storage symlink

```bash
php artisan storage:link
```

If you can't run Artisan on Hostinger, manually create a symlink in cPanel → File Manager:
`public/storage` → `../storage/app/public`

## Step 7: File permissions

```bash
chmod -R 775 storage bootstrap/cache
```

## Step 8: Production optimise (optional but recommended)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Shared Hosting Notes

| Concern | Solution |
|---|---|
| No cron jobs needed | Reminders checked on dashboard load + manual button |
| No Node.js | Pure Blade + vanilla JS, zero npm |
| MySQL | Standard shared hosting MySQL |
| Sessions | File driver (no Redis needed) |
| Cache | File driver (no Redis needed) |
| Queues | Sync driver (no worker process needed) |

---

## Database

Create a MySQL database in Hostinger control panel, then set credentials in `.env`.

```
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```
