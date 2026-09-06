# Code Structure

## Package Layout

```
laravel-events/
├── config/
│   └── events.php
├── src/
│   ├── Console/
│   │   └── InstallIndexesCommand.php
│   ├── Data/
│   │   ├── DocumentIdentity.php
│   │   ├── EventDiffData.php
│   │   ├── EventEnvelopeData.php
│   │   ├── EventLogData.php
│   │   ├── EventMetadataData.php
│   │   └── StoredEventData.php
│   ├── EventLog/
│   │   ├── Concerns/
│   │   ├── Contracts/
│   │   ├── EventLogAction.php
│   │   ├── EventLogSubscriber.php
│   │   └── Models/
│   ├── EventSourcing/
│   │   ├── Concerns/
│   │   ├── Contracts/
│   │   ├── EventSourcingSubscriber.php
│   │   └── Models/
│   ├── Exceptions/
│   ├── Query/
│   │   ├── EventLogQueryService.php
│   │   ├── QueryExecutor.php
│   │   ├── QueryGuard.php
│   │   └── StoredEventQueryService.php
│   ├── Serialization/
│   │   ├── ArrayEventSerializer.php
│   │   └── EventSerializerInterface.php
│   ├── Support/
│   │   ├── DateTimeParser.php
│   │   ├── DiffHelper.php
│   │   ├── EventMetadata.php
│   │   ├── EventMetadataBuilder.php
│   │   └── PayloadRedactor.php
│   ├── EventsServiceProvider.php
│   └── EventService.php
├── tests/
│   ├── Integration/
│   ├── Unit/
│   └── TestCase.php
└── docs/
```

## Namespace Map

| Namespace | Responsibility |
|-----------|----------------|
| `JOOservices\LaravelEvents` | Service provider, EventService |
| `JOOservices\LaravelEvents\Console` | Artisan commands (indexes) |
| `JOOservices\LaravelEvents\Data` | Typed records for stored events, logs, envelopes |
| `JOOservices\LaravelEvents\EventSourcing` | EventSourcing subscriber and contract |
| `JOOservices\LaravelEvents\EventSourcing\Concerns` | HasEventSourcingDefaults trait |
| `JOOservices\LaravelEvents\EventSourcing\Contracts` | EventSourcingInterface |
| `JOOservices\LaravelEvents\EventSourcing\Models` | StoredEvent MongoDB model |
| `JOOservices\LaravelEvents\EventLog` | EventLog subscriber and action taxonomy |
| `JOOservices\LaravelEvents\EventLog\Concerns` | DefaultsToUpdatedAction trait |
| `JOOservices\LaravelEvents\EventLog\Contracts` | LoggableModelInterface, HasLogAction |
| `JOOservices\LaravelEvents\EventLog\Models` | EventLogEntry MongoDB model |
| `JOOservices\LaravelEvents\Exceptions` | Package validation / query exceptions |
| `JOOservices\LaravelEvents\Query` | Read helpers with allowlisted filters |
| `JOOservices\LaravelEvents\Serialization` | Event → StoredEventData mapping |
| `JOOservices\LaravelEvents\Support` | DiffHelper, DateTimeParser, redaction, metadata helpers |

## Key Types

| Type | Role |
|------|------|
| **EventsServiceProvider** | Registers config, EventService singleton, subscribers, and `events:install-indexes` |
| **EventService** | Persists to `stored_events` / `event_logs`; applies context, redaction, envelope |
| **EventSourcingSubscriber** | Listens for `EventSourcingInterface`; calls EventService::storeEvent |
| **EventLogSubscriber** | Listens for `LoggableModelInterface`; builds diff via DiffHelper; calls logChange |
| **DiffHelper** | Per-field diff (old/new), including removals |
| **StoredEventQueryService / EventLogQueryService** | Small allowlisted query helpers returning DTOs |
| **EventSerializerInterface** | Serialize + ensureEnvelope for single and bulk writes |
| **EventMetadata** | Constants and small helpers for metadata conventions |
| **EventLogAction** | Constants for recommended event log action taxonomy |
| **StoredEvent / EventLogEntry** | MongoDB Eloquent models |

## Dependency Flow

- **EventsServiceProvider** → EventService, subscribers, InstallIndexesCommand, serializer binding
- **EventSourcingSubscriber** → EventService
- **EventLogSubscriber** → EventService, DiffHelper
- **EventService** → StoredEvent, EventLogEntry, serializer, redactor
- **Query services** → models → DTOs
