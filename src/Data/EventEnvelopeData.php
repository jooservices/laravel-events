<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Data;

final readonly class EventEnvelopeData
{
    public function __construct(
        public ?string $eventId = null,
        public ?string $eventName = null,
        public ?string $aggregateType = null,
        public int|string|null $schemaVersion = null,
        public int|string|null $eventVersion = null,
        public ?string $correlationId = null,
        public ?string $causationId = null,
    ) {}

    /** @param array<string, mixed> $values */
    public static function fromArray(array $values): self
    {
        return new self(
            eventId: self::nullableString($values, 'event_id', 'eventId'),
            eventName: self::nullableString($values, 'event_name', 'eventName'),
            aggregateType: self::nullableString($values, 'aggregate_type', 'aggregateType'),
            schemaVersion: self::nullableStringOrInt($values, 'schema_version', 'schemaVersion'),
            eventVersion: self::nullableStringOrInt($values, 'event_version', 'eventVersion'),
            correlationId: self::nullableString($values, 'correlation_id', 'correlationId'),
            causationId: self::nullableString($values, 'causation_id', 'causationId'),
        );
    }

    /** @param array<string, mixed> $values */
    private static function nullableString(array $values, string $snakeKey, string $camelKey): ?string
    {
        $value = $values[$snakeKey] ?? $values[$camelKey] ?? null;

        return $value === null ? null : (string) $value;
    }

    /** @param array<string, mixed> $values */
    private static function nullableStringOrInt(array $values, string $snakeKey, string $camelKey): int|string|null
    {
        $value = $values[$snakeKey] ?? $values[$camelKey] ?? null;

        return is_int($value) || is_string($value) ? $value : null;
    }
}
