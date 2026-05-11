# Samples

## Event Sourcing: minimal with defaults trait

Implement only `payload()` and `aggregateId()` by using `HasEventSourcingDefaults`:

```php
namespace App\Events;

use JOOservices\LaravelEvents\EventSourcing\Concerns\HasEventSourcingDefaults;
use JOOservices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

class OrderCreated implements EventSourcingInterface
{
    use HasEventSourcingDefaults;

    public function __construct(public string $orderId, public float $total) {}

    public function payload(): array
    {
        return ['order_id' => $this->orderId, 'total' => $this->total];
    }

    public function aggregateId(): ?string
    {
        return $this->orderId;
    }
}
```

## Event Sourcing: with occurredAt (Carbon)

```php
namespace App\Events;

use Carbon\CarbonInterface;
use JOOservices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;
use JOOservices\LaravelEvents\Support\EventMetadata;

class OrderCreated implements EventSourcingInterface
{
    public function __construct(
        public string $orderId,
        public float $total,
        public ?CarbonInterface $occurredAt = null,
    ) {}

    public function payload(): array
    {
        return [
            'order_id' => $this->orderId,
            'total' => $this->total,
        ];
    }

    public function aggregateId(): ?string
    {
        return $this->orderId;
    }

    public function occurredAt(): ?CarbonInterface
    {
        return $this->occurredAt;
    }

    public function metadata(): array
    {
        return EventMetadata::merge(
            EventMetadata::source('orders', 'api'),
            EventMetadata::version(schemaVersion: 1),
        );
    }
}
```

Dispatch and query:

```php
event(new OrderCreated('ORD-123', 99.99));

$stored = \JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent::on('mongodb')
    ->where('aggregate_id', 'ORD-123')
    ->orderBy('created_at')
    ->get();
```

## Event Log: default "updated" action trait

When the action is always `updated`, use `DefaultsToUpdatedAction` so you don't implement `getAction()`:

```php
namespace App\Events;

use App\Models\Order;
use JOOservices\LaravelEvents\EventLog\Concerns\DefaultsToUpdatedAction;
use JOOservices\LaravelEvents\EventLog\Contracts\HasLogAction;
use JOOservices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;

class OrderAuditEvent implements LoggableModelInterface, HasLogAction
{
    use DefaultsToUpdatedAction;

    public function __construct(public Order $order, public array $prev) {}

    public function getLoggableType(): string
    {
        return $this->order->getMorphClass();
    }

    public function getLoggableId(): string
    {
        return (string) $this->order->getKey();
    }

    public function getPrev(): array
    {
        return $this->prev;
    }

    public function getChanged(): array
    {
        return $this->order->getAttributes();
    }
}
```

## Event Log: custom action (created/updated/deleted)

Override `getAction()` or omit the trait when the action varies (e.g. pass `'created'` or `'deleted'`).

```php
use JOOservices\LaravelEvents\EventLog\EventLogAction;

public function getAction(): string
{
    return EventLogAction::STATUS_CHANGED;
}
```

In a controller or observer:

```php
$prev = $order->getOriginal();
$order->update($request->validated());
event(new OrderAuditEvent($order, $prev));
```

## Using EventService Directly

You can inject `EventService` and call it without dispatching:

```php
use JOOservices\LaravelEvents\EventService;

class SomeService
{
    public function __construct(private EventService $eventService) {}

    public function recordOrderEvent(object $event): void
    {
        $this->eventService->storeEvent(
            $event,
            $event->payload(),
            $event->aggregateId(),
            auth()->id(),
            null,
            [],
        );
    }
}
```

## Context Provider Example

In `AppServiceProvider::boot()`:

```php
config([
    'events.context_provider' => function () {
        $ctx = [];
        if (app()->has('request') && request()) {
            $ctx['request_id'] = request()->header('X-Request-ID');
            $ctx['ip'] = request()->ip();
        }
        return $ctx;
    },
]);
```

Or use the metadata helper for standard keys:

```php
use JOOservices\LaravelEvents\Support\EventMetadata;

config([
    'events.context_provider' => fn () => EventMetadata::merge(
        EventMetadata::trace(request()->header('X-Request-ID')),
        EventMetadata::source(config('app.name'), app()->runningInConsole() ? 'cli' : 'web'),
    ),
]);
```
