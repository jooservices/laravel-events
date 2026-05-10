<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Data;

final readonly class EventDiffData
{
    /** @param array<string, mixed> $values */
    public function __construct(public array $values = []) {}

    /** @param array<string, mixed> $values */
    public static function fromArray(array $values): self
    {
        return new self($values);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->values;
    }
}
