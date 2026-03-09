# Event Log (Audit Trail)

## Concept

Events that implement `LoggableModelInterface` are persisted to the **event_logs** MongoDB collection. Each document stores entity type/id, action, previous state (`prev`), changed state (`changed`), and a per-field diff (`diff`). Use this for audit trails and compliance.

## Interfaces

### LoggableModelInterface

```php
namespace JooServices\LaravelEvents\EventLog\Contracts;

interface LoggableModelInterface
{
    public function getLoggableType(): string;  // e.g. morph type or "App\Models\Order"
    public function getLoggableId(): string;    // entity id
    public function getPrev(): array;           // attributes before change
    public function getChanged(): array;         // attributes after change
}
```

### HasLogAction (optional)

When implemented, the stored `action` is taken from your event. Otherwise the subscriber uses `"updated"`. Use the `DefaultsToUpdatedAction` trait when the action is always `updated`.

```php
namespace JooServices\LaravelEvents\EventLog\Contracts;

interface HasLogAction
{
    public function getAction(): string;  // e.g. "created", "updated", "deleted", "restored"
}
```

## Implementing an Event

Example: log when an order is updated, using `DefaultsToUpdatedAction` so you don't implement `getAction()`.

```php
use App\Models\Order;
use JooServices\LaravelEvents\EventLog\Concerns\DefaultsToUpdatedAction;
use JooServices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;
use JooServices\LaravelEvents\EventLog\Contracts\HasLogAction;

class OrderUpdated implements LoggableModelInterface, HasLogAction
{
    use DefaultsToUpdatedAction;

    public function __construct(
        public Order $model,
        public array $prev,
    ) {}

    public function getLoggableType(): string
    {
        return $this->model->getMorphClass();
    }

    public function getLoggableId(): string
    {
        return (string) $this->model->getKey();
    }

    public function getPrev(): array
    {
        return $this->prev;
    }

    public function getChanged(): array
    {
        return $this->model->getAttributes();
    }
}
```

For creates, pass an empty `prev` and override `getAction()` to return `'created'` (or omit the trait and implement `getAction()` yourself).

## Stored Document Shape (MongoDB)

- `entity_type`, `entity_id`: from getLoggableType/getLoggableId
- `action`: from HasLogAction or `"updated"`
- `prev`: previous attributes
- `changed`: current/changed attributes
- `diff`: per-field `['old' => x, 'new' => y]` computed by DiffHelper
- `meta`: merge of context_provider and any passed meta (e.g. user_id)
- `user_id`: from auth or meta
- `created_at`: set by MongoDB/Eloquent

## Querying History for an Entity

```php
use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;

$history = EventLogEntry::on(config('events.connection'))
    ->where('entity_type', 'orders')
    ->where('entity_id', 'ORD-001')
    ->orderByDesc('created_at')
    ->get();
```

## Disabling Event Log

Set `config('events.event_log.enabled', false)` or `EVENTS_EVENT_LOG_ENABLED=false` so the subscriber does not register.
