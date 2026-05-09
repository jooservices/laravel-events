# Data flow

## Event sourcing

1. Application code dispatches an event implementing `EventSourcingInterface`.
2. `EventSourcingSubscriber` receives the event from Laravel's dispatcher.
3. The subscriber reads `payload()`, `aggregateId()`, optional `occurredAt()`,
   and optional `metadata()`.
4. `EventService::storeEvent()` merges configured context metadata.
5. The `StoredEvent` MongoDB model persists the record.

## Event log

1. Application code dispatches an event implementing `LoggableModelInterface`.
2. `EventLogSubscriber` reads entity type, entity id, previous attributes, and
   changed attributes.
3. The subscriber chooses an action from `HasLogAction` or defaults to
   `updated`.
4. `DiffHelper` computes the field diff.
5. `EventService::logChange()` merges configured context metadata.
6. The `EventLogEntry` MongoDB model persists the audit record.

The package keeps conversion to persisted MongoDB documents inside the package
boundary. Application code should dispatch clear events rather than writing
directly to package collections.
