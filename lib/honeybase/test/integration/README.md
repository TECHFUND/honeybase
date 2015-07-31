# Integration test

## Usage
- `gulp`
- `http://localhost:8000`

## Checking Behavior
- JavaScript SDK initialized?
- Auth function called?
- DB function returned?
- Query function returned?
- PubSub function returned?
- Accessor works in particular condition?

## Not Checking Behavior
- Accessor works in almost all condition?
- Auth function returned?
- Is HTTPS secure?

## Next features
- Temporary `test` environment & test accessor needed
- Various accessor definition should be checked in unit test (DI)
