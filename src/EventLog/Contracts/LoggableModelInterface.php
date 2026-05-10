<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventLog\Contracts;

interface LoggableModelInterface
{
    public function getLoggableType(): string;

    public function getLoggableId(): string;

    /** @return array<string, mixed> Previous attributes (before change). */
    public function getPrev(): array;

    /** @return array<string, mixed> Current/changed attributes. */
    public function getChanged(): array;
}
