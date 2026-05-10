<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventSourcing;

use Illuminate\Contracts\Events\Dispatcher;
use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;

class EventSourcingSubscriber
{
    public function __construct(protected EventService $eventService) {}

    public function subscribe(Dispatcher $events): void
    {
        if (config('events.eventsourcing.enabled', true)) {
            $events->listen(EventSourcingInterface::class, [$this, 'persistEvent']);
        }
    }

    public function persistEvent(EventSourcingInterface $event): void
    {
        $occurredAt = method_exists($event, 'occurredAt') ? $event->occurredAt() : null;
        $metadata = method_exists($event, 'metadata') ? $event->metadata() : [];
        $this->eventService->storeEvent(
            $event,
            $event->payload(),
            $event->aggregateId(),
            auth()->id(),
            $occurredAt,
            $metadata,
        );
    }
}
