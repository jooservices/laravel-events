<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Exceptions;

use DateTimeImmutable;
use Faker\Factory as FakerFactory;
use JOOservices\LaravelEvents\Exceptions\InvalidConfigurationException;
use JOOservices\LaravelEvents\Exceptions\InvalidEventDataException;
use JOOservices\LaravelEvents\Exceptions\InvalidQueryException;
use JOOservices\LaravelEvents\Tests\TestCase;

final class PackageExceptionsTest extends TestCase
{
    public function test_invalid_event_data_exception_exposes_error_metadata(): void
    {
        $faker = FakerFactory::create();
        $field = $faker->word();

        $exception = InvalidEventDataException::missingField($field)
            ->withContext(['trace_id' => $faker->uuid()]);

        self::assertSame('events.data.invalid', $exception->errorCode());
        self::assertSame('warning', $exception->logLevel());
        self::assertSame($field, $exception->getRawContext()['field']);
        self::assertArrayHasKey('trace_id', $exception->getRawContext());
    }

    public function test_invalid_event_data_exception_factories(): void
    {
        $empty = InvalidEventDataException::emptyEventClass();
        $invalidType = InvalidEventDataException::invalidType('payload', 'arrays');

        self::assertSame('events.data.invalid', $empty->errorCode());
        self::assertSame('event_class', $empty->getRawContext()['field']);
        self::assertSame('payload', $invalidType->getRawContext()['field']);
        self::assertSame('arrays', $invalidType->getRawContext()['expected']);
    }

    public function test_invalid_query_exception_exposes_error_metadata(): void
    {
        $faker = FakerFactory::create();
        $limit = $faker->numberBetween(501, 900);

        $exception = InvalidQueryException::limitOutOfRange($limit)
            ->withContext(['source' => 'unit']);

        self::assertSame('events.query.limit', $exception->errorCode());
        self::assertSame('warning', $exception->logLevel());
        self::assertSame($limit, $exception->getRawContext()['limit']);
        self::assertSame('unit', $exception->getRawContext()['source']);
    }

    public function test_invalid_query_exception_filter_and_range_factories(): void
    {
        $from = new DateTimeImmutable('2026-05-02');
        $to = new DateTimeImmutable('2026-05-01');
        $range = InvalidQueryException::invalidDateRange($from, $to);
        $filter = InvalidQueryException::disallowedFilter('payload.secret');

        self::assertSame('events.query.date_range', $range->errorCode());
        self::assertSame('events.query.filter', $filter->errorCode());
        self::assertSame('payload.secret', $filter->getRawContext()['filter']);
    }

    public function test_invalid_configuration_exception_exposes_provider_context(): void
    {
        $exception = InvalidConfigurationException::invalidContextProvider('App\\Missing')
            ->withContext(['trace_id' => 'cfg-1']);

        self::assertSame('events.config.invalid', $exception->errorCode());
        self::assertSame('error', $exception->logLevel());
        self::assertSame('App\\Missing', $exception->getRawContext()['provider']);
        self::assertSame('cfg-1', $exception->getRawContext()['trace_id']);

        $nonCallable = InvalidConfigurationException::nonCallableContextProvider('int');
        self::assertSame('events.config.invalid', $nonCallable->errorCode());
        self::assertSame('int', $nonCallable->getRawContext()['provider']);
    }
}
