# Skill Shared

# class map
- `/app/Http/route.php` and `config/accessor.json` is all basis.
- `/app/Http/Controller/V1Controller.php` is doing all view's handling.
- `/app/Http/Middleware`'s filters are security logics.
- `/resources/views/v1`'s `.php` files are all of this MVP's view. JS logic is also there.
- JS library including honeybase is under the `/public/assets/js`.
- `/config/config.json` and `/public/assets/config.json.php` are secret files.

# state
- `/resources/views/v1/index.php` should be integrated at first.


---

## setup
- install composer & lumen in [here](http://lumen.laravel.com/docs/installation#install-composer)
- `composer update`
- Tweak `public/assets/config` `config/config` `config/main` `config/accessor`

## test
- `vendor/bin/phpunit`

## serve
- `php artisan serve`

## deploy
`sudo php artisan serve --host 0.0.0.0 --port 80 --env staging`
or
use some http server

## sample
- see `sample/index.html`

### insert
`curl -XPOST 'http://localhost:8000/api/v1/db/push' -d 'table=users_tbl&value={"name":"Shogo", "age": 24, "job":"engineer", "address": "Setagaya"}'`
### update
`curl -XPOST 'http://localhost:8000/api/v1/db/update' -d 'table=users_tbl' -d 'id=9' -d 'value={"name":"Peaske","age":"27","job":"Designer","address":"Shibuya"}'`
### delete
`curl -XPOST 'http://localhost:8000/api/v1/db/delete' -d 'table=users_tbl' -d 'id=9''`
### select
`curl -XGET 'http://localhost:8000/api/v1/db/select' -d 'table=users_tbl' -d 'value={"age":"24"}''`


### log check
- `tail -f storage/logs/lumen.log`

## development roadmap status

### finished
- insert
- update
- delete
- select
- auth(facebook)
- signup
- signin
- email_verify
- current_user
- logout
- uploader
- mailer
- login checker
- template engine integration
- pubsub(use redis)

### not finished
- SSL (use tinycert)
