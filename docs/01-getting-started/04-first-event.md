# First event

Create an event implementing `EventSourcingInterface`:

```php
use JOOservices\LaravelEvents\EventSourcing\Concerns\HasEventSourcingDefaults;
use JOOservices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

final class OrderCreated implements EventSourcingInterface
{
    use HasEventSourcingDefaults;

    public function __construct(public string $orderId) {}

    public function payload(): array
    {
        return ['order_id' => $this->orderId];
    }

    public function aggregateId(): ?string
    {
        return $this->orderId;
    }
}
```

Dispatch it with Laravel's event dispatcher:

```php
event(new OrderCreated('ORD-001'));
```

The package subscriber persists the event to the configured `stored_events`
collection when event sourcing is enabled.
