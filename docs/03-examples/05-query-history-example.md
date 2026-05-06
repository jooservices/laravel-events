# Query history example

Current direct model query:

```php
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;

$history = StoredEvent::query()
    ->where('aggregate_id', 'ORD-001')
    ->orderBy('created_at')
    ->get();
```

Current audit query:

```php
use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;

$audit = EventLogEntry::query()
    ->where('entity_type', 'orders')
    ->where('entity_id', 'ORD-001')
    ->orderByDesc('created_at')
    ->limit(50)
    ->get();
```

Query services are planned for this pass and will be documented here after
implementation.
