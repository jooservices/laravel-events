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
| `context_provider` | invokable class\|null | `null` | Config-cache-safe invokable class returning an array merged into EventSourcing `metadata` and EventLog `meta`. Closures break `config:cache`. |
| `redaction.enabled` | bool | `true` | Enable recursive masking before persistence |
| `redaction.keys` | string[] | common secret keys | Case-insensitive keys to mask |
| `redaction.replacement` | string | `'[REDACTED]'` | Replacement value |
| `retention.stored_events_days` | int\|null | `null` | Preferred TTL retention for stored events |
| `retention.event_logs_days` | int\|null | `null` | Preferred TTL retention for event logs |
| `eventsourcing.enabled` | bool | `true` | Enable EventSourcing subscriber |
| `eventsourcing.collection` | string | `stored_events` | MongoDB collection for stored events |
| `eventsourcing.ttl_days` | int\|null | `null` | Legacy TTL in days; `retention.stored_events_days` is preferred |
| `event_log.enabled` | bool | `true` | Enable EventLog subscriber |
| `event_log.collection` | string | `event_logs` | MongoDB collection for event log entries |
| `event_log.ttl_days` | int\|null | `null` | Legacy TTL in days; `retention.event_logs_days` is preferred |

## Environment Variables

| Variable | Used as | Example |
|----------|---------|---------|
| `EVENTS_EVENTSOURCING_ENABLED` | `eventsourcing.enabled` | `true` |
| `EVENTS_STORED_EVENTS_COLLECTION` | `eventsourcing.collection` | `stored_events` |
| `EVENTS_EVENTSOURCING_TTL_DAYS` | `eventsourcing.ttl_days` | `90` or empty |
| `EVENTS_STORED_EVENTS_RETENTION_DAYS` | `retention.stored_events_days` | `90` or empty |
| `EVENTS_EVENT_LOG_ENABLED` | `event_log.enabled` | `true` |
| `EVENTS_EVENT_LOGS_COLLECTION` | `event_log.collection` | `event_logs` |
| `EVENTS_EVENT_LOG_TTL_DAYS` | `event_log.ttl_days` | `365` or empty |
| `EVENTS_EVENT_LOGS_RETENTION_DAYS` | `retention.event_logs_days` | `365` or empty |
| `EVENTS_REDACTION_ENABLED` | `redaction.enabled` | `true` |

`redaction.keys` and `redaction.replacement` are configured in
`config/events.php` or application code, not through package-provided
environment variables.

## Context Provider

Set `context_provider` to an **invokable class** (or other config:cache-safe
callable). Closures in published `config/events.php` break
`php artisan config:cache`. Prefer:

```php
// app/Support/EventsContextProvider.php
namespace App\Support;

use JOOservices\LaravelEvents\Support\EventMetadata;

final class EventsContextProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return EventMetadata::merge(
            EventMetadata::trace(request()->header('X-Request-ID')),
            EventMetadata::source(config('app.name'), request()->route() ? 'web' : 'cli'),
            ['user_id' => auth()->id()],
        );
    }
}
```

```php
// config/events.php
'context_provider' => App\Support\EventsContextProvider::class,
```

Return `[]` from the provider or set `context_provider` to `null` to disable.

See [Metadata, Versioning, and Corrections](../02-user-guide/03-metadata-correlation-causation.md) for recommended keys.

## Transaction and queue caveats

Subscribers persist to MongoDB when Laravel dispatches the event:

- Prefer `Illuminate\Contracts\Events\ShouldDispatchAfterCommit` (or dispatch
  after your SQL transaction commits) so a rolled-back DB transaction does not
  leave orphan Mongo rows.
- If the event implements `ShouldQueue`, capture `user_id`, `correlation_id`,
  and request metadata **on the event at dispatch time** (or via a
  context_provider that does not rely on `auth()` / `request()` in the worker).
  Worker processes are guests with no HTTP request.
