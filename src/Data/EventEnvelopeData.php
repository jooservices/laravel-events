<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use JOOservices\Dto\Attributes\MapFrom;
use JOOservices\Dto\Attributes\MapTo;
use JOOservices\Dto\Core\Context;
use JOOservices\Dto\Core\Dto;

final class EventEnvelopeData extends Dto
{
    public function __construct(
        #[MapFrom('event_id')]
        #[MapTo('event_id')]
        public readonly ?string $eventId = null,
        #[MapFrom('event_name')]
        #[MapTo('event_name')]
        public readonly ?string $eventName = null,
        #[MapFrom('event_category')]
        #[MapTo('event_category')]
        public readonly ?string $eventCategory = null,
        #[MapFrom('aggregate_type')]
        #[MapTo('aggregate_type')]
        public readonly ?string $aggregateType = null,
        #[MapFrom('schema_version')]
        #[MapTo('schema_version')]
        public readonly int | string | null $schemaVersion = null,
        #[MapFrom('event_version')]
        #[MapTo('event_version')]
        public readonly int | string | null $eventVersion = null,
        #[MapFrom('correlation_id')]
        #[MapTo('correlation_id')]
        public readonly ?string $correlationId = null,
        #[MapFrom('causation_id')]
        #[MapTo('causation_id')]
        public readonly ?string $causationId = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?Context $ctx = null): static
    {
        return parent::fromArray(self::normalizeInput($data), $ctx);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private static function normalizeInput(array $values): array
    {
        return [
            'event_id' => self::nullableString($values, 'event_id', 'eventId'),
            'event_name' => self::nullableString($values, 'event_name', 'eventName'),
            'event_category' => self::nullableString($values, 'event_category', 'eventCategory'),
            'aggregate_type' => self::nullableString($values, 'aggregate_type', 'aggregateType'),
            'schema_version' => self::nullableStringOrInt($values, 'schema_version', 'schemaVersion'),
            'event_version' => self::nullableStringOrInt($values, 'event_version', 'eventVersion'),
            'correlation_id' => self::nullableString($values, 'correlation_id', 'correlationId'),
            'causation_id' => self::nullableString($values, 'causation_id', 'causationId'),
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private static function nullableString(array $values, string $snakeKey, string $camelKey): ?string
    {
        $value = $values[$snakeKey] ?? $values[$camelKey] ?? null;

        return $value === null ? null : (string) $value;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    private static function nullableStringOrInt(array $values, string $snakeKey, string $camelKey): int | string | null
    {
        $value = $values[$snakeKey] ?? $values[$camelKey] ?? null;

        return is_int($value) || is_string($value) ? $value : null;
    }
}
