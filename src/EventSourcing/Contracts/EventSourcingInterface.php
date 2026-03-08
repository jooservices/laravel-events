<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventSourcing\Contracts;

/**
 * Events implementing this interface are persisted to the stored_events collection.
 *
 * Optional methods (use method_exists or implement in your event):
 * - occurredAt(): ?\DateTimeInterface — event time; when null/absent, created_at is used.
 * - metadata(): array — extra metadata merged with config context_provider (e.g. request_id, channel).
 */
interface EventSourcingInterface
{
    public function payload(): array;

    public function aggregateId(): ?string;
}
