<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventLog\Contracts;

interface LoggableModelInterface
{
    public function getLoggableType(): string;

    public function getLoggableId(): string;

    /** Previous attributes (before change). */
    public function getPrev(): array;

    /** Current/changed attributes. */
    public function getChanged(): array;
}
