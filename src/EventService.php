<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use JooServices\LaravelEvents\Data\EventLogData;
use JooServices\LaravelEvents\Data\StoredEventData;
use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JooServices\LaravelEvents\Serialization\ArrayEventSerializer;
use JooServices\LaravelEvents\Serialization\EventSerializerInterface;
use JooServices\LaravelEvents\Support\PayloadRedactor;

class EventService
{
    protected PayloadRedactor $redactor;

    protected EventSerializerInterface $serializer;

    public function __construct(
        protected StoredEvent $storedEventModel,
        protected EventLogEntry $eventLogEntryModel,
        ?PayloadRedactor $redactor = null,
        ?EventSerializerInterface $serializer = null,
    ) {
        $this->redactor = $redactor ?? new PayloadRedactor;
        $this->serializer = $serializer ?? new ArrayEventSerializer;
    }

    /**
     * Store dispatched event payload (EventSourcing).
     *
     * @param  array<string, mixed>  $payload
     * @param  int|string|null  $userId  Authorized user id (int or string/UUID); null when guest.
     * @param  CarbonInterface|null  $occurredAt  Event time (Carbon); null uses document created_at.
     * @param  array<string, mixed>  $metadata  Merged with config context_provider when storing.
     */
    public function storeEvent(
        object $event,
        array $payload,
        ?string $aggregateId = null,
        int|string|null $userId = null,
        ?CarbonInterface $occurredAt = null,
        array $metadata = [],
    ): StoredEvent {
        $data = $this->serializer->serializeStoredEvent(
            event: $event,
            payload: $payload,
            aggregateId: $aggregateId,
            userId: $userId ?? auth()->id(),
            occurredAt: $occurredAt,
            metadata: array_merge($this->getContext(), $metadata),
        );
        $attributes = $this->normalizeStoredEvent($data)->toArray();

        return $this->storedEventModel->newQuery()->create($attributes);
    }

    /**
     * Store model change with prev/changed/diff (EventLog).
     *
     * @param  array<string, mixed>  $prev
     * @param  array<string, mixed>  $changed
     * @param  array<string, mixed>  $diff
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
        $data = new EventLogData(
            entityType: $entityType,
            entityId: $entityId,
            action: $action,
            prev: $prev,
            changed: $changed,
            diff: $diff,
            meta: array_merge($this->getContext(), $meta),
            userId: $userId,
        );
        $attributes = $this->normalizeEventLog($data)->toArray();

        return $this->eventLogEntryModel->newQuery()->create($attributes);
    }

    /** @param iterable<StoredEventData|array<string, mixed>> $events */
    public function recordManyStoredEvents(iterable $events): void
    {
        $records = [];
        foreach ($events as $event) {
            $data = $event instanceof StoredEventData ? $event : StoredEventData::fromArray($event);
            $metadata = array_merge($this->getContext(), $data->metadata);
            $enriched = new StoredEventData(
                eventClass: $data->eventClass,
                payload: $data->payload,
                aggregateId: $data->aggregateId,
                userId: $data->userId ?? auth()->id(),
                occurredAt: $data->occurredAt,
                metadata: $metadata,
                envelope: $data->envelope,
            );

            $records[] = $this->withTimestamps($this->normalizeStoredEvent($enriched)->toArray());
        }

        if ($records === []) {
            return;
        }

        $this->storedEventModel->newQuery()->insert($records);
    }

    /** @param iterable<EventLogData|array<string, mixed>> $logs */
    public function recordManyEventLogs(iterable $logs): void
    {
        $records = [];
        foreach ($logs as $log) {
            $data = $log instanceof EventLogData ? $log : EventLogData::fromArray($log);
            $meta = array_merge($this->getContext(), $data->meta);
            $enriched = new EventLogData(
                entityType: $data->entityType,
                entityId: $data->entityId,
                action: $data->action,
                prev: $data->prev,
                changed: $data->changed,
                diff: $data->diff,
                meta: $meta,
                userId: $data->userId ?? $meta['user_id'] ?? auth()->id(),
            );

            $records[] = $this->withTimestamps($this->normalizeEventLog($enriched)->toArray());
        }

        if ($records === []) {
            return;
        }

        $this->eventLogEntryModel->newQuery()->insert($records);
    }

    private function normalizeStoredEvent(StoredEventData $data): StoredEventData
    {
        return new StoredEventData(
            eventClass: $data->eventClass,
            payload: $this->redactor->redact($data->payload),
            aggregateId: $data->aggregateId,
            userId: $data->userId,
            occurredAt: $data->occurredAt,
            metadata: $this->redactor->redact($data->metadata),
            envelope: $data->envelope,
        );
    }

    private function normalizeEventLog(EventLogData $data): EventLogData
    {
        return new EventLogData(
            entityType: $data->entityType,
            entityId: $data->entityId,
            action: $data->action,
            prev: $this->redactor->redact($data->prev),
            changed: $this->redactor->redact($data->changed),
            diff: $this->redactor->redact($data->diff),
            meta: $this->redactor->redact($data->meta),
            userId: $data->userId,
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function withTimestamps(array $attributes): array
    {
        if (! array_key_exists('created_at', $attributes)) {
            $attributes['created_at'] = Carbon::now();
        }
        if (! array_key_exists('updated_at', $attributes)) {
            $attributes['updated_at'] = Carbon::now();
        }

        return $attributes;
    }

    /** @return array<string, mixed> */
    private function getContext(): array
    {
        $provider = config('events.context_provider');
        if ($provider === null || ! is_callable($provider)) {
            return [];
        }

        $context = $provider();

        if (! is_array($context)) {
            return [];
        }

        $normalized = [];
        foreach ($context as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
