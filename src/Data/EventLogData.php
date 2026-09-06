<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use DateTimeInterface;
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
        public readonly ?DocumentIdentity $identity = null,
    ) {
        if ($this->entityType === '' || $this->entityId === '' || $this->action === '') {
            throw InvalidEventDataException::missingField('entity_type, entity_id, and action');
        }
    }

    public function documentId(): mixed
    {
        return $this->identity?->id;
    }

    public function createdAt(): ?DateTimeInterface
    {
        return $this->identity?->createdAt;
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
        $entityType = $values['entity_type'] ?? $values['entityType'] ?? null;
        $entityIdRaw = $values['entity_id'] ?? $values['entityId'] ?? null;
        $action = $values['action'] ?? null;

        if (! is_string($entityType)) {
            throw InvalidEventDataException::missingField('entity_type');
        }
        if (! is_string($entityIdRaw) && ! is_int($entityIdRaw)) {
            throw InvalidEventDataException::missingField('entity_id');
        }
        if (! is_string($action)) {
            throw InvalidEventDataException::missingField('action');
        }

        self::assertArrayFields($values, ['prev', 'changed', 'diff', 'meta']);

        return [
            'entity_type' => $entityType,
            'entity_id' => (string) $entityIdRaw,
            'action' => $action,
            'prev' => $values['prev'] ?? [],
            'changed' => $values['changed'] ?? [],
            'diff' => $values['diff'] ?? [],
            'meta' => $values['meta'] ?? [],
            'user_id' => $values['user_id'] ?? $values['userId'] ?? null,
            'identity' => DocumentIdentity::fromStorageArray($values),
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  list<string>  $keys
     */
    private static function assertArrayFields(array $values, array $keys): void
    {
        foreach ($keys as $key) {
            if (isset($values[$key]) && ! is_array($values[$key])) {
                throw InvalidEventDataException::invalidType($key, 'an array');
            }
        }
    }
}
