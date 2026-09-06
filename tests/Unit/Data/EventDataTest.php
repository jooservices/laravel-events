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

        self::assertNotNull($data->envelope);
        self::assertSame('OrderCreated', $data->eventClass);
        self::assertSame('ORD-1', $data->aggregateId);
        self::assertSame('evt-1', $data->envelope->eventId);
        self::assertSame('order.created', $data->envelope->eventName);
        self::assertSame('domain', $data->envelope->eventCategory);
        self::assertSame('orders', $data->envelope->aggregateType);
        self::assertSame(1, $data->envelope->schemaVersion);
        self::assertSame('v1', $data->envelope->eventVersion);
        self::assertSame('corr-1', $data->envelope->correlationId);
        self::assertSame('cmd-1', $data->envelope->causationId);
        self::assertSame('domain', $data->toArray()['event_category']);
        self::assertSame(['total' => 10], $data->toArray()['payload']);
    }

    public function test_stored_event_data_requires_event_class(): void
    {
        self::expectException(InvalidEventDataException::class);

        StoredEventData::fromArray(['payload' => []]);
    }

    public function test_stored_event_data_requires_array_payload_and_metadata(): void
    {
        self::expectException(InvalidEventDataException::class);

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

        self::assertSame('123', $data->aggregateId);
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

        self::assertSame('orders', $data->entityType);
        self::assertSame('updated', $data->action);
        self::assertSame(['status' => 'paid'], $data->toArray()['changed']);
    }

    public function test_event_log_data_accepts_camel_case_aliases(): void
    {
        $data = EventLogData::fromArray([
            'entityType' => 'orders',
            'entityId' => 99,
            'action' => 'updated',
            'userId' => 'user-9',
        ]);

        self::assertSame('orders', $data->entityType);
        self::assertSame('99', $data->entityId);
        self::assertSame('user-9', $data->userId);
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

        self::assertSame('mongo-1', $stored->documentId());
        self::assertSame($createdAt, $stored->createdAt());
        self::assertArrayNotHasKey('id', $stored->toArray());
        self::assertArrayNotHasKey('created_at', $stored->toArray());
        self::assertSame('mongo-2', $log->documentId());
        self::assertSame($createdAt, $log->createdAt());
        self::assertArrayNotHasKey('id', $log->toArray());
        self::assertArrayNotHasKey('created_at', $log->toArray());
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

        self::assertSame('mongo-str-1', $stored->documentId());
        $createdAt = $stored->createdAt();
        self::assertInstanceOf(DateTimeImmutable::class, $createdAt);
        self::assertSame('2026-05-01T12:00:00+00:00', $createdAt->format(DateTimeImmutable::ATOM));
        self::assertInstanceOf(DateTimeImmutable::class, $stored->occurredAt);
        self::assertSame('mongo-str-2', $log->documentId());
        self::assertInstanceOf(DateTimeImmutable::class, $log->createdAt());
    }

    public function test_document_identity_from_array_parses_created_at_string(): void
    {
        $identity = DocumentIdentity::fromArray([
            '_id' => 'doc-1',
            'created_at' => '2026-07-01T08:00:00Z',
        ]);

        self::assertSame('doc-1', $identity->id);
        self::assertInstanceOf(DateTimeImmutable::class, $identity->createdAt);
    }

    public function test_event_log_data_requires_required_fields(): void
    {
        self::expectException(InvalidEventDataException::class);

        EventLogData::fromArray(['entity_type' => 'orders']);
    }
}
