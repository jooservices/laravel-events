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

        $this->assertSame('events.data.invalid', $exception->errorCode());
        $this->assertSame('warning', $exception->logLevel());
        $this->assertSame($field, $exception->getRawContext()['field']);
        $this->assertArrayHasKey('trace_id', $exception->getRawContext());
    }

    public function test_invalid_event_data_exception_factories(): void
    {
        $empty = InvalidEventDataException::emptyEventClass();
        $invalidType = InvalidEventDataException::invalidType('payload', 'arrays');

        $this->assertSame('events.data.invalid', $empty->errorCode());
        $this->assertSame('event_class', $empty->getRawContext()['field']);
        $this->assertSame('payload', $invalidType->getRawContext()['field']);
        $this->assertSame('arrays', $invalidType->getRawContext()['expected']);
    }

    public function test_invalid_query_exception_exposes_error_metadata(): void
    {
        $faker = FakerFactory::create();
        $limit = $faker->numberBetween(501, 900);

        $exception = InvalidQueryException::limitOutOfRange($limit)
            ->withContext(['source' => 'unit']);

        $this->assertSame('events.query.limit', $exception->errorCode());
        $this->assertSame('warning', $exception->logLevel());
        $this->assertSame($limit, $exception->getRawContext()['limit']);
        $this->assertSame('unit', $exception->getRawContext()['source']);
    }

    public function test_invalid_query_exception_filter_and_range_factories(): void
    {
        $from = new DateTimeImmutable('2026-05-02');
        $to = new DateTimeImmutable('2026-05-01');
        $range = InvalidQueryException::invalidDateRange($from, $to);
        $filter = InvalidQueryException::disallowedFilter('payload.secret');

        $this->assertSame('events.query.date_range', $range->errorCode());
        $this->assertSame('events.query.filter', $filter->errorCode());
        $this->assertSame('payload.secret', $filter->getRawContext()['filter']);
    }

    public function test_invalid_configuration_exception_exposes_provider_context(): void
    {
        $exception = InvalidConfigurationException::invalidContextProvider('App\\Missing')
            ->withContext(['trace_id' => 'cfg-1']);

        $this->assertSame('events.config.invalid', $exception->errorCode());
        $this->assertSame('error', $exception->logLevel());
        $this->assertSame('App\\Missing', $exception->getRawContext()['provider']);
        $this->assertSame('cfg-1', $exception->getRawContext()['trace_id']);
    }
}
