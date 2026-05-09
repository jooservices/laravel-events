# Event Sourcing

## Concept

Events that implement `EventSourcingInterface` are persisted to the **stored_events** MongoDB collection. Each document stores the event class, aggregate id, payload, metadata, user id, and optional occurred-at time. Use this for aggregate history, replay-aware workflows, or audit by aggregate.

For choosing between Event Sourcing and Event Log, see the [Decision Guide](10-best-practices.md).

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
use JooServices\LaravelEvents\Support\EventMetadata;

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
        return EventMetadata::merge(
            EventMetadata::source(source: 'orders', channel: 'api'),
            EventMetadata::version(schemaVersion: 1),
        );
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
- `event_id`: generated UUID unless metadata provides `event_id`
- `event_name`: short event class name unless metadata provides `event_name`
- `aggregate_id`: from `aggregateId()`
- `aggregate_type`: optional metadata-derived aggregate type
- `payload`: from `payload()`
- `metadata`: merge of context_provider and event `metadata()`
- `schema_version`, `event_version`: optional top-level copies from metadata for easier filtering
- `correlation_id`, `causation_id`: optional top-level copies from metadata for trace queries
- `user_id`: from `auth()->id()` or passed to EventService
- `occurred_at`: from `occurredAt()` or null
- `created_at`: set by MongoDB/Eloquent

The original storage fields remain backward compatible. New envelope fields are
nullable/additive and are derived by the default serializer.

## Serializer

`EventSerializerInterface` converts a dispatched Laravel event plus payload,
aggregate id, user id, occurred-at time, and merged metadata into
`StoredEventData`. The default `ArrayEventSerializer` preserves the current
array-payload behavior while adding optional envelope fields.

Applications may bind their own serializer in the Laravel container when they
need DTO payload mapping or a stricter event naming convention:

```php
use JooServices\LaravelEvents\Serialization\EventSerializerInterface;

$this->app->bind(EventSerializerInterface::class, App\Events\AppEventSerializer::class);
```

## Versioning Guidance

Include `schema_version` in metadata when payload fields may evolve. Include `event_version` when the event's business meaning changes. Keep payload changes additive where possible and keep historical event class names readable by your application.

This package does not include an upcaster framework. Replay and schema transformation belong in the application layer that consumes stored events.

## Querying by Aggregate

Use the same MongoDB connection and collection (e.g. `StoredEvent` model or raw collection). Example:

```php
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;

$events = StoredEvent::on(config('events.connection'))
    ->where('aggregate_id', 'ORD-001')
    ->orderBy('created_at')
    ->get();
```

Run `php artisan events:install-indexes` to create aggregate and chronological indexes for this query pattern.

## Disabling Event Sourcing

Set `config('events.eventsourcing.enabled', false)` or `EVENTS_EVENTSOURCING_ENABLED=false` so the subscriber does not register.
