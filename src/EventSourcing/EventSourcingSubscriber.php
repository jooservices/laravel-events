<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventSourcing;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use JOOservices\LaravelEvents\EventPersisterInterface;
use JOOservices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

final class EventSourcingSubscriber
{
    public function __construct(private readonly EventPersisterInterface $eventService)
    {
    }

    public function subscribe(Dispatcher $events): void
    {
        if (! $this->featureEnabled('events.eventsourcing.enabled', true)) {
            return;
        }

        $events->listen(EventSourcingInterface::class, [$this, 'persistEvent']);
    }

    public function persistEvent(EventSourcingInterface $event): void
    {
        $occurredAt = method_exists($event, 'occurredAt') ? $event->occurredAt() : null;
        $metadata = method_exists($event, 'metadata') ? $event->metadata() : [];

        $this->eventService->storeEvent(
            $event,
            $event->payload(),
            $event->aggregateId(),
            Auth::id(),
            $occurredAt instanceof CarbonInterface ? $occurredAt : null,
            is_array($metadata) ? $this->stringKeyedArray($metadata) : [],
        );
    }

    private function featureEnabled(string $key, bool $default): bool
    {
        $enabled = config($key, $default);

        return is_bool($enabled) ? $enabled : filter_var($enabled, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param  array<mixed>  $values
     * @return array<string, mixed>
     */
    private function stringKeyedArray(array $values): array
    {
        $normalized = [];
        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
