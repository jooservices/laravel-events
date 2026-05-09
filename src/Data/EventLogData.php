<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Data;

use InvalidArgumentException;

final readonly class EventLogData
{
    /**
     * @param  array<string, mixed>  $prev
     * @param  array<string, mixed>  $changed
     * @param  array<string, mixed>  $diff
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public string $entityType,
        public string $entityId,
        public string $action,
        public array $prev = [],
        public array $changed = [],
        public array $diff = [],
        public array $meta = [],
        public int|string|null $userId = null,
    ) {
        if ($this->entityType === '' || $this->entityId === '' || $this->action === '') {
            throw new InvalidArgumentException('Event log entity type, entity id, and action are required.');
        }
    }

    /** @param array<string, mixed> $values */
    public static function fromArray(array $values): self
    {
        foreach (['entity_type', 'entity_id', 'action'] as $key) {
            if (! isset($values[$key]) || ! is_string($values[$key])) {
                throw new InvalidArgumentException("Event log data requires {$key}.");
            }
        }

        foreach (['prev', 'changed', 'diff', 'meta'] as $key) {
            if (isset($values[$key]) && ! is_array($values[$key])) {
                throw new InvalidArgumentException("Event log {$key} must be an array.");
            }
        }

        return new self(
            entityType: $values['entity_type'],
            entityId: $values['entity_id'],
            action: $values['action'],
            prev: $values['prev'] ?? [],
            changed: $values['changed'] ?? [],
            diff: $values['diff'] ?? [],
            meta: $values['meta'] ?? [],
            userId: $values['user_id'] ?? null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'action' => $this->action,
            'prev' => $this->prev,
            'changed' => $this->changed,
            'diff' => $this->diff,
            'meta' => $this->meta,
            'user_id' => $this->userId,
        ];
    }
}
