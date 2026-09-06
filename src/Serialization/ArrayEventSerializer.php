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
        $envelope = $this->buildEnvelope($data);
        $metadata = $this->syncTraceMetadata($data->metadata, $envelope);

        return new StoredEventData(
            eventClass: $data->eventClass,
            payload: $data->payload,
            aggregateId: $data->aggregateId,
            userId: $data->userId,
            occurredAt: $data->occurredAt,
            metadata: $metadata,
            envelope: $envelope,
            identity: $data->identity,
        );
    }

    private function buildEnvelope(StoredEventData $data): EventEnvelopeData
    {
        $metadata = $data->metadata;
        $existing = $data->envelope;

        return new EventEnvelopeData(
            eventId: $this->firstNonEmptyString(
                $this->envelopeString($existing, 'eventId'),
                $this->stringMetadata($metadata, EventMetadata::EVENT_ID),
            ) ?? (string) Str::uuid(),
            eventName: $this->firstNonEmptyString(
                $this->envelopeString($existing, 'eventName'),
                $this->stringMetadata($metadata, EventMetadata::EVENT_NAME),
            ) ?? $this->shortClassName($data->eventClass),
            eventCategory: $this->envelopeString($existing, 'eventCategory')
                ?? $this->stringMetadata($metadata, EventMetadata::EVENT_CATEGORY),
            aggregateType: $this->envelopeString($existing, 'aggregateType')
                ?? $this->stringMetadata($metadata, EventMetadata::AGGREGATE_TYPE),
            schemaVersion: $this->envelopeStringOrInt($existing, 'schemaVersion')
                ?? $this->stringOrIntMetadata($metadata, EventMetadata::SCHEMA_VERSION),
            eventVersion: $this->envelopeStringOrInt($existing, 'eventVersion')
                ?? $this->stringOrIntMetadata($metadata, EventMetadata::EVENT_VERSION),
            correlationId: $this->envelopeString($existing, 'correlationId')
                ?? $this->stringMetadata($metadata, EventMetadata::CORRELATION_ID),
            causationId: $this->envelopeString($existing, 'causationId')
                ?? $this->stringMetadata($metadata, EventMetadata::CAUSATION_ID),
        );
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private function syncTraceMetadata(array $metadata, EventEnvelopeData $envelope): array
    {
        if ($envelope->correlationId !== null && ! isset($metadata[EventMetadata::CORRELATION_ID])) {
            $metadata[EventMetadata::CORRELATION_ID] = $envelope->correlationId;
        }
        if ($envelope->causationId !== null && ! isset($metadata[EventMetadata::CAUSATION_ID])) {
            $metadata[EventMetadata::CAUSATION_ID] = $envelope->causationId;
        }

        return $metadata;
    }

    private function envelopeString(?EventEnvelopeData $envelope, string $property): ?string
    {
        if (! $envelope instanceof EventEnvelopeData) {
            return null;
        }

        $value = $envelope->{$property};

        return is_string($value) ? $value : null;
    }

    private function envelopeStringOrInt(?EventEnvelopeData $envelope, string $property): int | string | null
    {
        if (! $envelope instanceof EventEnvelopeData) {
            return null;
        }

        $value = $envelope->{$property};

        return is_string($value) || is_int($value) ? $value : null;
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
