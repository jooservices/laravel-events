# Metadata, Versioning, and Corrections

Metadata is intentionally flexible. The package stores whatever metadata your event returns and whatever your `context_provider` supplies. It does not require every key to exist.

Use consistent keys so records are easier to trace, audit, and evolve.

## Recommended Metadata Keys

| Key | Purpose |
|-----|---------|
| `event_id` | Optional stable event identifier; generated when omitted |
| `event_name` | Optional event type/name override; defaults to event class basename |
| `aggregate_type` | Optional aggregate category such as `orders` or `users` |
| `request_id` | Incoming request or job identifier |
| `correlation_id` | Groups related work across services/actions |
| `causation_id` | Identifies the event/command that caused this record |
| `source` | Application, service, module, or integration name |
| `channel` | `web`, `api`, `queue`, `cli`, `cron`, etc. |
| `reason_code` | Stable machine-readable reason for the change |
| `schema_version` | Payload/metadata schema version |
| `event_version` | Version of the event meaning or contract |
| `tenant_id` | Optional tenant identifier when the app is multi-tenant |

The `JooServices\LaravelEvents\Support\EventMetadata` helper exposes constants and small factory methods for these conventions:

```php
use JooServices\LaravelEvents\Support\EventMetadata;

public function metadata(): array
{
    return EventMetadata::merge(
        EventMetadata::trace(request()->header('X-Request-ID'), 'checkout-123'),
        EventMetadata::source('orders', 'api', 'customer_checkout'),
        EventMetadata::version(schemaVersion: 1),
    );
}
```

## Context Provider

Put request-scoped values in `config('events.context_provider')` so they are applied to both Event Sourcing metadata and Event Log meta:

```php
config([
    'events.context_provider' => fn () => EventMetadata::merge(
        EventMetadata::trace(request()->header('X-Request-ID')),
        EventMetadata::source(source: config('app.name'), channel: app()->runningInConsole() ? 'cli' : 'web'),
    ),
]);
```

Event-specific metadata wins when keys overlap because it is merged after context metadata.

The default serializer also copies `event_id`, `event_name`, `aggregate_type`,
`schema_version`, `event_version`, `correlation_id`, and `causation_id` to
nullable top-level stored-event fields. The full metadata array is still stored
unchanged for backward compatibility and application-owned context.

## Versioning and Schema Evolution

Stored events live longer than application code. Prefer additive payload changes and version every persisted event contract.

Recommended approach:

- include `schema_version` in metadata when the payload structure may evolve
- include `event_version` when the event's business meaning changes
- keep old event classes available as long as records reference them by FQCN
- when renaming event classes, maintain an application-level alias map for historical reads
- avoid destructive payload changes; add new keys and keep consumers tolerant of missing old/new keys
- document replay assumptions in the application that performs replay

This package does not include an upcaster framework. If an app needs replay-time transformation, implement it in the application layer close to the replay consumer.

## Corrections and Reversions

Use metadata to link corrective records to the event or log entry they correct. Recommended keys:

| Key | Purpose |
|-----|---------|
| `reverted_event_id` | The stored record being reverted |
| `supersedes_event_id` | The stored record replaced by this one |
| `correction_of` | Logical correction target when no stored id is available |
| `correction_reason` | Stable reason code or short explanation |

Example:

```php
use JooServices\LaravelEvents\Support\EventMetadata;

public function metadata(): array
{
    return EventMetadata::merge(
        EventMetadata::version(schemaVersion: 2),
        EventMetadata::correction(
            supersedesEventId: '65f1...',
            correctionReason: 'incorrect_total',
        ),
    );
}
```

The package records relationships for traceability only. It does not run compensation workflows, rollbacks, or correction automation.
