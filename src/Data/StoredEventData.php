<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use DateTimeInterface;
use JOOservices\Dto\Attributes\MapFrom;
use JOOservices\Dto\Attributes\MapTo;
use JOOservices\Dto\Core\Context;
use JOOservices\Dto\Core\Dto;
use JOOservices\LaravelEvents\Exceptions\InvalidEventDataException;

final class StoredEventData extends Dto
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        #[MapFrom('event_class')]
        #[MapTo('event_class')]
        public readonly string $eventClass,
        public readonly array $payload,
        #[MapFrom('aggregate_id')]
        #[MapTo('aggregate_id')]
        public readonly ?string $aggregateId = null,
        #[MapFrom('user_id')]
        #[MapTo('user_id')]
        public readonly int | string | null $userId = null,
        #[MapFrom('occurred_at')]
        #[MapTo('occurred_at')]
        public readonly ?DateTimeInterface $occurredAt = null,
        public readonly array $metadata = [],
        public readonly ?EventEnvelopeData $envelope = null,
        public readonly ?DocumentIdentity $identity = null,
    ) {
        if ($this->eventClass === '') {
            throw InvalidEventDataException::emptyEventClass();
        }
    }

    public function documentId(): mixed
    {
        return $this->identity?->id;
    }

    public function createdAt(): ?DateTimeInterface
    {
        return $this->identity?->createdAt;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?Context $ctx = null): static
    {
        return parent::fromArray(self::normalizeInput($data), $ctx);
    }

    /**
     * Persist as a flat MongoDB document (envelope fields at the top level).
     * Query identity fields are omitted so writes stay insert-safe.
     *
     * @return array{
     *     event_class: string,
     *     aggregate_id: string|null,
     *     payload: array<string, mixed>,
     *     metadata: array<string, mixed>,
     *     user_id: int|string|null,
     *     occurred_at: DateTimeInterface|null,
     *     event_id: string|null,
     *     event_name: string|null,
     *     event_category: string|null,
     *     aggregate_type: string|null,
     *     schema_version: int|string|null,
     *     event_version: int|string|null,
     *     correlation_id: string|null,
     *     causation_id: string|null
     * }
     */
    public function toArray(?Context $ctx = null): array
    {
        return [
            'event_class' => $this->eventClass,
            'aggregate_id' => $this->aggregateId,
            'payload' => $this->payload,
            'metadata' => $this->metadata,
            'user_id' => $this->userId,
            'occurred_at' => $this->occurredAt,
            'event_id' => $this->envelope?->eventId,
            'event_name' => $this->envelope?->eventName,
            'event_category' => $this->envelope?->eventCategory,
            'aggregate_type' => $this->envelope?->aggregateType,
            'schema_version' => $this->envelope?->schemaVersion,
            'event_version' => $this->envelope?->eventVersion,
            'correlation_id' => $this->envelope?->correlationId,
            'causation_id' => $this->envelope?->causationId,
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private static function normalizeInput(array $values): array
    {
        $eventClass = $values['event_class'] ?? $values['eventClass'] ?? null;
        if (! is_string($eventClass)) {
            throw InvalidEventDataException::missingField('event_class');
        }

        $payload = $values['payload'] ?? [];
        $metadata = $values['metadata'] ?? [];
        if (! is_array($payload) || ! is_array($metadata)) {
            throw InvalidEventDataException::invalidType('payload and metadata', 'arrays');
        }

        $occurredAt = self::optionalDateTime($values, 'occurred_at', 'occurredAt');
        $aggregateId = $values['aggregate_id'] ?? $values['aggregateId'] ?? null;

        return [
            'event_class' => $eventClass,
            'payload' => $payload,
            'aggregate_id' => $aggregateId === null ? null : (string) $aggregateId,
            'user_id' => $values['user_id'] ?? $values['userId'] ?? null,
            'occurred_at' => $occurredAt,
            'metadata' => $metadata,
            'envelope' => self::resolveEnvelope($values),
            'identity' => DocumentIdentity::fromStorageArray($values),
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private static function resolveEnvelope(array $values): EventEnvelopeData
    {
        $envelope = $values['envelope'] ?? null;
        if ($envelope instanceof EventEnvelopeData) {
            return $envelope;
        }
        if (is_array($envelope)) {
            return EventEnvelopeData::fromArray($envelope);
        }

        return EventEnvelopeData::fromArray($values);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private static function optionalDateTime(array $values, string $snake, string $camel): ?DateTimeInterface
    {
        $value = $values[$snake] ?? $values[$camel] ?? null;
        if ($value !== null && ! $value instanceof DateTimeInterface) {
            throw InvalidEventDataException::invalidType($snake, 'a DateTimeInterface or null');
        }

        return $value;
    }
}
