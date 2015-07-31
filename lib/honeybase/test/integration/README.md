# Integration test

## Usage
- `gulp`
- `http://localhost:8001`

## Checking Behavior

### Context `all`
- JavaScript SDK initialized?
- Auth function called?
- DB function returned?
- HB function returned?
- Query function returned?
- PubSub function returned?

### Context `sample-all`
- DB function returned?
- HB function returned?
- ↑call as "functions"

### Context `no path`, `no table`, `no action`, `not mathced role`, `not mathced role`
- Functions returns correct error message?

### Context `typo path`, `typo table`, `typo action`, `typo role`, `typo role`
- Functions returns correct error message?

## Not Checking Behavior
- Auth function returned?
- Auth function validated by accessor?
- Is HTTPS secure?
- Context `sample-login`
- Context `sample-owner`
- Context `sample-original`
