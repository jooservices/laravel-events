# Audit log for model change

```php
use App\Models\Order;
use JOOservices\LaravelEvents\EventLog\Concerns\DefaultsToUpdatedAction;
use JOOservices\LaravelEvents\EventLog\Contracts\HasLogAction;
use JOOservices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;

final class OrderUpdated implements LoggableModelInterface, HasLogAction
{
    use DefaultsToUpdatedAction;

    public function __construct(
        private Order $order,
        private array $previous,
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
        return $this->previous;
    }

    public function getChanged(): array
    {
        return $this->order->getAttributes();
    }
}
```

Dispatch the event after saving the model. The package stores `prev`, `changed`,
and a computed `diff` in `event_logs`.
