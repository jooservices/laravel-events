<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Data;

use Faker\Factory as FakerFactory;
use JOOservices\LaravelEvents\Data\EventDiffData;
use JOOservices\LaravelEvents\Data\EventEnvelopeData;
use JOOservices\LaravelEvents\Data\EventMetadataData;
use JOOservices\LaravelEvents\Tests\TestCase;

final class SupportingDataObjectsTest extends TestCase
{
    public function test_event_diff_data_round_trips_values(): void
    {
        $faker = FakerFactory::create();
        $values = [
            $faker->word() => [
                'old' => $faker->word(),
                'new' => $faker->word(),
            ],
        ];

        $data = EventDiffData::fromArray($values);

        $this->assertSame($values, $data->values);
        $this->assertSame($values, $data->toArray());
    }

    public function test_event_metadata_data_round_trips_values(): void
    {
        $faker = FakerFactory::create();
        $values = [
            'correlation_id' => $faker->uuid(),
            'source' => $faker->slug(),
        ];

        $data = EventMetadataData::fromArray($values);

        $this->assertSame($values, $data->values);
        $this->assertSame($values, $data->toArray());
    }

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

        $this->assertSame($eventId, $fromSnake->eventId);
        $this->assertSame($eventName, $fromSnake->eventName);
        $this->assertSame(2, $fromSnake->schemaVersion);
        $this->assertSame($eventId, $fromCamel->eventId);
        $this->assertSame('v2', $fromCamel->schemaVersion);
        $this->assertSame($eventId, $fromSnake->toArray()['event_id']);
        $this->assertSame($eventName, $fromSnake->toArray()['event_name']);
    }
}
