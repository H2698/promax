# Deployment on Vercel

Promax is a Laravel 12 application. The Vercel project uses the community PHP 8.4
runtime (`vercel-php@0.8.0`), Node.js 22, a Neon PostgreSQL database, and a public
Vercel Blob store for product and category images.

## Project configuration

`vercel.json` sets the framework to Other and defines the build and routing.
Vite builds the frontend; `scripts/vercel-static.mjs` copies only public assets
to `dist`. PHP files are excluded from static output. The PHP function retains
the Laravel source and `public/build/manifest.json`.

`api/index.php` initializes disposable view and package caches in `/tmp`.
Sessions and application cache must use the database so they survive restarts.
Uploaded images are stored in Vercel Blob, not the function filesystem.

## Environment variables

Set these for production and preview in the Vercel project settings:

| Variable | Value |
| --- | --- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | A persistent Laravel key, stored as a sensitive variable |
| `APP_URL` | The production HTTPS URL |
| `APP_LOCALE`, `APP_FALLBACK_LOCALE` | `fr` |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | Supplied by the connected Neon integration |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER`, `CACHE_STORE` | `database` |
| `SESSION_SECURE_COOKIE`, `SESSION_ENCRYPT` | `true` |
| `QUEUE_CONNECTION` | `sync` |
| `LOG_CHANNEL` | `stderr` |
| `UPLOADS_DRIVER` | `vercel-blob` |
| `BLOB_READ_WRITE_TOKEN` | Supplied by the connected public Blob store |

Configure a mail provider before enabling outbound email. The initial deployment
uses the `log` mailer. A custom domain requires updating `APP_URL` and redeploying.

## Initialize a new database

Run migrations from a trusted local terminal with the production database
connection and the PHP PostgreSQL extension enabled:

```sh
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=SettingSeeder --force
php artisan db:seed --class=AdminSeeder --force
```

The administrator seeder requires a valid `ADMIN_EMAIL` and an initial
`ADMIN_PASSWORD` of at least 12 characters. It does not reset an existing
administrator's password. These initialization values need not remain on Vercel.
The production setup leaves the product catalog empty; `ProductSeeder` contains
demonstration products and should only be used deliberately.

Do not run migrations or seeders on every HTTP request or build. Apply future
schema changes deliberately before deploying code that needs them.

## Validation

```sh
npm ci
npm run build
node scripts/vercel-static.mjs
composer install
php vendor/bin/phpunit
```

After deployment, check `/up`, `/`, `/boutique`, `/cart`, `/admin/login`, the
administrator login, and an image upload. No `.env` files or database exports
should be committed or uploaded as static content.
