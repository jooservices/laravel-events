<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit\Query;

use InvalidArgumentException;
use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JooServices\LaravelEvents\Query\EventLogQueryService;
use JooServices\LaravelEvents\Query\StoredEventQueryService;
use JooServices\LaravelEvents\Tests\TestCase;

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
