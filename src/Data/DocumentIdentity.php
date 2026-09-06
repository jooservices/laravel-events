<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use DateTimeInterface;
use JOOservices\Dto\Attributes\MapFrom;
use JOOservices\Dto\Attributes\MapTo;
use JOOservices\Dto\Core\Context;
use JOOservices\Dto\Core\Dto;
use JOOservices\LaravelEvents\Support\DateTimeParser;

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
        return new self(
            id: $values['_id'] ?? $values['id'] ?? null,
            createdAt: DateTimeParser::optional(
                $values['created_at'] ?? $values['createdAt'] ?? null,
                'created_at',
            ),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?Context $ctx = null): static
    {
        $identity = self::fromStorageArray($data);

        return parent::fromArray([
            'id' => $identity->id,
            'created_at' => $identity->createdAt,
        ], $ctx);
    }
}
