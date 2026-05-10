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
        $serializer = new ArrayEventSerializer;

        $data = $serializer->serializeStoredEvent(
            $event,
            ['order_id' => 'ORD-1'],
            'ORD-1',
            'user-1',
            metadata: [
                EventMetadata::EVENT_ID => 'evt-1',
                EventMetadata::EVENT_NAME => 'order.created',
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
        $this->assertSame('orders', $data->envelope->aggregateType);
        $this->assertSame(2, $data->envelope->schemaVersion);
        $this->assertSame('2026-05', $data->envelope->eventVersion);
        $this->assertSame('corr-1', $data->envelope->correlationId);
        $this->assertSame('cmd-1', $data->envelope->causationId);
    }

    public function test_serializer_generates_event_id_and_uses_class_basename_when_metadata_is_missing(): void
    {
        $event = new class {};
        $data = (new ArrayEventSerializer)->serializeStoredEvent($event, []);

        $this->assertNotNull($data->envelope);
        $this->assertNotNull($data->envelope->eventId);
        $this->assertNotSame('', $data->envelope->eventId);
        $this->assertStringStartsWith('class@anonymous', (string) $data->envelope->eventName);
    }
}
