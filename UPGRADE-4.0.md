# Upgrade to v4.0.0

`v4.0.0` is a **breaking** minor-line jump from `1.x` (correctness + JOOservices
quality floor). There is no shim for enum/const or subclassing of newly `final`
types.

## Requirements

- PHP `^8.5`
- Laravel `^12.0|^13.0`
- `mongodb/laravel-mongodb` `^5.7`
- `jooservices/dto` `^3.2`
- `jooservices/exceptions` `^4.0`
- `psr/clock` `^1.0`, `psr/log` `^3.0` (direct requires)

## Steps

```bash
composer require jooservices/laravel-events:^4.0
php artisan vendor:publish --tag=laravel-events-config --force   # if you customize config
php artisan events:install-indexes
```

## Call-site changes

### Enums

```php
// Before (1.x constant bags)
return EventLogAction::UPDATED;
$category = EventCategory::DOMAIN;

// After (4.x string-backed enums)
return EventLogAction::UPDATED->value;
$category = EventCategory::DOMAIN->value;
```

`EventLogAction::all()` / `EventCategory::all()` still return string lists.

### Final classes

Do not extend `EventService`, subscribers, query services, or `ArrayEventSerializer`.
For tests, mock `EventPersisterInterface` (bound to `EventService` in the provider).

### Removed bag DTOs

`EventDiffData` and `EventMetadataData` are gone. Keep using arrays on
`EventLogData::$diff` / `$meta` and `StoredEventData::$metadata`.

## Operations (unchanged app responsibilities)

- Prefer `ShouldDispatchAfterCommit` so Mongo appends do not outlive a rolled-back SQL transaction.
- On `ShouldQueue` events, embed `user_id` / correlation — workers have no `auth()` / `request()`.
- This remains a persistence helper, not a full event store (no stream version / outbox / replay).

See [Operations](docs/02-user-guide/08-operations.md) and [CHANGELOG](CHANGELOG.md).
