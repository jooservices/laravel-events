# Configuration

## Config File

After publishing:

```bash
php artisan vendor:publish --tag=laravel-events-config
```

the file is `config/events.php`.

## Options Reference

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `connection` | string | `'mongodb'` | MongoDB connection name from `config/database.php` |
| `context_provider` | callable\|null | `null` | Callable returning an array merged into EventSourcing `metadata` and EventLog `meta` (e.g. request_id, correlation_id, source, channel) |
| `eventsourcing.enabled` | bool | `true` | Enable EventSourcing subscriber |
| `eventsourcing.collection` | string | `stored_events` | MongoDB collection for stored events |
| `eventsourcing.ttl_days` | int\|null | `null` | TTL in days; documents older than this are removed. `null` = no TTL |
| `event_log.enabled` | bool | `true` | Enable EventLog subscriber |
| `event_log.collection` | string | `event_logs` | MongoDB collection for event log entries |
| `event_log.ttl_days` | int\|null | `null` | TTL in days for event_logs; `null` = no TTL |

## Environment Variables

| Variable | Used as | Example |
|----------|---------|---------|
| `EVENTS_EVENTSOURCING_ENABLED` | `eventsourcing.enabled` | `true` |
| `EVENTS_STORED_EVENTS_COLLECTION` | `eventsourcing.collection` | `stored_events` |
| `EVENTS_EVENTSOURCING_TTL_DAYS` | `eventsourcing.ttl_days` | `90` or empty |
| `EVENTS_EVENT_LOG_ENABLED` | `event_log.enabled` | `true` |
| `EVENTS_EVENT_LOGS_COLLECTION` | `event_log.collection` | `event_logs` |
| `EVENTS_EVENT_LOG_TTL_DAYS` | `event_log.ttl_days` | `365` or empty |

## Context Provider

Set `context_provider` to a callable that returns an array. That array is merged into:

- EventSourcing: `metadata` when storing an event
- EventLog: `meta` when storing a change

Example (e.g. in `AppServiceProvider`):

```php
use JooServices\LaravelEvents\Support\EventMetadata;

config([
    'events.context_provider' => function () {
        return EventMetadata::merge(
            EventMetadata::trace(request()->header('X-Request-ID')),
            EventMetadata::source(config('app.name'), request()->route() ? 'web' : 'cli'),
        );
    },
]);
```

Return `[]` or set to `null` to disable.

See [Metadata, Versioning, and Corrections](./metadata.md) for recommended keys.
