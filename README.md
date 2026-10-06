# jooservices/laravel-events

[![CI](https://github.com/jooservices/laravel-events/actions/workflows/ci.yml/badge.svg?branch=develop)](https://github.com/jooservices/laravel-events/actions/workflows/ci.yml)
[![Coverage (develop)](https://codecov.io/gh/jooservices/laravel-events/branch/develop/graph/badge.svg)](https://codecov.io/gh/jooservices/laravel-events/branch/develop)
[![Quality Gate (master)](https://sonarcloud.io/api/project_badges/measure?project=jooservices_laravel-events&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=jooservices_laravel-events)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/jooservices/laravel-events/badge)](https://securityscorecards.dev/viewer/?uri=github.com/jooservices/laravel-events)
[![PHP Version](https://img.shields.io/badge/PHP-8.5%2B-blue.svg)](https://www.php.net/)
[![GitHub Release](https://img.shields.io/github/v/release/jooservices/laravel-events?display_name=tag)](https://github.com/jooservices/laravel-events/releases)
[![Packagist Version](https://img.shields.io/packagist/v/jooservices/laravel-events)](https://packagist.org/packages/jooservices/laravel-events)
[![Total Downloads](https://img.shields.io/packagist/dt/jooservices/laravel-events)](https://packagist.org/packages/jooservices/laravel-events)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

`jooservices/laravel-events` provides Laravel event sourcing and event-log persistence using MongoDB.

> [!WARNING]
> **`v4.0.0` includes breaking API changes from `1.x`.** Review [UPGRADE-4.0.md](UPGRADE-4.0.md) and the [changelog](CHANGELOG.md) before upgrading. The current release is `4.0.0`.

## Features

- Persist domain events by aggregate in MongoDB's `stored_events` collection.
- Persist model-change audit records, including previous values and field diffs, in the `event_logs` collection.
- Dispatch events through Laravel's native event dispatcher; the package subscribers handle persistence.
- Publish package configuration and install recommended MongoDB indexes with Artisan commands.

## Requirements

- PHP `^8.5`
- Laravel `^12.0` or `^13.0`
- A MongoDB server and the PHP MongoDB extension
- `mongodb/laravel-mongodb` `^5.7`

## Installation

Install the package with Composer:

```bash
composer require jooservices/laravel-events:^4.0
```

The service provider is registered through Laravel package discovery. To customize the package settings, publish its configuration:

```bash
php artisan vendor:publish --tag=laravel-events-config
```

Configure a `mongodb` connection in `config/database.php`. The default package configuration reads these optional environment variables:

```env
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=your_db
EVENTS_EVENTSOURCING_ENABLED=true
EVENTS_EVENT_LOG_ENABLED=true
```

Create the recommended indexes after configuring MongoDB:

```bash
php artisan events:install-indexes
```

## Quick start

### Event sourcing

Implement `EventSourcingInterface` and dispatch the event through Laravel:

```php
use JOOservices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

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

### Event log

Implement `LoggableModelInterface` to record a model's previous and changed state. `DefaultsToUpdatedAction` supplies the `updated` action:

```php
use JOOservices\LaravelEvents\EventLog\Concerns\DefaultsToUpdatedAction;
use JOOservices\LaravelEvents\EventLog\Contracts\HasLogAction;
use JOOservices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;

class OrderUpdated implements LoggableModelInterface, HasLogAction
{
    use DefaultsToUpdatedAction;

    public function __construct(public Order $model, public array $prev) {}

    public function getLoggableType(): string { return $this->model->getMorphClass(); }
    public function getLoggableId(): string { return (string) $this->model->getKey(); }
    public function getPrev(): array { return $this->prev; }
    public function getChanged(): array { return $this->model->getAttributes(); }
}
```

## Design notes

This package persists event records; it is not a full event store. It does not provide stream version checks, an outbox, projections, or a replay command. Persisted records use `created_at` and may include `occurred_at` when supplied by an event.

The two storage features are independent: use event sourcing for aggregate event history and the event log for model-change audit trails. Package subscribers persist dispatched events synchronously. For events tied to a database transaction, Laravel's `ShouldDispatchAfterCommit` can defer dispatch until commit.

The package recursively redacts common secret fields by default. This is defensive masking and does not replace keeping secrets out of dispatched events. Optional MongoDB TTL retention is configured with `EVENTS_STORED_EVENTS_RETENTION_DAYS` and `EVENTS_EVENT_LOGS_RETENTION_DAYS`; MongoDB applies TTL deletion asynchronously.

Query services are available through Laravel's container:

```php
use JOOservices\LaravelEvents\Query\EventLogQueryService;
use JOOservices\LaravelEvents\Query\StoredEventQueryService;

$events = app(StoredEventQueryService::class)->byAggregateId('ORD-001');
$audit = app(EventLogQueryService::class)->byEntity('orders', 'ORD-001');
```

See the user guide for metadata, redaction, retention, query filters, and bulk recording details.

## Documentation

- [Documentation index](docs/README.md)
- [Installation and configuration](docs/01-getting-started/01-installation.md)
- [First event](docs/01-getting-started/04-first-event.md)
- [Installing indexes](docs/01-getting-started/05-index-installation.md)
- [Event sourcing](docs/02-user-guide/01-event-sourcing.md)
- [Event log](docs/02-user-guide/02-event-log.md)
- [Decision guide](docs/02-user-guide/10-best-practices.md)
- [Operations](docs/02-user-guide/08-operations.md)
- [API reference](docs/02-user-guide/11-api-reference.md)
- [Examples](docs/03-examples/01-basic-domain-event.md)
- [Upgrade guide](UPGRADE-4.0.md)
- [Changelog](CHANGELOG.md)
- [Workflow details](WORKFLOWS.md)

## Development

Run the development commands with PHP `8.5` and Composer dependencies installed. The `make` targets use the repository's Docker Compose environment:

```bash
make build
make install
make lint
make test
make ci
```

Run `composer validate --strict` before using the Composer scripts `lint`, `lint:fix`, `test`, `test:coverage`, `coverage:check`, `check`, and `ci`. Composer install and update also install the configured CaptainHook hooks. See the [development setup](docs/04-development/01-setup.md) and [contributing guide](CONTRIBUTING.md) for local details.

## Community

- [Contributing guide](CONTRIBUTING.md)
- [Security policy](SECURITY.md)
- [Support](SUPPORT.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)
- [Governance](GOVERNANCE.md)

## License

MIT — see [LICENSE](LICENSE).
