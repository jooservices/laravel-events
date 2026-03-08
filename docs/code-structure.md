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
│   │   ├── Contracts/
│   │   │   ├── HasLogAction.php
│   │   │   └── LoggableModelInterface.php
│   │   ├── EventLogSubscriber.php
│   │   └── Models/
│   │       └── EventLogEntry.php
│   ├── EventSourcing/
│   │   ├── Contracts/
│   │   │   └── EventSourcingInterface.php
│   │   ├── EventSourcingSubscriber.php
│   │   └── Models/
│   │       └── StoredEvent.php
│   ├── EventsServiceProvider.php
│   ├── EventService.php
│   └── Support/
│       └── DiffHelper.php
├── tests/
│   ├── Integration/
│   ├── Unit/
│   └── TestCase.php
└── docs/
```

## Namespace Map

| Namespace | Responsibility |
|-----------|----------------|
| `JooServices\LaravelEvents` | Service provider, EventService |
| `JooServices\LaravelEvents\Console` | Artisan commands (indexes) |
| `JooServices\LaravelEvents\EventSourcing` | EventSourcing subscriber and contract |
| `JooServices\LaravelEvents\EventSourcing\Contracts` | EventSourcingInterface |
| `JooServices\LaravelEvents\EventSourcing\Models` | StoredEvent MongoDB model |
| `JooServices\LaravelEvents\EventLog` | EventLog subscriber |
| `JooServices\LaravelEvents\EventLog\Contracts` | LoggableModelInterface, HasLogAction |
| `JooServices\LaravelEvents\EventLog\Models` | EventLogEntry MongoDB model |
| `JooServices\LaravelEvents\Support` | DiffHelper utility |

## Key Types

| Type | Role |
|------|------|
| **EventsServiceProvider** | Registers config, EventService singleton, subscribers, and `events:install-indexes` command |
| **EventService** | Persists to `stored_events` (storeEvent) and `event_logs` (logChange); applies context_provider |
| **EventSourcingSubscriber** | Listens for `EventSourcingInterface`; calls EventService::storeEvent |
| **EventLogSubscriber** | Listens for `LoggableModelInterface`; builds diff via DiffHelper; calls EventService::logChange |
| **DiffHelper** | Computes per-field diff (old/new) between prev and current arrays |
| **StoredEvent / EventLogEntry** | MongoDB Eloquent models (connection/collection from config) |

## Dependency Flow

- **EventsServiceProvider** → EventService (singleton), EventSourcingSubscriber, EventLogSubscriber, InstallIndexesCommand
- **EventSourcingSubscriber** → EventService
- **EventLogSubscriber** → EventService, DiffHelper
- **EventService** → StoredEvent, EventLogEntry (models)
