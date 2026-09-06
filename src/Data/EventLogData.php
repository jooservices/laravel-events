<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use JOOservices\Dto\Attributes\MapFrom;
use JOOservices\Dto\Attributes\MapTo;
use JOOservices\Dto\Core\Context;
use JOOservices\Dto\Core\Dto;
use JOOservices\LaravelEvents\Exceptions\InvalidEventDataException;

final class EventLogData extends Dto
{
    /**
     * @param  array<string, mixed>  $prev
     * @param  array<string, mixed>  $changed
     * @param  array<string, mixed>  $diff
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        #[MapFrom('entity_type')]
        #[MapTo('entity_type')]
        public readonly string $entityType,
        #[MapFrom('entity_id')]
        #[MapTo('entity_id')]
        public readonly string $entityId,
        public readonly string $action,
        public readonly array $prev = [],
        public readonly array $changed = [],
        public readonly array $diff = [],
        public readonly array $meta = [],
        #[MapFrom('user_id')]
        #[MapTo('user_id')]
        public readonly int | string | null $userId = null,
    ) {
        if ($this->entityType === '' || $this->entityId === '' || $this->action === '') {
            throw InvalidEventDataException::missingField('entity_type, entity_id, and action');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?Context $ctx = null): static
    {
        return parent::fromArray(self::normalizeInput($data), $ctx);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(?Context $ctx = null): array
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

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private static function normalizeInput(array $values): array
    {
        foreach (['entity_type', 'entity_id', 'action'] as $key) {
            if (! isset($values[$key]) || ! is_string($values[$key])) {
                throw InvalidEventDataException::missingField($key);
            }
        }

        foreach (['prev', 'changed', 'diff', 'meta'] as $key) {
            if (isset($values[$key]) && ! is_array($values[$key])) {
                throw InvalidEventDataException::invalidType($key, 'an array');
            }
        }

        return [
            'entity_type' => $values['entity_type'],
            'entity_id' => $values['entity_id'],
            'action' => $values['action'],
            'prev' => $values['prev'] ?? [],
            'changed' => $values['changed'] ?? [],
            'diff' => $values['diff'] ?? [],
            'meta' => $values['meta'] ?? [],
            'user_id' => $values['user_id'] ?? null,
        ];
    }
}
