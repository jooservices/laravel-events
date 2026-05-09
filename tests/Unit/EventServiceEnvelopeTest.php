<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit;

use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\EventService;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JooServices\LaravelEvents\Support\EventMetadata;
use JooServices\LaravelEvents\Tests\TestCase;
use Mockery;

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
            ->andReturn(new StoredEvent);

        $service = new EventService($storedEventModel, Mockery::mock(EventLogEntry::class));
        $service->storeEvent(new \stdClass, [], 'ORD-1', metadata: [
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
}
