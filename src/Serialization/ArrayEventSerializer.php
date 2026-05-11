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
        int|string|null $userId = null,
        ?CarbonInterface $occurredAt = null,
        array $metadata = [],
    ): StoredEventData {
        return new StoredEventData(
            eventClass: $event::class,
            payload: $payload,
            aggregateId: $aggregateId,
            userId: $userId,
            occurredAt: $occurredAt,
            metadata: $metadata,
            envelope: new EventEnvelopeData(
                eventId: $this->stringMetadata($metadata, EventMetadata::EVENT_ID)
                    ?? (string) Str::uuid(),
                eventName: $this->stringMetadata($metadata, EventMetadata::EVENT_NAME)
                    ?? $this->shortClassName($event::class),
                eventCategory: $this->stringMetadata($metadata, EventMetadata::EVENT_CATEGORY),
                aggregateType: $this->stringMetadata($metadata, EventMetadata::AGGREGATE_TYPE),
                schemaVersion: $this->stringOrIntMetadata($metadata, EventMetadata::SCHEMA_VERSION),
                eventVersion: $this->stringOrIntMetadata($metadata, EventMetadata::EVENT_VERSION),
                correlationId: $this->stringMetadata($metadata, EventMetadata::CORRELATION_ID),
                causationId: $this->stringMetadata($metadata, EventMetadata::CAUSATION_ID),
            ),
        );
    }

    private function shortClassName(string $class): string
    {
        $position = strrpos($class, '\\');

        return $position === false ? $class : substr($class, $position + 1);
    }

    /** @param array<string, mixed> $metadata */
    private function stringMetadata(array $metadata, string $key): ?string
    {
        $value = $metadata[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** @param array<string, mixed> $metadata */
    private function stringOrIntMetadata(array $metadata, string $key): int|string|null
    {
        $value = $metadata[$key] ?? null;

        return is_string($value) || is_int($value) ? $value : null;
    }
}
