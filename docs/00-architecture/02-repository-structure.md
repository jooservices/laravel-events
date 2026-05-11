# Code Structure

## Package Layout

```
laravel-events/
├── config/
│   └── events.php              # Default configuration
├── src/
│   ├── Console/
│   │   └── InstallIndexesCommand.php
│   ├── EventLog/
│   │   ├── Concerns/
│   │   │   └── DefaultsToUpdatedAction.php
│   │   ├── Contracts/
│   │   │   ├── HasLogAction.php
│   │   │   └── LoggableModelInterface.php
│   │   ├── EventLogAction.php
│   │   ├── EventLogSubscriber.php
│   │   └── Models/
│   │       └── EventLogEntry.php
│   ├── EventSourcing/
│   │   ├── Concerns/
│   │   │   └── HasEventSourcingDefaults.php
│   │   ├── Contracts/
│   │   │   └── EventSourcingInterface.php
│   │   ├── EventSourcingSubscriber.php
│   │   └── Models/
│   │       └── StoredEvent.php
│   ├── EventsServiceProvider.php
│   ├── EventService.php
│   └── Support/
│       ├── DiffHelper.php
│       └── EventMetadata.php
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
| `JOOservices\LaravelEvents\EventSourcing` | EventSourcing subscriber and contract |
| `JOOservices\LaravelEvents\EventSourcing\Concerns` | HasEventSourcingDefaults trait (optional occurredAt/metadata) |
| `JOOservices\LaravelEvents\EventSourcing\Contracts` | EventSourcingInterface |
| `JOOservices\LaravelEvents\EventSourcing\Models` | StoredEvent MongoDB model |
| `JOOservices\LaravelEvents\EventLog` | EventLog subscriber and action taxonomy |
| `JOOservices\LaravelEvents\EventLog\Concerns` | DefaultsToUpdatedAction trait (default getAction) |
| `JOOservices\LaravelEvents\EventLog\Contracts` | LoggableModelInterface, HasLogAction |
| `JOOservices\LaravelEvents\EventLog\Models` | EventLogEntry MongoDB model |
| `JOOservices\LaravelEvents\Support` | DiffHelper and metadata convention helpers |

## Key Types

| Type | Role |
|------|------|
| **EventsServiceProvider** | Registers config, EventService singleton, subscribers, and `events:install-indexes` command |
| **EventService** | Persists to `stored_events` (storeEvent) and `event_logs` (logChange); applies context_provider |
| **EventSourcingSubscriber** | Listens for `EventSourcingInterface`; calls EventService::storeEvent |
| **EventLogSubscriber** | Listens for `LoggableModelInterface`; builds diff via DiffHelper; calls EventService::logChange |
| **DiffHelper** | Computes per-field diff (old/new) between prev and current arrays |
| **EventMetadata** | Constants and small helpers for metadata conventions |
| **EventLogAction** | Constants for recommended event log action taxonomy |
| **StoredEvent / EventLogEntry** | MongoDB Eloquent models (connection/collection from config) |

## Dependency Flow

- **EventsServiceProvider** → EventService (singleton), EventSourcingSubscriber, EventLogSubscriber, InstallIndexesCommand
- **EventSourcingSubscriber** → EventService
- **EventLogSubscriber** → EventService, DiffHelper
- **EventService** → StoredEvent, EventLogEntry (models)
