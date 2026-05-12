<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Support;

final class EventMetadataBuilder
{
    /** @var array<string, mixed> */
    private array $metadata = [];

    public function correlationId(string $id): self
    {
        $this->metadata[EventMetadata::CORRELATION_ID] = $id;

        return $this;
    }

    public function causationId(string $id): self
    {
        $this->metadata[EventMetadata::CAUSATION_ID] = $id;

        return $this;
    }

    public function requestId(string $id): self
    {
        $this->metadata[EventMetadata::REQUEST_ID] = $id;

        return $this;
    }

    public function source(string $source, ?string $channel = null): self
    {
        $this->metadata[EventMetadata::SOURCE] = $source;
        if ($channel !== null) {
            $this->metadata[EventMetadata::CHANNEL] = $channel;
        } else {
            unset($this->metadata[EventMetadata::CHANNEL]);
        }

        return $this;
    }

    public function schemaVersion(int|string $version): self
    {
        $this->metadata[EventMetadata::SCHEMA_VERSION] = $version;

        return $this;
    }

    public function eventVersion(int|string $version): self
    {
        $this->metadata[EventMetadata::EVENT_VERSION] = $version;

        return $this;
    }

    public function tenantId(int|string $tenantId): self
    {
        $this->metadata[EventMetadata::TENANT_ID] = $tenantId;

        return $this;
    }

    public function eventCategory(string $eventCategory): self
    {
        $this->metadata[EventMetadata::EVENT_CATEGORY] = $eventCategory;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->metadata;
    }
}
