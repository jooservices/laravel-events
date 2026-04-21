# Decision Guide

Use this package when you want lightweight MongoDB persistence for Laravel events. It adds persistence around Laravel's native event dispatcher; it does not replace the dispatcher or add a projection, analytics, reporting, or AI query layer.

## Event Sourcing vs Event Log

| Question | Event Sourcing | Event Log |
|----------|----------------|-----------|
| Purpose | Persist domain event payloads by aggregate | Persist model/entity change history |
| Typical use | Rebuild or inspect aggregate history | Audit who/what changed on an entity |
| Stored structure | `event_class`, `aggregate_id`, `payload`, `metadata`, `occurred_at` | `entity_type`, `entity_id`, `action`, `prev`, `changed`, `diff`, `meta` |
| Replay suitability | Suitable when event payloads are designed for replay | Usually not suitable for domain replay |
| Audit suitability | Good for aggregate-level audit | Strong for field-level audit |
| Tradeoff | Requires stable event names, payload versions, and replay discipline | Easier to adopt, but records state changes rather than domain intent |

## When to Use Event Sourcing

Use Event Sourcing when the event is part of your domain history and should remain meaningful over time:

```php
event(new OrderPlaced($orderId, $items, $total));
```

Good candidates:

- aggregate lifecycle events such as order placed, invoice issued, shipment confirmed
- events that may be replayed or inspected in sequence
- events with explicit payload schema/version conventions

Avoid Event Sourcing when the event is only a notification or temporary integration signal.

## When Event Log Is Enough

Use Event Log when you need an audit trail of model or entity changes:

```php
event(new OrderAuditEvent($order, $order->getOriginal()));
```

Good candidates:

- admin edits
- status changes
- soft delete/restore history
- compliance trail for changed fields

Event Log alone is often enough when you do not plan to replay domain behavior from historical events.

## Using Both Together

Use both when one application action has domain meaning and also changes audited data. For example:

- `OrderApproved` stored as Event Sourcing for aggregate history
- `orders.status` change stored as Event Log for field-level audit

Keep each record focused. Do not duplicate entire model snapshots into domain event payloads unless replay requires them.

## Anti-Patterns

- Do not use Event Sourcing as a general database backup.
- Do not treat Event Log as a projection/read-model framework.
- Do not store every framework event by default.
- Do not store secrets, raw tokens, or unnecessary PII in payloads or metadata.
- Do not add domain-specific event catalogs to this package; keep those in the application.
