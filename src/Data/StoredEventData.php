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
    ) {
        if ($this->eventClass === '') {
            throw InvalidEventDataException::emptyEventClass();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?Context $ctx = null): static
    {
        $normalized = self::normalizeInput($data);

        return parent::fromArray($normalized, $ctx);
    }

    /**
     * Persist as a flat MongoDB document (envelope fields at the top level).
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

        $occurredAt = $values['occurred_at'] ?? $values['occurredAt'] ?? null;
        if ($occurredAt !== null && ! $occurredAt instanceof DateTimeInterface) {
            throw InvalidEventDataException::invalidType('occurred_at', 'a DateTimeInterface or null');
        }

        $envelope = $values['envelope'] ?? null;
        if ($envelope instanceof EventEnvelopeData) {
            $envelopeData = $envelope;
        } elseif (is_array($envelope)) {
            $envelopeData = EventEnvelopeData::fromArray($envelope);
        } else {
            $envelopeData = EventEnvelopeData::fromArray($values);
        }

        $aggregateId = $values['aggregate_id'] ?? $values['aggregateId'] ?? null;

        return [
            'event_class' => $eventClass,
            'payload' => $payload,
            'aggregate_id' => $aggregateId === null ? null : (string) $aggregateId,
            'user_id' => $values['user_id'] ?? $values['userId'] ?? null,
            'occurred_at' => $occurredAt,
            'metadata' => $metadata,
            'envelope' => $envelopeData,
        ];
    }
}
