<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Support;

final class EventMetadata
{
    public const REQUEST_ID = 'request_id';

    public const CORRELATION_ID = 'correlation_id';

    public const CAUSATION_ID = 'causation_id';

    public const SOURCE = 'source';

    public const CHANNEL = 'channel';

    public const REASON_CODE = 'reason_code';

    public const SCHEMA_VERSION = 'schema_version';

    public const EVENT_VERSION = 'event_version';

    public const TENANT_ID = 'tenant_id';

    public const REVERTED_EVENT_ID = 'reverted_event_id';

    public const SUPERSEDES_EVENT_ID = 'supersedes_event_id';

    public const CORRECTION_OF = 'correction_of';

    public const CORRECTION_REASON = 'correction_reason';

    public static function make(): EventMetadataBuilder
    {
        return new EventMetadataBuilder;
    }

    /**
     * @return array<string, mixed>
     */
    public static function trace(
        ?string $requestId = null,
        ?string $correlationId = null,
        ?string $causationId = null,
    ): array {
        return self::withoutNulls([
            self::REQUEST_ID => $requestId,
            self::CORRELATION_ID => $correlationId,
            self::CAUSATION_ID => $causationId,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function source(
        ?string $source = null,
        ?string $channel = null,
        ?string $reasonCode = null,
    ): array {
        return self::withoutNulls([
            self::SOURCE => $source,
            self::CHANNEL => $channel,
            self::REASON_CODE => $reasonCode,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function version(int|string|null $schemaVersion = null, int|string|null $eventVersion = null): array
    {
        return self::withoutNulls([
            self::SCHEMA_VERSION => $schemaVersion,
            self::EVENT_VERSION => $eventVersion,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function tenant(int|string|null $tenantId = null): array
    {
        return self::withoutNulls([self::TENANT_ID => $tenantId]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function correction(
        ?string $revertedEventId = null,
        ?string $supersedesEventId = null,
        ?string $correctionOf = null,
        ?string $correctionReason = null,
    ): array {
        return self::withoutNulls([
            self::REVERTED_EVENT_ID => $revertedEventId,
            self::SUPERSEDES_EVENT_ID => $supersedesEventId,
            self::CORRECTION_OF => $correctionOf,
            self::CORRECTION_REASON => $correctionReason,
        ]);
    }

    /**
     * @param  array<string, mixed>  ...$metadata
     * @return array<string, mixed>
     */
    public static function merge(array ...$metadata): array
    {
        return self::withoutNulls(array_merge(...$metadata));
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    public static function withoutNulls(array $metadata): array
    {
        return array_filter($metadata, static fn (mixed $value): bool => $value !== null);
    }
}
