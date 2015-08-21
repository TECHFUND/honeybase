# unit test
- `vendor/bin/phpunit`

## About AccessorParser test strategy
- In simple, it is a "configuration file test"
- Normal, abnormal, typo, wrong name, wrong character, several configuration files are executed
- Parser should behave as gentleman.

## ってか
- サーバー起動時にaccessorをparseしてエラー出したらもっと使いやすいよね
- 通信があるごとにjsonを読む設計がよくない。起動時に読んで持っとくべき
- でも今はやらないでおこう