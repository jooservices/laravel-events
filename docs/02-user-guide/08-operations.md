# Operations

Event persistence is operational data. Keep query patterns, indexes, retention, and payload size in mind before production traffic grows.

## Recommended Indexes

Run:

```bash
php artisan events:install-indexes
```

The command creates indexes for common access patterns:

| Collection | Indexes |
|------------|---------|
| `stored_events` | `aggregate_id + created_at`, `event_class + created_at`, `event_name + created_at`, sparse unique `event_id`, `event_category`, `metadata.correlation_id` / `causation_id`, top-level `correlation_id` / `causation_id`, `user_id`, `created_at` or TTL |
| `event_logs` | `entity_type + entity_id + created_at`, `action + created_at`, `meta.correlation_id` / `causation_id`, `user_id`, `created_at` or TTL |

Prefix-only single-field indexes that duplicated compound prefixes were removed
to reduce write overhead. Re-run `events:install-indexes` after upgrading
(required for `4.0.0` — new sparse unique `event_id`, `event_name`, and
top-level correlation/causation indexes).


Add application-specific indexes only when you have measured query patterns, especially for metadata keys such as `metadata.tenant_id` or `meta.request_id`.

## Typical Queries

Aggregate history:

```php
StoredEvent::query()
    ->where('aggregate_id', $orderId)
    ->orderBy('created_at')
    ->get();
```

Entity audit history:

```php
EventLogEntry::query()
    ->where('entity_type', 'orders')
    ->where('entity_id', $orderId)
    ->orderByDesc('created_at')
    ->get();
```

Recent action audit:

```php
EventLogEntry::query()
    ->where('action', 'corrected')
    ->orderByDesc('created_at')
    ->limit(100)
    ->get();
```

## Retention and Archive

Use TTL only when automatic deletion is acceptable:

```env
EVENTS_EVENTSOURCING_TTL_DAYS=
EVENTS_EVENT_LOG_TTL_DAYS=365
```

Before enabling TTL:

- confirm legal/audit retention requirements
- archive records first if long-term history is required
- avoid TTL on event-sourced records that may be needed for replay
- remember TTL deletion is performed by MongoDB asynchronously

## Payload Size

Store the information required to understand or replay the event, not entire object graphs. Prefer stable identifiers over large embedded documents unless replay explicitly needs the embedded data.

Avoid storing:

- secrets, passwords, tokens, private keys
- unnecessary PII
- large binary/blob content
- request bodies that contain unrelated data

## Production Safety

- Treat replay as application behavior, not just data reading. Replayed events should be idempotent where possible.
- Make consumers tolerant of missing payload keys from older schema versions.
- Use `correlation_id` and `causation_id` for multi-step workflows.
- Use tenant-aware authorization in application queries when storing tenant metadata.
- Redact sensitive fields before dispatching events or before returning stored records to users.
- Persistence is synchronous on dispatch and fail-closed (Mongo errors bubble). Prefer
  `ShouldDispatchAfterCommit` (or dispatch after SQL commit) to avoid orphan Mongo rows
  when a relational transaction rolls back.
- For `ShouldQueue` events, put actor/request context on the event (or a worker-safe
  context provider). `auth()->id()` and `request()` are empty in queue workers.
- This package is not a full event store: no stream version, optimistic concurrency,
  outbox, or built-in replay.
