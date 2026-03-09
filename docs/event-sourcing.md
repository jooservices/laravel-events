# Event Sourcing

## Concept

Events that implement `EventSourcingInterface` are persisted to the **stored_events** MongoDB collection. Each document stores the event class, aggregate id, payload, metadata, user id, and optional occurred-at time. Use this for event replay, analytics, or audit by aggregate.

## Interface

```php
namespace JooServices\LaravelEvents\EventSourcing\Contracts;

interface EventSourcingInterface
{
    public function payload(): array;
    public function aggregateId(): ?string;
}
```

Optional methods on your event (detected via `method_exists`):

- **occurredAt(): ?\Carbon\CarbonInterface** — Event time; when null or not implemented, `created_at` is used.
- **metadata(): array** — Extra metadata merged with `config('events.context_provider')` when storing.

## Using the defaults trait

Use `HasEventSourcingDefaults` so you only implement `payload()` and `aggregateId()`. Override `occurredAt()` or `metadata()` when needed.

```php
use JooServices\LaravelEvents\EventSourcing\Concerns\HasEventSourcingDefaults;
use JooServices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

class OrderCreated implements EventSourcingInterface
{
    use HasEventSourcingDefaults;

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
```

## Implementing an Event (with optional methods)

```php
use Carbon\CarbonInterface;
use JooServices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

class OrderCreated implements EventSourcingInterface
{
    public function __construct(
        public string $orderId,
        public array $items,
        public ?CarbonInterface $occurredAt = null,
    ) {}

    public function payload(): array
    {
        return [
            'order_id' => $this->orderId,
            'items' => $this->items,
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
        return ['channel' => 'api'];
    }
}
```

## Dispatching

Dispatch the event as usual. The package subscriber listens and persists it.

```php
event(new OrderCreated('ORD-001', [['sku' => 'X', 'qty' => 2]]));
```

## Stored Document Shape (MongoDB)

- `event_class`: FQCN of the event
- `aggregate_id`: from `aggregateId()`
- `payload`: from `payload()`
- `metadata`: merge of context_provider and event `metadata()`
- `user_id`: from `auth()->id()` or passed to EventService
- `occurred_at`: from `occurredAt()` or null
- `created_at`: set by MongoDB/Eloquent

## Querying by Aggregate

Use the same MongoDB connection and collection (e.g. `StoredEvent` model or raw collection). Example:

```php
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;

$events = StoredEvent::on(config('events.connection'))
    ->where('aggregate_id', 'ORD-001')
    ->orderBy('created_at')
    ->get();
```

## Disabling Event Sourcing

Set `config('events.eventsourcing.enabled', false)` or `EVENTS_EVENTSOURCING_ENABLED=false` so the subscriber does not register.
