# Skill Shared

# Class Map
- `/app/routes.php` and `app/accessor.json` is all basis.
- `/app/Controller/V1Controller.php` is doing all view's handling.
- `/app/Middleware`'s filters are security logics.
- `/app/View`'s `.php` files are all of this MVP's view. JS logic is also there.
- JS library including honeybase is under the `/public/assets/js`.
- `/config/config.json` and `/public/assets/config.json.php` are secret files.

---

## Setup
- Install composer & lumen in [here](http://lumen.laravel.com/docs/installation#install-composer)
- `composer update`
- Add `'Lib\\' => array($baseDir . '/lib'),` `'Util\\' => array($baseDir . '/util'),` lines inside of  `/vendor/composer/autoload_psr4.php`
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
- SSL (use tinycert)
- Payment(Strip/Webpay)
- Node SDK
- iOS/Android SDK
