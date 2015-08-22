# Integration test

## Usage
- `gulp`
- `php artisan serve`
- `http://localhost:8001`
- (originとかportとかの問題でintegration test動いてないから動くようにする)
- (gulpするとlib/honeybaseは自動的に最新版が上書きされる)
- (gulpでhtmlとサーバーのドメインが異なってしまっている時点でテスト環境の設計に無理があるっぽい。origins.jsonのドメイン不一致をケアしても今度はdbの設定が邪魔してくる。)
- (要するに、テスト環境はlocalhost:8000でホストされているべき)
- (この問題、前にも悪戦苦道してたなあ・・・)
- (なぜlocalhost:8000のルーティングでホストしたらダメなんだったっけ。)
- (PHP的に「なんか美しくないから」という理由だったような。あとビルドシステムでサーブしないのも美しくない。)

## Required Behavior

### Context `all`
- JavaScript SDK initialized?
- Auth function called?
- DataBase&HoneyBase function returned? <- `call as "functions"`
- Query function returned?
- PubSub function returned?

### Context `sample-all`
- Functions returns correct error message?

### Context `no-path`, `no-table`, `no-action`, `not-matched-role`
- Functions returns correct error message?

### Context `typo-path`, `typo-table`, `typo-action`, `typo-role`
- Functions returns correct error message?

## Not Covered Behavior
### Web Driver
- Auth function returned?
- Auth function validated by accessor?
- Is HTTPS secure?
- Context `sample-login`
- Context `sample-owner`
- Context `sample-original`
