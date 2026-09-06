<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit;

use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;
use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JOOservices\LaravelEvents\Exceptions\InvalidConfigurationException;
use JOOservices\LaravelEvents\Support\EventMetadata;
use JOOservices\LaravelEvents\Tests\TestCase;
use Mockery;
use stdClass;

class EventServiceEnvelopeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_store_event_maps_envelope_fields_from_metadata(): void
    {
        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $arg) {
                return $arg['event_id'] === 'evt-123'
                    && $arg['event_name'] === 'order.created'
                    && $arg['aggregate_type'] === 'orders'
                    && $arg['schema_version'] === 1
                    && $arg['event_version'] === '2026-05'
                    && $arg['correlation_id'] === 'corr-123'
                    && $arg['causation_id'] === 'cmd-123';
            }))
            ->andReturn(new StoredEvent());

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->storeEvent(new stdClass(), [], 'ORD-1', metadata: [
            EventMetadata::EVENT_ID => 'evt-123',
            EventMetadata::EVENT_NAME => 'order.created',
            EventMetadata::AGGREGATE_TYPE => 'orders',
            EventMetadata::SCHEMA_VERSION => 1,
            EventMetadata::EVENT_VERSION => '2026-05',
            EventMetadata::CORRELATION_ID => 'corr-123',
            EventMetadata::CAUSATION_ID => 'cmd-123',
        ]);
        $this->addToAssertionCount(1);
    }

    public function test_record_many_stored_events_preserves_envelope_fields(): void
    {
        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('insert')
            ->once()
            ->with(Mockery::on(function (array $records) {
                $record = $records[0] ?? [];

                return $record['event_id'] === 'evt-bulk'
                    && $record['event_name'] === 'order.bulk'
                    && $record['aggregate_type'] === 'orders'
                    && $record['schema_version'] === 1
                    && $record['event_version'] === '2026-05'
                    && $record['correlation_id'] === 'corr-bulk'
                    && $record['causation_id'] === 'cmd-bulk';
            }));

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->recordManyStoredEvents([
            [
                'event_class' => 'BulkEvent',
                'payload' => [],
                'event_id' => 'evt-bulk',
                'event_name' => 'order.bulk',
                'aggregate_type' => 'orders',
                'schema_version' => 1,
                'event_version' => '2026-05',
                'correlation_id' => 'corr-bulk',
                'causation_id' => 'cmd-bulk',
            ],
        ]);
        $this->addToAssertionCount(1);
    }

    public function test_record_many_stored_events_generates_envelope_when_missing(): void
    {
        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('insert')
            ->once()
            ->with(Mockery::on(function (array $records) {
                $record = $records[0] ?? [];

                return is_string($record['event_id'] ?? null)
                    && ($record['event_id'] ?? '') !== ''
                    && ($record['event_name'] ?? null) === 'BulkEvent'
                    && ($record['user_id'] ?? null) === 'context-user';
            }));

        config()->set('events.context_provider', fn(): array => ['user_id' => 'context-user']);

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->recordManyStoredEvents([
            [
                'event_class' => 'BulkEvent',
                'payload' => ['id' => 1],
            ],
        ]);
        $this->addToAssertionCount(1);
    }

    public function test_context_provider_class_string_is_resolved_from_container(): void
    {
        $this->app->bind(
            TestEventsContextProvider::class,
            static fn(): TestEventsContextProvider => new TestEventsContextProvider(),
        );
        config()->set('events.context_provider', TestEventsContextProvider::class);

        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(static function (array $arg): bool {
                return ($arg['user_id'] ?? null) === 'from-provider'
                    && ($arg['metadata']['user_id'] ?? null) === 'from-provider'
                    && ($arg['metadata']['source'] ?? null) === 'test';
            }))
            ->andReturn(new StoredEvent());

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->storeEvent(new stdClass(), ['id' => 1]);
        $this->addToAssertionCount(1);
    }

    public function test_log_change_uses_context_user_id_when_meta_omits_user_id(): void
    {
        config()->set('events.context_provider', TestEventsContextProvider::class);
        $this->app->bind(
            TestEventsContextProvider::class,
            static fn(): TestEventsContextProvider => new TestEventsContextProvider(),
        );

        $eventLogModel = Mockery::mock(EventLogEntry::class)->makePartial();
        $eventLogModel->shouldReceive('newQuery')->andReturnSelf();
        $eventLogModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(
                static fn(array $arg): bool => ($arg['user_id'] ?? null) === 'from-provider',
            ))
            ->andReturn(new EventLogEntry());

        $service = new EventService(Mockery::mock(StoredEvent::class), $eventLogModel);
        $service->logChange('Order', '1', 'updated', [], [], [], []);
        $this->addToAssertionCount(1);
    }

    public function test_context_provider_ignores_non_array_and_non_callable_values(): void
    {
        config()->set('events.context_provider', static fn(): string => 'not-an-array');

        $storedEventModel = Mockery::mock(StoredEvent::class)->makePartial();
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(static fn(array $arg): bool => ($arg['metadata'] ?? null) === []))
            ->andReturn(new StoredEvent());

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->storeEvent(new stdClass(), []);
        $this->addToAssertionCount(1);

        config()->set('events.context_provider', 42);
        $storedEventModel->shouldReceive('newQuery')->andReturnSelf();
        $storedEventModel->shouldReceive('create')
            ->once()
            ->with(Mockery::on(static fn(array $arg): bool => ($arg['metadata'] ?? null) === []))
            ->andReturn(new StoredEvent());
        $service->storeEvent(new stdClass(), []);
        $this->addToAssertionCount(1);
    }

    public function test_invalid_context_provider_class_string_throws_package_exception(): void
    {
        config()->set('events.context_provider', 'App\\Does\\Not\\ExistContextProvider');

        $this->expectException(InvalidConfigurationException::class);

        $service = new EventService(
            Mockery::mock(StoredEvent::class),
            Mockery::mock(EventLogEntry::class),
        );
        $service->storeEvent(new stdClass(), []);
    }
}
