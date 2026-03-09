<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventSourcing\Concerns;

use Carbon\CarbonInterface;

/**
 * Trait providing default implementations for optional EventSourcing methods.
 * Use with EventSourcingInterface so you only need to implement payload() and aggregateId().
 */
trait HasEventSourcingDefaults
{
    /**
     * Event time; null means use document created_at.
     */
    public function occurredAt(): ?CarbonInterface
    {
        return null;
    }

    /**
     * Extra metadata merged with config context_provider when storing.
     *
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return [];
    }
}
