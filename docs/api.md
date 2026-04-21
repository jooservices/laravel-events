# API Reference

## EventService

Singleton. Injected via Laravel container.

### storeEvent

Stores an event in the **stored_events** collection (Event Sourcing).

```php
public function storeEvent(
    object $event,
    array $payload,
    ?string $aggregateId = null,
    int|string|null $userId = null,
    ?\Carbon\CarbonInterface $occurredAt = null,
    array $metadata = [],
): StoredEvent
```

| Parameter | Description |
|-----------|-------------|
| `$event` | Event object (FQCN stored as `event_class`) |
| `$payload` | Payload array to store |
| `$aggregateId` | Optional aggregate identifier |
| `$userId` | User id (int or string); null uses `auth()->id()` |
| `$occurredAt` | Optional event time (Carbon); null uses document creation time |
| `$metadata` | Merged with config `context_provider`; see `EventMetadata` conventions |

**Returns:** `StoredEvent` model instance.

---

### logChange

Stores a change record in the **event_logs** collection (Event Log / audit).

```php
public function logChange(
    string $entityType,
    string $entityId,
    string $action,
    array $prev,
    array $changed,
    array $diff,
    array $meta = [],
    int|string|null $userId = null,
): EventLogEntry
```

| Parameter | Description |
|-----------|-------------|
| `$entityType` | Entity type (e.g. morph type) |
| `$entityId` | Entity id |
| `$action` | e.g. `created`, `updated`, `deleted`, `restored`, `status_changed`, `corrected` |
| `$prev` | Previous attributes |
| `$changed` | Current/changed attributes |
| `$diff` | Per-field diff e.g. `['field' => ['old' => x, 'new' => y]]` |
| `$meta` | Merged with config `context_provider` |
| `$userId` | User id; null uses `$meta['user_id']` or `auth()->id()` |

**Returns:** `EventLogEntry` model instance.

---

## Interfaces

### EventSourcingInterface

- `payload(): array`
- `aggregateId(): ?string`  
Optional: `occurredAt(): ?\Carbon\CarbonInterface`, `metadata(): array`. Use trait `HasEventSourcingDefaults` for defaults.

### LoggableModelInterface

- `getLoggableType(): string`
- `getLoggableId(): string`
- `getPrev(): array`
- `getChanged(): array`

### HasLogAction

- `getAction(): string` — e.g. `created`, `updated`, `deleted`, `restored`. Use trait `DefaultsToUpdatedAction` for default `'updated'`.

### EventLogAction

Constants for recommended action names:

- `CREATED`
- `UPDATED`
- `DELETED`
- `RESTORED`
- `STATUS_CHANGED`
- `CORRECTED`
- `SYNCHRONIZED`
- `IMPORTED`

`EventLogAction::all()` returns the full list.

### EventMetadata

Constants and helpers for metadata conventions:

- trace keys: `request_id`, `correlation_id`, `causation_id`
- source keys: `source`, `channel`, `reason_code`
- version keys: `schema_version`, `event_version`
- optional tenancy key: `tenant_id`
- correction keys: `reverted_event_id`, `supersedes_event_id`, `correction_of`, `correction_reason`

Helpers: `trace()`, `source()`, `version()`, `tenant()`, `correction()`, `merge()`, `withoutNulls()`.

---

## Console Commands

### events:install-indexes

Create or drop MongoDB indexes for `stored_events` and `event_logs`.

```bash
php artisan events:install-indexes
php artisan events:install-indexes --drop [--force]
```

- **Create:** Adds indexes for aggregate_id, aggregate_id+created_at, event_class, event_class+created_at, user_id, created_at (stored_events); entity_type+entity_id, entity_type+entity_id+created_at, action, action+created_at, user_id, created_at (event_logs). If TTL is configured, creates TTL index on `created_at`.
- **Drop:** `--drop` drops indexes (data is not deleted). `--force` skips confirmation.

---

## Models

### StoredEvent

MongoDB Eloquent model. Connection and collection from config. Fillable: `event_class`, `aggregate_id`, `payload`, `metadata`, `user_id`, `occurred_at`. Casts: `payload`/`metadata` => array, `occurred_at` => datetime (Carbon).

### EventLogEntry

MongoDB Eloquent model. Fillable: `entity_type`, `entity_id`, `action`, `prev`, `changed`, `diff`, `meta`, `user_id`. Casts: `prev`, `changed`, `diff`, `meta` => array.
