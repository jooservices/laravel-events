# Query history example

Query by aggregate:

```php
use JooServices\LaravelEvents\Query\StoredEventQueryService;

$history = app(StoredEventQueryService::class)->byAggregateId('ORD-001');
```

Query audit history:

```php
use JooServices\LaravelEvents\Query\EventLogQueryService;

$audit = app(EventLogQueryService::class)->byEntity('orders', 'ORD-001');
```
