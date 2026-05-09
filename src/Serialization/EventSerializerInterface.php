<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Serialization;

use Carbon\CarbonInterface;
use JooServices\LaravelEvents\Data\StoredEventData;

interface EventSerializerInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $metadata
     */
    public function serializeStoredEvent(
        object $event,
        array $payload,
        ?string $aggregateId = null,
        int|string|null $userId = null,
        ?CarbonInterface $occurredAt = null,
        array $metadata = [],
    ): StoredEventData;
}
