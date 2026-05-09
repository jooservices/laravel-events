# Querying events and logs

The package exposes lightweight query services for common lookup patterns:

```php
use JooServices\LaravelEvents\Query\StoredEventQueryService;

$events = app(StoredEventQueryService::class)->byAggregateId($orderId);
$recent = app(StoredEventQueryService::class)->latest(50);
$correlated = app(StoredEventQueryService::class)->byCorrelationId('corr-123');
```

```php
use JooServices\LaravelEvents\Query\EventLogQueryService;

$logs = app(EventLogQueryService::class)->byEntity('orders', $orderId);
$caused = app(EventLogQueryService::class)->byCausationId('cmd-123');
```

Query services return typed data records. Keep dashboards, reporting, and
application-specific analytics in the application layer.

`StoredEventQueryService::byAggregateId()` filters by `aggregate_id`. The
additive `aggregate_type` envelope field is persisted for consumers that need
type context, but the package keeps the default aggregate lookup compatible with
existing records.
