<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Serialization;

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

        $this->assertNotNull($data->envelope);
        $this->assertSame('evt-1', $data->envelope->eventId);
        $this->assertSame('order.created', $data->envelope->eventName);
        $this->assertSame('domain', $data->envelope->eventCategory);
        $this->assertSame('orders', $data->envelope->aggregateType);
        $this->assertSame(2, $data->envelope->schemaVersion);
        $this->assertSame('2026-05', $data->envelope->eventVersion);
        $this->assertSame('corr-1', $data->envelope->correlationId);
        $this->assertSame('cmd-1', $data->envelope->causationId);
    }

    public function test_serializer_generates_event_id_and_uses_class_basename_when_metadata_is_missing(): void
    {
        $event = new class {};
        $data = (new ArrayEventSerializer())->serializeStoredEvent($event, []);

        $this->assertNotNull($data->envelope);
        $this->assertNotNull($data->envelope->eventId);
        $this->assertNotSame('', $data->envelope->eventId);
        $this->assertStringStartsWith('class@anonymous', (string) $data->envelope->eventName);
    }

    public function test_ensure_envelope_fills_missing_event_id_and_name_for_bulk_records(): void
    {
        $data = (new ArrayEventSerializer())->ensureEnvelope(
            new \JOOservices\LaravelEvents\Data\StoredEventData('App\\OrderCreated', ['id' => 1]),
        );

        $this->assertNotNull($data->envelope);
        $this->assertNotNull($data->envelope->eventId);
        $this->assertSame('OrderCreated', $data->envelope->eventName);
    }

    public function test_ensure_envelope_copies_top_level_correlation_into_metadata(): void
    {
        $data = (new ArrayEventSerializer())->ensureEnvelope(
            \JOOservices\LaravelEvents\Data\StoredEventData::fromArray([
                'event_class' => 'OrderCreated',
                'payload' => [],
                'correlation_id' => 'corr-top',
                'causation_id' => 'cmd-top',
            ]),
        );

        $this->assertNotNull($data->envelope);
        $this->assertSame('corr-top', $data->envelope->correlationId);
        $this->assertSame('cmd-top', $data->envelope->causationId);
        $this->assertSame('corr-top', $data->metadata[EventMetadata::CORRELATION_ID] ?? null);
        $this->assertSame('cmd-top', $data->metadata[EventMetadata::CAUSATION_ID] ?? null);
    }
}
