# Integration test

## Usage
- `gulp`
- `http://localhost:8001`

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
