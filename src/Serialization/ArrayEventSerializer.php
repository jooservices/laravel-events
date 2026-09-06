<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Serialization;

use Carbon\CarbonInterface;
use Illuminate\Support\Str;
use JOOservices\LaravelEvents\Data\EventEnvelopeData;
use JOOservices\LaravelEvents\Data\StoredEventData;
use JOOservices\LaravelEvents\Support\EventMetadata;

class ArrayEventSerializer implements EventSerializerInterface
{
    public function serializeStoredEvent(
        object $event,
        array $payload,
        ?string $aggregateId = null,
        int | string | null $userId = null,
        ?CarbonInterface $occurredAt = null,
        array $metadata = [],
    ): StoredEventData {
        return $this->ensureEnvelope(new StoredEventData(
            eventClass: $event::class,
            payload: $payload,
            aggregateId: $aggregateId,
            userId: $userId,
            occurredAt: $occurredAt,
            metadata: $metadata,
            envelope: EventEnvelopeData::fromArray($metadata),
        ));
    }

    public function ensureEnvelope(StoredEventData $data): StoredEventData
    {
        $metadata = $data->metadata;
        $existing = $data->envelope;

        return new StoredEventData(
            eventClass: $data->eventClass,
            payload: $data->payload,
            aggregateId: $data->aggregateId,
            userId: $data->userId,
            occurredAt: $data->occurredAt,
            metadata: $metadata,
            envelope: new EventEnvelopeData(
                eventId: $this->firstNonEmptyString(
                    $existing instanceof EventEnvelopeData ? $existing->eventId : null,
                    $this->stringMetadata($metadata, EventMetadata::EVENT_ID),
                ) ?? (string) Str::uuid(),
                eventName: $this->firstNonEmptyString(
                    $existing instanceof EventEnvelopeData ? $existing->eventName : null,
                    $this->stringMetadata($metadata, EventMetadata::EVENT_NAME),
                ) ?? $this->shortClassName($data->eventClass),
                eventCategory: ($existing instanceof EventEnvelopeData ? $existing->eventCategory : null)
                    ?? $this->stringMetadata($metadata, EventMetadata::EVENT_CATEGORY),
                aggregateType: ($existing instanceof EventEnvelopeData ? $existing->aggregateType : null)
                    ?? $this->stringMetadata($metadata, EventMetadata::AGGREGATE_TYPE),
                schemaVersion: ($existing instanceof EventEnvelopeData ? $existing->schemaVersion : null)
                    ?? $this->stringOrIntMetadata($metadata, EventMetadata::SCHEMA_VERSION),
                eventVersion: ($existing instanceof EventEnvelopeData ? $existing->eventVersion : null)
                    ?? $this->stringOrIntMetadata($metadata, EventMetadata::EVENT_VERSION),
                correlationId: ($existing instanceof EventEnvelopeData ? $existing->correlationId : null)
                    ?? $this->stringMetadata($metadata, EventMetadata::CORRELATION_ID),
                causationId: ($existing instanceof EventEnvelopeData ? $existing->causationId : null)
                    ?? $this->stringMetadata($metadata, EventMetadata::CAUSATION_ID),
            ),
            identity: $data->identity,
        );
    }

    private function shortClassName(string $class): string
    {
        $position = strrpos($class, '\\');

        return $position === false ? $class : substr($class, $position + 1);
    }

    private function firstNonEmptyString(?string ...$candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if ($candidate !== null && $candidate !== '') {
                return $candidate;
            }
        }

        return null;
    }

    /** @param array<string, mixed> $metadata */
    private function stringMetadata(array $metadata, string $key): ?string
    {
        $value = $metadata[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** @param array<string, mixed> $metadata */
    private function stringOrIntMetadata(array $metadata, string $key): int | string | null
    {
        $value = $metadata[$key] ?? null;

        return is_string($value) || is_int($value) ? $value : null;
    }
}
