<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Exceptions;

use DateTimeInterface;
use JOOservices\Exceptions\Support\ExceptionContext;
use JOOservices\Exceptions\Support\LogLevel;
use Throwable;

final class InvalidQueryException extends LaravelEventsException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?ExceptionContext $context = null,
        private readonly string $queryErrorCode = 'events.query.invalid',
    ) {
        parent::__construct($message, $code, $previous, $context);
    }

    public static function limitOutOfRange(int $limit): self
    {
        return (new self(
            'Query limit must be between 1 and 500.',
            queryErrorCode: 'events.query.limit',
        ))->withContext(['limit' => $limit, 'min' => 1, 'max' => 500]);
    }

    public static function invalidDateRange(DateTimeInterface $from, DateTimeInterface $to): self
    {
        return (new self(
            'Query date range requires $from <= $to.',
            queryErrorCode: 'events.query.date_range',
        ))->withContext([
            'from' => $from->format(DateTimeInterface::ATOM),
            'to' => $to->format(DateTimeInterface::ATOM),
        ]);
    }

    public static function disallowedFilter(string $key): self
    {
        return (new self(
            'Query filter key is not allowed.',
            queryErrorCode: 'events.query.filter',
        ))->withContext(['filter' => $key]);
    }

    public function errorCode(): string
    {
        return $this->queryErrorCode;
    }

    public function logLevel(): string
    {
        return LogLevel::WARNING->value;
    }

    protected function copyWithContext(ExceptionContext $context): static
    {
        return new self(
            $this->getMessage(),
            $this->getCode(),
            $this->getPrevious(),
            $context,
            $this->queryErrorCode,
        );
    }
}
