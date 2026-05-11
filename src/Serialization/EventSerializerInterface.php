<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Serialization;

use Carbon\CarbonInterface;
use JOOservices\LaravelEvents\Data\StoredEventData;

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
