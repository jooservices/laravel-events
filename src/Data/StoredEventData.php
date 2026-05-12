<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use DateTimeInterface;
use InvalidArgumentException;

final readonly class StoredEventData
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $eventClass,
        public array $payload,
        public ?string $aggregateId = null,
        public int|string|null $userId = null,
        public ?DateTimeInterface $occurredAt = null,
        public array $metadata = [],
        public ?EventEnvelopeData $envelope = null,
    ) {
        if ($this->eventClass === '') {
            throw new InvalidArgumentException('Stored event class cannot be empty.');
        }
    }

    /** @param array<string, mixed> $values */
    public static function fromArray(array $values): self
    {
        $eventClass = $values['event_class'] ?? $values['eventClass'] ?? null;
        if (! is_string($eventClass)) {
            throw new InvalidArgumentException('Stored event data requires an event_class string.');
        }

        $payload = $values['payload'] ?? [];
        $metadata = $values['metadata'] ?? [];

        if (! is_array($payload) || ! is_array($metadata)) {
            throw new InvalidArgumentException('Stored event payload and metadata must be arrays.');
        }

        $occurredAt = $values['occurred_at'] ?? $values['occurredAt'] ?? null;
        if ($occurredAt !== null && ! $occurredAt instanceof DateTimeInterface) {
            throw new InvalidArgumentException('Stored event occurred_at must be a DateTimeInterface or null.');
        }

        return new self(
            eventClass: $eventClass,
            payload: $payload,
            aggregateId: isset($values['aggregate_id'])
                ? (string) $values['aggregate_id']
                : (isset($values['aggregateId']) ? (string) $values['aggregateId'] : null),
            userId: $values['user_id'] ?? $values['userId'] ?? null,
            occurredAt: $occurredAt,
            metadata: $metadata,
            envelope: EventEnvelopeData::fromArray($values),
        );
    }

    /**
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
    public function toArray(): array
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
}
