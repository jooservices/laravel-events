<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents;

use Carbon\CarbonInterface;
use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;

interface EventPersisterInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $metadata
     */
    public function storeEvent(
        object $event,
        array $payload,
        ?string $aggregateId = null,
        int | string | null $userId = null,
        ?CarbonInterface $occurredAt = null,
        array $metadata = [],
    ): StoredEvent;

    /**
     * @param  array<string, mixed>  $prev
     * @param  array<string, mixed>  $changed
     * @param  array<string, mixed>  $diff
     * @param  array<string, mixed>  $meta
     */
    public function logChange(
        string $entityType,
        string $entityId,
        string $action,
        array $prev,
        array $changed,
        array $diff,
        array $meta = [],
        int | string | null $userId = null,
    ): EventLogEntry;
}
