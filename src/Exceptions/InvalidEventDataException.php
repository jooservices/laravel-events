<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Exceptions;

use JOOservices\Exceptions\Support\ExceptionContext;
use JOOservices\Exceptions\Support\LogLevel;
use Throwable;

final class InvalidEventDataException extends LaravelEventsException
{
    public static function emptyEventClass(?Throwable $previous = null): self
    {
        return (new self('Stored event class cannot be empty.', 0, $previous))
            ->withContext(['field' => 'event_class']);
    }

    public static function missingField(string $field, ?Throwable $previous = null): self
    {
        return (new self("Event data requires {$field}.", 0, $previous))
            ->withContext(['field' => $field]);
    }

    public static function invalidType(string $field, string $expected, ?Throwable $previous = null): self
    {
        return (new self("Event data {$field} must be {$expected}.", 0, $previous))
            ->withContext(['field' => $field, 'expected' => $expected]);
    }

    public function errorCode(): string
    {
        return 'events.data.invalid';
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
