<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Data;

use DateTimeImmutable;
use JOOservices\LaravelEvents\Data\DocumentIdentity;
use JOOservices\LaravelEvents\Data\EventLogData;
use JOOservices\LaravelEvents\Data\StoredEventData;
use JOOservices\LaravelEvents\Exceptions\InvalidEventDataException;
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
        $this->expectException(InvalidEventDataException::class);

        StoredEventData::fromArray(['payload' => []]);
    }

    public function test_stored_event_data_requires_array_payload_and_metadata(): void
    {
        $this->expectException(InvalidEventDataException::class);

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

    public function test_event_log_data_accepts_camel_case_aliases(): void
    {
        $data = EventLogData::fromArray([
            'entityType' => 'orders',
            'entityId' => 99,
            'action' => 'updated',
            'userId' => 'user-9',
        ]);

        $this->assertSame('orders', $data->entityType);
        $this->assertSame('99', $data->entityId);
        $this->assertSame('user-9', $data->userId);
    }

    public function test_query_identity_fields_hydrate_but_are_omitted_from_persistence_array(): void
    {
        $createdAt = new DateTimeImmutable('2026-05-01T12:00:00Z');
        $stored = StoredEventData::fromArray([
            'event_class' => 'OrderCreated',
            'payload' => [],
            '_id' => 'mongo-1',
            'created_at' => $createdAt,
        ]);
        $log = EventLogData::fromArray([
            'entity_type' => 'orders',
            'entity_id' => '1',
            'action' => 'updated',
            '_id' => 'mongo-2',
            'created_at' => $createdAt,
        ]);

        $this->assertSame('mongo-1', $stored->documentId());
        $this->assertSame($createdAt, $stored->createdAt());
        $this->assertArrayNotHasKey('id', $stored->toArray());
        $this->assertArrayNotHasKey('created_at', $stored->toArray());
        $this->assertSame('mongo-2', $log->documentId());
        $this->assertSame($createdAt, $log->createdAt());
        $this->assertArrayNotHasKey('id', $log->toArray());
        $this->assertArrayNotHasKey('created_at', $log->toArray());
    }

    public function test_query_identity_accepts_eloquent_serialized_timestamp_strings(): void
    {
        $stored = StoredEventData::fromArray([
            'event_class' => 'OrderCreated',
            'payload' => [],
            '_id' => 'mongo-str-1',
            'created_at' => '2026-05-01T12:00:00.000000Z',
            'occurred_at' => '2026-05-01 11:59:00',
        ]);
        $log = EventLogData::fromArray([
            'entity_type' => 'orders',
            'entity_id' => '1',
            'action' => 'updated',
            '_id' => 'mongo-str-2',
            'created_at' => '2026-05-01T12:00:00.000000Z',
        ]);

        $this->assertSame('mongo-str-1', $stored->documentId());
        $createdAt = $stored->createdAt();
        $this->assertInstanceOf(DateTimeImmutable::class, $createdAt);
        $this->assertSame('2026-05-01T12:00:00+00:00', $createdAt->format(DateTimeImmutable::ATOM));
        $this->assertInstanceOf(DateTimeImmutable::class, $stored->occurredAt);
        $this->assertSame('mongo-str-2', $log->documentId());
        $this->assertInstanceOf(DateTimeImmutable::class, $log->createdAt());
    }

    public function test_document_identity_from_array_parses_created_at_string(): void
    {
        $identity = DocumentIdentity::fromArray([
            '_id' => 'doc-1',
            'created_at' => '2026-07-01T08:00:00Z',
        ]);

        $this->assertSame('doc-1', $identity->id);
        $this->assertInstanceOf(DateTimeImmutable::class, $identity->createdAt);
    }

    public function test_event_log_data_requires_required_fields(): void
    {
        $this->expectException(InvalidEventDataException::class);

        EventLogData::fromArray(['entity_type' => 'orders']);
    }
}
