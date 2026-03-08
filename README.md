# Laravel Events

EventSourcing and EventLog for Laravel with **MongoDB** storage. Store domain event payloads by aggregate and/or model change audit trails (prev/changed/diff) via Laravel's event dispatcher.

- **Laravel 12** · **PHP 8.5+**
- **MongoDB** via [mongodb/laravel-mongodb](https://github.com/mongodb/laravel-mongodb)

---

## Introduction

This package adds two persistence features on top of Laravel's event system:

1. **Event Sourcing** — Events implementing `EventSourcingInterface` are stored in a `stored_events` MongoDB collection (payload, aggregate id, metadata, user, time). Use for event replay, analytics, or audit by aggregate.
2. **Event Log** — Events implementing `LoggableModelInterface` are stored in an `event_logs` collection with previous/changed state and a per-field diff. Use for audit trails and compliance.

You dispatch events as usual; package subscribers persist them to MongoDB. No custom bus or queue required.

---

## Quick Start

### Install

```bash
composer require jooservices/laravel-events
```

### Publish config (optional)

```bash
php artisan vendor:publish --tag=laravel-events-config
```

### Environment

```env
EVENTS_MONGO_CONNECTION=mongodb
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=your_db
EVENTS_EVENTSOURCING_ENABLED=true
EVENTS_EVENT_LOG_ENABLED=true
```

Ensure a `mongodb` connection exists in `config/database.php` (see [mongodb/laravel-mongodb](https://github.com/mongodb/laravel-mongodb)).

### Indexes

```bash
php artisan events:install-indexes
```

---

## Basic Usage

### Event Sourcing

Implement `EventSourcingInterface` and dispatch:

```php
use JooServices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

class OrderCreated implements EventSourcingInterface
{
    public function __construct(public string $orderId, public array $items) {}

    public function payload(): array
    {
        return ['order_id' => $this->orderId, 'items' => $this->items];
    }

    public function aggregateId(): ?string
    {
        return $this->orderId;
    }
}

event(new OrderCreated('ORD-001', [['sku' => 'X', 'qty' => 2]]));
```

Events are stored in the `stored_events` collection. Optional: `occurredAt(): ?\DateTimeInterface`, `metadata(): array`.

### Event Log (Audit)

Implement `LoggableModelInterface` (and optionally `HasLogAction`) and dispatch with prev/changed state:

```php
use JooServices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;
use JooServices\LaravelEvents\EventLog\Contracts\HasLogAction;

class OrderUpdated implements LoggableModelInterface, HasLogAction
{
    public function __construct(public Order $model, public array $prev) {}

    public function getLoggableType(): string { return $this->model->getMorphClass(); }
    public function getLoggableId(): string { return (string) $this->model->getKey(); }
    public function getPrev(): array { return $this->prev; }
    public function getChanged(): array { return $this->model->getAttributes(); }
    public function getAction(): string { return 'updated'; }
}
```

Changes are stored in `event_logs` with a computed diff. Query by `entity_type` + `entity_id`.

---

## Documentation

Full documentation is in the **`./docs`** folder:

| Document | Description |
|----------|-------------|
| [docs/README.md](docs/README.md) | Documentation index |
| [Architecture](docs/architecture.md) | Design, data flow, diagrams |
| [Code structure](docs/code-structure.md) | Package layout and namespaces |
| [Installation](docs/installation.md) | Requirements and setup |
| [Configuration](docs/configuration.md) | Config and context provider |
| [Event Sourcing](docs/event-sourcing.md) | Stored events and aggregates |
| [Event Log](docs/event-log.md) | Audit trail and diff |
| [Samples](docs/samples.md) | Complete code examples |
| [API Reference](docs/api.md) | EventService, interfaces, commands |

---

## Testing & Linting

```bash
composer test
composer lint   # Pint, PHPStan, PHPMD, PHPCS
```

---

## License

MIT
