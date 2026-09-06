<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use JOOservices\LaravelEvents\Data\EventLogData;
use JOOservices\LaravelEvents\Data\StoredEventData;
use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JOOservices\LaravelEvents\Exceptions\InvalidConfigurationException;
use JOOservices\LaravelEvents\Serialization\ArrayEventSerializer;
use JOOservices\LaravelEvents\Serialization\EventSerializerInterface;
use JOOservices\LaravelEvents\Support\PayloadRedactor;
use Psr\Clock\ClockInterface;
use Throwable;

final class EventService implements EventPersisterInterface
{
    public function __construct(
        private readonly StoredEvent $storedEventModel,
        private readonly EventLogEntry $eventLogEntryModel,
        private readonly PayloadRedactor $redactor = new PayloadRedactor(),
        private readonly EventSerializerInterface $serializer = new ArrayEventSerializer(),
        private readonly ?ClockInterface $clock = null,
    ) {
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
        int | string | null $userId = null,
        ?CarbonInterface $occurredAt = null,
        array $metadata = [],
    ): StoredEvent {
        $mergedMetadata = array_merge($this->getContext(), $metadata);
        $data = $this->serializer->serializeStoredEvent(
            event: $event,
            payload: $payload,
            aggregateId: $aggregateId,
            userId: $userId ?? $this->resolveUserId($mergedMetadata),
            occurredAt: $occurredAt,
            metadata: $mergedMetadata,
        );
        $attributes = $this->normalizeStoredEvent($data)->toArray();

        /** @var StoredEvent $created */
        $created = $this->storedEventModel->newQuery()->create($attributes);

        return $created;
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
        int | string | null $userId = null,
    ): EventLogEntry {
        $mergedMeta = array_merge($this->getContext(), $meta);
        $userId = $userId ?? $this->resolveUserId($mergedMeta);
        $data = new EventLogData(
            entityType: $entityType,
            entityId: $entityId,
            action: $action,
            prev: $prev,
            changed: $changed,
            diff: $diff,
            meta: $mergedMeta,
            userId: $userId,
        );
        $attributes = $this->normalizeEventLog($data)->toArray();

        /** @var EventLogEntry $created */
        $created = $this->eventLogEntryModel->newQuery()->create($attributes);

        return $created;
    }

    /** @param iterable<StoredEventData|array<string, mixed>> $events */
    public function recordManyStoredEvents(iterable $events): void
    {
        $records = [];
        $context = $this->getContext();
        $timestamp = $this->now();

        foreach ($events as $event) {
            $data = $event instanceof StoredEventData ? $event : StoredEventData::fromArray($event);
            $metadata = array_merge($context, $data->metadata);
            $enriched = $this->serializer->ensureEnvelope(new StoredEventData(
                eventClass: $data->eventClass,
                payload: $data->payload,
                aggregateId: $data->aggregateId,
                userId: $data->userId ?? $this->resolveUserId($metadata),
                occurredAt: $data->occurredAt,
                metadata: $metadata,
                envelope: $data->envelope,
            ));

            $records[] = $this->withTimestamps($this->normalizeStoredEvent($enriched)->toArray(), $timestamp);
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
        $context = $this->getContext();
        $timestamp = $this->now();

        foreach ($logs as $log) {
            $data = $log instanceof EventLogData ? $log : EventLogData::fromArray($log);
            $meta = array_merge($context, $data->meta);
            $enriched = new EventLogData(
                entityType: $data->entityType,
                entityId: $data->entityId,
                action: $data->action,
                prev: $data->prev,
                changed: $data->changed,
                diff: $data->diff,
                meta: $meta,
                userId: $data->userId ?? $this->resolveUserId($meta),
            );

            $records[] = $this->withTimestamps($this->normalizeEventLog($enriched)->toArray(), $timestamp);
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
    private function withTimestamps(array $attributes, ?CarbonInterface $timestamp = null): array
    {
        $timestamp ??= $this->now();

        if (! array_key_exists('created_at', $attributes)) {
            $attributes['created_at'] = $timestamp;
        }
        if (! array_key_exists('updated_at', $attributes)) {
            $attributes['updated_at'] = $timestamp;
        }

        return $attributes;
    }

    private function now(): CarbonInterface
    {
        if ($this->clock !== null) {
            return Carbon::instance($this->clock->now());
        }

        return Carbon::now();
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function resolveUserId(array $context): int | string | null
    {
        $fromContext = $context['user_id'] ?? null;
        if (is_int($fromContext) || is_string($fromContext)) {
            return $fromContext;
        }

        $authId = Auth::id();

        return is_int($authId) || is_string($authId) ? $authId : null;
    }

    /** @return array<string, mixed> */
    private function getContext(): array
    {
        $provider = $this->resolveContextProvider();
        if ($provider === null) {
            return [];
        }

        $context = $provider();

        return is_array($context) ? $this->stringKeyed($context) : [];
    }

    /**
     * @return (callable(): mixed)|null
     */
    private function resolveContextProvider(): ?callable
    {
        $provider = config('events.context_provider');
        if ($provider === null) {
            return null;
        }

        if (is_string($provider) && $provider !== '') {
            try {
                $provider = app($provider);
            } catch (Throwable $exception) {
                throw InvalidConfigurationException::invalidContextProvider($provider, $exception);
            }
        }

        if (! is_callable($provider)) {
            $label = is_object($provider) ? $provider::class : get_debug_type($provider);

            throw InvalidConfigurationException::nonCallableContextProvider($label);
        }

        return $provider;
    }

    /**
     * @param  array<mixed>  $values
     * @return array<string, mixed>
     */
    private function stringKeyed(array $values): array
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
