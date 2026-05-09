# Correlation id flow

Use `EventMetadata` to keep trace metadata consistent:

Place this in your `AppServiceProvider::boot()` method, or equivalent
application bootstrap, so the context provider is registered once at startup.

```php
use JooServices\LaravelEvents\Support\EventMetadata;

config([
    'events.context_provider' => fn () => EventMetadata::merge(
        EventMetadata::trace(
            request()->header('X-Request-ID'),
            request()->header('X-Correlation-ID'),
        ),
        EventMetadata::source(config('app.name'), 'api'),
    ),
]);
```

Event-specific metadata can add causation or version details:

```php
public function metadata(): array
{
    return EventMetadata::merge(
        EventMetadata::trace(causationId: 'command-123'),
        EventMetadata::version(schemaVersion: 1, eventVersion: 1),
    );
}
```
