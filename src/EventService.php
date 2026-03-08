<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents;

use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;

class EventService
{
    public function __construct(
        protected StoredEvent $storedEventModel,
        protected EventLogEntry $eventLogEntryModel,
    ) {}

    /**
     * Store dispatched event payload (EventSourcing).
     *
     * @param  int|string|null  $userId  Authorized user id (int or string/UUID); null when guest.
     * @param  array<string, mixed>  $metadata  Merged with config context_provider when storing.
     */
    public function storeEvent(
        object $event,
        array $payload,
        ?string $aggregateId = null,
        int|string|null $userId = null,
        ?\DateTimeInterface $occurredAt = null,
        array $metadata = [],
    ): StoredEvent {
        $metadata = array_merge($this->getContext(), $metadata);
        $attributes = [
            'event_class' => $event::class,
            'aggregate_id' => $aggregateId,
            'payload' => $payload,
            'metadata' => $metadata,
            'user_id' => $userId ?? auth()->id(),
            'occurred_at' => $occurredAt,
        ];

        return $this->storedEventModel->newQuery()->create($attributes);
    }

    /**
     * Store model change with prev/changed/diff (EventLog).
     *
     * @param  int|string|null  $userId  Authorized user id (int or string/UUID); null when guest.
     * @param  array<string, mixed>  $meta  Merged with config context_provider when storing.
     */
    public function logChange(
        string $entityType,
        string $entityId,
        string $action,
        array $prev,
        array $changed,
        array $diff,
        array $meta = [],
        int|string|null $userId = null,
    ): EventLogEntry {
        $userId = $userId ?? $meta['user_id'] ?? auth()->id();
        $meta = array_merge($this->getContext(), $meta);
        $attributes = [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'prev' => $prev,
            'changed' => $changed,
            'diff' => $diff,
            'meta' => $meta,
            'user_id' => $userId,
        ];

        return $this->eventLogEntryModel->newQuery()->create($attributes);
    }

    /** @return array<string, mixed> */
    private function getContext(): array
    {
        $provider = config('events.context_provider');
        if ($provider === null || ! is_callable($provider)) {
            return [];
        }

        $context = $provider();

        return is_array($context) ? $context : [];
    }
}
