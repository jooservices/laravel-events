<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit;

use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;
use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JOOservices\LaravelEvents\Tests\TestCase;
use Mockery;
use Psr\Log\LoggerInterface;
use stdClass;

final class EventServiceLoggingTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_store_event_emits_debug_log_when_logger_provided(): void
    {
        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')->once()->andReturn(new StoredEvent());

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('debug')
            ->once()
            ->with(
                'laravel-events.stored_event.persisted',
                Mockery::on(static fn(array $context): bool => isset($context['event_class'])),
            );

        $service = new EventService(
            $storedEventModel,
            Mockery::mock(EventLogEntry::class),
            logger: $logger,
        );
        $service->storeEvent(new stdClass(), ['id' => 1]);
        $this->addToAssertionCount(1);
    }

    public function test_log_change_emits_debug_log_when_logger_provided(): void
    {
        $eventLogModel = Mockery::mock(EventLogEntry::class)->makePartial();
        $eventLogModel->shouldReceive('newQuery')->andReturnSelf();
        $eventLogModel->shouldReceive('create')->once()->andReturn(new EventLogEntry());

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('debug')
            ->once()
            ->with(
                'laravel-events.event_log.persisted',
                Mockery::on(static fn(array $context): bool => ($context['action'] ?? null) === 'updated'),
            );

        $service = new EventService(
            Mockery::mock(StoredEvent::class),
            $eventLogModel,
            logger: $logger,
        );
        $service->logChange('Order', '1', 'updated', [], [], []);
        $this->addToAssertionCount(1);
    }
}
