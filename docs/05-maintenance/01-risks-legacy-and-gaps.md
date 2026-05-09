# Risks, legacy, and gaps

- Documentation was recently moved from a flat structure into the current
  DTO-style docs tree.
- Optional model observer helpers should remain deferred unless they can be
  small, explicit, and opt-in.
- Codecov, Sonar, Codacy, and Fortify integrations should only be enabled when
  repository configuration and secrets make them safe.
- The event serializer is intentionally minimal. A future DTO-aware serializer
  can map package or application DTOs into payload arrays, but the default
  behavior must stay compatible with `EventSourcingInterface::payload()`.
- Stored event envelope fields are additive and nullable. Existing MongoDB
  documents without these fields must remain readable.
- CI currently enforces an 80% temporary statement coverage threshold. The
  measured local baseline on 2026-05-09 was 84.51%. The long-term DTO-standard
  target remains 95%.

## Advanced scale roadmap

### Event Store Contract

An event store contract would centralize append and query operations when
applications need more than the current `EventService` and query services. It
should only be added when multiple storage adapters or richer append semantics
are required. MongoDB remains the current package storage target.

### Outbox Pattern

An outbox would help transactional reliability when an application must persist
events and publish them later with retry/idempotency guarantees. It is not
implemented yet because it adds operational state, publisher commands, retry
policy, and duplicate protection that should be driven by real application
needs.

### Replay Command

Replay must be guarded because replayed events can trigger side effects. A
future command should support dry-run, filters, checkpointing, and explicit
idempotency expectations. Replay side effects remain application-owned.

### Event Versioning / Upcaster

The package records `schema_version` and `event_version` conventions, and the
serializer exposes those as top-level nullable fields. A future upcaster registry
should only be added when applications need historical payload transformation
during reads or replay.

### Observability Hooks

Future hooks may emit Laravel events or metrics around successful and failed
persistence. They could integrate with `jooservices/laravel-logging` or another
observability package, but should remain optional and low-noise.
