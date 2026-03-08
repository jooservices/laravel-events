# Samples

## Event Sourcing: Order Events

```php
namespace App\Events;

use JooServices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

class OrderCreated implements EventSourcingInterface
{
    public function __construct(
        public string $orderId,
        public float $total,
        public ?\DateTimeInterface $occurredAt = null,
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

    public function occurredAt(): ?\DateTimeInterface
    {
        return $this->occurredAt;
    }
}
```

Dispatch and query:

```php
event(new OrderCreated('ORD-123', 99.99));

$stored = \JooServices\LaravelEvents\EventSourcing\Models\StoredEvent::on('mongodb')
    ->where('aggregate_id', 'ORD-123')
    ->orderBy('created_at')
    ->get();
```

## Event Log: Model Change Audit

```php
namespace App\Events;

use App\Models\Order;
use JooServices\LaravelEvents\EventLog\Contracts\HasLogAction;
use JooServices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;

class OrderAuditEvent implements LoggableModelInterface, HasLogAction
{
    public function __construct(
        public Order $order,
        public array $prev,
        public string $action = 'updated',
    ) {}

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

    public function getAction(): string
    {
        return $this->action;
    }
}
```

In a controller or observer:

```php
$prev = $order->getOriginal();
$order->update($request->validated());
event(new OrderAuditEvent($order, $prev, 'updated'));
```

## Using EventService Directly

You can inject `EventService` and call it without dispatching:

```php
use JooServices\LaravelEvents\EventService;

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
