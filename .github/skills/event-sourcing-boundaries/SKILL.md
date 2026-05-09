# Event Sourcing Boundaries

Use this skill when changing `EventSourcingInterface`, stored event models, stored event data records, serializers, query services, or Event Sourcing docs.

## What

Event Sourcing records represent domain events that should remain meaningful by aggregate over time. They are persisted to the configured `stored_events` MongoDB collection with event identity, aggregate identity, payload, metadata, timestamps, and additive envelope fields.

## Why

Stored events can become long-lived historical records. Breaking payload semantics, aggregate identity, version metadata, or replay assumptions can damage consumers that rely on the event stream as a domain history.

## How

- Inspect existing stored event schema, config, serializer behavior, tests, and docs before changing fields.
- Do not duplicate complete model snapshots into domain events unless replay explicitly requires them.
- Keep serializer output compatible with `EventSourcingInterface::payload()`.
- Treat `schema_version`, `event_version`, `correlation_id`, and `causation_id` as metadata conventions, not mandatory application policy.
- Preserve readability of older MongoDB documents when adding nullable fields.
- Test storage behavior against real MongoDB; do not mock persisted stored event records.
- Stop and ask when a change implies replay orchestration, projections, outbox behavior, or side effects.
- Use Laravel 12 / PHP 8.5 standards and let Pint win formatter conflicts.
- Update README/docs and run Composer quality gates when public Event Sourcing behavior changes.
