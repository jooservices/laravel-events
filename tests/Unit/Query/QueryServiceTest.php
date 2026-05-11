<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Query;

use InvalidArgumentException;
use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JOOservices\LaravelEvents\Query\EventLogQueryService;
use JOOservices\LaravelEvents\Query\StoredEventQueryService;
use JOOservices\LaravelEvents\Tests\TestCase;

class QueryServiceTest extends TestCase
{
    public function test_stored_event_query_service_rejects_invalid_limits(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new StoredEventQueryService(new StoredEvent))->latest(0);
    }

    public function test_event_log_query_service_rejects_invalid_limits(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new EventLogQueryService(new EventLogEntry))->latest(501);
    }
}
