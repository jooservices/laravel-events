<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Data;

use InvalidArgumentException;
use JOOservices\LaravelEvents\Data\EventLogData;
use JOOservices\LaravelEvents\Data\StoredEventData;
use JOOservices\LaravelEvents\Tests\TestCase;

class EventDataTest extends TestCase
{
    public function test_stored_event_data_hydrates_and_serializes(): void
    {
        $data = StoredEventData::fromArray([
            'event_class' => 'OrderCreated',
            'aggregate_id' => 'ORD-1',
            'payload' => ['total' => 10],
            'metadata' => ['correlation_id' => 'corr-1'],
            'user_id' => 'user-1',
            'event_id' => 'evt-1',
            'event_name' => 'order.created',
            'event_category' => 'domain',
            'aggregate_type' => 'orders',
            'schema_version' => 1,
            'event_version' => 'v1',
            'correlation_id' => 'corr-1',
            'causation_id' => 'cmd-1',
        ]);

        $this->assertNotNull($data->envelope);
        $this->assertSame('OrderCreated', $data->eventClass);
        $this->assertSame('ORD-1', $data->aggregateId);
        $this->assertSame('evt-1', $data->envelope->eventId);
        $this->assertSame('order.created', $data->envelope->eventName);
        $this->assertSame('domain', $data->envelope->eventCategory);
        $this->assertSame('orders', $data->envelope->aggregateType);
        $this->assertSame(1, $data->envelope->schemaVersion);
        $this->assertSame('v1', $data->envelope->eventVersion);
        $this->assertSame('corr-1', $data->envelope->correlationId);
        $this->assertSame('cmd-1', $data->envelope->causationId);
        $this->assertSame('domain', $data->toArray()['event_category']);
        $this->assertSame(['total' => 10], $data->toArray()['payload']);
    }

    public function test_stored_event_data_requires_event_class(): void
    {
        $this->expectException(InvalidArgumentException::class);

        StoredEventData::fromArray(['payload' => []]);
    }

    public function test_stored_event_data_requires_array_payload_and_metadata(): void
    {
        $this->expectException(InvalidArgumentException::class);

        StoredEventData::fromArray([
            'event_class' => 'OrderCreated',
            'payload' => 'not-an-array',
            'metadata' => [],
        ]);
    }

    public function test_stored_event_data_casts_camel_case_aggregate_id(): void
    {
        $data = StoredEventData::fromArray([
            'eventClass' => 'OrderCreated',
            'aggregateId' => 123,
            'payload' => [],
        ]);

        $this->assertSame('123', $data->aggregateId);
    }

    public function test_event_log_data_hydrates_and_serializes(): void
    {
        $data = EventLogData::fromArray([
            'entity_type' => 'orders',
            'entity_id' => 'ORD-1',
            'action' => 'updated',
            'prev' => ['status' => 'pending'],
            'changed' => ['status' => 'paid'],
            'diff' => ['status' => ['old' => 'pending', 'new' => 'paid']],
            'meta' => ['correlation_id' => 'corr-1'],
        ]);

        $this->assertSame('orders', $data->entityType);
        $this->assertSame('updated', $data->action);
        $this->assertSame(['status' => 'paid'], $data->toArray()['changed']);
    }

    public function test_event_log_data_requires_required_fields(): void
    {
        $this->expectException(InvalidArgumentException::class);

        EventLogData::fromArray(['entity_type' => 'orders']);
    }
}
