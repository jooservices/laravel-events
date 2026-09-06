<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

use JOOservices\Dto\Core\Context;
use JOOservices\Dto\Core\Dto;

final class EventMetadataData extends Dto
{
    /** @param array<string, mixed> $values */
    public function __construct(public readonly array $values = [])
    {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?Context $ctx = null): static
    {
        return new self($data);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(?Context $ctx = null): array
    {
        return $this->values;
    }
}
