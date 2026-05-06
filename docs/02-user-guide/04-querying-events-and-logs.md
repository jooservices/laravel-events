# Querying events and logs

Current package versions expose MongoDB Eloquent models for direct queries:

```php
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;

$events = StoredEvent::query()
    ->where('aggregate_id', $orderId)
    ->orderBy('created_at')
    ->get();
```

```php
use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;

$logs = EventLogEntry::query()
    ->where('entity_type', 'orders')
    ->where('entity_id', $orderId)
    ->orderByDesc('created_at')
    ->limit(50)
    ->get();
```

Lightweight query services are planned for this pass. Until they are
implemented, use the models directly and keep application-specific reporting
queries in the application layer.
