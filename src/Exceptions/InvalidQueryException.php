<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Exceptions;

use JOOservices\Exceptions\Support\ExceptionContext;
use JOOservices\Exceptions\Support\LogLevel;

final class InvalidQueryException extends LaravelEventsException
{
    public static function limitOutOfRange(int $limit): self
    {
        return (new self('Query limit must be between 1 and 500.'))
            ->withContext(['limit' => $limit, 'min' => 1, 'max' => 500]);
    }

    public function errorCode(): string
    {
        return 'events.query.limit';
    }

    public function logLevel(): string
    {
        return LogLevel::WARNING->value;
    }

    protected function copyWithContext(ExceptionContext $context): static
    {
        return new self($this->getMessage(), $this->getCode(), $this->getPrevious(), $context);
    }
}
