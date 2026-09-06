<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use DateTimeInterface;
use JOOservices\Dto\Attributes\MapFrom;
use JOOservices\Dto\Attributes\MapTo;
use JOOservices\Dto\Core\Context;
use JOOservices\Dto\Core\Dto;
use JOOservices\LaravelEvents\Exceptions\InvalidEventDataException;

/**
 * Storage identity for query results. Omitted from persistence toArray() payloads.
 */
final class DocumentIdentity extends Dto
{
    public function __construct(
        public readonly mixed $id = null,
        #[MapFrom('created_at')]
        #[MapTo('created_at')]
        public readonly ?DateTimeInterface $createdAt = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function fromStorageArray(array $values): self
    {
        $createdAt = $values['created_at'] ?? $values['createdAt'] ?? null;
        if ($createdAt !== null && ! $createdAt instanceof DateTimeInterface) {
            throw InvalidEventDataException::invalidType('created_at', 'a DateTimeInterface or null');
        }

        return new self(
            id: $values['_id'] ?? $values['id'] ?? null,
            createdAt: $createdAt,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?Context $ctx = null): static
    {
        return parent::fromArray([
            'id' => $data['id'] ?? $data['_id'] ?? null,
            'created_at' => $data['created_at'] ?? $data['createdAt'] ?? null,
        ], $ctx);
    }
}
