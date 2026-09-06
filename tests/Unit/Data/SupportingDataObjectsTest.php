<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Data;

use Faker\Factory as FakerFactory;
use JOOservices\LaravelEvents\Data\EventEnvelopeData;
use JOOservices\LaravelEvents\Tests\TestCase;

final class SupportingDataObjectsTest extends TestCase
{
    public function test_event_envelope_data_accepts_snake_and_camel_keys(): void
    {
        $faker = FakerFactory::create();
        $eventId = $faker->uuid();
        $eventName = $faker->slug();

        $fromSnake = EventEnvelopeData::fromArray([
            'event_id' => $eventId,
            'event_name' => $eventName,
            'schema_version' => 2,
        ]);
        $fromCamel = EventEnvelopeData::fromArray([
            'eventId' => $eventId,
            'eventName' => $eventName,
            'schemaVersion' => 'v2',
        ]);

        self::assertSame($eventId, $fromSnake->eventId);
        self::assertSame($eventName, $fromSnake->eventName);
        self::assertSame(2, $fromSnake->schemaVersion);
        self::assertSame($eventId, $fromCamel->eventId);
        self::assertSame('v2', $fromCamel->schemaVersion);
        self::assertSame($eventId, $fromSnake->toArray()['event_id']);
        self::assertSame($eventName, $fromSnake->toArray()['event_name']);
    }
}
