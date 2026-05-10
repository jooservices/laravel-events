# Query history example

Query by aggregate:

```php
use JOOservices\LaravelEvents\Query\StoredEventQueryService;

$history = app(StoredEventQueryService::class)->byAggregateId('ORD-001');
```

Query audit history:

```php
use JOOservices\LaravelEvents\Query\EventLogQueryService;

$audit = app(EventLogQueryService::class)->byEntity('orders', 'ORD-001');
```
