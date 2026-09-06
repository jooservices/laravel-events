<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Exceptions;

use JOOservices\Exceptions\Support\ExceptionContext;
use JOOservices\Exceptions\Support\LogLevel;
use Throwable;

final class InvalidConfigurationException extends LaravelEventsException
{
    public static function invalidContextProvider(string $provider, ?Throwable $previous = null): self
    {
        return (new self(
            "events.context_provider class '{$provider}' could not be resolved from the container.",
            0,
            $previous,
        ))->withContext(['provider' => $provider]);
    }

    public function errorCode(): string
    {
        return 'events.config.invalid';
    }

    public function logLevel(): string
    {
        return LogLevel::ERROR->value;
    }

    protected function copyWithContext(ExceptionContext $context): static
    {
        return new self($this->getMessage(), $this->getCode(), $this->getPrevious(), $context);
    }
}
