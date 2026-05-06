# Bulk record example

Use `EventService` for explicit batch writes:

```php
use JooServices\LaravelEvents\Data\StoredEventData;
use JooServices\LaravelEvents\EventService;

app(EventService::class)->recordManyStoredEvents([
    new StoredEventData('OrderImported', ['order_id' => 'ORD-1'], 'ORD-1'),
    new StoredEventData('OrderImported', ['order_id' => 'ORD-2'], 'ORD-2'),
]);
```

Each item is normalized and redacted before MongoDB batch insert.
