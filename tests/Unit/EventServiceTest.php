<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit;

use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\EventService;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JooServices\LaravelEvents\Tests\TestCase;
use Mockery;

class EventServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_store_event_creates_stored_event(): void
    {
        $event = new class
        {
            public function __toString(): string
            {
                return 'StubEvent';
            }
        };
        $payload = ['id' => 1, 'name' => 'test'];
        $aggregateId = 'agg-123';

        $stored = new StoredEvent;
        $stored->event_class = get_class($event);
        $stored->aggregate_id = $aggregateId;
        $stored->payload = $payload;

        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $arg) use ($event, $payload, $aggregateId) {
                return $arg['event_class'] === $event::class
                    && $arg['payload'] === $payload
                    && $arg['aggregate_id'] === $aggregateId
                    && isset($arg['metadata'])
                    && array_key_exists('user_id', $arg);
            }))
            ->andReturn($stored);

        $eventLogModel = Mockery::mock(EventLogEntry::class);
        $service = new EventService($storedEventModel, $eventLogModel);

        $result = $service->storeEvent($event, $payload, $aggregateId);

        $this->assertSame($stored, $result);
    }

    public function test_store_event_accepts_null_aggregate_id(): void
    {
        $event = new \stdClass;
        $payload = [];

        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $arg) => $arg['aggregate_id'] === null))
            ->andReturn(new StoredEvent);

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $result = $service->storeEvent($event, $payload, null);
        $this->assertInstanceOf(StoredEvent::class, $result);
    }

    public function test_log_change_creates_event_log_entry(): void
    {
        $storedEventModel = Mockery::mock(StoredEvent::class);
        $eventLogModel = Mockery::mock(EventLogEntry::class)->makePartial();
        $eventLogModel->shouldReceive('newQuery')->andReturnSelf();
        $entry = new EventLogEntry;
        $eventLogModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $arg) {
                return $arg['entity_type'] === 'User'
                    && $arg['entity_id'] === '1'
                    && $arg['action'] === 'updated'
                    && $arg['prev'] === ['name' => 'Old']
                    && $arg['changed'] === ['name' => 'New']
                    && isset($arg['diff'])
                    && isset($arg['meta'])
                    && $arg['user_id'] === 99;
            }))
            ->andReturn($entry);

        $service = new EventService($storedEventModel, $eventLogModel);
        $result = $service->logChange(
            'User',
            '1',
            'updated',
            ['name' => 'Old'],
            ['name' => 'New'],
            ['name' => ['old' => 'Old', 'new' => 'New']],
            ['user_id' => 99]
        );

        $this->assertSame($entry, $result);
    }

    public function test_store_event_stores_user_id_when_provided(): void
    {
        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $arg) => $arg['user_id'] === 42))
            ->andReturn(new StoredEvent);

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->storeEvent(new \stdClass, [], null, 42);
        $this->addToAssertionCount(1);
    }

    public function test_store_event_stores_null_user_id_when_not_provided_and_guest(): void
    {
        $this->assertNull(auth()->id());
        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $arg) => $arg['user_id'] === null))
            ->andReturn(new StoredEvent);

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->storeEvent(new \stdClass, [], null);
        $this->addToAssertionCount(1);
    }

    public function test_log_change_stores_user_id_when_provided(): void
    {
        $eventLogModel = Mockery::mock(EventLogEntry::class)->makePartial();
        $eventLogModel->shouldReceive('newQuery')->andReturnSelf();
        $eventLogModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $arg) => $arg['user_id'] === 7))
            ->andReturn(new EventLogEntry);

        $service = new EventService(Mockery::mock(StoredEvent::class), $eventLogModel);
        $service->logChange('Order', '1', 'updated', [], [], [], [], 7);
        $this->addToAssertionCount(1);
    }

    public function test_log_change_stores_null_user_id_when_guest(): void
    {
        $eventLogModel = Mockery::mock(EventLogEntry::class)->makePartial();
        $eventLogModel->shouldReceive('newQuery')->andReturnSelf();
        $eventLogModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $arg) => $arg['user_id'] === null))
            ->andReturn(new EventLogEntry);

        $service = new EventService(Mockery::mock(StoredEvent::class), $eventLogModel);
        $service->logChange('Order', '1', 'updated', [], [], [], []);
        $this->addToAssertionCount(1);
    }

    public function test_log_change_uses_user_id_from_meta_when_not_passed(): void
    {
        $eventLogModel = Mockery::mock(EventLogEntry::class)->makePartial();
        $eventLogModel->shouldReceive('newQuery')->andReturnSelf();
        $eventLogModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $arg) => $arg['user_id'] === 100 && $arg['meta']['user_id'] === 100))
            ->andReturn(new EventLogEntry);

        $service = new EventService(Mockery::mock(StoredEvent::class), $eventLogModel);
        $service->logChange('Order', '1', 'updated', [], [], [], ['user_id' => 100]);
        $this->addToAssertionCount(1);
    }

    public function test_store_event_stores_occurred_at_and_metadata(): void
    {
        $occurredAt = new \DateTimeImmutable('2025-01-15 12:00:00');
        $metadata = ['request_id' => 'req-123', 'channel' => 'api'];

        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $arg) use ($occurredAt, $metadata) {
                return $arg['occurred_at'] === $occurredAt
                    && $arg['metadata'] === $metadata;
            }))
            ->andReturn(new StoredEvent);

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->storeEvent(new \stdClass, [], null, null, $occurredAt, $metadata);
        $this->addToAssertionCount(1);
    }

    public function test_log_change_accepts_string_user_id(): void
    {
        $eventLogModel = Mockery::mock(EventLogEntry::class)->makePartial();
        $eventLogModel->shouldReceive('newQuery')->andReturnSelf();
        $eventLogModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $arg) => $arg['user_id'] === 'uuid-abc-123'))
            ->andReturn(new EventLogEntry);

        $service = new EventService(Mockery::mock(StoredEvent::class), $eventLogModel);
        $service->logChange('Order', '1', 'updated', [], [], [], [], 'uuid-abc-123');
        $this->addToAssertionCount(1);
    }
}
