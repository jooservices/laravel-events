<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Serialization;

use JOOservices\LaravelEvents\Data\StoredEventData;
use JOOservices\LaravelEvents\Serialization\ArrayEventSerializer;
use JOOservices\LaravelEvents\Support\EventMetadata;
use PHPUnit\Framework\TestCase;

class ArrayEventSerializerTest extends TestCase
{
    public function test_serializer_builds_additive_event_envelope_fields_from_metadata(): void
    {
        $event = new class {};
        $serializer = new ArrayEventSerializer();

        $data = $serializer->serializeStoredEvent(
            $event,
            ['order_id' => 'ORD-1'],
            'ORD-1',
            'user-1',
            metadata: [
                EventMetadata::EVENT_ID => 'evt-1',
                EventMetadata::EVENT_NAME => 'order.created',
                EventMetadata::EVENT_CATEGORY => 'domain',
                EventMetadata::AGGREGATE_TYPE => 'orders',
                EventMetadata::SCHEMA_VERSION => 2,
                EventMetadata::EVENT_VERSION => '2026-05',
                EventMetadata::CORRELATION_ID => 'corr-1',
                EventMetadata::CAUSATION_ID => 'cmd-1',
            ],
        );

        self::assertNotNull($data->envelope);
        self::assertSame('evt-1', $data->envelope->eventId);
        self::assertSame('order.created', $data->envelope->eventName);
        self::assertSame('domain', $data->envelope->eventCategory);
        self::assertSame('orders', $data->envelope->aggregateType);
        self::assertSame(2, $data->envelope->schemaVersion);
        self::assertSame('2026-05', $data->envelope->eventVersion);
        self::assertSame('corr-1', $data->envelope->correlationId);
        self::assertSame('cmd-1', $data->envelope->causationId);
    }

    public function test_serializer_generates_event_id_and_uses_class_basename_when_metadata_is_missing(): void
    {
        $event = new class {};
        $data = (new ArrayEventSerializer())->serializeStoredEvent($event, []);

        self::assertNotNull($data->envelope);
        self::assertNotNull($data->envelope->eventId);
        self::assertNotSame('', $data->envelope->eventId);
        self::assertStringStartsWith('class@anonymous', (string) $data->envelope->eventName);
    }

    public function test_ensure_envelope_fills_missing_event_id_and_name_for_bulk_records(): void
    {
        $data = (new ArrayEventSerializer())->ensureEnvelope(
            new StoredEventData('App\\OrderCreated', ['id' => 1]),
        );

        self::assertNotNull($data->envelope);
        self::assertNotNull($data->envelope->eventId);
        self::assertSame('OrderCreated', $data->envelope->eventName);
    }

    public function test_ensure_envelope_copies_top_level_correlation_into_metadata(): void
    {
        $data = (new ArrayEventSerializer())->ensureEnvelope(
            StoredEventData::fromArray([
                'event_class' => 'OrderCreated',
                'payload' => [],
                'correlation_id' => 'corr-top',
                'causation_id' => 'cmd-top',
            ]),
        );

        self::assertNotNull($data->envelope);
        self::assertSame('corr-top', $data->envelope->correlationId);
        self::assertSame('cmd-top', $data->envelope->causationId);
        self::assertSame('corr-top', $data->metadata[EventMetadata::CORRELATION_ID] ?? null);
        self::assertSame('cmd-top', $data->metadata[EventMetadata::CAUSATION_ID] ?? null);
    }
}
