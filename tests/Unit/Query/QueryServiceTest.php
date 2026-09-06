<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Query;

use DateTimeImmutable;
use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JOOservices\LaravelEvents\Exceptions\InvalidQueryException;
use JOOservices\LaravelEvents\Query\EventLogQueryService;
use JOOservices\LaravelEvents\Query\StoredEventQueryService;
use JOOservices\LaravelEvents\Tests\TestCase;

class QueryServiceTest extends TestCase
{
    public function test_stored_event_query_service_rejects_invalid_limits(): void
    {
        self::expectException(InvalidQueryException::class);

        (new StoredEventQueryService(new StoredEvent()))->latest(0);
    }

    public function test_event_log_query_service_rejects_invalid_limits(): void
    {
        self::expectException(InvalidQueryException::class);

        (new EventLogQueryService(new EventLogEntry()))->latest(501);
    }

    public function test_stored_event_query_rejects_inverted_date_range(): void
    {
        self::expectException(InvalidQueryException::class);
        self::expectExceptionMessage('Query date range requires $from <= $to.');

        (new StoredEventQueryService(new StoredEvent()))->between(
            new DateTimeImmutable('2026-05-02'),
            new DateTimeImmutable('2026-05-01'),
        );
    }

    public function test_stored_event_query_rejects_disallowed_filters(): void
    {
        self::expectException(InvalidQueryException::class);
        self::expectExceptionMessage('Query filter key is not allowed.');

        (new StoredEventQueryService(new StoredEvent()))->latest(10, ['payload.secret' => 'x']);
    }

    public function test_event_log_query_rejects_disallowed_filters(): void
    {
        self::expectException(InvalidQueryException::class);

        (new EventLogQueryService(new EventLogEntry()))->latest(10, ['prev.email' => 'x']);
    }
}
