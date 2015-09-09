# HoneyBase

---

# What's this?
HoneyBase is made for rapid prototyping. Thus, it is realy focusing on frontend development. Its backend is already implemented, and developer mainly use JavaScriptSDK (hoenybase.js). It allows you UI-first development.

At the same time, HoneyBase is a [laravel/lumen](http://lumen.laravel.com/) extended PHP framework. So, you can customize its pre-implemented backend by adding new API to its router. And if you want, you can use MVC framework functions such as ORM, Controller, Middleware, template engine, test framework and so on!

# When is it usuful?
- When you want to evaluate your business hypothesis as soon as possible.
- But when you feel the BaaS(Backend as a Service) like [parse.com](parse.com) seems little untrustable.
- Product requirements haven't decided yet and it likely to change easily.
- Product life time more than 1 month but less than 1 year. ("To develop it sloppily is scary but to develop it precisely is also scary." situation.)

# Why Lumen?
- Both `ruby` and `php` developer are able to understand that codebase. (Rails flavored architecture written in PHP)
- Not so much "Convention", easy to learn, easy to configure.
- If your project getting bigger, ride the larabel-way and write maintainable code.

# How can I use?
- Install Lumen, Composer and MySQL
- Honeybase CLI API
- Honeybase Accessor(Validator)
- Custom API implementation

# TODO
- More test coverage
- More prepared function (SSL, Payment)
- Frontend ORMapper/QueryBuilder (Better performance needed if you beyond prototyping)

---

# Architecture

Based on MVC architecture derived from laravel/lumen.

## Definition of All
- `/app/routes.php`, `app/accessor.json`, `database/migrations/`

## Custom Model and Controller
- `/app/Controller`, `/app/Model`, `/app/Middleware`

## Frontend
- `public/views` is PHP views
- `public/js` is front logic
- `public/assets/lib` is `honeybase`'s JavaScript SDK

## HoneyBase Core Library
- `lib/honeybase`

## Configuration
- `/config/config.json`
- `/public/assets/honeybase_config.json.php`
- `/config/constants.php`
- `/config/mail.json`

---

## Setup
- Install composer & lumen in [here](http://lumen.laravel.com/docs/installation#install-composer)
- `composer update`
- Add `'HoneyBase\\' => array($baseDir . '/lib/honeybase'),`, `'Util\\' => array($baseDir . '/util'),` lines inside of  `/vendor/composer/autoload_psr4.php`
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
- Conbination feature with FrontendMVW
- SSL (use tinycert)
- Payment(Strip/Webpay)
- iOS/Android SDK
