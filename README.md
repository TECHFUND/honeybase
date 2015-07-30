# HoneyBase

# Class Map

## Definition of All
- `/app/routes.php`, `public/js/accessor.json`, `database/migrations/`

## Backend Model and Controller
- `/app/Controller`, `/app/Model`, `/app/Middleware`

## Frontend
- `public/views` is PHP views
- `public/js` is front logic
- `public/assets/lib` is `honeybase`'s JavaScript SDK

## Configuration
- `/config/config.json`
- `/public/assets/honeybase_config.json.php`
- `/config/constants.php`
- `/config/mail.json`

---

## Setup
- Install composer & lumen in [here](http://lumen.laravel.com/docs/installation#install-composer)
- `composer update`
- Add `'Lib\\' => array($baseDir . '/lib'),` `'Util\\' => array($baseDir . '/util'),` lines inside of  `/vendor/composer/autoload_psr4.php`
- Rewrite `laravel/lumen-framework/config/view.php`'s `paths` to `base_path('public/views')`
- Tweak `public/assets/config` `config/config`
- Start coding from `app/routes.php`, `app/accessor.json`, `database/migrations`

## Test
- `vendor/bin/phpunit`

## Serve
- `php artisan serve`

## Deploy
`sudo php artisan serve --host 0.0.0.0 --port 80 --env staging`
or
ref `/public/index.php` by httpd

### Log check
- `tail -f storage/logs/lumen.log`

### Next features
- Thiking about the suitable dir of js files
- SSL (use tinycert)
- Payment(Strip/Webpay)
- Node SDK
- iOS/Android SDK
